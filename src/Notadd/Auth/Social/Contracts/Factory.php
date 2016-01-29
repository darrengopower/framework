<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 18:39
 */
namespace Notadd\Auth\Social\Contracts;
/**
 * Interface Factory
 * @package Notadd\Auth\Social\Contracts
 */
interface Factory {
    /**
     * @param \Notadd\Auth\Social\Contracts\Provider $driver
     * @return mixed
     */
    public function driver($driver = null);
}