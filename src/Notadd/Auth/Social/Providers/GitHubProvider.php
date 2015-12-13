<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 21:41
 */
namespace Notadd\Auth\Social\Providers;
use Exception;
use Notadd\Auth\Social\Contracts\AccessToken as AccessTokenContract;
use Notadd\Auth\Social\Contracts\Provider as ProviderContract;
use Notadd\Auth\Social\User;
class GitHubProvider extends Provider implements ProviderContract {
    /**
     * @var array
     */
    protected $scopes = ['user:email'];
    /**
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state) {
        return $this->buildAuthUrlFromBase('https://github.com/login/oauth/authorize', $state);
    }
    /**
     * @return string
     */
    protected function getTokenUrl() {
        return 'https://github.com/login/oauth/access_token';
    }
    /**
     * @param \Notadd\Auth\Social\Contracts\AccessToken $token
     * @return mixed
     */
    protected function getUserByToken(AccessTokenContract $token) {
        $userUrl = 'https://api.github.com/user?access_token=' . $token->getToken();
        $response = $this->getHttpClient()->get($userUrl, $this->getRequestOptions());
        $user = json_decode($response->getBody(), true);
        if(in_array('user:email', $this->scopes)) {
            $user['email'] = $this->getEmailByToken($token);
        }
        return $user;
    }
    /**
     * @param $token
     */
    protected function getEmailByToken($token) {
        $emailsUrl = 'https://api.github.com/user/emails?access_token=' . $token->getToken();
        try {
            $response = $this->getHttpClient()->get($emailsUrl, $this->getRequestOptions());
        } catch(Exception $e) {
            return;
        }
        foreach(json_decode($response->getBody(), true) as $email) {
            if($email['primary'] && $email['verified']) {
                return $email['email'];
            }
        }
    }
    /**
     * @param array $user
     * @return \Notadd\Auth\Social\User
     */
    protected function mapUserToObject(array $user) {
        return new User([
            'id' => $this->arrayItem($user, 'id'),
            'nickname' => $this->arrayItem($user, 'login'),
            'name' => $this->arrayItem($user, 'name'),
            'email' => $this->arrayItem($user, 'email'),
            'avatar' => $this->arrayItem($user, 'avatar_url'),
        ]);
    }
    /**
     * @return array
     */
    protected function getRequestOptions() {
        return [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
            ],
        ];
    }
}