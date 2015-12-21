<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 21:16
 */
namespace Notadd\Auth\Social\Providers;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Notadd\Auth\Social\AccessToken;
use Notadd\Auth\Social\Contracts\AccessToken as AccessTokenContract;
use Notadd\Auth\Social\Contracts\Provider as ProviderContract;
use Notadd\Auth\Social\Exceptions\InvalidStateException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
abstract class Provider implements ProviderContract {
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var string
     */
    protected $clientId;
    /**
     * @var string
     */
    protected $clientSecret;
    /**
     * @var string
     */
    protected $redirectUrl;
    /**
     * @var array
     */
    protected $parameters = [];
    /**
     * @var array
     */
    protected $scopes = [];
    /**
     * @var string
     */
    protected $scopeSeparator = ',';
    /**
     * @var int
     */
    protected $encodingType = PHP_QUERY_RFC1738;
    /**
     * @var bool
     */
    protected $stateless = false;
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $clientId
     * @param $clientSecret
     * @param $redirectUrl
     */
    public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl) {
        $this->request = $request;
        $this->clientId = $clientId;
        $this->redirectUrl = $redirectUrl;
        $this->clientSecret = $clientSecret;
    }
    /**
     * @param string $state
     * @return string
     */
    abstract protected function getAuthUrl($state);
    /**
     * @return string
     */
    abstract protected function getTokenUrl();
    /**
     * @param \Notadd\Auth\Social\Contracts\AccessToken $token
     * @return array
     */
    abstract protected function getUserByToken(AccessTokenContract $token);
    /**
     * @param array $user
     * @return \Notadd\Auth\Social\User
     */
    abstract protected function mapUserToObject(array $user);
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect() {
        $state = null;
        if($this->usesState()) {
            $this->request->getSession()->set('state', $state = md5(time()));
        }
        return new RedirectResponse($this->getAuthUrl($state));
    }
    /**
     * @param string $url
     * @param string $state
     * @return string
     */
    protected function buildAuthUrlFromBase($url, $state) {
        return $url . '?' . http_build_query($this->getCodeFields($state), '', '&', $this->encodingType);
    }
    /**
     * @param string|null $state
     * @return array
     */
    protected function getCodeFields($state = null) {
        $fields = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'response_type' => 'code',
        ];
        if($this->usesState()) {
            $fields['state'] = $state;
        }
        return array_merge($fields, $this->parameters);
    }
    /**
     * @param array $scopes
     * @param string $scopeSeparator
     * @return string
     */
    protected function formatScopes(array $scopes, $scopeSeparator) {
        return implode($scopeSeparator, $scopes);
    }
    /**
     * @return mixed
     * @throws \Notadd\Auth\Social\Exceptions\InvalidStateException
     */
    public function user() {
        if($this->hasInvalidState()) {
            throw new InvalidStateException();
        }
        $user = $this->getUserByToken($token = $this->getAccessToken($this->getCode()));
        $user = $this->mapUserToObject($user)->merge(['original' => $user]);
        return $user->setToken($token);
    }
    /**
     * @return bool
     */
    protected function hasInvalidState() {
        if($this->isStateless()) {
            return false;
        }
        $state = $this->request->getSession()->get('state');
        return !(strlen($state) > 0 && $this->request->get('state') === $state);
    }
    /**
     * @param string $code
     * @return \Notadd\Auth\Social\AccessToken
     */
    public function getAccessToken($code) {
        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json'],
            $postKey => $this->getTokenFields($code),
        ]);
        return $this->parseAccessToken($response->getBody());
    }
    /**
     * @param string $code
     * @return array
     */
    protected function getTokenFields($code) {
        return [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUrl,
        ];
    }
    /**
     * @param \Psr\Http\Message\StreamInterface $body
     * @return \Notadd\Auth\Social\AccessToken
     */
    protected function parseAccessToken($body) {
        return new AccessToken((array)json_decode($body, true));
    }
    /**
     * @return string
     */
    protected function getCode() {
        return $this->request->get('code');
    }
    /**
     * @param array $scopes
     * @return $this
     */
    public function scopes(array $scopes) {
        $this->scopes = $scopes;
        return $this;
    }
    /**
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient() {
        return new Client();
    }
    /**
     * Set the request instance.
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request) {
        $this->request = $request;
        return $this;
    }
    /**
     * @return bool
     */
    protected function usesState() {
        return !$this->stateless;
    }
    /**
     * Determine if the provider is operating as stateless.
     * @return bool
     */
    protected function isStateless() {
        return $this->stateless;
    }
    /**
     * @return $this
     */
    public function stateless() {
        $this->stateless = true;
        return $this;
    }
    /**
     * @param array $parameters
     * @return $this
     */
    public function with(array $parameters) {
        $this->parameters = $parameters;
        return $this;
    }
    /**
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function arrayItem(array $array, $key, $default = null) {
        if(is_null($key)) {
            return $array;
        }
        if(isset($array[$key])) {
            return $array[$key];
        }
        foreach(explode('.', $key) as $segment) {
            if(!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }
        return $array;
    }
}