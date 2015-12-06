<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-16 21:25
 */
namespace Notadd\Foundation;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
class ProviderRepository {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    /**
     * @var string
     */
    protected $manifestPath;
    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param string $manifestPath
     * @return void
     */
    public function __construct(ApplicationContract $app, Filesystem $files, $manifestPath) {
        $this->app = $app;
        $this->files = $files;
        $this->manifestPath = $manifestPath;
    }
    /**
     * @param array $providers
     * @return void
     */
    public function load(array $providers) {
        $manifest = $this->loadManifest();
        if($this->shouldRecompile($manifest, $providers)) {
            $manifest = $this->compileManifest($providers);
        }
        foreach($manifest['when'] as $provider => $events) {
            $this->registerLoadEvents($provider, $events);
        }
        foreach($manifest['eager'] as $provider) {
            $this->app->register($this->createProvider($provider));
        }
        $this->app->setDeferredServices($manifest['deferred']);
    }
    /**
     * @param string $provider
     * @param array $events
     * @return void
     */
    protected function registerLoadEvents($provider, array $events) {
        if(count($events) < 1) {
            return;
        }
        $app = $this->app;
        $app->make('events')->listen($events, function () use ($app, $provider) {
            $app->register($provider);
        });
    }
    /**
     * @param array $providers
     * @return array
     */
    protected function compileManifest($providers) {
        $manifest = $this->freshManifest($providers);
        foreach($providers as $provider) {
            $instance = $this->createProvider($provider);
            if($instance->isDeferred()) {
                foreach($instance->provides() as $service) {
                    $manifest['deferred'][$service] = $provider;
                }
                $manifest['when'][$provider] = $instance->when();
            } else {
                $manifest['eager'][] = $provider;
            }
        }
        return $this->writeManifest($manifest);
    }
    /**
     * @param string $provider
     * @return \Illuminate\Support\ServiceProvider
     */
    public function createProvider($provider) {
        return new $provider($this->app);
    }
    /**
     * @param array $manifest
     * @param array $providers
     * @return bool
     */
    public function shouldRecompile($manifest, $providers) {
        return is_null($manifest) || $manifest['providers'] != $providers;
    }
    /**
     * @return array
     */
    public function loadManifest() {
        if($this->files->exists($this->manifestPath)) {
            $manifest = json_decode($this->files->get($this->manifestPath), true);
            return array_merge(['when' => []], $manifest);
        }
    }
    /**
     * @param array $manifest
     * @return array
     */
    public function writeManifest($manifest) {
        $this->files->put($this->manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
        return $manifest;
    }
    /**
     * @param array $providers
     * @return array
     */
    protected function freshManifest(array $providers) {
        return [
            'providers' => $providers,
            'eager' => [],
            'deferred' => []
        ];
    }
}