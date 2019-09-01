<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteHomeAction extends Action
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

        // Abort deletion if pets still assigned to this home
        if (!$home->pets()->get()->isEmpty()) {
            return $this->respondWithJson(["message" => "Cannot delete home - pets still assigned to this home"], 409);
        }

        // Database delete
        $home->delete();

        // Response
        $this->logger->info("Deleted home #" . $home->id . " - '" . $home->name . "'");
        return $this->respondWithJson(["message" => "Successfully deleted home"]);
    }
}
