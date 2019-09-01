<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteHomesAction extends Action
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        // Database query
        $homes = Home::all();

        // Database delete
        $omittedHomes = [];
        foreach ($homes as $home) {
            // Skip deletion if pets still assigned to this home
            if ($home->pets()->get()->isEmpty()) {
                $home->delete();
            } else {
                $omittedHomes[] = $home->id;
            }
        }

        // Response
        if ($omittedHomes) {
            $this->logger->info("Attempted to delete all homes - some remain untouched");
            return $this->respondWithJson(
                [
                    "message" => "Cannot delete following homes - pets still assigned",
                    "homes" => $omittedHomes
                ],
                409
            );
        }
        $this->logger->info("Deleted all homes");
        return $this->respondWithJson(["message" => "Successfully deleted all homes"]);
    }
}
