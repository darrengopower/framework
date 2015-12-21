<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 18:01
 */
namespace Notadd\Install\Prerequisites;
use Notadd\Install\Contracts\Prerequisite as PrerequisiteContract;
class Composite implements PrerequisiteContract {
    /**
     * @var array
     */
    protected $prerequisites = [];
    /**
     * @param \Notadd\Install\Contracts\Prerequisite| $first
     */
    public function __construct(PrerequisiteContract $first) {
        foreach(func_get_args() as $prerequisite) {
            $this->prerequisites[] = $prerequisite;
        }
    }
    /**
     * @return mixed
     */
    public function check() {
        return array_reduce($this->prerequisites, function ($previous, PrerequisiteContract $prerequisite) {
            return $prerequisite->check() && $previous;
        }, true);
    }
    /**
     * @return mixed
     */
    public function getErrors() {
        return collect($this->prerequisites)->map(function (PrerequisiteContract $prerequisite) {
            return $prerequisite->getErrors();
        })->reduce('array_merge', []);
    }
}