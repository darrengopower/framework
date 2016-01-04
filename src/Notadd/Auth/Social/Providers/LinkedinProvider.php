<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 21:44
 */
namespace Notadd\Auth\Social\Providers;
use Notadd\Auth\Social\Contracts\AccessToken as AccessTokenContract;
use Notadd\Auth\Social\Contracts\Provider as ProviderContract;
use Notadd\Auth\Social\User;
/**
 * Class LinkedinProvider
 * @package Notadd\Auth\Social\Providers
 */
class LinkedinProvider extends Provider implements ProviderContract {
    /**
     * @var array
     */
    protected $scopes = [
        'r_basicprofile',
        'r_emailaddress'
    ];
    /**
     * @var array
     */
    protected $fields = [
        'id',
        'first-name',
        'last-name',
        'formatted-name',
        'email-address',
        'headline',
        'location',
        'industry',
        'public-profile-url',
        'picture-url',
        'picture-urls::(original)',
    ];
    /**
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state) {
        return $this->buildAuthUrlFromBase('https://www.linkedin.com/uas/oauth2/authorization', $state);
    }
    /**
     * @return string
     */
    protected function getTokenUrl() {
        return 'https://www.linkedin.com/uas/oauth2/accessToken';
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
        $fields = implode(',', $this->fields);
        $url = 'https://api.linkedin.com/v1/people/~:(' . $fields . ')';
        $response = $this->getHttpClient()->get($url, [
            'headers' => [
                'x-li-format' => 'json',
                'Authorization' => 'Bearer ' . $token,
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
            'nickname' => $this->arrayItem($user, 'formattedName'),
            'name' => $this->arrayItem($user, 'formattedName'),
            'email' => $this->arrayItem($user, 'emailAddress'),
            'avatar' => $this->arrayItem($user, 'pictureUrl'),
            'avatar_original' => $this->arrayItem($user, 'pictureUrls.values.0'),
        ]);
    }
    /**
     * @param array $fields
     * @return $this
     */
    public function fields(array $fields) {
        $this->fields = $fields;
        return $this;
    }
}