<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-17 11:21
 */
namespace Notadd\Foundation\Http;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Validation\ValidatesWhenResolvedTrait;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
/**
 * Class FormRequest
 * @package Notadd\Foundation\Http
 */
class FormRequest extends Request implements ValidatesWhenResolved {
    use ValidatesWhenResolvedTrait;
    /**
     * @var \Illuminate\Container\Container
     */
    protected $container;
    /**
     * The redirector instance.
     * @var \Illuminate\Routing\Redirector
     */
    protected $redirector;
    /**
     * @var string
     */
    protected $redirect;
    /**
     * @var string
     */
    protected $redirectRoute;
    /**
     * @var string
     */
    protected $redirectAction;
    /**
     * @var string
     */
    protected $errorBag = 'default';
    /**
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation'
    ];
    /**
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance() {
        $factory = $this->container->make('Illuminate\Validation\Factory');
        if(method_exists($this, 'validator')) {
            return $this->container->call([
                $this,
                'validator'
            ], compact('factory'));
        }
        return $factory->make($this->all(), $this->container->call([
            $this,
            'rules'
        ]), $this->messages(), $this->attributes());
    }
    /**
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return mixed
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException($this->response($this->formatErrors($validator)));
    }
    /**
     * @return bool
     */
    protected function passesAuthorization() {
        if(method_exists($this, 'authorize')) {
            return $this->container->call([
                $this,
                'authorize'
            ]);
        }
        return false;
    }
    /**
     * @return mixed
     */
    protected function failedAuthorization() {
        throw new HttpResponseException($this->forbiddenResponse());
    }
    /**
     * @param array $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(array $errors) {
        if($this->ajax() || $this->wantsJson()) {
            return new JsonResponse($errors, 422);
        }
        return $this->redirector->to($this->getRedirectUrl())->withInput($this->except($this->dontFlash))->withErrors($errors, $this->errorBag);
    }
    /**
     * @return \Illuminate\Http\Response
     */
    public function forbiddenResponse() {
        return new Response('Forbidden', 403);
    }
    /**
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return array
     */
    protected function formatErrors(Validator $validator) {
        return $validator->errors()->getMessages();
    }
    /**
     * @return string
     */
    protected function getRedirectUrl() {
        $url = $this->redirector->getUrlGenerator();
        if($this->redirect) {
            return $url->to($this->redirect);
        } elseif($this->redirectRoute) {
            return $url->route($this->redirectRoute);
        } elseif($this->redirectAction) {
            return $url->action($this->redirectAction);
        }
        return $url->previous();
    }
    /**
     * @param \Illuminate\Routing\Redirector $redirector
     * @return \Illuminate\Foundation\Http\FormRequest
     */
    public function setRedirector(Redirector $redirector) {
        $this->redirector = $redirector;
        return $this;
    }
    /**
     * @param \Illuminate\Container\Container $container
     * @return $this
     */
    public function setContainer(Container $container) {
        $this->container = $container;
        return $this;
    }
    /**
     * @return array
     */
    public function messages() {
        return [];
    }
    /**
     * @return array
     */
    public function attributes() {
        return [];
    }
}