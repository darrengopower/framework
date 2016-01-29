<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 20:04
 */
namespace Notadd\Auth\Social;
use ArrayAccess;
use InvalidArgumentException;
use JsonSerializable;
use Notadd\Auth\Social\Contracts\AccessToken as AccessTokenContract;
use Notadd\Auth\Social\Traits\AttributeTrait;
/**
 * Class AccessToken
 * @package Notadd\Auth\Social
 */
class AccessToken implements AccessTokenContract, ArrayAccess, JsonSerializable {
    use AttributeTrait;
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes) {
        if(empty($attributes['access_token'])) {
            throw new InvalidArgumentException('The key "access_token" could not be empty.');
        }
        $this->attributes = $attributes;
    }
    /**
     * @return string
     */
    public function getToken() {
        return $this->getAttribute('access_token');
    }
    /**
     * @return string
     */
    public function __toString() {
        return strval($this->getAttribute('access_token', ''));
    }
    /**
     * @return string
     */
    public function jsonSerialize() {
        return $this->getToken();
    }
}