<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 21:45
 */
namespace Notadd\Auth\Social\Providers;
use Notadd\Auth\Social\AccessToken;
use Notadd\Auth\Social\Contracts\AccessToken as AccessTokenContract;
use Notadd\Auth\Social\Contracts\Provider as ProviderContract;
use Notadd\Auth\Social\User;
/**
 * Class QQProvider
 * @package Notadd\Auth\Social\Providers
 */
class QQProvider extends Provider implements ProviderContract {
    /**
     * @var string
     */
    protected $baseUrl = 'https://graph.qq.com';
    /**
     * @var string
     */
    protected $openId;
    /**
     * @var array
     */
    protected $scopes = ['get_user_info'];
    /**
     * @var int
     */
    protected $uid;
    /**
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state) {
        return $this->buildAuthUrlFromBase($this->baseUrl . '/oauth2.0/authorize', $state);
    }
    /**
     * @return string
     */
    protected function getTokenUrl() {
        return $this->baseUrl . '/oauth2.0/token';
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
        $response = $this->getHttpClient()->get($this->getTokenUrl(), [
            'query' => $this->getTokenFields($code),
        ]);
        return $this->parseAccessToken($response->getBody()->getContents());
    }
    /**
     * @param string $body
     * @return \Notadd\Auth\Social\AccessToken
     */
    public function parseAccessToken($body) {
        parse_str($body, $token);
        return new AccessToken($token);
    }
    /**
     * @param \Notadd\Auth\Social\Contracts\AccessToken $token
     * @return mixed
     */
    protected function getUserByToken(AccessTokenContract $token) {
        $response = $this->getHttpClient()->get($this->baseUrl . '/oauth2.0/me?access_token=' . $token->getToken());
        $this->openId = json_decode($this->removeCallback($response->getBody()->getContents()), true)['openid'];
        $queries = [
            'access_token' => $token->getToken(),
            'openid' => $this->openId,
            'oauth_consumer_key' => $this->clientId,
        ];
        $response = $this->getHttpClient()->get($this->baseUrl . '/user/get_user_info?' . http_build_query($queries));
        return json_decode($this->removeCallback($response->getBody()->getContents()), true);
    }
    /**
     * @param array $user
     * @return \Notadd\Auth\Social\User
     */
    protected function mapUserToObject(array $user) {
        return new User([
            'id' => $this->openId,
            'nickname' => $this->arrayItem($user, 'nickname'),
            'name' => $this->arrayItem($user, 'nickname'),
            'email' => $this->arrayItem($user, 'email'),
            'avatar' => $this->arrayItem($user, 'figureurl_qq_2'),
        ]);
    }
    /**
     * @param string $response
     * @return string
     */
    protected function removeCallback($response) {
        if(strpos($response, 'callback') !== false) {
            $lpos = strpos($response, '(');
            $rpos = strrpos($response, ')');
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        }
        return $response;
    }
}