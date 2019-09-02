<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions;

use Illuminate\Database\Capsule\Manager as DBManager;
use PetWatcher\Application\Validation\Validator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use RuntimeException;

abstract class Action
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @var DBManager $db
     */
    protected $db;

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var Validator $validator
     */
    protected $validator;

    /**
     * @var Request $request
     */
    protected $request;

    /**
     * @var Response $response
     */
    protected $response;

    /**
     * @var array $args
     */
    protected $args;

    /**
     * Action constructor.
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
        $this->container = $container;
        $this->db = $db;
        $this->logger = $logger;
        $this->validator = $validator;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        return $this->action();
    }

    /**
     * @return Response
     */
    abstract protected function action(): Response;

    /**
     * Prepare response with JSON encoded payload.
     *
     * @param mixed    $payload
     * @param int      $status
     *
     * @return Response
     */
    protected function respondWithJson($payload, int $status = 200): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        if ($json === false) {
            throw new RuntimeException('Malformed UTF-8 characters, possibly incorrectly encoded.');
        }

        $this->response->getBody()->write($json);
        return $this->response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
