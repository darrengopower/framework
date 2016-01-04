<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 01:57
 */
namespace Notadd\Foundation\Database\Connectors;
use Illuminate\Support\Arr;
use PDO;
/**
 * Class SqlServerConnector
 * @package Notadd\Foundation\Database\Connectors
 */
class SqlServerConnector extends Connector implements ConnectorInterface {
    /**
     * @var array
     */
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ];
    /**
     * @param array $config
     * @return \PDO
     */
    public function connect(array $config) {
        $options = $this->getOptions($config);
        return $this->createConnection($this->getDsn($config), $config, $options);
    }
    /**
     * @param array $config
     * @return string
     */
    protected function getDsn(array $config) {
        if(in_array('dblib', $this->getAvailableDrivers())) {
            return $this->getDblibDsn($config);
        } else {
            return $this->getSqlSrvDsn($config);
        }
    }
    /**
     * @param array $config
     * @return string
     */
    protected function getDblibDsn(array $config) {
        $arguments = [
            'host' => $this->buildHostString($config, ':'),
            'dbname' => $config['database'],
        ];
        $arguments = array_merge($arguments, Arr::only($config, [
            'appname',
            'charset'
        ]));
        return $this->buildConnectString('dblib', $arguments);
    }
    /**
     * @param array $config
     * @return string
     */
    protected function getSqlSrvDsn(array $config) {
        $arguments = [
            'Server' => $this->buildHostString($config, ','),
        ];
        if(isset($config['database'])) {
            $arguments['Database'] = $config['database'];
        }
        if(isset($config['appname'])) {
            $arguments['APP'] = $config['appname'];
        }
        return $this->buildConnectString('sqlsrv', $arguments);
    }
    /**
     * @param string $driver
     * @param array $arguments
     * @return string
     */
    protected function buildConnectString($driver, array $arguments) {
        $options = array_map(function ($key) use ($arguments) {
            return sprintf('%s=%s', $key, $arguments[$key]);
        }, array_keys($arguments));
        return $driver . ':' . implode(';', $options);
    }
    /**
     * @param array $config
     * @param string $separator
     * @return string
     */
    protected function buildHostString(array $config, $separator) {
        if(isset($config['port'])) {
            return $config['host'] . $separator . $config['port'];
        } else {
            return $config['host'];
        }
    }
    /**
     * @return array
     */
    protected function getAvailableDrivers() {
        return PDO::getAvailableDrivers();
    }
}