<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 21:42
 */
namespace Notadd\Auth\Social\Providers;
use GuzzleHttp\ClientInterface;
use Notadd\Auth\Social\Contracts\AccessToken as AccessTokenContract;
use Notadd\Auth\Social\Contracts\Provider as ProviderContract;
use Notadd\Auth\Social\User;
class GoogleProvider extends Provider implements ProviderContract {
    /**
     * @var string
     */
    protected $scopeSeparator = ' ';
    /**
     * @var array
     */
    protected $scopes = [
        'https://www.googleapis.com/auth/plus.me',
        'https://www.googleapis.com/auth/plus.login',
        'https://www.googleapis.com/auth/plus.profile.emails.read',
    ];
    /**
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state) {
        return $this->buildAuthUrlFromBase('https://accounts.google.com/o/oauth2/auth', $state);
    }
    /**
     * @return string
     */
    protected function getTokenUrl() {
        return 'https://accounts.google.com/o/oauth2/token';
    }
    /**
     * @param string $code
     * @return \Notadd\Auth\Social\AccessToken
     */
    public function getAccessToken($code) {
        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            $postKey => $this->getTokenFields($code),
        ]);
        return $this->parseAccessToken($response->getBody());
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
        $response = $this->getHttpClient()->get('https://www.googleapis.com/plus/v1/people/me?', [
            'query' => [
                'prettyPrint' => 'false',
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token->getToken(),
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
            'nickname' => $this->arrayItem($user, 'nickname'),
            'name' => $this->arrayItem($user, 'displayName'),
            'email' => $this->arrayItem($user, 'emails.0.value'),
            'avatar' => $this->arrayItem($user, 'image.url'),
        ]);
    }
}