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
     * @var string Action resulted in a success
     */
    protected const SUCCESS = 'success';

    /**
     * @var string Action resulted in a failure (due to invalid data or call conditions)
     */
    protected const FAILURE = 'failure';

    /**
     * @var string Action resulted in an error (due to an error on the server)
     */
    protected const ERROR = 'error';

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
     * Respond with JSON encoded payload and additional information.
     *
     * @param string      $status
     * @param int         $statusCode
     * @param mixed       $payload
     * @param string|null $message
     *
     * @return Response
     */
    protected function respondWithJson(
        string $status,
        int $statusCode,
        $payload,
        string $message = null
    ): Response {
        // Prepare response
        $response = [
            'status' => $status,
            'code' => $statusCode,
            'message' => $message,
            'data' => $payload,
        ];
        if ($status === self::SUCCESS) {
            unset($response['message']);
        }

        // Encode response
        $json = json_encode($response, JSON_PRETTY_PRINT);
        if ($json === false) {
            throw new RuntimeException('Malformed UTF-8 characters, possibly incorrectly encoded.');
        }

        // Response
        $this->response->getBody()->write($json);
        return $this->response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }
}
