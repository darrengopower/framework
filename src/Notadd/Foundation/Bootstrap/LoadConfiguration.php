<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-17 09:58
 */
namespace Notadd\Foundation\Bootstrap;
use Illuminate\Config\Repository;
use Notadd\Foundation\Configuration\DefaultConfiguration;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
class LoadConfiguration {
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app) {
        $items = [];
        if(file_exists($cached = $app->getCachedConfigPath())) {
            $items = require $cached;
            $loadedFromCache = true;
        }
        $app->instance('config', $config = new Repository($items));
        if(!isset($loadedFromCache)) {
            $this->loadConfigurationFiles($app, $config);
        }
        $this->loadDefaultConfiguration($config);
        date_default_timezone_set($config['app.timezone']);
        mb_internal_encoding('UTF-8');
    }
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Contracts\Config\Repository $config
     * @return void
     */
    protected function loadConfigurationFiles(Application $app, RepositoryContract $config) {
        $data = require $this->getConfigurationFile($app);
        foreach($data as $key => $value) {
            $config->set($key, $value);
        }
    }
    public function loadDefaultConfiguration(Repository $config) {
        $default = new DefaultConfiguration($config);
        $default->loadApplicationConfiguration();
        $default->loadAuthConfiguration();
        $default->loadBroadcastingConfiguration();
        $default->loadCacheConfiguration();
        $default->loadCompileConfiguration();
        $default->loadFilesystemsConfiguration();
        $default->loadMailConfiguration();
        $default->loadQueueConfiguration();
        $default->loadServicesConfiguration();
        $default->loadSessionConfiguration();
        $default->loadViewConfiguration();
    }
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return array
     */
    protected function getConfigurationFile(Application $app) {
        return $app->basePath() . DIRECTORY_SEPARATOR . 'config.php';
    }
    /**
     * @param  \Symfony\Component\Finder\SplFileInfo $file
     * @return string
     */
    protected function getConfigurationNesting(SplFileInfo $file) {
        $directory = dirname($file->getRealPath());
        if($tree = trim(str_replace(config_path(), '', $directory), DIRECTORY_SEPARATOR)) {
            $tree = str_replace(DIRECTORY_SEPARATOR, '.', $tree) . '.';
        }
        return $tree;
    }
}