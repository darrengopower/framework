<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-10 1:16:45
 */
namespace Notadd\Page\Events;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\View\Factory;
use Notadd\Page\Page;
class OnPageShow {
    private $app;
    private $page;
    private $view;
    public function __construct(Application $app, Factory $view, Page $page) {
        $this->app = $app;
        $this->page = $page;
        $this->view = $view;
    }
    public function getPage() {
        return $this->page->getModel();
    }
    public function share($key, $value) {
        $this->view->share($key, $value);
    }
}