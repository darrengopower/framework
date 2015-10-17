<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Foundation\Auth\Access;
use Illuminate\Contracts\Auth\Access\Gate;
trait Authorizable {
    /**
     * @param  string $ability
     * @param  array|mixed $arguments
     * @return bool
     */
    public function can($ability, $arguments = []) {
        return app(Gate::class)->forUser($this)->check($ability, $arguments);
    }
    /**
     * @param  string $ability
     * @param  array|mixed $arguments
     * @return bool
     */
    public function cant($ability, $arguments = []) {
        return !$this->can($ability, $arguments);
    }
    /**
     * @param  string $ability
     * @param  array|mixed $arguments
     * @return bool
     */
    public function cannot($ability, $arguments = []) {
        return $this->cant($ability, $arguments);
    }
}