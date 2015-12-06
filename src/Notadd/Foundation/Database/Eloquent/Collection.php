<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:22
 */
namespace Notadd\Foundation\Database\Eloquent;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;
class Collection extends BaseCollection {
    /**
     * @param mixed $key
     * @param mixed $default
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function find($key, $default = null) {
        if($key instanceof Model) {
            $key = $key->getKey();
        }
        return Arr::first($this->items, function ($itemKey, $model) use ($key) {
            return $model->getKey() == $key;
        }, $default);
    }
    /**
     * @param mixed $relations
     * @return $this
     */
    public function load($relations) {
        if(count($this->items) > 0) {
            if(is_string($relations)) {
                $relations = func_get_args();
            }
            $query = $this->first()->newQuery()->with($relations);
            $this->items = $query->eagerLoadRelations($this->items);
        }
        return $this;
    }
    /**
     * @param mixed $item
     * @return $this
     */
    public function add($item) {
        $this->items[] = $item;
        return $this;
    }
    /**
     * @param mixed $key
     * @param mixed $value
     * @return bool
     */
    public function contains($key, $value = null) {
        if(func_num_args() == 2) {
            return parent::contains($key, $value);
        }
        if($this->useAsCallable($key)) {
            return parent::contains($key);
        }
        $key = $key instanceof Model ? $key->getKey() : $key;
        return parent::contains(function ($k, $m) use ($key) {
            return $m->getKey() == $key;
        });
    }
    /**
     * @param string $key
     * @return static
     * @deprecated since version 5.1. Use pluck instead.
     */
    public function fetch($key) {
        return new static(Arr::fetch($this->toArray(), $key));
    }
    /**
     * @return array
     */
    public function modelKeys() {
        return array_map(function ($m) {
            return $m->getKey();
        }, $this->items);
    }
    /**
     * @param \ArrayAccess|array $items
     * @return static
     */
    public function merge($items) {
        $dictionary = $this->getDictionary();
        foreach($items as $item) {
            $dictionary[$item->getKey()] = $item;
        }
        return new static(array_values($dictionary));
    }
    /**
     * @param \ArrayAccess|array $items
     * @return static
     */
    public function diff($items) {
        $diff = new static;
        $dictionary = $this->getDictionary($items);
        foreach($this->items as $item) {
            if(!isset($dictionary[$item->getKey()])) {
                $diff->add($item);
            }
        }
        return $diff;
    }
    /**
     * @param \ArrayAccess|array $items
     * @return static
     */
    public function intersect($items) {
        $intersect = new static;
        $dictionary = $this->getDictionary($items);
        foreach($this->items as $item) {
            if(isset($dictionary[$item->getKey()])) {
                $intersect->add($item);
            }
        }
        return $intersect;
    }
    /**
     * @param string|callable|null $key
     * @return static
     */
    public function unique($key = null) {
        if(!is_null($key)) {
            return parent::unique($key);
        }
        return new static(array_values($this->getDictionary()));
    }
    /**
     * @param mixed $keys
     * @return static
     */
    public function only($keys) {
        $dictionary = Arr::only($this->getDictionary(), $keys);
        return new static(array_values($dictionary));
    }
    /**
     * @param mixed $keys
     * @return static
     */
    public function except($keys) {
        $dictionary = array_except($this->getDictionary(), $keys);
        return new static(array_values($dictionary));
    }
    /**
     * @param array|string $attributes
     * @return $this
     */
    public function withHidden($attributes) {
        $this->each(function ($model) use ($attributes) {
            $model->withHidden($attributes);
        });
        return $this;
    }
    /**
     * @param \ArrayAccess|array $items
     * @return array
     */
    public function getDictionary($items = null) {
        $items = is_null($items) ? $this->items : $items;
        $dictionary = [];
        foreach($items as $value) {
            $dictionary[$value->getKey()] = $value;
        }
        return $dictionary;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function toBase() {
        return new BaseCollection($this->items);
    }
}