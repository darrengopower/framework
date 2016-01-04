<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 16:17
 */
namespace Notadd\Article\Requests;
use Notadd\Foundation\Http\FormRequest;
/**
 * Class ArticleCreateRequest
 * @package Notadd\Article\Requests
 */
class ArticleCreateRequest extends FormRequest {
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
            'title.required' => '必须填写标题！',
            'title.max' => '标题长度超过最大限制字数！',
            'content.required' => '必须填写内容！'
        ];
    }
    /**
     * @return array
     */
    public function rules() {
        return [
            'title' => 'required|max:300',
            'content' => 'required',
        ];
    }
}