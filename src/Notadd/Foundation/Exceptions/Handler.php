<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-16 22:21
 */
namespace Notadd\Foundation\Exceptions;
use Exception;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Debug\ExceptionHandler as SymfonyDisplayer;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
class Handler implements ExceptionHandlerContract {
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $log;
    /**
     * @var array
     */
    protected $dontReport = [];
    /**
     * @param \Psr\Log\LoggerInterface $log
     * @return void
     */
    public function __construct(LoggerInterface $log) {
        $this->log = $log;
    }
    /**
     * @param \Exception $e
     * @return void
     */
    public function report(Exception $e) {
        if($this->shouldReport($e)) {
            $this->log->error($e);
        }
    }
    /**
     * @param \Exception $e
     * @return bool
     */
    public function shouldReport(Exception $e) {
        return !$this->shouldntReport($e);
    }
    /**
     * @param \Exception $e
     * @return bool
     */
    protected function shouldntReport(Exception $e) {
        foreach($this->dontReport as $type) {
            if($e instanceof $type) {
                return true;
            }
        }
        return false;
    }
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e) {
        if($this->isHttpException($e)) {
            return $this->toIlluminateResponse($this->renderHttpException($e), $e);
        } else {
            return $this->toIlluminateResponse($this->convertExceptionToResponse($e), $e);
        }
    }
    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Exception $e
     * @return \Illuminate\Http\Response
     */
    protected function toIlluminateResponse($response, Exception $e) {
        $response = new Response($response->getContent(), $response->getStatusCode(), $response->headers->all());
        $response->exception = $e;
        return $response;
    }
    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Exception $e
     * @return void
     */
    public function renderForConsole($output, Exception $e) {
        (new ConsoleApplication)->renderException($e, $output);
    }
    /**
     * @param \Symfony\Component\HttpKernel\Exception\HttpException $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpException $e) {
        $status = $e->getStatusCode();
        if(view()->exists("errors.{$status}")) {
            return response()->view("errors.{$status}", ['exception' => $e], $status);
        } else {
            return $this->convertExceptionToResponse($e);
        }
    }
    /**
     * @param \Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertExceptionToResponse(Exception $e) {
        return (new SymfonyDisplayer(config('app.debug')))->createResponse($e);
    }
    /**
     * @param \Exception $e
     * @return bool
     */
    protected function isHttpException(Exception $e) {
        return $e instanceof HttpException;
    }
}