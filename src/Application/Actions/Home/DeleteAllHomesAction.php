<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteAllHomesAction extends Action
{
    /**
     * Delete all unoccupied homes.
     *
     * @return Response
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
                self::FAILURE,
                409,
                ['homes' => $omittedHomes],
                "Cannot delete the following homes - pets still assigned"
            );
        }
        $this->logger->info("Deleted all homes");
        return $this->respondWithJson(self::SUCCESS, 200, null);
    }
}
