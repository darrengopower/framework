<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 02:01
 */
namespace Notadd\Foundation\Database;
use Closure;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use LogicException;
use Notadd\Foundation\Database\Query\Expression;
use Notadd\Foundation\Database\Query\Builder as QueryBuilder;
use Notadd\Foundation\Database\Query\Grammars\Grammar as QueryGrammar;
use Notadd\Foundation\Database\Query\Processors\Processor;
use Notadd\Foundation\Database\Schema\Builder as SchemaBuilder;
use PDO;
use RuntimeException;
use Throwable;
class Connection implements ConnectionInterface {
    use DetectsLostConnections;
    /**
     * @var PDO
     */
    protected $pdo;
    /**
     * @var PDO
     */
    protected $readPdo;
    /**
     * @var callable
     */
    protected $reconnector;
    /**
     * @var \Notadd\Foundation\Database\Query\Grammars\Grammar
     */
    protected $queryGrammar;
    /**
     * @var \Notadd\Foundation\Database\Schema\Grammars\Grammar
     */
    protected $schemaGrammar;
    /**
     * @var \Notadd\Foundation\Database\Query\Processors\Processor
     */
    protected $postProcessor;
    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;
    /**
     * @var int
     */
    protected $fetchMode = PDO::FETCH_OBJ;
    /**
     * @var int
     */
    protected $transactions = 0;
    /**
     * @var array
     */
    protected $queryLog = [];
    /**
     * @var bool
     */
    protected $loggingQueries = false;
    /**
     * @var bool
     */
    protected $pretending = false;
    /**
     * @var string
     */
    protected $database;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $doctrineConnection;
    /**
     * @var string
     */
    protected $tablePrefix = '';
    /**
     * @var array
     */
    protected $config = [];
    /**
     * @param \PDO $pdo
     * @param string $database
     * @param string $tablePrefix
     * @param array $config
     */
    public function __construct(PDO $pdo, $database = '', $tablePrefix = '', array $config = []) {
        $this->pdo = $pdo;
        $this->database = $database;
        $this->tablePrefix = $tablePrefix;
        $this->config = $config;
        $this->useDefaultQueryGrammar();
        $this->useDefaultPostProcessor();
    }
    /**
     * @return void
     */
    public function useDefaultQueryGrammar() {
        $this->queryGrammar = $this->getDefaultQueryGrammar();
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Grammars\Grammar
     */
    protected function getDefaultQueryGrammar() {
        return new QueryGrammar;
    }
    /**
     * @return void
     */
    public function useDefaultSchemaGrammar() {
        $this->schemaGrammar = $this->getDefaultSchemaGrammar();
    }
    /**
     * @return \Notadd\Foundation\Database\Schema\Grammars\Grammar
     */
    protected function getDefaultSchemaGrammar() {
    }
    /**
     * @return void
     */
    public function useDefaultPostProcessor() {
        $this->postProcessor = $this->getDefaultPostProcessor();
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Processors\Processor
     */
    protected function getDefaultPostProcessor() {
        return new Processor;
    }
    /**
     * @return \Notadd\Foundation\Database\Schema\Builder
     */
    public function getSchemaBuilder() {
        if(is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }
        return new SchemaBuilder($this);
    }
    /**
     * @param string $table
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    public function table($table) {
        return $this->query()->from($table);
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    public function query() {
        return new QueryBuilder($this, $this->getQueryGrammar(), $this->getPostProcessor());
    }
    /**
     * @param mixed $value
     * @return \Notadd\Foundation\Database\Query\Expression
     */
    public function raw($value) {
        return new Expression($value);
    }
    /**
     * @param string $query
     * @param array $bindings
     * @return mixed
     */
    public function selectOne($query, $bindings = []) {
        $records = $this->select($query, $bindings);
        return count($records) > 0 ? reset($records) : null;
    }
    /**
     * @param string $query
     * @param array $bindings
     * @return array
     */
    public function selectFromWriteConnection($query, $bindings = []) {
        return $this->select($query, $bindings, false);
    }
    /**
     * @param string $query
     * @param array $bindings
     * @param bool $useReadPdo
     * @return array
     */
    public function select($query, $bindings = [], $useReadPdo = true) {
        return $this->run($query, $bindings, function ($me, $query, $bindings) use ($useReadPdo) {
            if($me->pretending()) {
                return [];
            }
            $statement = $this->getPdoForSelect($useReadPdo)->prepare($query);
            $statement->execute($me->prepareBindings($bindings));
            return $statement->fetchAll($me->getFetchMode());
        });
    }
    /**
     * @param bool $useReadPdo
     * @return \PDO
     */
    protected function getPdoForSelect($useReadPdo = true) {
        return $useReadPdo ? $this->getReadPdo() : $this->getPdo();
    }
    /**
     * @param string $query
     * @param array $bindings
     * @return bool
     */
    public function insert($query, $bindings = []) {
        return $this->statement($query, $bindings);
    }
    /**
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function update($query, $bindings = []) {
        return $this->affectingStatement($query, $bindings);
    }
    /**
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function delete($query, $bindings = []) {
        return $this->affectingStatement($query, $bindings);
    }
    /**
     * @param string $query
     * @param array $bindings
     * @return bool
     */
    public function statement($query, $bindings = []) {
        return $this->run($query, $bindings, function ($me, $query, $bindings) {
            if($me->pretending()) {
                return true;
            }
            $bindings = $me->prepareBindings($bindings);
            return $me->getPdo()->prepare($query)->execute($bindings);
        });
    }
    /**
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function affectingStatement($query, $bindings = []) {
        return $this->run($query, $bindings, function ($me, $query, $bindings) {
            if($me->pretending()) {
                return 0;
            }
            $statement = $me->getPdo()->prepare($query);
            $statement->execute($me->prepareBindings($bindings));
            return $statement->rowCount();
        });
    }
    /**
     * @param string $query
     * @return bool
     */
    public function unprepared($query) {
        return $this->run($query, [], function ($me, $query) {
            if($me->pretending()) {
                return true;
            }
            return (bool)$me->getPdo()->exec($query);
        });
    }
    /**
     * @param array $bindings
     * @return array
     */
    public function prepareBindings(array $bindings) {
        $grammar = $this->getQueryGrammar();
        foreach($bindings as $key => $value) {
            if($value instanceof DateTimeInterface) {
                $bindings[$key] = $value->format($grammar->getDateFormat());
            } elseif($value === false) {
                $bindings[$key] = 0;
            }
        }
        return $bindings;
    }
    /**
     * @param \Closure $callback
     * @return mixed
     * @throws \Throwable
     */
    public function transaction(Closure $callback) {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
        } catch(Exception $e) {
            $this->rollBack();
            throw $e;
        } catch(Throwable $e) {
            $this->rollBack();
            throw $e;
        }
        return $result;
    }
    /**
     * @return void
     */
    public function beginTransaction() {
        ++$this->transactions;
        if($this->transactions == 1) {
            $this->pdo->beginTransaction();
        } elseif($this->transactions > 1 && $this->queryGrammar->supportsSavepoints()) {
            $this->pdo->exec($this->queryGrammar->compileSavepoint('trans' . $this->transactions));
        }
        $this->fireConnectionEvent('beganTransaction');
    }
    /**
     * @return void
     */
    public function commit() {
        if($this->transactions == 1) {
            $this->pdo->commit();
        }
        --$this->transactions;
        $this->fireConnectionEvent('committed');
    }
    /**
     * @return void
     */
    public function rollBack() {
        if($this->transactions == 1) {
            $this->pdo->rollBack();
        } elseif($this->transactions > 1 && $this->queryGrammar->supportsSavepoints()) {
            $this->pdo->exec($this->queryGrammar->compileSavepointRollBack('trans' . $this->transactions));
        }
        $this->transactions = max(0, $this->transactions - 1);
        $this->fireConnectionEvent('rollingBack');
    }
    /**
     * @return int
     */
    public function transactionLevel() {
        return $this->transactions;
    }
    /**
     * @param \Closure $callback
     * @return array
     */
    public function pretend(Closure $callback) {
        $loggingQueries = $this->loggingQueries;
        $this->enableQueryLog();
        $this->pretending = true;
        $this->queryLog = [];
        $callback($this);
        $this->pretending = false;
        $this->loggingQueries = $loggingQueries;
        return $this->queryLog;
    }
    /**
     * @param string $query
     * @param array $bindings
     * @param \Closure $callback
     * @return mixed
     * @throws \Notadd\Foundation\Database\QueryException
     */
    protected function run($query, $bindings, Closure $callback) {
        $this->reconnectIfMissingConnection();
        $start = microtime(true);
        try {
            $result = $this->runQueryCallback($query, $bindings, $callback);
        } catch(QueryException $e) {
            $result = $this->tryAgainIfCausedByLostConnection($e, $query, $bindings, $callback);
        }
        $time = $this->getElapsedTime($start);
        $this->logQuery($query, $bindings, $time);
        return $result;
    }
    /**
     * @param string $query
     * @param array $bindings
     * @param \Closure $callback
     * @return mixed
     * @throws \Notadd\Foundation\Database\QueryException
     */
    protected function runQueryCallback($query, $bindings, Closure $callback) {
        try {
            $result = $callback($this, $query, $bindings);
        } catch(Exception $e) {
            throw new QueryException($query, $this->prepareBindings($bindings), $e);
        }
        return $result;
    }
    /**
     * @param \Notadd\Foundation\Database\QueryException $e
     * @param string $query
     * @param array $bindings
     * @param \Closure $callback
     * @return mixed
     * @throws \Notadd\Foundation\Database\QueryException
     */
    protected function tryAgainIfCausedByLostConnection(QueryException $e, $query, $bindings, Closure $callback) {
        if($this->causedByLostConnection($e->getPrevious())) {
            $this->reconnect();
            return $this->runQueryCallback($query, $bindings, $callback);
        }
        throw $e;
    }
    /**
     * @return void
     */
    public function disconnect() {
        $this->setPdo(null)->setReadPdo(null);
    }
    /**
     * @return void
     * @throws \LogicException
     */
    public function reconnect() {
        if(is_callable($this->reconnector)) {
            return call_user_func($this->reconnector, $this);
        }
        throw new LogicException('Lost connection and no reconnector available.');
    }
    /**
     * @return void
     */
    protected function reconnectIfMissingConnection() {
        if(is_null($this->getPdo()) || is_null($this->getReadPdo())) {
            $this->reconnect();
        }
    }
    /**
     * @param string $query
     * @param array $bindings
     * @param float|null $time
     * @return void
     */
    public function logQuery($query, $bindings, $time = null) {
        if(isset($this->events)) {
            $this->events->fire('illuminate.query', [
                $query,
                $bindings,
                $time,
                $this->getName()
            ]);
        }
        if(!$this->loggingQueries) {
            return;
        }
        $this->queryLog[] = compact('query', 'bindings', 'time');
    }
    /**
     * @param \Closure $callback
     * @return void
     */
    public function listen(Closure $callback) {
        if(isset($this->events)) {
            $this->events->listen('illuminate.query', $callback);
        }
    }
    /**
     * @param string $event
     * @return void
     */
    protected function fireConnectionEvent($event) {
        if(isset($this->events)) {
            $this->events->fire('connection.' . $this->getName() . '.' . $event, $this);
        }
    }
    /**
     * @param int $start
     * @return float
     */
    protected function getElapsedTime($start) {
        return round((microtime(true) - $start) * 1000, 2);
    }
    /**
     * @return bool
     */
    public function isDoctrineAvailable() {
        return class_exists('Doctrine\DBAL\Connection');
    }
    /**
     * @param string $table
     * @param string $column
     * @return \Doctrine\DBAL\Schema\Column
     */
    public function getDoctrineColumn($table, $column) {
        $schema = $this->getDoctrineSchemaManager();
        return $schema->listTableDetails($table)->getColumn($column);
    }
    /**
     * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    public function getDoctrineSchemaManager() {
        return $this->getDoctrineDriver()->getSchemaManager($this->getDoctrineConnection());
    }
    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getDoctrineConnection() {
        if(is_null($this->doctrineConnection)) {
            $driver = $this->getDoctrineDriver();
            $data = [
                'pdo' => $this->pdo,
                'dbname' => $this->getConfig('database')
            ];
            $this->doctrineConnection = new DoctrineConnection($data, $driver);
        }
        return $this->doctrineConnection;
    }
    /**
     * @return \PDO
     */
    public function getPdo() {
        return $this->pdo;
    }
    /**
     * @return \PDO
     */
    public function getReadPdo() {
        if($this->transactions >= 1) {
            return $this->getPdo();
        }
        return $this->readPdo ?: $this->pdo;
    }
    /**
     * @param \PDO|null $pdo
     * @return $this
     */
    public function setPdo($pdo) {
        if($this->transactions >= 1) {
            throw new RuntimeException("Can't swap PDO instance while within transaction.");
        }
        $this->pdo = $pdo;
        return $this;
    }
    /**
     * @param \PDO|null $pdo
     * @return $this
     */
    public function setReadPdo($pdo) {
        $this->readPdo = $pdo;
        return $this;
    }
    /**
     * @param callable $reconnector
     * @return $this
     */
    public function setReconnector(callable $reconnector) {
        $this->reconnector = $reconnector;
        return $this;
    }
    /**
     * @return string|null
     */
    public function getName() {
        return $this->getConfig('name');
    }
    /**
     * @param string $option
     * @return mixed
     */
    public function getConfig($option) {
        return Arr::get($this->config, $option);
    }
    /**
     * @return string
     */
    public function getDriverName() {
        return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Grammars\Grammar
     */
    public function getQueryGrammar() {
        return $this->queryGrammar;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Grammars\Grammar $grammar
     * @return void
     */
    public function setQueryGrammar(Query\Grammars\Grammar $grammar) {
        $this->queryGrammar = $grammar;
    }
    /**
     * @return \Notadd\Foundation\Database\Schema\Grammars\Grammar
     */
    public function getSchemaGrammar() {
        return $this->schemaGrammar;
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Grammars\Grammar $grammar
     * @return void
     */
    public function setSchemaGrammar(Schema\Grammars\Grammar $grammar) {
        $this->schemaGrammar = $grammar;
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Processors\Processor
     */
    public function getPostProcessor() {
        return $this->postProcessor;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Processors\Processor $processor
     * @return void
     */
    public function setPostProcessor(Processor $processor) {
        $this->postProcessor = $processor;
    }
    /**
     * Get the event dispatcher used by the connection.
     * @return \Illuminate\Contracts\Events\Dispatcher
     */
    public function getEventDispatcher() {
        return $this->events;
    }
    /**
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     */
    public function setEventDispatcher(Dispatcher $events) {
        $this->events = $events;
    }
    /**
     * @return bool
     */
    public function pretending() {
        return $this->pretending === true;
    }
    /**
     * @return int
     */
    public function getFetchMode() {
        return $this->fetchMode;
    }
    /**
     * @param int $fetchMode
     * @return int
     */
    public function setFetchMode($fetchMode) {
        $this->fetchMode = $fetchMode;
    }
    /**
     * @return array
     */
    public function getQueryLog() {
        return $this->queryLog;
    }
    /**
     * @return void
     */
    public function flushQueryLog() {
        $this->queryLog = [];
    }
    /**
     * @return void
     */
    public function enableQueryLog() {
        $this->loggingQueries = true;
    }
    /**
     * @return void
     */
    public function disableQueryLog() {
        $this->loggingQueries = false;
    }
    /**
     * @return bool
     */
    public function logging() {
        return $this->loggingQueries;
    }
    /**
     * @return string
     */
    public function getDatabaseName() {
        return $this->database;
    }
    /**
     * @param string $database
     * @return string
     */
    public function setDatabaseName($database) {
        $this->database = $database;
    }
    /**
     * @return string
     */
    public function getTablePrefix() {
        return $this->tablePrefix;
    }
    /**
     * @param string $prefix
     * @return void
     */
    public function setTablePrefix($prefix) {
        $this->tablePrefix = $prefix;
        $this->getQueryGrammar()->setTablePrefix($prefix);
    }
    /**
     * @param \Notadd\Foundation\Database\Grammar $grammar
     * @return \Notadd\Foundation\Database\Grammar
     */
    public function withTablePrefix(Grammar $grammar) {
        $grammar->setTablePrefix($this->tablePrefix);
        return $grammar;
    }
}