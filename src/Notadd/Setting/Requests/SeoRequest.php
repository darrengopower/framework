<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 11:28
 */
namespace Notadd\Setting\Requests;
use Notadd\Foundation\Http\FormRequest;
class SeoRequest extends FormRequest {
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