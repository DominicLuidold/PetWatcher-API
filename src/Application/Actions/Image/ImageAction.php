<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Image;

use Exception;
use Illuminate\Database\Capsule\Manager as DBManager;
use Illuminate\Database\Eloquent\Model;
use PetWatcher\Application\Actions\Action;
use PetWatcher\Application\Validation\Validator;
use PetWatcher\Domain\Home;
use PetWatcher\Domain\Pet;
use PetWatcher\Domain\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface as UploadedFile;
use Psr\Log\LoggerInterface;
use Respect\Validation\Validator as v;
use Slim\Routing\RouteContext;

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
     * Perform image action based on specified URI to determine model.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Choose appropriate model based on URI
        $routeContext = RouteContext::fromRequest($this->request);
        switch ($this->request->getRequestTarget()) {
            case $routeContext->getRouteParser()->relativeUrlFor('view-home-image', ['id' => $this->args['id']]):
                $model = new Home();
                $modelName = 'home';
                break;
            case $routeContext->getRouteParser()->relativeUrlFor('view-pet-image', ['id' => $this->args['id']]):
                $model = new Pet();
                $modelName = 'pet';
                break;
            case $routeContext->getRouteParser()->relativeUrlFor('view-user-image', ['id' => $this->args['id']]):
                $model = new User();
                $modelName = 'user';
                break;
            default:
                $model = null;
                $modelName = null;
        }
        return $this->imageAction($model, $modelName);
    }

    /**
     * @param Model  $model     Type of model
     * @param string $modelName String representation of model
     *
     * @return Response
     */
    abstract protected function imageAction(Model $model, string $modelName): Response;

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
