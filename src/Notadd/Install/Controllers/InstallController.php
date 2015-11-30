<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-27 23:17
 */
namespace Notadd\Install\Controllers;
use Notadd\Foundation\Routing\Controller;
use Psr\Http\Message\ServerRequestInterface;
class InstallController extends Controller {
    public function handle(ServerRequestInterface $request) {
        dd($this);
    }
}