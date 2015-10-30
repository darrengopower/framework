<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 11:28
 */
namespace Notadd\Setting\Requests;
use Notadd\Foundation\Http\FormRequest;
class SiteRequest extends FormRequest {
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
            'title.required' => '网站标题为必填项！',
            'domain.required' => '域名为必填项！',
            'email.email' => '请填写正确的Email格式！'
        ];
    }
    /**
     * @return array
     */
    public function rules() {
        return [
            'title' => 'required',
            'domain' => 'required',
            'email' => 'email'
        ];
    }
}