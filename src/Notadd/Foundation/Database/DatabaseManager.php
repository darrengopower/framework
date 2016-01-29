<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:31
 */
namespace Notadd\Foundation\Database;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Notadd\Foundation\Database\Connectors\ConnectionFactory;
/**
 * Class DatabaseManager
 * @package Notadd\Foundation\Database
 */
class DatabaseManager implements ConnectionResolverInterface {
    /**
     * @var \Notadd\Foundation\Application
     */
    protected $app;
    /**
     * @var \Notadd\Foundation\Database\Connectors\ConnectionFactory
     */
    protected $factory;
    /**
     * @var array
     */
    protected $connections = [];
    /**
     * @var array
     */
    protected $extensions = [];
    /**
     * DatabaseManager constructor.
     * @param $app
     * @param \Notadd\Foundation\Database\Connectors\ConnectionFactory $factory
     */
    public function __construct($app, ConnectionFactory $factory) {
        $this->app = $app;
        $this->factory = $factory;
    }
    /**
     * @param string $name
     * @return \Notadd\Foundation\Database\Connection
     */
    public function connection($name = null) {
        list($name, $type) = $this->parseConnectionName($name);
        if(!isset($this->connections[$name])) {
            $connection = $this->makeConnection($name);
            $this->setPdoForType($connection, $type);
            $this->connections[$name] = $this->prepare($connection);
        }
        return $this->connections[$name];
    }
    /**
     * @param string $name
     * @return array
     */
    protected function parseConnectionName($name) {
        $name = $name ?: $this->getDefaultConnection();
        return Str::endsWith($name, [
            '::read',
            '::write'
        ]) ? explode('::', $name, 2) : [
            $name,
            null
        ];
    }
    /**
     * @param string $name
     * @return void
     */
    public function purge($name = null) {
        $this->disconnect($name);
        unset($this->connections[$name]);
    }
    /**
     * @param string $name
     * @return void
     */
    public function disconnect($name = null) {
        if(isset($this->connections[$name = $name ?: $this->getDefaultConnection()])) {
            $this->connections[$name]->disconnect();
        }
    }
    /**
     * @param string $name
     * @return \Notadd\Foundation\Database\Connection
     */
    public function reconnect($name = null) {
        $this->disconnect($name = $name ?: $this->getDefaultConnection());
        if(!isset($this->connections[$name])) {
            return $this->connection($name);
        }
        return $this->refreshPdoConnections($name);
    }
    /**
     * @param string $name
     * @return \Notadd\Foundation\Database\Connection
     */
    protected function refreshPdoConnections($name) {
        $fresh = $this->makeConnection($name);
        return $this->connections[$name]->setPdo($fresh->getPdo())->setReadPdo($fresh->getReadPdo());
    }
    /**
     * @param string $name
     * @return \Notadd\Foundation\Database\Connection
     */
    protected function makeConnection($name) {
        $config = $this->getConfig($name);
        if(isset($this->extensions[$name])) {
            return call_user_func($this->extensions[$name], $config, $name);
        }
        $driver = $config['driver'];
        if(isset($this->extensions[$driver])) {
            return call_user_func($this->extensions[$driver], $config, $name);
        }
        return $this->factory->make($config, $name);
    }
    /**
     * @param \Notadd\Foundation\Database\Connection $connection
     * @return \Notadd\Foundation\Database\Connection
     */
    protected function prepare(Connection $connection) {
        $connection->setFetchMode($this->app['config']['database.fetch']);
        if($this->app->bound('events')) {
            $connection->setEventDispatcher($this->app['events']);
        }
        $connection->setReconnector(function ($connection) {
            $this->reconnect($connection->getName());
        });
        return $connection;
    }
    /**
     * @param \Notadd\Foundation\Database\Connection $connection
     * @param string $type
     * @return \Notadd\Foundation\Database\Connection
     */
    protected function setPdoForType(Connection $connection, $type = null) {
        if($type == 'read') {
            $connection->setPdo($connection->getReadPdo());
        } elseif($type == 'write') {
            $connection->setReadPdo($connection->getPdo());
        }
        return $connection;
    }
    /**
     * @param string $name
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function getConfig($name) {
        $name = $name ?: $this->getDefaultConnection();
        $connections = $this->app['config']['database.connections'];
        if(is_null($config = Arr::get($connections, $name))) {
            throw new InvalidArgumentException("Database [$name] not configured.");
        }
        return $config;
    }
    /**
     * @return string
     */
    public function getDefaultConnection() {
        return $this->app['config']['database.default'];
    }
    /**
     * @param string $name
     * @return void
     */
    public function setDefaultConnection($name) {
        $this->app['config']['database.default'] = $name;
    }
    /**
     * @param string $name
     * @param callable $resolver
     * @return void
     */
    public function extend($name, callable $resolver) {
        $this->extensions[$name] = $resolver;
    }
    /**
     * @return array
     */
    public function getConnections() {
        return $this->connections;
    }
    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        return call_user_func_array([
            $this->connection(),
            $method
        ], $parameters);
    }
}