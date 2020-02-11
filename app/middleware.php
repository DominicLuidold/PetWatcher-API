<?php

declare(strict_types=1);

use PetWatcher\Application\Middleware\JwtAuthentication\RequestPathMethodRule;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use Tuupola\Middleware\JwtAuthentication;

return function (App $app) {
    $app->add(new JwtAuthentication([
        'secret' => getenv('ACCESS_TOKEN_SECRET'),
        'rules' => [
            // Allow access to specific routes without authentication
            new RequestPathMethodRule([
                'passthrough' => [
                    '/v1/token' => ['POST'],
                    '/v1/users' => ['POST'],
                ],
            ])
        ],
        'error' => function (Response $response, array $args) {
            // Prepare payload
            $payload = [
                'status' => 'failure',
                'code' => 401,
                'message' => 'Authentication does not match requirements',
                'data' => [
                    'accessToken' => $args['message'],
                ],
            ];

            // Encode payload
            $json = json_encode($payload, JSON_PRETTY_PRINT);
            if ($json === false) {
                throw new RuntimeException('Malformed UTF-8 characters, possibly incorrectly encoded.');
            }

            // Response
            $response->getBody()->write($json);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        },
    ]));
};
