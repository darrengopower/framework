<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation\Http\Middleware;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpKernel\Exception\HttpException;
/**
 * Class CheckForMaintenanceMode
 * @package Notadd\Foundation\Http\Middleware
 */
class CheckForMaintenanceMode {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;
    /**
     * CheckForMaintenanceMode constructor.
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if($this->app->isDownForMaintenance()) {
            throw new HttpException(503);
        }
        return $next($request);
    }
}