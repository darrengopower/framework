<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-04 16:27
 */
namespace Notadd\Install\Requests;
use Notadd\Foundation\Http\FormRequest;
class InstallRequest extends FormRequest {
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
            'title.required' => '必须填写网站标题！',
            'host.required' => '必须填写数据库服务器！',
            'database.required' => '必须填写数据库名！',
            'username.required' => '必须填写数据库用户名！',
            'password.required' => '必须填写数据库密码！',
            'prefix.required' => '必须填写数据库表前缀！',
            'admin_username.required' => '必须填写管理员帐号！',
            'admin_password.required' => '必须填写管理员密码！',
            'admin_password_confirmation.required' => '必须填写重复密码！',
            'admin_email.required' => '必须填写电子邮箱！',
        ];
    }
    /**
     * @return array
     */
    public function rules() {
        return [
            'title' => 'required',
            'host' => 'required',
            'database' => 'required',
            'username' => 'required',
            'password' => 'required',
            'prefix' => 'required',
            'admin_username' => 'required',
            'admin_password' => 'required',
            'admin_password_confirmation' => 'required',
            'admin_email' => 'required',
        ];
    }
}