<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 18:42
 */
namespace Notadd\Auth\Social;
use Closure;
use InvalidArgumentException;
use Notadd\Auth\Social\Contracts\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
class SocialManager implements Factory {
    /**
     * @var \Notadd\Auth\Social\Config
     */
    protected $config;
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;
    /**
     * The registered custom driver creators.
     * @var array
     */
    protected $customCreators = [];
    /**
     * @var array
     */
    protected $initialDrivers = [
        'facebook' => 'Facebook',
        'github' => 'GitHub',
        'google' => 'Google',
        'linkedin' => 'Linkedin',
        'weibo' => 'Weibo',
        'qq' => 'QQ',
        'wechat' => 'WeChat',
        'douban' => 'Douban',
    ];
    /**
     * @var array
     */
    protected $drivers = [];
    /**
     * @param array $config
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     */
    public function __construct(array $config, Request $request = null) {
        $this->config = new Config($config);
        $this->request = $request ?: $this->createDefaultRequest();
    }
    /**
     * @param \Notadd\Auth\Social\Config $config
     * @return $this
     */
    public function config(Config $config) {
        $this->config = $config;
        return $this;
    }
    /**
     * @return string
     */
    public function getDefaultDriver() {
        throw new InvalidArgumentException('No Socialite driver was specified.');
    }
    /**
     * @param string $driver
     * @return mixed
     */
    public function driver($driver = null) {
        $driver = $driver ?: $this->getDefaultDriver();
        if(!isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->createDriver($driver);
        }
        return $this->drivers[$driver];
    }
    /**
     * @param string $driver
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver) {
        if($provider = $this->initialDrivers[$driver]) {
            $provider = __NAMESPACE__ . '\\Providers\\' . $provider . 'Provider';
            return $this->buildProvider($provider, $this->formatConfig($this->config->get($driver)));
        }
        if(isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }
        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }
    /**
     * @param string $driver
     * @return mixed
     */
    protected function callCustomCreator($driver) {
        return $this->customCreators[$driver]($this->config);
    }
    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createDefaultRequest() {
        $request = Request::createFromGlobals();
        $session = new Session();
        $session->start();
        $request->setSession($session);
        return $request;
    }
    /**
     * @param string $driver
     * @param \Closure $callback
     * @return $this
     */
    public function extend($driver, Closure $callback) {
        $this->customCreators[$driver] = $callback;
        return $this;
    }
    /**
     * @return array
     */
    public function getDrivers() {
        return $this->drivers;
    }
    /**
     * @param string $driver
     * @return mixed
     */
    public function with($driver) {
        return $this->driver($driver);
    }
    /**
     * @param string $provider
     * @param array $config
     * @return \Notadd\Auth\Social\Providers\Provider
     */
    public function buildProvider($provider, $config) {
        return new $provider($this->request, $config['client_id'], $config['client_secret'], $config['redirect']);
    }
    /**
     * @param array $config
     * @return array
     */
    public function formatConfig(array $config) {
        return array_merge([
            'identifier' => $config['client_id'],
            'secret' => $config['client_secret'],
            'callback_uri' => $config['redirect'],
        ], $config);
    }
    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        return call_user_func_array([
            $this->driver(),
            $method
        ], $parameters);
    }
}