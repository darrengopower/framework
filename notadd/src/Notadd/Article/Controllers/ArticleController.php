<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Article\Controllers;
use Notadd\Article\Events\OnArticleShow;
use Notadd\Article\Models\Article;
use Notadd\Foundation\Routing\Controller;
class ArticleController extends Controller {
    /**
     * @param type $id
     */
    public function show($id) {
        $article = Article::findOrFail($id);
        $this->app['events']->fire(new OnArticleShow($this->app, $this->view, $article));
        $this->share('article', $article);
        return $this->view($article->getShowTemplate());
    }
}