<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:42
 */
namespace Notadd\Foundation\Database;
use Closure;
use Doctrine\DBAL\Driver\PDOSqlsrv\Driver as DoctrineDriver;
use Exception;
use Notadd\Foundation\Database\Query\Grammars\SqlServerGrammar as QueryGrammar;
use Notadd\Foundation\Database\Query\Processors\SqlServerProcessor;
use Notadd\Foundation\Database\Schema\Grammars\SqlServerGrammar as SchemaGrammar;
use Throwable;
class SqlServerConnection extends Connection {
    /**
     * @param \Closure $callback
     * @return mixed
     * @throws \Throwable
     */
    public function transaction(Closure $callback) {
        if($this->getDriverName() == 'sqlsrv') {
            return parent::transaction($callback);
        }
        $this->pdo->exec('BEGIN TRAN');
        try {
            $result = $callback($this);
            $this->pdo->exec('COMMIT TRAN');
        }
        catch(Exception $e) {
            $this->pdo->exec('ROLLBACK TRAN');
            throw $e;
        } catch(Throwable $e) {
            $this->pdo->exec('ROLLBACK TRAN');
            throw $e;
        }
        return $result;
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Grammars\SqlServerGrammar
     */
    protected function getDefaultQueryGrammar() {
        return $this->withTablePrefix(new QueryGrammar);
    }
    /**
     * @return \Notadd\Foundation\Database\Schema\Grammars\SqlServerGrammar
     */
    protected function getDefaultSchemaGrammar() {
        return $this->withTablePrefix(new SchemaGrammar);
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Processors\SqlServerProcessor
     */
    protected function getDefaultPostProcessor() {
        return new SqlServerProcessor;
    }
    /**
     * @return \Doctrine\DBAL\Driver\PDOSqlsrv\Driver
     */
    protected function getDoctrineDriver() {
        return new DoctrineDriver;
    }
}