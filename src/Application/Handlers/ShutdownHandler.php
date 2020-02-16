<?php

declare(strict_types=1);

namespace PetWatcher\Application\Handlers;

use Slim\Exception\HttpInternalServerErrorException;
use Slim\ResponseEmitter;
use Psr\Http\Message\ServerRequestInterface as Request;

class ShutdownHandler
{
    /**
     * @var Request $request
     */
    private $request;

    /**
     * @var HttpErrorHandler $errorHandler
     */
    private $errorHandler;

    /**
     * @var bool $displayErrorDetails
     */
    private $displayErrorDetails;

    /**
     * ShutdownHandler constructor.
     *
     * @param Request          $request
     * @param HttpErrorHandler $errorHandler
     * @param bool             $displayErrorDetails
     */
    public function __construct(Request $request, HttpErrorHandler $errorHandler, bool $displayErrorDetails)
    {
        $this->request = $request;
        $this->errorHandler = $errorHandler;
        $this->displayErrorDetails = $displayErrorDetails;
    }

    /**
     * Determine error message (level of detail) and emit response.
     */
    public function __invoke(): void
    {
        $error = error_get_last();
        if ($error) {
            $errorFile = $error['file'];
            $errorLine = $error['line'];
            $errorMessage = $error['message'];
            $errorType = $error['type'];
            $message = 'An error occurred while processing your request. Please try again later.';

            // Determine level of detail of error message
            if ($this->displayErrorDetails) {
                switch ($errorType) {
                    case E_USER_ERROR:
                        $message = "FATAL ERROR: {$errorMessage}. ";
                        $message .= " on line {$errorLine} in file {$errorFile}.";
                        break;
                    case E_USER_WARNING:
                        $message = "WARNING: {$errorMessage}";
                        break;
                    case E_USER_NOTICE:
                        $message = "NOTICE: {$errorMessage}";
                        break;
                    default:
                        $message = "ERROR: {$errorMessage}";
                        $message .= " on line {$errorLine} in file {$errorFile}.";
                        break;
                }
            }

            // Prepare error response
            $exception = new HttpInternalServerErrorException($this->request, $message);
            $response = $this->errorHandler->__invoke(
                $this->request,
                $exception,
                $this->displayErrorDetails,
                false,
                false
            );

            if (ob_get_length()) {
                ob_clean();
            }

            // Emit response
            $responseEmitter = new ResponseEmitter();
            $responseEmitter->emit($response);
        }
    }
}
