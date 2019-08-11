<?php
declare(strict_types=1);

namespace PetWatcher\Controllers;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use PetWatcher\Validation\Validator;
use Respect\Validation\Validator as v;
use Slim\Psr7\UploadedFile;

abstract class BaseImageController extends BaseController {
    /**
     * @var array $imgUpload Image upload settings
     */
    protected $imgUpload;

    /**
     * BaseImageController constructor
     *
     * @param Container $container
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->imgUpload = $container->get('settings')['upload'];
    }

    /**
     * Validate the uploaded file
     *
     * @param array $image
     * @return Validator
     */
    protected function validateUploadedFile(array $image): Validator {
        return $this->validator->validate($image, [
            'file' => v::image(),
            'size' => v::size(null, $this->imgUpload['maxSize']),
        ], true);
    }

    /**
     * Move the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file
     *
     * @param string $directory Directory to which the file is moved
     * @param UploadedFile $uploadedFile Uploaded file to move
     * @return string Filename of moved file
     * @throws Exception on any error during the move operation
     */
    protected function moveUploadedFile(string $directory, UploadedFile $uploadedFile): string {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(16));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}
