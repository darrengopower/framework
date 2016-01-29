<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 01:52
 */
namespace Notadd\Foundation\Database\Connectors;
use Exception;
use Illuminate\Support\Arr;
use Notadd\Foundation\Database\DetectsLostConnections;
use PDO;
/**
 * Class Connector
 * @package Notadd\Foundation\Database\Connectors
 */
class Connector {
    use DetectsLostConnections;
    /**
     * The default PDO connection options.
     * @var array
     */
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    /**
     * @param array $config
     * @return array
     */
    public function getOptions(array $config) {
        $options = Arr::get($config, 'options', []);
        return array_diff_key($this->options, $options) + $options;
    }
    /**
     * @param string $dsn
     * @param array $config
     * @param array $options
     * @return \PDO
     */
    public function createConnection($dsn, array $config, array $options) {
        $username = Arr::get($config, 'username');
        $password = Arr::get($config, 'password');
        try {
            $pdo = new PDO($dsn, $username, $password, $options);
        } catch(Exception $e) {
            $pdo = $this->tryAgainIfCausedByLostConnection($e, $dsn, $username, $password, $options);
        }
        return $pdo;
    }
    /**
     * @return array
     */
    public function getDefaultOptions() {
        return $this->options;
    }
    /**
     * @param array $options
     * @return void
     */
    public function setDefaultOptions(array $options) {
        $this->options = $options;
    }
    /**
     * @param \Exception $e
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array $options
     * @return \PDO
     * @throws \Exception
     */
    protected function tryAgainIfCausedByLostConnection(Exception $e, $dsn, $username, $password, $options) {
        if($this->causedByLostConnection($e)) {
            return new PDO($dsn, $username, $password, $options);
        }
        throw $e;
    }
}