<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home\Image;

use PetWatcher\Application\Actions\ImageAction;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteHomeImageAction extends ImageAction
{
    /**
     * Delete an image of a specific home.
     *
     * @return Response
     */
    public function action(): Response
    {
        // Database query
        $home = Home::find($this->args['id']);
        if (!$home) {
            return $this->respondWithJson(self::FAILURE, 404, null, "Home not found");
        }
        if ($home->image == "") {
            return $this->respondWithJson(self::FAILURE, 404, null, "Image not found");
        }

        // File deletion
        if (!is_writable($this->imgUpload['directory'] . $home->image)
            || !unlink($this->imgUpload['directory'] . $home->image)) {
            $this->logger->error("Attempt to delete image of home #" . $home->id . " failed");
            return $this->respondWithJson(self::ERROR, 500, null, "Image deletion failed");
        }

        // Database update
        $home->update(
            [
                'image' => '',
            ]
        );

        // Response
        $this->logger->info("Deleted image of home #" . $home->id . " - '" . $home->name . "'");
        return $this->respondWithJson(self::SUCCESS, 200, null);
    }
}
