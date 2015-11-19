<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-11 0:56:56
 */
namespace Notadd\Article;
use Illuminate\Support\Str;
use Notadd\Article\Models\Article as ArticleModel;
class Article {
    private $id;
    private $model;
    public function __construct($id) {
        $this->id = $id;
        $this->model = ArticleModel::findOrFail($id);
    }
    public function getArticleId() {
        return $this->id;
    }
    public function getCategory() {
        return $this->model->getAttribute('category');
    }
    public function getContent() {
        return $this->model->getAttribute('content');
    }
    public function getDescription($limit = 130) {
        return Str::limit(strip_tags($this->model->getAttribute('content')), $limit, '...');
    }
    public function getModel() {
        return $this->model;
    }
    public function getTitle() {
        return $this->model->getAttribute('title');
    }
}