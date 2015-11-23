<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-17 10:00
 */
namespace Notadd\Foundation\Bootstrap;
use Illuminate\Log\Writer;
use Monolog\Logger as Monolog;
use Illuminate\Contracts\Foundation\Application;
class ConfigureLogging {
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app) {
        $log = $this->registerLogger($app);
        if($app->hasMonologConfigurator()) {
            call_user_func($app->getMonologConfigurator(), $log->getMonolog());
        } else {
            $this->configureHandlers($app, $log);
        }
        $app->bind('Psr\Log\LoggerInterface', function ($app) {
            return $app['log']->getMonolog();
        });
    }
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return \Illuminate\Log\Writer
     */
    protected function registerLogger(Application $app) {
        $app->instance('log', $log = new Writer(new Monolog($app->environment()), $app['events']));
        return $log;
    }
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Log\Writer $log
     * @return void
     */
    protected function configureHandlers(Application $app, Writer $log) {
        $method = 'configure' . ucfirst($app['config']['app.log']) . 'Handler';
        $this->{$method}($app, $log);
    }
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Log\Writer $log
     * @return void
     */
    protected function configureSingleHandler(Application $app, Writer $log) {
        $log->useFiles($app->storagePath() . '/logs/notadd.log');
    }
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Log\Writer $log
     * @return void
     */
    protected function configureDailyHandler(Application $app, Writer $log) {
        $log->useDailyFiles($app->storagePath() . '/logs/notadd.log', $app->make('config')->get('app.log_max_files', 5));
    }
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Log\Writer $log
     * @return void
     */
    protected function configureSyslogHandler(Application $app, Writer $log) {
        $log->useSyslog('notadd');
    }
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Log\Writer $log
     * @return void
     */
    protected function configureErrorlogHandler(Application $app, Writer $log) {
        $log->useErrorLog();
    }
}