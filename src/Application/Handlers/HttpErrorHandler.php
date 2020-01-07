<?php

declare(strict_types=1);

namespace PetWatcher\Application\Handlers;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler;
use Exception;
use Throwable;

class HttpErrorHandler extends ErrorHandler
{

    /**
     * Respond with information from occurring error using the default response schema.
     *
     * @return ResponseInterface
     */
    protected function respond(): ResponseInterface
    {
        // Gather details
        $exception = $this->exception;
        $statusCode = 500;
        $description = 'An internal error has occurred while processing your request.';

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $description = $exception->getMessage();
        } elseif (($exception instanceof Exception || $exception instanceof Throwable) && $this->displayErrorDetails) {
            $description = $exception->getMessage();
        }

        // Prepare response
        $response = [
            'status' => ($statusCode == 500 ? 'error' : 'failure'),
            'code' => $statusCode,
            'message' => $description,
            'data' => null,
        ];

        // Encode response
        $json = json_encode($response, JSON_PRETTY_PRINT);
        if ($json === false) {
            throw new RuntimeException('Malformed UTF-8 characters, possibly incorrectly encoded.');
        }

        // Response
        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($json);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
