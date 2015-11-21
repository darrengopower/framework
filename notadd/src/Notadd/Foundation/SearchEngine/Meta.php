<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-21 15:15
 */
namespace Notadd\Foundation\SearchEngine;
use Illuminate\Support\Collection;
class Meta {
    private $title;
    private $description;
    private $keywords;
    public function __construct() {
        $this->title = '{sitename}';
        $this->description = '{sitename}';
        $this->keywords = '{sitename}';
    }
    public function getData() {
        $data = Collection::make();
        $data->put('title', $this->title);
        $data->put('description', $this->description);
        $data->put('keywords', $this->keywords);
        return $data;
    }
    public function setTitle($title) {
        $this->title = trim($title);
    }
    public function setDescription($description) {
        $this->description = trim($description);
    }
    public function setKeywords($keywords) {
        $this->keywords = trim($keywords);
    }
}