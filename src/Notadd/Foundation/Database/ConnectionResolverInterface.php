<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:15
 */
namespace Notadd\Foundation\Database;
interface ConnectionResolverInterface {
    /**
     * @param string $name
     * @return \Notadd\Foundation\Database\ConnectionInterface
     */
    public function connection($name = null);
    /**
     * @return string
     */
    public function getDefaultConnection();
    /**
     * @param string $name
     * @return void
     */
    public function setDefaultConnection($name);
}