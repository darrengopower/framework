<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 01:56
 */
namespace Notadd\Foundation\Database\Connectors;
use PDO;
/**
 * Class PostgresConnector
 * @package Notadd\Foundation\Database\Connectors
 */
class PostgresConnector extends Connector implements ConnectorInterface {
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
        $dsn = $this->getDsn($config);
        $options = $this->getOptions($config);
        $connection = $this->createConnection($dsn, $config, $options);
        $charset = $config['charset'];
        $connection->prepare("set names '$charset'")->execute();
        if(isset($config['timezone'])) {
            $timezone = $config['timezone'];
            $connection->prepare("set time zone '$timezone'")->execute();
        }
        if(isset($config['schema'])) {
            $schema = $this->formatSchema($config['schema']);
            $connection->prepare("set search_path to {$schema}")->execute();
        }
        if(isset($config['application_name'])) {
            $applicationName = $config['application_name'];
            $connection->prepare("set application_name to '$applicationName'")->execute();
        }
        return $connection;
    }
    /**
     * @param array $config
     * @return string
     */
    protected function getDsn(array $config) {
        extract($config);
        $host = isset($host) ? "host={$host};" : '';
        $dsn = "pgsql:{$host}dbname={$database}";
        if(isset($config['port'])) {
            $dsn .= ";port={$port}";
        }
        if(isset($config['sslmode'])) {
            $dsn .= ";sslmode={$sslmode}";
        }
        return $dsn;
    }
    /**
     * @param array|string $schema
     * @return string
     */
    protected function formatSchema($schema) {
        if(is_array($schema)) {
            return '"' . implode('", "', $schema) . '"';
        } else {
            return '"' . $schema . '"';
        }
    }
}