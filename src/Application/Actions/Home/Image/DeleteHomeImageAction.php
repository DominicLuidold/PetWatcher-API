<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home\Image;

use PetWatcher\Application\Actions\ImageAction;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteHomeImageAction extends ImageAction
{
    /**
     * {@inheritDoc}
     */
    public function action(): Response
    {
        // Database query
        $home = Home::find($this->args['id']);
        if (!$home) {
            return $this->respondWithJson(["message" => "Home not found"], 404);
        }
        if ($home->image == "") {
            return $this->respondWithJson(["message" => "Image not found"], 404);
        }

        // File deletion
        if (!is_writable($this->imgUpload['directory'] . $home->image)
            || !unlink($this->imgUpload['directory'] . $home->image)) {
            $this->logger->error("Attempt to delete image of home #" . $home->id . " failed");
            return $this->respondWithJson(["message" => "Image deletion failed"], 500);
        }

        // Database update
        $home->update(
            [
                'image' => '',
            ]
        );

        // Response
        $this->logger->info("Deleted image of home #" . $home->id . " - '" . $home->name . "'");
        return $this->respondWithJson(["message" => "Successfully deleted image"]);
    }
}
