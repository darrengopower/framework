<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:29
 */
namespace Notadd\Foundation\Database;
class ConnectionResolver implements ConnectionResolverInterface {
    /**
     * @var array
     */
    protected $connections = [];
    /**
     * @var string
     */
    protected $default;
    /**
     * @param array $connections
     */
    public function __construct(array $connections = []) {
        foreach($connections as $name => $connection) {
            $this->addConnection($name, $connection);
        }
    }
    /**
     * @param string $name
     * @return \Notadd\Foundation\Database\ConnectionInterface
     */
    public function connection($name = null) {
        if(is_null($name)) {
            $name = $this->getDefaultConnection();
        }
        return $this->connections[$name];
    }
    /**
     * @param string $name
     * @param \Notadd\Foundation\Database\ConnectionInterface $connection
     * @return void
     */
    public function addConnection($name, ConnectionInterface $connection) {
        $this->connections[$name] = $connection;
    }
    /**
     * @param string $name
     * @return bool
     */
    public function hasConnection($name) {
        return isset($this->connections[$name]);
    }
    /**
     * @return string
     */
    public function getDefaultConnection() {
        return $this->default;
    }
    /**
     * @param string $name
     * @return void
     */
    public function setDefaultConnection($name) {
        $this->default = $name;
    }
}