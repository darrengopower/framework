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
            'driver.required' => '必须选择数据库驱动！',
            'driver.in' => '数据库驱动必须为Mysql，Sqlite，Pgsql或Sqlsrv！',
            'host.required' => '必须填写数据库服务器！',
            'database.required' => '必须填写数据库名！',
            'database.alpha_dash' => '数据库名仅允许字母、数字、破折号（-）以及底线（_）！',
            'username.required' => '必须填写数据库用户名！',
            'username.alpha_dash' => '数据库用户名仅允许字母、数字、破折号（-）以及底线（_）！',
            'password.required' => '必须填写数据库密码！',
            'prefix.required' => '必须填写数据库表前缀！',
            'prefix.alpha_dash' => '数据库表前缀仅允许字母、数字、破折号（-）以及底线（_）！',
            'prefix.max' => '数据库表前缀最长为10个字符！',
            'admin_username.required' => '必须填写管理员用户名！',
            'admin_username.alpha_dash' => '管理员用户名仅允许字母、数字、破折号（-）以及底线（_）！',
            'admin_password.required' => '必须填写管理员密码！',
            'admin_password.alpha_dash' => '管理员密码仅允许字母、数字、破折号（-）以及底线（_）！',
            'admin_password.between' => '管理员密码长度必须在6到18个字符之间！',
            'admin_password.confirmed' => '两次管理员密码不一致！',
            'admin_password_confirmation.required' => '必须填写重复密码！',
            'admin_password_confirmation.alpha_dash' => '重复密码仅允许字母、数字、破折号（-）以及底线（_）！',
            'admin_email.required' => '必须填写管理员Email！',
            'admin_email.email' => '管理员Email格式不正确！',
        ];
    }
    /**
     * @return array
     */
    public function rules() {
        return [
            'title' => 'required',
            'driver' => 'required|in:mysql,sqlite,pgsql,sqlsrv',
            'host' => 'required',
            'database' => 'required|alpha_dash',
            'username' => 'required|alpha_dash',
            'password' => 'required',
            'prefix' => 'required|alpha_dash|max:10',
            'admin_username' => 'required|alpha_dash',
            'admin_password' => 'required|alpha_dash|between:6,18|confirmed',
            'admin_password_confirmation' => 'required|alpha_dash',
            'admin_email' => 'required|email',
        ];
    }
}