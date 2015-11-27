<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 17:59
 */
namespace Notadd\Install\Contracts;
interface Prerequisite {
    /**
     * @return mixed
     */
    public function check();
    /**
     * @return mixed
     */
    public function getErrors();
}