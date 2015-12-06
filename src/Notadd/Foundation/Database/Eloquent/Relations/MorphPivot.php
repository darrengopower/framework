<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 18:55
 */
namespace Notadd\Foundation\Database\Eloquent\Relations;
use Notadd\Foundation\Database\Eloquent\Builder;
class MorphPivot extends Pivot {
    /**
     * @var string
     */
    protected $morphType;
    /**
     * @var string
     */
    protected $morphClass;
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query) {
        $query->where($this->morphType, $this->morphClass);
        return parent::setKeysForSaveQuery($query);
    }
    /**
     * @return int
     */
    public function delete() {
        $query = $this->getDeleteQuery();
        $query->where($this->morphType, $this->morphClass);
        return $query->delete();
    }
    /**
     * @param string $morphType
     * @return $this
     */
    public function setMorphType($morphType) {
        $this->morphType = $morphType;
        return $this;
    }
    /**
     * @param string $morphClass
     * @return \Notadd\Foundation\Database\Eloquent\Relations\MorphPivot
     */
    public function setMorphClass($morphClass) {
        $this->morphClass = $morphClass;
        return $this;
    }
}