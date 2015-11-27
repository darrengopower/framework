<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-5 15:08
 */
namespace Notadd\Article\Events;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Notadd\Article\Article;
class OnArticleShow {
    private $app;
    private $view;
    private $article;
    public function __construct(Application $app, Factory $view, Article $article) {
        $this->app = $app;
        $this->view = $view;
        $this->article = $article;
    }
    public function getArticle() {
        return $this->article->getModel();
    }
    public function getCategory() {
        return $this->article->getCategory();
    }
    public function setArticleShowTemplate($template) {
        $this->article->getModel()->setShowTemplate($template);
    }
    public function share($key, $value) {
        $this->view->share($key, $value);
    }
}