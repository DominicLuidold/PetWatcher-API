<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use Exception;
use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteHomeAction extends Action
{
    /**
     * Delete a specific home, if unoccupied.
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

        // Abort deletion if pets still assigned to this home
        if (!$home->pets()->get()->isEmpty()) {
            return $this->respondWithJson(["message" => "Cannot delete home - pets still assigned to this home"], 409);
        }

        // Database delete
        try {
            $home->delete();
        } catch (Exception $e) {
            $this->logger->error("Attempt to delete home #" . $home->id . " failed");
            return $this->respondWithJson(["message" => "Home deletion failed"], 500);
        }

        // Response
        $this->logger->info("Deleted home #" . $home->id . " - '" . $home->name . "'");
        return $this->respondWithJson(["message" => "Successfully deleted home"]);
    }
}
