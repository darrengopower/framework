<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 02:00
 */
namespace Notadd\Foundation\Database;
use Closure;
/**
 * Interface ConnectionInterface
 * @package Notadd\Foundation\Database
 */
interface ConnectionInterface {
    /**
     * @param string $table
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    public function table($table);
    /**
     * @param mixed $value
     * @return \Notadd\Foundation\Database\Query\Expression
     */
    public function raw($value);
    /**
     * @param string $query
     * @param array $bindings
     * @return mixed
     */
    public function selectOne($query, $bindings = []);
    /**
     * @param string $query
     * @param array $bindings
     * @return array
     */
    public function select($query, $bindings = []);
    /**
     * @param string $query
     * @param array $bindings
     * @return bool
     */
    public function insert($query, $bindings = []);
    /**
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function update($query, $bindings = []);
    /**
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function delete($query, $bindings = []);
    /**
     * @param string $query
     * @param array $bindings
     * @return bool
     */
    public function statement($query, $bindings = []);
    /**
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function affectingStatement($query, $bindings = []);
    /**
     * @param string $query
     * @return bool
     */
    public function unprepared($query);
    /**
     * @param array $bindings
     * @return array
     */
    public function prepareBindings(array $bindings);
    /**
     * @param \Closure $callback
     * @return mixed
     * @throws \Throwable
     */
    public function transaction(Closure $callback);
    /**
     * @return void
     */
    public function beginTransaction();
    /**
     * @return void
     */
    public function commit();
    /**
     * @return void
     */
    public function rollBack();
    /**
     * @return int
     */
    public function transactionLevel();
    /**
     * @param \Closure $callback
     * @return array
     */
    public function pretend(Closure $callback);
}