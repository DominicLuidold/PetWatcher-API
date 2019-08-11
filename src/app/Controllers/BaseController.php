<?php
declare(strict_types=1);

namespace PetWatcher\Controllers;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Illuminate\Database\Capsule\Manager;
use Monolog\Logger;
use PetWatcher\Validation\Validator;
use Psr\Http\Message\ResponseInterface as Response;

abstract class BaseController {
    /**
     * @var Container $container Instance of dependency container
     */
    protected $container;

    /**
     * @var Manager $db Instance of database manager
     */
    protected $db;

    /**
     * @var Logger $logger Instance of logger
     */
    protected $logger;

    /**
     * @var Validator $validator Instance of validator
     */
    protected $validator;

    /**
     * BaseController constructor
     *
     * @param Container $container
     *
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(Container $container) {
        $this->container = $container;
        $this->db = $container->get('db');
        $this->logger = $container->get('logger');
        $this->validator = $container->get('validator');
    }

    /**
     * Prepare response with JSON encoded payload
     *
     * @param Response $response
     * @param array    $payload
     * @param int      $status
     *
     * @return Response
     */
    protected function respondWithJson(Response $response, array $payload, int $status = 200): Response {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $response->getBody()->write($json);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
