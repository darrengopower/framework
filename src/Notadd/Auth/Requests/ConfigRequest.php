<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-25 16:32
 */
namespace Notadd\Auth\Requests;
use Notadd\Foundation\Http\FormRequest;
/**
 * Class ConfigRequest
 * @package Notadd\Auth\Requests
 */
class ConfigRequest extends FormRequest{
    /**
     * @return bool
     */
    public function authorize() {
        return true;
    }
    /**
     * @return array
     */
    public function messages() {
        return [
            'title.required' => 'SEO设置标题为必填项！'
        ];
    }
    /**
     * @return array
     */
    public function rules() {
        return [
            'title' => 'required'
        ];
    }
}