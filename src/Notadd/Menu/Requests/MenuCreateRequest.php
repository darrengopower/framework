<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:27
 */
namespace Notadd\Menu\Requests;
use Notadd\Foundation\Http\FormRequest;
/**
 * Class MenuCreateRequest
 * @package Notadd\Menu\Requests
 */
class MenuCreateRequest extends FormRequest {
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
            'parent_id.required' => '缺少上级菜单的参数！',
            'group_id.required' => '缺少菜单分组的参数！',
            'title.required' => '必须填写菜单名称！',
            'title.max' => '菜单名称长度超过最大限制字数！',
            'link.required' => '必须填写菜单链接！',
        ];
    }
    /**
     * @return array
     */
    public function rules() {
        return [
            'parent_id' => 'required',
            'group_id' => 'required',
            'title' => 'required|max:300',
            'link' => 'required',
        ];
    }
}