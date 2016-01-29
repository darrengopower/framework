<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 01:57
 */
namespace Notadd\Foundation\Database\Connectors;
use InvalidArgumentException;
/**
 * Class SQLiteConnector
 * @package Notadd\Foundation\Database\Connectors
 */
class SQLiteConnector extends Connector implements ConnectorInterface {
    /**
     * @param array $config
     * @return \PDO
     * @throws \InvalidArgumentException
     */
    public function connect(array $config) {
        $options = $this->getOptions($config);
        if($config['database'] == ':memory:') {
            return $this->createConnection('sqlite::memory:', $config, $options);
        }
        $path = realpath($config['database']);
        if($path === false) {
            throw new InvalidArgumentException("Database (${config['database']}) does not exist.");
        }
        return $this->createConnection("sqlite:{$path}", $config, $options);
    }
}