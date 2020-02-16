<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class ViewHomeAction extends Action
{
    /**
     * View a specific home.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Database query
        $home = Home::find($this->args['id']);
        if ($home == null) {
            return $this->respondWithJson(self::FAILURE, 404, null, 'Home not found');
        }

        // Insert resource-specific URIs to ease further navigation
        $home->owner = RouteContext::fromRequest($this->request)->getRouteParser()->relativeUrlFor(
            'view-user',
            ['id' => $home->owner]
        );
        if ($home->image != null) {
            $home->image = RouteContext::fromRequest($this->request)->getRouteParser()->relativeUrlFor(
                'view-home-image',
                ['id' => $home->id]
            );
        }


        // Response
        return $this->respondWithJson(self::SUCCESS, 200, ['home' => $home]);
    }
}
