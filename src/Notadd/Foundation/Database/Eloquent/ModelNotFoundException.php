<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:42
 */
namespace Notadd\Foundation\Database\Eloquent;
use RuntimeException;
/**
 * Class ModelNotFoundException
 * @package Notadd\Foundation\Database\Eloquent
 */
class ModelNotFoundException extends RuntimeException {
    /**
     * @var string
     */
    protected $model;
    /**
     * @param string $model
     * @return $this
     */
    public function setModel($model) {
        $this->model = $model;
        $this->message = "No query results for model [{$model}].";
        return $this;
    }
    /**
     * @return string
     */
    public function getModel() {
        return $this->model;
    }
}