<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 21:34
 */
namespace Notadd\Auth\Social\Providers;
use Notadd\Auth\Social\Contracts\AccessToken as AccessTokenContract;
use Notadd\Auth\Social\Contracts\Provider as ProviderContract;
use Notadd\Auth\Social\User;
/**
 * Class DoubanProvider
 * @package Notadd\Auth\Social\Providers
 */
class DoubanProvider extends Provider implements ProviderContract {
    /**
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state) {
        return $this->buildAuthUrlFromBase('https://www.douban.com/service/auth2/auth', $state);
    }
    /**
     * @return string
     */
    protected function getTokenUrl() {
        return 'https://www.douban.com/service/auth2/token';
    }
    /**
     * @param \Notadd\Auth\Social\Contracts\AccessToken $token
     * @return mixed
     */
    protected function getUserByToken(AccessTokenContract $token) {
        $response = $this->getHttpClient()->get('https://api.douban.com/v2/user/~me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token->getToken(),
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
    /**
     * @param array $user
     * @return \Notadd\Auth\Social\User
     */
    protected function mapUserToObject(array $user) {
        return new User([
            'id' => $this->arrayItem($user, 'id'),
            'nickname' => $this->arrayItem($user, 'name'),
            'name' => $this->arrayItem($user, 'name'),
            'avatar' => $this->arrayItem($user, 'large_avatar'),
            'email' => null,
        ]);
    }
    /**
     * @param string $code
     * @return array
     */
    protected function getTokenFields($code) {
        return parent::getTokenFields($code) + ['grant_type' => 'authorization_code'];
    }
    /**
     * @param string $code
     * @return \Notadd\Auth\Social\AccessToken
     */
    public function getAccessToken($code) {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'form_params' => $this->getTokenFields($code),
        ]);
        return $this->parseAccessToken($response->getBody()->getContents());
    }
}