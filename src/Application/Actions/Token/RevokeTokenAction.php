<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Token;

use PetWatcher\Domain\Token;
use Psr\Http\Message\ResponseInterface as Response;

class RevokeTokenAction extends TokenAction
{
    /**
     * Revoke a single or all refresh tokens of a user based on provided authentication.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Invalidate expired refresh token(s)
        Token::where('validThru', '<', time())->delete();

        // Verify credentials
        if (!$this->verifyAuthentication($this->request)) {
            return $this->respondWithJson(
                self::FAILURE,
                401,
                ['Authentication' => 'Provided user credentials and/or token invalid'],
                'Authentication does not match requirements'
            );
        }

        // Extract user from request
        $user = $this->getUser($this->request);

        // Invalidate provided refresh token
        if (!empty($this->request->getParsedBody()['refreshToken'])) {
            $token = explode(':', $this->request->getParsedBody()['refreshToken'])[1];
            Token::where('user_id', $user->id)->where('token', $token)->delete();
            return $this->respondWithJson(self::SUCCESS, 200, null);
        }

        // Invalidate all user-specific refresh token(s)
        Token::where('user_id', $user->id)->delete();
        return $this->respondWithJson(self::SUCCESS, 200, null);
    }
}
