<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\User;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\User;
use Psr\Http\Message\ResponseInterface as Response;

class ViewUserAction extends Action
{
    /**
     * View a specific user, if sufficient permissions are given.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Restrict viewing details of single user to user itself and admins
        if (($this->token['user'] != $this->args['id']) && !$this->token['admin']) {
            return $this->respondWithJson(self::FAILURE, 401, null, 'Insufficient permissions');
        }

        // Database query
        $user = User::find($this->args['id']);
        if (!$user) {
            return $this->respondWithJson(self::FAILURE, 404, null, 'User not found');
        }

        // Response
        return $this->respondWithJson(self::SUCCESS, 200, ['user' => $user->makeHidden('password')]);
    }
}
