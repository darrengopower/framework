<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation\Validation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exception\HttpResponseException;
trait ValidatesRequests {
    /**
     * @var string
     */
    protected $validatesRequestErrorBag;
    /**
     * @param  \Illuminate\Http\Request $request
     * @param  array $rules
     * @param  array $messages
     * @param  array $customAttributes
     * @return void
     * @throws \Illuminate\Http\Exception\HttpResponseException
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = []) {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);
        if($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }
    }
    /**
     * @param  string $errorBag
     * @param  \Illuminate\Http\Request $request
     * @param  array $rules
     * @param  array $messages
     * @param  array $customAttributes
     * @return void
     * @throws \Illuminate\Http\Exception\HttpResponseException
     */
    public function validateWithBag($errorBag, Request $request, array $rules, array $messages = [], array $customAttributes = []) {
        $this->withErrorBag($errorBag, function () use ($request, $rules, $messages, $customAttributes) {
            $this->validate($request, $rules, $messages, $customAttributes);
        });
    }
    /**
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     * @throws \Illuminate\Http\Exception\HttpResponseException
     */
    protected function throwValidationException(Request $request, $validator) {
        throw new HttpResponseException($this->buildFailedValidationResponse($request, $this->formatValidationErrors($validator)));
    }
    /**
     * @param  \Illuminate\Http\Request $request
     * @param  array $errors
     * @return \Illuminate\Http\Response
     */
    protected function buildFailedValidationResponse(Request $request, array $errors) {
        if($request->ajax() || $request->wantsJson()) {
            return new JsonResponse($errors, 422);
        }
        return redirect()->to($this->getRedirectUrl())->withInput($request->input())->withErrors($errors, $this->errorBag());
    }
    /**
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return array
     */
    protected function formatValidationErrors(Validator $validator) {
        return $validator->errors()->getMessages();
    }
    /**
     * @return string
     */
    protected function getRedirectUrl() {
        return app('Illuminate\Routing\UrlGenerator')->previous();
    }
    /**
     * @return \Illuminate\Contracts\Validation\Factory
     */
    protected function getValidationFactory() {
        return app('Illuminate\Contracts\Validation\Factory');
    }
    /**
     * @param  string $errorBag
     * @param  callable $callback
     * @return void
     */
    protected function withErrorBag($errorBag, callable $callback) {
        $this->validatesRequestErrorBag = $errorBag;
        call_user_func($callback);
        $this->validatesRequestErrorBag = null;
    }
    /**
     * @return string
     */
    protected function errorBag() {
        return $this->validatesRequestErrorBag ?: 'default';
    }
}