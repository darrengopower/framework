<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-17 10:09
 */
namespace Notadd\Foundation\Bootstrap;
use Exception;
use ErrorException;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
class HandleExceptions {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app) {
        $this->app = $app;
        error_reporting(-1);
        set_error_handler([
            $this,
            'handleError'
        ]);
        set_exception_handler([
            $this,
            'handleException'
        ]);
        register_shutdown_function([
            $this,
            'handleShutdown'
        ]);
        if(!$app->environment('testing')) {
            ini_set('display_errors', 'Off');
        }
    }
    /**
     * @param  int $level
     * @param  string $message
     * @param  string $file
     * @param  int $line
     * @param  array $context
     * @return void
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = []) {
        if(error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }
    /**
     * @param  \Throwable $e
     * @return void
     */
    public function handleException($e) {
        if(!$e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }
        $this->getExceptionHandler()->report($e);
        if($this->app->runningInConsole()) {
            $this->renderForConsole($e);
        } else {
            $this->renderHttpResponse($e);
        }
    }
    /**
     * @param  \Exception $e
     * @return void
     */
    protected function renderForConsole($e) {
        $this->getExceptionHandler()->renderForConsole(new ConsoleOutput, $e);
    }
    /**
     * @param  \Exception $e
     * @return void
     */
    protected function renderHttpResponse($e) {
        $this->getExceptionHandler()->render($this->app['request'], $e)->send();
    }
    /**
     * @return void
     */
    public function handleShutdown() {
        if(!is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            $this->handleException($this->fatalExceptionFromError($error, 0));
        }
    }
    /**
     * @param  array $error
     * @param  int|null $traceOffset
     * @return \Symfony\Component\Debug\Exception\FatalErrorException
     */
    protected function fatalExceptionFromError(array $error, $traceOffset = null) {
        return new FatalErrorException($error['message'], $error['type'], 0, $error['file'], $error['line'], $traceOffset);
    }
    /**
     * @param  int $type
     * @return bool
     */
    protected function isFatal($type) {
        return in_array($type, [
            E_ERROR,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_PARSE
        ]);
    }
    /**
     * @return \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected function getExceptionHandler() {
        return $this->app->make('Illuminate\Contracts\Debug\ExceptionHandler');
    }
}