<?php

namespace PetWatcher\Controllers;

use Slim\Container;

abstract class BaseController {
    /**
     * @var \Slim\Container $container Instance of dependency container
     */
    protected $container;

    /**
     * @var \Illuminate\Database\Capsule\Manager $db Instance of database manager
     */
    protected $db;

    /**
     * @var \Monolog\Logger $logger Instance of logger
     */
    protected $logger;

    /**
     * @var \PetWatcher\Validation\Validator $validator Instance of validator
     */
    protected $validator;

    /**
     * Create a new base controller
     *
     * @param \Slim\Container $container
     */
    public function __construct(Container $container) {
        $this->container = $container;
        $this->db = $container['db'];
        $this->logger = $container['logger'];
        $this->validator = $container['validator'];
    }
}
