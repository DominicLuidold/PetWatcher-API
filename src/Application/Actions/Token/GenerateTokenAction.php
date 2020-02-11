<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Token;

use Exception;
use PetWatcher\Domain\Token;
use Psr\Http\Message\ResponseInterface as Response;

class GenerateTokenAction extends TokenAction
{

    /**
     * Generate access and/or refresh token based on provided authentication.
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
                ["Authentication" => "Provided user credentials and/or token invalid"],
                "Authentication does not match requirements"
            );
        }

        // Extract user from request
        $user = $this->getUser($this->request);

        // Generate token using refresh token
        if (!empty($this->request->getParsedBody()['refreshToken'])) {
            // Generate new access token
            return $this->respondWithJson(
                self::SUCCESS,
                201,
                ['accessToken' => $this->generateAccessToken($user),]
            );
        }

        // Generate token using login credentials
        if ($this->request->getParsedBody()['remember'] ?? false) {
            // Generate new access & refresh token
            try {
                return $this->respondWithJson(
                    self::SUCCESS,
                    201,
                    [
                        'accessToken' => $this->generateAccessToken($user),
                        'refreshToken' => $this->generateRefreshToken($user),
                    ]
                );
            } catch (Exception $e) {
                return $this->respondWithJson(self::SUCCESS, 500, null, "Internal error");
            }
        }

        // Generate new access token
        return $this->respondWithJson(self::SUCCESS, 201, ['accessToken' => $this->generateAccessToken($user)]);
    }
}
