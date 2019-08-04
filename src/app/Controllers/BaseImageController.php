<?php

namespace PetWatcher\Controllers;

use Slim\Container;
use Slim\Http\UploadedFile;

abstract class BaseImageController extends BaseController {
    /**
     * @var String $imgUpload Image upload settings
     */
    protected $imgUpload;

    /**
     * Create a new base image controller
     *
     * @param Container $container
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->imgUpload = $container->get('settings')['upload'];
    }

    /**
     * Move the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file
     *
     * @param string $directory Directory to which the file is moved
     * @param UploadedFile $uploadedFile Uploaded file to move
     * @return string Filename of moved file
     * @throws \Exception on any error during the move operation
     */
    protected function moveUploadedFile(string $directory, UploadedFile $uploadedFile): string {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(16));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}
