<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:36
 */
namespace Notadd\Foundation\Database;
use Doctrine\DBAL\Driver\PDOMySql\Driver as DoctrineDriver;
use Notadd\Foundation\Database\Query\Grammars\MySqlGrammar as QueryGrammar;
use Notadd\Foundation\Database\Query\Processors\MySqlProcessor;
use Notadd\Foundation\Database\Schema\Grammars\MySqlGrammar as SchemaGrammar;
use Notadd\Foundation\Database\Schema\MySqlBuilder;
/**
 * Class MySqlConnection
 * @package Notadd\Foundation\Database
 */
class MySqlConnection extends Connection {
    /**
     * @return \Notadd\Foundation\Database\Schema\MySqlBuilder
     */
    public function getSchemaBuilder() {
        if(is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }
        return new MySqlBuilder($this);
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Grammars\MySqlGrammar
     */
    protected function getDefaultQueryGrammar() {
        return $this->withTablePrefix(new QueryGrammar);
    }
    /**
     * @return \Notadd\Foundation\Database\Schema\Grammars\MySqlGrammar
     */
    protected function getDefaultSchemaGrammar() {
        return $this->withTablePrefix(new SchemaGrammar);
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Processors\MySqlProcessor
     */
    protected function getDefaultPostProcessor() {
        return new MySqlProcessor;
    }
    /**
     * @return \Doctrine\DBAL\Driver\PDOMySql\Driver
     */
    protected function getDoctrineDriver() {
        return new DoctrineDriver;
    }
}