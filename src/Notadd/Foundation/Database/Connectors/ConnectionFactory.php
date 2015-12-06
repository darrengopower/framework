<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 01:49
 */
namespace Notadd\Foundation\Database\Connectors;
use Illuminate\Contracts\Container\Container;
use PDO;
class ConnectionFactory {
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;
    /**
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(Container $container) {
        $this->container = $container;
    }
    /**
     * @param array $config
     * @param string $name
     * @return \Notadd\Foundation\Database\Connection
     */
    public function make(array $config, $name = null) {
        $config = $this->parseConfig($config, $name);
        if(isset($config['read'])) {
            return $this->createReadWriteConnection($config);
        }
        return $this->createSingleConnection($config);
    }
    /**
     * @param array $config
     * @return \Notadd\Foundation\Database\Connection
     */
    protected function createSingleConnection(array $config) {
        $pdo = $this->createConnector($config)->connect($config);
        return $this->createConnection($config['driver'], $pdo, $config['database'], $config['prefix'], $config);
    }
    /**
     * @param array $config
     * @return \Notadd\Foundation\Database\Connection
     */
    protected function createReadWriteConnection(array $config) {
        $connection = $this->createSingleConnection($this->getWriteConfig($config));
        return $connection->setReadPdo($this->createReadPdo($config));
    }
    /**
     * @param array $config
     * @return \PDO
     */
    protected function createReadPdo(array $config) {
        $readConfig = $this->getReadConfig($config);
        return $this->createConnector($readConfig)->connect($readConfig);
    }
    /**
     * @param array $config
     * @return array
     */
    protected function getReadConfig(array $config) {
        $readConfig = $this->getReadWriteConfig($config, 'read');
        if(isset($readConfig['host']) && is_array($readConfig['host'])) {
            $readConfig['host'] = count($readConfig['host']) > 1 ? $readConfig['host'][array_rand($readConfig['host'])] : $readConfig['host'][0];
        }
        return $this->mergeReadWriteConfig($config, $readConfig);
    }
    /**
     * @param array $config
     * @return array
     */
    protected function getWriteConfig(array $config) {
        $writeConfig = $this->getReadWriteConfig($config, 'write');
        return $this->mergeReadWriteConfig($config, $writeConfig);
    }
    /**
     * @param array $config
     * @param string $type
     * @return array
     */
    protected function getReadWriteConfig(array $config, $type) {
        if(isset($config[$type][0])) {
            return $config[$type][array_rand($config[$type])];
        }
        return $config[$type];
    }
    /**
     * @param array $config
     * @param array $merge
     * @return array
     */
    protected function mergeReadWriteConfig(array $config, array $merge) {
        return array_except(array_merge($config, $merge), [
            'read',
            'write'
        ]);
    }
    /**
     * @param array $config
     * @param string $name
     * @return array
     */
    protected function parseConfig(array $config, $name) {
        return Arr::add(Arr::add($config, 'prefix', ''), 'name', $name);
    }
    /**
     * @param array $config
     * @return \Notadd\Foundation\Database\Connectors\ConnectorInterface
     * @throws \InvalidArgumentException
     */
    public function createConnector(array $config) {
        if(!isset($config['driver'])) {
            throw new InvalidArgumentException('A driver must be specified.');
        }
        if($this->container->bound($key = "db.connector.{$config['driver']}")) {
            return $this->container->make($key);
        }
        switch($config['driver']) {
            case 'mysql':
                return new MySqlConnector;
            case 'pgsql':
                return new PostgresConnector;
            case 'sqlite':
                return new SQLiteConnector;
            case 'sqlsrv':
                return new SqlServerConnector;
        }
        throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }
    /**
     * @param string $driver
     * @param \PDO $connection
     * @param string $database
     * @param string $prefix
     * @param array $config
     * @return \Notadd\Foundation\Database\Connection
     * @throws \InvalidArgumentException
     */
    protected function createConnection($driver, PDO $connection, $database, $prefix = '', array $config = []) {
        if($this->container->bound($key = "db.connection.{$driver}")) {
            return $this->container->make($key, [
                $connection,
                $database,
                $prefix,
                $config
            ]);
        }
        switch($driver) {
            case 'mysql':
                return new MySqlConnection($connection, $database, $prefix, $config);
            case 'pgsql':
                return new PostgresConnection($connection, $database, $prefix, $config);
            case 'sqlite':
                return new SQLiteConnection($connection, $database, $prefix, $config);
            case 'sqlsrv':
                return new SqlServerConnection($connection, $database, $prefix, $config);
        }
        throw new InvalidArgumentException("Unsupported driver [$driver]");
    }
}