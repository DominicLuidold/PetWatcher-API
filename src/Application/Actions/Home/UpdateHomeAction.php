<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class UpdateHomeAction extends HomeAction
{
    /**
     * Update a specific home or create a new one, if home does not exist.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Database query
        $home = Home::find($this->args['id']);
        if ($home == null) {
            // Create home if specified id does not exist yet
            return $this->response->withHeader(
                'Location',
                RouteContext::fromRequest($this->request)->getRouteParser()->relativeUrlFor('create-home')
            )->withStatus(307);
        }

        // Input validation
        $validation = $this->validateInput($this->request);
        if ($validation->failed()) {
            return $this->respondWithJson(
                self::FAILURE,
                400,
                $validation->getErrors(),
                'Input does not match requirements'
            );
        }

        // Database update
        $home->update(
            [
                'name' => $this->request->getParsedBody()['name'],
                'owner' => $this->request->getParsedBody()['owner'],
            ]
        );

        // Response
        $this->logger->info("Updated home #{$home->id} - '{$home->name}'", ['user' => $this->token['user']]);
        return $this->respondWithJson(self::SUCCESS, 200, ['home' => $home]);
    }
}
