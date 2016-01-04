<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 01:53
 */
namespace Notadd\Foundation\Database;
use Exception;
use Illuminate\Support\Str;
/**
 * Class DetectsLostConnections
 * @package Notadd\Foundation\Database
 */
trait DetectsLostConnections {
    /**
     * @param \Exception $e
     * @return bool
     */
    protected function causedByLostConnection(Exception $e) {
        $message = $e->getMessage();
        return Str::contains($message, [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
        ]);
    }
}