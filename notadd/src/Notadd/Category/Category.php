<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-10 14:51:12
 */
namespace Notadd\Category;
use Illuminate\Support\Collection;
use Notadd\Article\Models\Article;
use Notadd\Category\Models\Category as CategoryModel;
class Category {
    private $id;
    private $model;
    public function __construct($id) {
        $this->id = $id;
        $this->model = CategoryModel::findOrFail($id);
    }
    public function getId() {
        return $this->id;
    }
    public function getList() {
        if($this->model->hasParent()) {
            $model = Article::whereCategoryId($this->model->getAttribute('id'));
        } else {
            $relations = $this->getRelationCategoryList();
            $list = Collection::make();
            foreach($relations as $relation) {
                $list->push($relation->getId());
            }
            $model = Article::whereIn('category_id', $list->toArray());
        }
        return $model->get();
    }
    public function getLoopParent(Collection &$list, CategoryModel $model = null) {
        if($model === null) {
            $model = $this->model;
        }
        if($model->hasParent()) {
            $parent = $model->getAttribute('parent');
            $list->prepend($parent);
            $this->getLoopParent($list, $parent);
        }
    }
    public function getModel() {
        return $this->model;
    }
    public function getName() {
        return $this->model->getAttribute('title');
    }
    public function getRelationCategoryList() {
        $list = Collection::make();
        if($this->model->hasParent()) {
            $data = CategoryModel::whereEnabled(true)->whereParentId($this->model->getAttribute('parent_id'))->orderBy('created_at', 'asc')->get();
        } else {
            $data = CategoryModel::whereEnabled(true)->whereParentId($this->model->getAttribute('id'))->orderBy('created_at', 'asc')->get();
        }
        foreach($data as $category) {
            $list->push(new Category($category->getAttribute('id')));
        }
        return $list;
    }
    public function getRouting() {
        $loopParent = Collection::make([$this->model]);
        $this->getLoopParent($loopParent);
        $routingString = Collection::make();
        foreach($loopParent as $model) {
            $model->getAttribute('alias') && $routingString->push($model->getAttribute('alias'));
        }
        return $routingString->implode('/');
    }
    public function getShowTemplate() {
        return $this->model->getShowTemplate();
    }
}