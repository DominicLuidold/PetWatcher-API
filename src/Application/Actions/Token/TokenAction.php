<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Token;

use Exception;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Token;
use PetWatcher\Domain\User;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class TokenAction extends Action
{
    /**
     * Verify provided authentication.
     *
     * If a refresh token is given, the token-verification is prioritized over the
     * verification of given user credentials.
     *
     * @param Request $request
     *
     * @return bool True if authentication is verified
     */
    protected function verifyAuthentication(Request $request): bool
    {
        // Extract user from request
        $user = $this->getUser($request);

        // Verify refresh token
        if (!empty($request->getParsedBody()['refreshToken'])) {
            list($userID, $token, $mac) = array_pad(explode(':', $request->getParsedBody()['refreshToken']), 3, null);
            if (
                $user &&
                hash_equals(hash_hmac('SHA512', $userID . ':' . $token, getenv('REFRESH_TOKEN_SECRET')), $mac) &&
                Token::where('user_id', $userID)->where('token', $token)->first()
            ) {
                return true;
            }
        }

        // Verify user credentials if no refresh token is provided
        if ($user && password_verify($request->getParsedBody()['password'] ?? '', $user->password)) {
            return true;
        }

        return false;
    }

    /**
     * Extract the user-object from the provided form of authentication.
     *
     * @param Request $request
     *
     * @return mixed|User
     */
    protected function getUser(Request $request)
    {
        if (!empty($request->getParsedBody()['refreshToken'])) {
            $userID = explode(':', $request->getParsedBody()['refreshToken'])[0];
            return User::find($userID);
        } else {
            return User::where('email', $request->getParsedBody()['email'] ?? '')
                ->orWhere('username', $request->getParsedBody()['username'] ?? '')->first();
        }
    }

    /**
     * Generate a JWT access token.
     *
     * An access token is used to authenticate a user when performing certain actions.
     * Valid for five minutes upon creation.
     *
     * @param User $user
     *
     * @return string
     */
    protected function generateAccessToken(User $user): string
    {
        // Generate list of homes accessible to user
        $homes = [];
        $homesOwned = $user->homesOwned()->get();
        $accessibleHomes = $user->accessibleHomes()->get();
        foreach ($homesOwned->merge($accessibleHomes) as $home) {
            $homes[] = $home->id;
        }

        // Create JWT token
        return JWT::encode(
            [
                'iss' => 'PetWatcher-API',
                'iat' => time(),
                'nbf' => time(),
                'exp' => time() + 60 * 5,
                'user' => $user->id,
                'admin' => $user->admin,
                'homes' => $homes,
            ],
            getenv('ACCESS_TOKEN_SECRET')
        );
    }

    /**
     * Generate a refresh token.
     *
     * A user-specific refresh token is used to generate access tokens without the
     * need to provide further credentials (e.g. username or password). Valid for
     * 14 days upon creation.
     *
     * @param User $user
     *
     * @return string
     * @throws Exception
     */
    protected function generateRefreshToken(User $user): string
    {
        // Token generation
        $token = bin2hex(random_bytes(32));

        // Database insert
        Token::create(
            [
                'user_id' => $user->id,
                'token' => $token,
                'validThru' => time() + 3600 * 24 * 14,
            ]
        );

        // Token + MAC generation
        $refreshToken = $user->id . ':' . $token;
        $mac = hash_hmac('SHA512', $refreshToken, getenv('REFRESH_TOKEN_SECRET'));
        $refreshToken .= ':' . $mac;

        return $refreshToken;
    }
}
