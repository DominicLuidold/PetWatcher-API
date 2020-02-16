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
        if ($home == null) {
            return $this->respondWithJson(self::FAILURE, 404, null, 'Home not found');
        }

        // Abort deletion if pets still assigned to this home
        if ($home->pets()->get()->isNotEmpty()) {
            return $this->respondWithJson(
                self::FAILURE,
                409,
                null,
                'Cannot delete home - pet(s) still assigned to this home'
            );
        }

        // Database delete
        try {
            $home->delete();
        } catch (Exception $e) {
            $this->logger->error("Attempt to delete home #{$home->id} failed", ['user' => $this->token['user']]);
            return $this->respondWithJson(self::ERROR, 500, null, 'Home deletion failed');
        }

        // Response
        $this->logger->info("Deleted home #{$home->id} - '{$home->name}'", ['user' => $this->token['user']]);
        return $this->respondWithJson(self::SUCCESS, 200, null);
    }
}
