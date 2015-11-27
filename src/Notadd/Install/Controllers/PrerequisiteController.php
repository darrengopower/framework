<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:39
 */
namespace Notadd\Install\Controllers;
use Notadd\Foundation\Routing\Controller;
use Notadd\Install\Contracts\Prerequisite;
use Psr\Http\Message\ServerRequestInterface;
class PrerequisiteController extends Controller {
    public function render(ServerRequestInterface $request, Prerequisite $prerequisite) {
        $view = $this->view->make('install::layout');
        $prerequisite->check();
        $errors = $prerequisite->getErrors();
        if(count($errors)) {
            $view->content = $this->view->make('install::errors');
            $view->content->errors = $errors;
        } else {
            $view->content = $this->view->make('install::install');
        }
        return $view;
    }
}