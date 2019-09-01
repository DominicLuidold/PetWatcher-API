<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class ListHomePetsAction extends Action
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        // Database query
        $home = Home::find($this->args['id']);
        if (!$home) {
            return $this->respondWithJson(["message" => "Home not found"], 404);
        }
        $pets = $home->pets()->get();

        // Insert resource-specific URI to ease further navigation
        // TODO
        foreach ($pets as $pet) {
            $pet->URI = "";
        }

        // Response
        return $this->respondWithJson($pets);
    }
}
