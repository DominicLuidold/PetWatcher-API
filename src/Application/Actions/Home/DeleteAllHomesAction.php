<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use Exception;
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
            if ($home->pets()->get()->isNotEmpty()) {
                $omittedHomes[] = $home->id;
            } else {
                try {
                    $home->delete();
                } catch (Exception $e) {
                    $omittedHomes[] = $home->id;
                    $this->logger->error(
                        "Attempted to delete home #{$home->id} failed",
                        ['user' => $this->token['user']]
                    );
                }
            }
        }

        // Response
        if ($omittedHomes) {
            $this->logger->notice(
                'Attempted to delete all homes - some remain untouched',
                ['user' => $this->token['user']]
            );
            return $this->respondWithJson(
                self::FAILURE,
                409,
                ['homes' => $omittedHomes],
                'Cannot delete the following homes - pets still assigned'
            );
        }
        $this->logger->info('Deleted all homes', ['user' => $this->token['user']]);
        return $this->respondWithJson(self::SUCCESS, 200, null);
    }
}
