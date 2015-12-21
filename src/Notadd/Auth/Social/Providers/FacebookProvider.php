<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 21:38
 */
namespace Notadd\Auth\Social\Providers;
use Notadd\Auth\Social\AccessToken;
use Notadd\Auth\Social\Contracts\AccessToken as AccessTokenContract;
use Notadd\Auth\Social\Contracts\Provider as ProviderContract;
use Notadd\Auth\Social\User;
class FacebookProvider extends Provider implements ProviderContract {
    /**
     * @var string
     */
    protected $graphUrl = 'https://graph.facebook.com';
    /**
     * @var string
     */
    protected $version = 'v2.5';
    /**
     * @var array
     */
    protected $fields = [
        'first_name',
        'last_name',
        'email',
        'gender',
        'verified'
    ];
    /**
     * @var array
     */
    protected $scopes = ['email'];
    /**
     * @var bool
     */
    protected $popup = false;
    /**
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state) {
        return $this->buildAuthUrlFromBase('https://www.facebook.com/' . $this->version . '/dialog/oauth', $state);
    }
    /**
     * @return string
     */
    protected function getTokenUrl() {
        return $this->graphUrl . '/oauth/access_token';
    }
    /**
     * @param string $code
     * @return \Notadd\Auth\Social\AccessToken
     */
    public function getAccessToken($code) {
        $response = $this->getHttpClient()->get($this->getTokenUrl(), [
            'query' => $this->getTokenFields($code),
        ]);
        return $this->parseAccessToken($response->getBody());
    }
    /**
     * {@inheritdoc}
     */
    protected function parseAccessToken($body) {
        parse_str($body, $token);
        return new AccessToken($token);
    }
    /**
     * @param \Notadd\Auth\Social\Contracts\AccessToken $token
     * @return mixed
     */
    protected function getUserByToken(AccessTokenContract $token) {
        $appSecretProof = hash_hmac('sha256', $token->getToken(), $this->clientSecret);
        $response = $this->getHttpClient()->get($this->graphUrl . '/' . $this->version . '/me?access_token=' . $token . '&appsecret_proof=' . $appSecretProof . '&fields=' . implode(',', $this->fields), [
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
        $avatarUrl = $this->graphUrl . '/' . $this->version . '/' . $user['id'] . '/picture';
        $firstName = $this->arrayItem($user, 'first_name');
        $lastName = $this->arrayItem($user, 'last_name');
        return new User([
            'id' => $this->arrayItem($user, 'id'),
            'nickname' => null,
            'name' => $firstName . ' ' . $lastName,
            'email' => $this->arrayItem($user, 'email'),
            'avatar' => $avatarUrl . '?type=normal',
            'avatar_original' => $avatarUrl . '?width=1920',
        ]);
    }
    /**
     * @param null $state
     * @return array
     */
    protected function getCodeFields($state = null) {
        $fields = parent::getCodeFields($state);
        if($this->popup) {
            $fields['display'] = 'popup';
        }
        return $fields;
    }
    /**
     * @param array $fields
     * @return $this
     */
    public function fields(array $fields) {
        $this->fields = $fields;
        return $this;
    }
    /**
     * @return $this
     */
    public function asPopup() {
        $this->popup = true;
        return $this;
    }
}