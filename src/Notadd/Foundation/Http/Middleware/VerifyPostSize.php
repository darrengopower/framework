<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation\Http\Middleware;
use Closure;
use Illuminate\Http\Exception\PostTooLargeException;
/**
 * Class VerifyPostSize
 * @package Notadd\Foundation\Http\Middleware
 */
class VerifyPostSize {
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Illuminate\Http\Exception\PostTooLargeException
     */
    public function handle($request, Closure $next) {
        if($request->server('CONTENT_LENGTH') > $this->getPostMaxSize()) {
            throw new PostTooLargeException;
        }
        return $next($request);
    }
    /**
     * @return int
     */
    protected function getPostMaxSize() {
        $postMaxSize = ini_get('post_max_size');
        switch(substr($postMaxSize, -1)) {
            case 'M':
            case 'm':
                return (int)$postMaxSize * 1048576;
            case 'K':
            case 'k':
                return (int)$postMaxSize * 1024;
            case 'G':
            case 'g':
                return (int)$postMaxSize * 1073741824;
        }
        return (int)$postMaxSize;
    }
}