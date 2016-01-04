<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:28
 */
namespace Notadd\Foundation\Database\Schema;
/**
 * Class MySqlBuilder
 * @package Notadd\Foundation\Database\Schema
 */
class MySqlBuilder extends Builder {
    /**
     * @param string $table
     * @return bool
     */
    public function hasTable($table) {
        $sql = $this->grammar->compileTableExists();
        $database = $this->connection->getDatabaseName();
        $table = $this->connection->getTablePrefix() . $table;
        return count($this->connection->select($sql, [
            $database,
            $table
        ])) > 0;
    }
    /**
     * @param string $table
     * @return array
     */
    public function getColumnListing($table) {
        $sql = $this->grammar->compileColumnExists();
        $database = $this->connection->getDatabaseName();
        $table = $this->connection->getTablePrefix() . $table;
        $results = $this->connection->select($sql, [
            $database,
            $table
        ]);
        return $this->connection->getPostProcessor()->processColumnListing($results);
    }
}