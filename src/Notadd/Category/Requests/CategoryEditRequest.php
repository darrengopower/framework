<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:49
 */
namespace Notadd\Category\Requests;
use Notadd\Foundation\Http\FormRequest;
class CategoryEditRequest extends FormRequest {
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
            'title.unique' => '已有重复标题的分类存在！'
        ];
    }
    /**
     * @return array
     */
    public function rules() {
        return [
            'title' => 'required|unique:categories,title,' . $this->route('category') . '|max:255',
        ];
    }
}