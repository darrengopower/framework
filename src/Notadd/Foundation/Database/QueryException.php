<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:38
 */
namespace Notadd\Foundation\Database;
use PDOException;
/**
 * Class QueryException
 * @package Notadd\Foundation\Database
 */
class QueryException extends PDOException {
    /**
     * @var string
     */
    protected $sql;
    /**
     * @var array
     */
    protected $bindings;
    /**
     * @param string $sql
     * @param array $bindings
     * @param \Exception $previous
     */
    public function __construct($sql, array $bindings, $previous) {
        parent::__construct('', 0, $previous);
        $this->sql = $sql;
        $this->bindings = $bindings;
        $this->previous = $previous;
        $this->code = $previous->getCode();
        $this->message = $this->formatMessage($sql, $bindings, $previous);
        if($previous instanceof PDOException) {
            $this->errorInfo = $previous->errorInfo;
        }
    }
    /**
     * @param string $sql
     * @param array $bindings
     * @param \Exception $previous
     * @return string
     */
    protected function formatMessage($sql, $bindings, $previous) {
        return $previous->getMessage() . ' (SQL: ' . str_replace_array('\?', $bindings, $sql) . ')';
    }
    /**
     * @return string
     */
    public function getSql() {
        return $this->sql;
    }
    /**
     * @return array
     */
    public function getBindings() {
        return $this->bindings;
    }
}