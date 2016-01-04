<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 01:54
 */
namespace Notadd\Foundation\Database\Connectors;
/**
 * Class MySqlConnector
 * @package Notadd\Foundation\Database\Connectors
 */
class MySqlConnector extends Connector implements ConnectorInterface {
    /**
     * @param array $config
     * @return \PDO
     */
    public function connect(array $config) {
        $dsn = $this->getDsn($config);
        $options = $this->getOptions($config);
        $connection = $this->createConnection($dsn, $config, $options);
        if(isset($config['unix_socket'])) {
            $connection->exec("use `{$config['database']}`;");
        }
        $collation = $config['collation'];
        $charset = $config['charset'];
        $names = "set names '$charset'" . (!is_null($collation) ? " collate '$collation'" : '');
        $connection->prepare($names)->execute();
        if(isset($config['timezone'])) {
            $connection->prepare('set time_zone="' . $config['timezone'] . '"')->execute();
        }
        if(isset($config['strict'])) {
            if($config['strict']) {
                $connection->prepare("set session sql_mode='STRICT_ALL_TABLES'")->execute();
            } else {
                $connection->prepare("set session sql_mode=''")->execute();
            }
        }
        return $connection;
    }
    /**
     * @param array $config
     * @return string
     */
    protected function getDsn(array $config) {
        return $this->configHasSocket($config) ? $this->getSocketDsn($config) : $this->getHostDsn($config);
    }
    /**
     * @param array $config
     * @return bool
     */
    protected function configHasSocket(array $config) {
        return isset($config['unix_socket']) && !empty($config['unix_socket']);
    }
    /**
     * @param array $config
     * @return string
     */
    protected function getSocketDsn(array $config) {
        return "mysql:unix_socket={$config['unix_socket']};dbname={$config['database']}";
    }
    /**
     * @param array $config
     * @return string
     */
    protected function getHostDsn(array $config) {
        extract($config);
        return isset($port) ? "mysql:host={$host};port={$port};dbname={$database}" : "mysql:host={$host};dbname={$database}";
    }
}