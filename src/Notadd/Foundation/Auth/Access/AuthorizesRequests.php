<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation\Auth\Access;
use Illuminate\Contracts\Auth\Access\Gate;
use Symfony\Component\HttpKernel\Exception\HttpException;
/**
 * Class AuthorizesRequests
 * @package Notadd\Foundation\Auth\Access
 */
trait AuthorizesRequests {
    /**
     * @param mixed $ability
     * @param mixed|array $arguments
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function authorize($ability, $arguments = []) {
        list($ability, $arguments) = $this->parseAbilityAndArguments($ability, $arguments);
        if(!$this->app->make(Gate::class)->check($ability, $arguments)) {
            throw $this->createGateUnauthorizedException($ability, $arguments);
        }
    }
    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable|mixed $user
     * @param mixed $ability
     * @param mixed|array $arguments
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function authorizeForUser($user, $ability, $arguments = []) {
        list($ability, $arguments) = $this->parseAbilityAndArguments($ability, $arguments);
        $result = app(Gate::class)->forUser($user)->check($ability, $arguments);
        if(!$result) {
            throw $this->createGateUnauthorizedException($ability, $arguments);
        }
    }
    /**
     * @param mixed $ability
     * @param mixed|array $arguments
     * @return array
     */
    protected function parseAbilityAndArguments($ability, $arguments) {
        if(is_string($ability)) {
            return [
                $ability,
                $arguments
            ];
        }
        return [
            debug_backtrace(false, 3)[2]['function'],
            $ability
        ];
    }
    /**
     * @param string $ability
     * @param array $arguments
     * @return \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function createGateUnauthorizedException($ability, $arguments) {
        return new HttpException(403, 'This action is unauthorized.');
    }
}