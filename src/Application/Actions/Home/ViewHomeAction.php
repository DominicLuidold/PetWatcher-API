<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class ViewHomeAction extends Action
{
    /**
     * View a specific home.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Database query
        $home = Home::find($this->args['id']);
        if (!$home) {
            return $this->respondWithJson(self::FAILURE, 404, null, "Home not found");
        }

        // Response
        return $this->respondWithJson(self::SUCCESS, 200, ['home' => $home]);
    }
}
