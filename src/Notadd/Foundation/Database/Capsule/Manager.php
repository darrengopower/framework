<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 01:47
 */
namespace Notadd\Foundation\Database\Capsule;
use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Traits\CapsuleManagerTrait;
use Notadd\Foundation\Database\Connectors\ConnectionFactory;
use Notadd\Foundation\Database\DatabaseManager;
use Notadd\Foundation\Database\Eloquent\Model as Eloquent;
use PDO;
/**
 * Class Manager
 * @package Notadd\Foundation\Database\Capsule
 */
class Manager {
    use CapsuleManagerTrait;
    /**
     * @var \Notadd\Foundation\Database\DatabaseManager
     */
    protected $manager;
    /**
     * Manager constructor.
     * @param \Illuminate\Container\Container|null $container
     */
    public function __construct(Container $container = null) {
        $this->setupContainer($container ?: new Container);
        $this->setupDefaultConfiguration();
        $this->setupManager();
    }
    /**
     * @return void
     */
    protected function setupDefaultConfiguration() {
        $this->container['config']['database.fetch'] = PDO::FETCH_OBJ;
        $this->container['config']['database.default'] = 'default';
    }
    /**
     * @return void
     */
    protected function setupManager() {
        $factory = new ConnectionFactory($this->container);
        $this->manager = new DatabaseManager($this->container, $factory);
    }
    /**
     * Get a connection instance from the global manager.
     * @param string $connection
     * @return \Notadd\Foundation\Database\Connection
     */
    public static function connection($connection = null) {
        return static::$instance->getConnection($connection);
    }
    /**
     * Get a fluent query builder instance.
     * @param string $table
     * @param string $connection
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    public static function table($table, $connection = null) {
        return static::$instance->connection($connection)->table($table);
    }
    /**
     * Get a schema builder instance.
     * @param string $connection
     * @return \Notadd\Foundation\Database\Schema\Builder
     */
    public static function schema($connection = null) {
        return static::$instance->connection($connection)->getSchemaBuilder();
    }
    /**
     * Get a registered connection instance.
     * @param string $name
     * @return \Notadd\Foundation\Database\Connection
     */
    public function getConnection($name = null) {
        return $this->manager->connection($name);
    }
    /**
     * Register a connection with the manager.
     * @param array $config
     * @param string $name
     * @return void
     */
    public function addConnection(array $config, $name = 'default') {
        $connections = $this->container['config']['database.connections'];
        $connections[$name] = $config;
        $this->container['config']['database.connections'] = $connections;
    }
    /**
     * Bootstrap Eloquent so it is ready for usage.
     * @return void
     */
    public function bootEloquent() {
        Eloquent::setConnectionResolver($this->manager);
        if($dispatcher = $this->getEventDispatcher()) {
            Eloquent::setEventDispatcher($dispatcher);
        }
    }
    /**
     * @param int $fetchMode
     * @return $this
     */
    public function setFetchMode($fetchMode) {
        $this->container['config']['database.fetch'] = $fetchMode;
        return $this;
    }
    /**
     * @return \Notadd\Foundation\Database\DatabaseManager
     */
    public function getDatabaseManager() {
        return $this->manager;
    }
    /**
     * @return \Illuminate\Contracts\Events\Dispatcher|null
     */
    public function getEventDispatcher() {
        if($this->container->bound('events')) {
            return $this->container['events'];
        }
    }
    /**
     * @param \Illuminate\Contracts\Events\Dispatcher $dispatcher
     * @return void
     */
    public function setEventDispatcher(Dispatcher $dispatcher) {
        $this->container->instance('events', $dispatcher);
    }
    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters) {
        return call_user_func_array([
            static::connection(),
            $method
        ], $parameters);
    }
}