<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 01:51
 */
namespace Notadd\Foundation\Database\Connectors;
interface ConnectorInterface {
    /**
     * @param array $config
     * @return \PDO
     */
    public function connect(array $config);
}