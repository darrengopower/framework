<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:49
 */
namespace Notadd\Foundation\Database\Eloquent;
class SoftDeletingScope implements ScopeInterface {
    /**
     * @var array
     */
    protected $extensions = [
        'ForceDelete',
        'Restore',
        'WithTrashed',
        'OnlyTrashed'
    ];
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $builder
     * @param \Notadd\Foundation\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model) {
        $builder->whereNull($model->getQualifiedDeletedAtColumn());
        $this->extend($builder);
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $builder
     * @param \Notadd\Foundation\Database\Eloquent\Model $model
     * @return void
     */
    public function remove(Builder $builder, Model $model) {
        $column = $model->getQualifiedDeletedAtColumn();
        $query = $builder->getQuery();
        $query->wheres = collect($query->wheres)->reject(function ($where) use ($column) {
            return $this->isSoftDeleteConstraint($where, $column);
        })->values()->all();
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $builder
     * @return void
     */
    public function extend(Builder $builder) {
        foreach($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
        $builder->onDelete(function (Builder $builder) {
            $column = $this->getDeletedAtColumn($builder);
            return $builder->update([
                $column => $builder->getModel()->freshTimestampString(),
            ]);
        });
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $builder
     * @return string
     */
    protected function getDeletedAtColumn(Builder $builder) {
        if(count($builder->getQuery()->joins) > 0) {
            return $builder->getModel()->getQualifiedDeletedAtColumn();
        } else {
            return $builder->getModel()->getDeletedAtColumn();
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $builder
     * @return void
     */
    protected function addForceDelete(Builder $builder) {
        $builder->macro('forceDelete', function (Builder $builder) {
            return $builder->getQuery()->delete();
        });
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $builder
     * @return void
     */
    protected function addRestore(Builder $builder) {
        $builder->macro('restore', function (Builder $builder) {
            $builder->withTrashed();
            return $builder->update([$builder->getModel()->getDeletedAtColumn() => null]);
        });
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $builder
     * @return void
     */
    protected function addWithTrashed(Builder $builder) {
        $builder->macro('withTrashed', function (Builder $builder) {
            $this->remove($builder, $builder->getModel());
            return $builder;
        });
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $builder
     * @return void
     */
    protected function addOnlyTrashed(Builder $builder) {
        $builder->macro('onlyTrashed', function (Builder $builder) {
            $model = $builder->getModel();
            $this->remove($builder, $model);
            $builder->getQuery()->whereNotNull($model->getQualifiedDeletedAtColumn());
            return $builder;
        });
    }
    /**
     * @param array $where
     * @param string $column
     * @return bool
     */
    protected function isSoftDeleteConstraint(array $where, $column) {
        return $where['type'] == 'Null' && $where['column'] == $column;
    }
}