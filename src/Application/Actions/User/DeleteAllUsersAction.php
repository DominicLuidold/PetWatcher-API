<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\User;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\User;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteAllUsersAction extends Action
{
    /**
     * Delete all users that do not own any homes, if sufficient permissions are given.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Restrict deletion of all users to admins
        if (!$this->token['admin']) {
            return $this->respondWithJson(self::FAILURE, 401, null, 'Insufficient permissions');
        }

        // Database query
        $users = User::all();

        // Database delete
        $omittedUsers = [];
        foreach ($users as $user) {
            // Skip deletion if user still owns home(s)
            if ($user->homesOwned()->get()->isEmpty()) {
                $user->delete();
            } else {
                $omittedUsers[] = $user->id;
            }
        }

        // Response
        if ($omittedUsers) {
            $this->logger->info('Attempted to delete all users - some remain untouched');
            return $this->respondWithJson(
                self::FAILURE,
                409,
                ['users' => $omittedUsers],
                'Cannot delete the following users - still owns home(s)'
            );
        }
        $this->logger->info('Deleted all users');
        return $this->respondWithJson(self::SUCCESS, 200, null);
    }
}
