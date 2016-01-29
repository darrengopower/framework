<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 18:44
 */
namespace Notadd\Auth\Social\Contracts;
/**
 * Interface AccessToken
 * @package Notadd\Auth\Social\Contracts
 */
interface AccessToken {
    /**
     * @return string
     */
    public function getToken();
}