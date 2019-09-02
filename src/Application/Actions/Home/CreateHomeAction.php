<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class CreateHomeAction extends HomeAction
{
    /**
     * Create a new home based on input.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Input validation
        $validation = $this->validateInput($this->request);
        if ($validation->failed()) {
            return $this->respondWithJson(["message" => $validation->getErrors()], 400);
        }

        // Database insert
        $home = Home::create(
            [
                'name' => $this->request->getParsedBody()['name'],
            ]
        );

        // Response
        $this->logger->info("Created home #" . $home->id . " - '" . $home->name . "'");
        return $this->respondWithJson(["message" => "Successfully created home", "id" => $home->id], 201);
    }
}
