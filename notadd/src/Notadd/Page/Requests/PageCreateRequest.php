<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 17:14
 */
namespace Notadd\Page\Requests;
use Notadd\Foundation\Http\FormRequest;
class PageCreateRequest extends FormRequest {
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
            'title.unique' => '已有一个相同标题的页面存在！',
            'title.required' => '必须填写标题！',
            'title.max' => '标题长度超过最大限制字数！',
            'alias.required' => '必须填写静态化名称！',
            'alias.unique' => '已有一个相同静态化名称的页面存在！',
            'alias.max' => '静态化名称长度超过最大限制字数！',
        ];
    }
    /**
     * @return array
     */
    public function rules() {
        return [
            'title' => 'required|unique:pages|max:255',
            'alias' => 'required|unique:pages|max:255',
        ];
    }
}