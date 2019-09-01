<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class ListHomesAction extends Action
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        // Database query
        $homes = Home::all();

        // Insert resource-specific URI to ease further navigation
        foreach ($homes as $home) {
            $home->URI = $this->request->getUri()->getPath() . '/' . $home->id;
        }

        // Response
        return $this->respondWithJson($homes);
    }
}
