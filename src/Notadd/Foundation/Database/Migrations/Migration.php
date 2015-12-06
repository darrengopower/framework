<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:03
 */
namespace Notadd\Foundation\Database\Migrations;
abstract class Migration {
    /**
     * @var string
     */
    protected $connection;
    /**
     * @return string
     */
    public function getConnection() {
        return $this->connection;
    }
}