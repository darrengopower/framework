<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 17:16
 */
namespace Notadd\Page;
use Illuminate\Support\Collection;
use Notadd\Page\Models\Page as PageModel;
class Page {
    private $id;
    protected $model;
    public function __construct($id) {
        $this->id = $id;
        $this->model = PageModel::findOrFail($id);
    }
    public function getAlias() {
        return $this->model->getAttribute('alias');
    }
    public function getContent() {
        return $this->model->getAttribute('content');
    }
    public function getDescription() {
        return $this->model->getAttribute('description');
    }
    public function getId() {
        return $this->id;
    }
    public function getKeywords() {
        return $this->model->getAttribute('keyword');
    }
    public function getLoopParent(Collection &$list, PageModel $model = null) {
        if ($model === null) {
            $model = $this->model;
        }
        if ($model->hasParent()) {
            $parent = $model->getAttribute('parent');
            $list->prepend($parent);
            $this->getLoopParent($list, $parent);
        }
    }
    public function getModel() {
        return $this->model;
    }
    public function getPageId() {
        return $this->id;
    }
    public function getRouting() {
        $loopParent = Collection::make([$this->model]);
        $this->getLoopParent($loopParent);
        $routingString = Collection::make();
        foreach ($loopParent as $model) {
            $model->getAttribute('alias') && $routingString->push($model->getAttribute('alias'));
        }
        return $routingString->implode('/');
    }
    public function getSubPages() {
        $list = Collection::make();
        $data = $this->model->whereParentId($this->model->getAttribute('id'))->get();
        foreach($data as $value) {
            $list->push(new Page($value->getAttribute('id')));
        }
        return $list;
    }
    public function getTemplate() {
        return $this->model->getAttribute('template');
    }
    public function getTitle() {
        return $this->model->getAttribute('title');
    }
}