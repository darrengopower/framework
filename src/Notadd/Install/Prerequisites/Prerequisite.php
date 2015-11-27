<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 17:59
 */
namespace Notadd\Install\Prerequisites;
use Notadd\Install\Contracts\Prerequisite as PrerequisiteContract;
abstract class Prerequisite implements PrerequisiteContract {
    /**
     * @var array
     */
    protected $errors = [];
    /**
     * @return mixed
     */
    abstract public function check();
    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
}