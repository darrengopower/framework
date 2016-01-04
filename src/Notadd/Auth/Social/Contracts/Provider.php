<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 18:39
 */
namespace Notadd\Auth\Social\Contracts;
/**
 * Interface Provider
 * @package Notadd\Auth\Social\Contracts
 */
interface Provider {
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect();
    /**
     * @return \Notadd\Auth\Social\Contracts\User
     */
    public function user();
}