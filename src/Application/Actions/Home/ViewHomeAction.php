<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

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
        if (!$home) {
            return $this->respondWithJson(["message" => "Home not found"], 404);
        }

        // Response
        return $this->respondWithJson($home);
    }
}
