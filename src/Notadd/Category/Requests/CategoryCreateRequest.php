<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:49
 */
namespace Notadd\Category\Requests;
use Notadd\Foundation\Http\FormRequest;
/**
 * Class CategoryCreateRequest
 * @package Notadd\Category\Requests
 */
class CategoryCreateRequest extends FormRequest {
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
            'title.required' => '必须填写标题！'
        ];
    }
    /**
     * @return array
     */
    public function rules() {
        return [
            'title' => 'required|unique:categories|max:255'
        ];
    }
}