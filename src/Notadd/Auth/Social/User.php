<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 21:10
 */
namespace Notadd\Auth\Social;
use ArrayAccess;
use JsonSerializable;
use Notadd\Auth\Social\Contracts\AccessToken as AccessTokenContract;
use Notadd\Auth\Social\Contracts\User as UserContract;
use Notadd\Auth\Social\Traits\AttributeTrait;
class User implements UserContract, ArrayAccess, JsonSerializable {
    use AttributeTrait;
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes) {
        $this->attributes = $attributes;
    }
    /**
     * @return string
     */
    public function getId() {
        return $this->getAttribute('id');
    }
    /**
     * @return string
     */
    public function getNickname() {
        return $this->getAttribute('nickname');
    }
    /**
     * @return string
     */
    public function getName() {
        return $this->getAttribute('name');
    }
    /**
     * @return string
     */
    public function getEmail() {
        return $this->getAttribute('email');
    }
    /**
     * @return string
     */
    public function getAvatar() {
        return $this->getAttribute('avatar');
    }
    /**
     * @param \Notadd\Auth\Social\Contracts\AccessToken $token
     * @return $this
     */
    public function setToken(AccessTokenContract $token) {
        $this->setAttribute('token', $token);
        return $this;
    }
    /**
     * @return \Notadd\Auth\Social\AccessToken
     */
    public function getToken() {
        return $this->getAttribute('token');
    }
    /**
     * @return \Notadd\Auth\Social\AccessToken
     */
    public function getAccessToken() {
        return $this->token;
    }
    /**
     * @return array
     */
    public function getOriginal() {
        return $this->getAttribute('original');
    }
    /**
     * @return array
     */
    public function jsonSerialize() {
        return array_merge($this->attributes, ['token' => $this->token->getAttributes()]);
    }
}