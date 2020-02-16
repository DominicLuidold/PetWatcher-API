<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\User;

use Exception;
use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\User;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteUserAction extends Action
{
    /**
     * Delete a specific user, if sufficient permissions are given
     * and no homes are owned by the user.
     *
     * @return Response
     * @throws Exception
     */
    protected function action(): Response
    {
        // Restrict deletion of single user to user itself and admins
        if (($this->token['user'] != $this->args['id']) && !$this->token['admin']) {
            return $this->respondWithJson(self::FAILURE, 401, null, 'Insufficient permissions');
        }

        // Database query
        $user = User::find($this->args['id']);
        if ($user == null) {
            return $this->respondWithJson(self::FAILURE, 404, null, 'User not found');
        }

        // Abort deletion if user still owns homes
        if ($user->homesOwned()->get()->isNotEmpty()) {
            return $this->respondWithJson(
                self::FAILURE,
                409,
                null,
                'Cannot delete user - home(s) still assigned to this user'
            );
        }

        // Database delete
        try {
            $user->delete();
        } catch (Exception $e) {
            $this->logger->error("Attempt to delete user #{$user->id} failed", ['user' => $this->token['user']]);
            return $this->respondWithJson(self::ERROR, 500, null, 'User deletion failed');
        }

        // Response
        $this->logger->info("Deleted user #{$user->id} - '{$user->username}'", ['user' => $this->token['user']]);
        return $this->respondWithJson(self::SUCCESS, 200, null);
    }
}
