<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions;

use Exception;
use Illuminate\Database\Capsule\Manager as DBManager;
use PetWatcher\Application\Validation\Validator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\UploadedFileInterface as UploadedFile;
use Psr\Log\LoggerInterface;
use Respect\Validation\Validator as v;

abstract class ImageAction extends Action
{
    /**
     * @var array $imgUpload Image upload settings
     */
    protected $imgUpload;

    /**
     * ImageAction constructor.
     *
     * @param ContainerInterface $container
     * @param DBManager          $db
     * @param LoggerInterface    $logger
     * @param Validator          $validator
     */
    public function __construct(
        ContainerInterface $container,
        DBManager $db,
        LoggerInterface $logger,
        Validator $validator
    ) {
        parent::__construct($container, $db, $logger, $validator);
        $this->imgUpload = $container->get('settings')['upload'];
    }

    /**
     * Validate the uploaded file.
     *
     * @param array $image
     *
     * @return Validator
     */
    protected function validateUploadedFile(array $image): Validator
    {
        return $this->validator->validate(
            $image,
            [
                'file' => v::image(),
                'size' => v::size(null, $this->imgUpload['maxSize']),
            ],
            true
        );
    }

    /**
     * Move the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file.
     *
     * @param string       $directory    Directory to which the file is moved
     * @param UploadedFile $uploadedFile Uploaded file to move
     *
     * @return string Filename of moved file
     * @throws Exception on any error during the move operation
     */
    protected function moveUploadedFile(string $directory, UploadedFile $uploadedFile): string
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(16));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}
