<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:26
 */
namespace Notadd\Foundation\Database\Schema;
use Closure;
use Notadd\Foundation\Database\Connection;
class Builder {
    /**
     * @var \Notadd\Foundation\Database\Connection
     */
    protected $connection;
    /**
     * @var \Notadd\Foundation\Database\Schema\Grammars\Grammar
     */
    protected $grammar;
    /**
     * @var \Closure
     */
    protected $resolver;
    /**
     * @param \Notadd\Foundation\Database\Connection $connection
     */
    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->grammar = $connection->getSchemaGrammar();
    }
    /**
     * @param string $table
     * @return bool
     */
    public function hasTable($table) {
        $sql = $this->grammar->compileTableExists();
        $table = $this->connection->getTablePrefix() . $table;
        return count($this->connection->select($sql, [$table])) > 0;
    }
    /**
     * @param string $table
     * @param string $column
     * @return bool
     */
    public function hasColumn($table, $column) {
        $column = strtolower($column);
        return in_array($column, array_map('strtolower', $this->getColumnListing($table)));
    }
    /**
     * @param string $table
     * @param array $columns
     * @return bool
     */
    public function hasColumns($table, array $columns) {
        $tableColumns = array_map('strtolower', $this->getColumnListing($table));
        foreach($columns as $column) {
            if(!in_array(strtolower($column), $tableColumns)) {
                return false;
            }
        }
        return true;
    }
    /**
     * @param string $table
     * @return array
     */
    public function getColumnListing($table) {
        $table = $this->connection->getTablePrefix() . $table;
        $results = $this->connection->select($this->grammar->compileColumnExists($table));
        return $this->connection->getPostProcessor()->processColumnListing($results);
    }
    /**
     * @param string $table
     * @param \Closure $callback
     * @return \Notadd\Foundation\Database\Schema\Blueprint
     */
    public function table($table, Closure $callback) {
        $this->build($this->createBlueprint($table, $callback));
    }
    /**
     * @param string $table
     * @param \Closure $callback
     * @return \Notadd\Foundation\Database\Schema\Blueprint
     */
    public function create($table, Closure $callback) {
        $blueprint = $this->createBlueprint($table);
        $blueprint->create();
        $callback($blueprint);
        $this->build($blueprint);
    }
    /**
     * @param string $table
     * @return \Notadd\Foundation\Database\Schema\Blueprint
     */
    public function drop($table) {
        $blueprint = $this->createBlueprint($table);
        $blueprint->drop();
        $this->build($blueprint);
    }
    /**
     * @param string $table
     * @return \Notadd\Foundation\Database\Schema\Blueprint
     */
    public function dropIfExists($table) {
        $blueprint = $this->createBlueprint($table);
        $blueprint->dropIfExists();
        $this->build($blueprint);
    }
    /**
     * @param string $from
     * @param string $to
     * @return \Notadd\Foundation\Database\Schema\Blueprint
     */
    public function rename($from, $to) {
        $blueprint = $this->createBlueprint($from);
        $blueprint->rename($to);
        $this->build($blueprint);
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @return void
     */
    protected function build(Blueprint $blueprint) {
        $blueprint->build($this->connection, $this->grammar);
    }
    /**
     * @param string $table
     * @param \Closure|null $callback
     * @return \Notadd\Foundation\Database\Schema\Blueprint
     */
    protected function createBlueprint($table, Closure $callback = null) {
        if(isset($this->resolver)) {
            return call_user_func($this->resolver, $table, $callback);
        }
        return new Blueprint($table, $callback);
    }
    /**
     * @return \Notadd\Foundation\Database\Connection
     */
    public function getConnection() {
        return $this->connection;
    }
    /**
     * @param \Notadd\Foundation\Database\Connection $connection
     * @return $this
     */
    public function setConnection(Connection $connection) {
        $this->connection = $connection;
        return $this;
    }
    /**
     * @param \Closure $resolver
     * @return void
     */
    public function blueprintResolver(Closure $resolver) {
        $this->resolver = $resolver;
    }
}