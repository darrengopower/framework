<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:38
 */
namespace Notadd\Foundation\Database;
use Doctrine\DBAL\Driver\PDOPgSql\Driver as DoctrineDriver;
use Notadd\Foundation\Database\Query\Processors\PostgresProcessor;
use Notadd\Foundation\Database\Query\Grammars\PostgresGrammar as QueryGrammar;
use Notadd\Foundation\Database\Schema\Grammars\PostgresGrammar as SchemaGrammar;
class PostgresConnection extends Connection {
    /**
     * @return \Notadd\Foundation\Database\Query\Grammars\PostgresGrammar
     */
    protected function getDefaultQueryGrammar() {
        return $this->withTablePrefix(new QueryGrammar);
    }
    /**
     * @return \Notadd\Foundation\Database\Schema\Grammars\PostgresGrammar
     */
    protected function getDefaultSchemaGrammar() {
        return $this->withTablePrefix(new SchemaGrammar);
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Processors\PostgresProcessor
     */
    protected function getDefaultPostProcessor() {
        return new PostgresProcessor;
    }
    /**
     * @return \Doctrine\DBAL\Driver\PDOPgSql\Driver
     */
    protected function getDoctrineDriver() {
        return new DoctrineDriver;
    }
}