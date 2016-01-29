<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 21:50
 */
namespace Notadd\Auth\Social\Providers;
use Notadd\Auth\Social\Contracts\AccessToken as AccessTokenContract;
use Notadd\Auth\Social\Contracts\Provider as ProviderContract;
use Notadd\Auth\Social\User;
/**
 * Class WeiboProvider
 * @package Notadd\Auth\Social\Providers
 */
class WeiboProvider extends Provider implements ProviderContract {
    /**
     * @var string
     */
    protected $baseUrl = 'https://api.weibo.com';
    /**
     * @var string
     */
    protected $version = '2';
    /**
     * @var array
     */
    protected $scopes = ['email'];
    /**
     * @var int
     */
    protected $uid;
    /**
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state) {
        return $this->buildAuthUrlFromBase($this->baseUrl . '/oauth2/authorize', $state);
    }
    /**
     * @return string
     */
    protected function getTokenUrl() {
        return $this->baseUrl . '/' . $this->version . '/oauth2/access_token';
    }
    /**
     * @param string $code
     * @return array
     */
    protected function getTokenFields($code) {
        return parent::getTokenFields($code) + ['grant_type' => 'authorization_code'];
    }
    /**
     * @param \Notadd\Auth\Social\Contracts\AccessToken $token
     * @return mixed
     */
    protected function getUserByToken(AccessTokenContract $token) {
        $response = $this->getHttpClient()->get($this->baseUrl . '/' . $this->version . '/users/show.json', [
            'query' => [
                'uid' => $token['uid'],
                'access_token' => $token->getToken(),
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
        return json_decode($response->getBody(), true);
    }
    /**
     * @param array $user
     * @return \Notadd\Auth\Social\User
     */
    protected function mapUserToObject(array $user) {
        return new User([
            'id' => $this->arrayItem($user, 'id'),
            'nickname' => $this->arrayItem($user, 'screen_name'),
            'name' => $this->arrayItem($user, 'name'),
            'email' => $this->arrayItem($user, 'email'),
            'avatar' => $this->arrayItem($user, 'avatar_large'),
        ]);
    }
}