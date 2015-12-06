<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:41
 */
namespace Notadd\Foundation\Database;
use Doctrine\DBAL\Driver\PDOSqlite\Driver as DoctrineDriver;
use Notadd\Foundation\Database\Query\Grammars\SQLiteGrammar as QueryGrammar;
use Notadd\Foundation\Database\Query\Processors\SQLiteProcessor;
use Notadd\Foundation\Database\Schema\Grammars\SQLiteGrammar as SchemaGrammar;
class SQLiteConnection extends Connection {
    /**
     * @return \Notadd\Foundation\Database\Query\Grammars\SQLiteGrammar
     */
    protected function getDefaultQueryGrammar() {
        return $this->withTablePrefix(new QueryGrammar);
    }
    /**
     * @return \Notadd\Foundation\Database\Schema\Grammars\SQLiteGrammar
     */
    protected function getDefaultSchemaGrammar() {
        return $this->withTablePrefix(new SchemaGrammar);
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Processors\SQLiteProcessor
     */
    protected function getDefaultPostProcessor() {
        return new SQLiteProcessor;
    }
    /**
     * @return \Doctrine\DBAL\Driver\PDOSqlite\Driver
     */
    protected function getDoctrineDriver() {
        return new DoctrineDriver;
    }
}