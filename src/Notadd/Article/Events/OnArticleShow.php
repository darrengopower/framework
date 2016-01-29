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
/**
 * Class OnArticleShow
 * @package Notadd\Article\Events
 */
class OnArticleShow {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $application;
    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    private $view;
    /**
     * @var \Notadd\Article\Article
     */
    private $article;
    /**
     * @param \Illuminate\Contracts\Foundation\Application $application
     * @param \Illuminate\Contracts\View\Factory $view
     * @param \Notadd\Article\Article $article
     */
    public function __construct(Application $application, Factory $view, Article $article) {
        $this->application = $application;
        $this->view = $view;
        $this->article = $article;
    }
    /**
     * @return \Notadd\Article\Models\Article
     */
    public function getArticle() {
        return $this->article->getModel();
    }
    /**
     * @return \Notadd\Category\Category
     */
    public function getCategory() {
        return $this->article->getCategory();
    }
    /**
     * @param $template
     */
    public function setArticleShowTemplate($template) {
        $this->article->getModel()->setShowTemplate($template);
    }
    /**
     * @param $key
     * @param $value
     */
    public function share($key, $value) {
        $this->view->share($key, $value);
    }
}