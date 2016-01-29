<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 18:40
 */
namespace Notadd\Auth\Social\Contracts;
/**
 * Interface User
 * @package Notadd\Auth\Social\Contracts
 */
interface User {
    /**
     * @return string
     */
    public function getId();
    /**
     * @return string
     */
    public function getNickname();
    /**
     * @return string
     */
    public function getName();
    /**
     * @return string
     */
    public function getEmail();
    /**
     * @return string
     */
    public function getAvatar();
}