<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateHomeAction extends HomeAction
{
    /**
     * Update a specific home or create a new one, if home does not exist.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Database query
        $home = Home::find($this->args['id']);
        if (!$home) {
            // Create home if specified id does not exist yet
            return $this->response->withHeader('Location', '/api/v1/homes')->withStatus(307);
        }

        // Input validation
        $validation = $this->validateInput($this->request);
        if ($validation->failed()) {
            return $this->respondWithJson(["message" => $validation->getErrors()], 400);
        }

        // Database update
        $home->update(
            [
                'name' => $this->request->getParsedBody()['name'],
            ]
        );

        // Response
        $this->logger->info("Updated home #" . $home->id . " - '" . $home->name . "'");
        return $this->respondWithJson(["message" => "Successfully updated home"]);
    }
}
