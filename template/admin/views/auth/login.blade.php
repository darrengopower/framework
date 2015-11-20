@extends('admin::auth.layouts')
@section('title'){{ Setting::get('site.company', 'iBenchu CMS')  }}后台管理系统@endsection
@section('content')
<div class="page page-auth clearfix">
    <div class="auth-container">
        <h1 class="site-logo h2 mb15"><a href="{{ url('/') }}"><span>{{ Setting::get('site.company', 'Notadd CMS')  }}</span>&nbsp;内容管理系统</a></h1>
        <h3 class="text-normal h4 text-center">欢迎登陆后台管理系统</h3>
        <div class="form-container">
            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>错误！</strong>请检查登陆账号是否填写正确。
            </div>
            @endif
            <form action="{{ url('admin/login') }}" class="form-horizontal" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group form-group-lg">
                    <input type="email" class="form-control" name="email" placeholder="邮件账户" value="">
                </div>
                <div class="form-group form-group-lg">
                    <input type="password" class="form-control" name="password" placeholder="密码" value="">
                </div>
                <div class="clearfix"><a href="{{ url('admin/password/email') }}" class="right small">忘记密码了吗？</a></div>
                <div class="clearfix mb15">
                    <button type="submit" class="btn btn-lg btn-w120 btn-primary text-uppercase">登录</button>
                    <div class="ui-checkbox ui-checkbox-primary mt15 right">
                        <label>
                            <input type="checkbox" name="remember">
                            <span>记住我</span>
                        </label>
                    </div>
                </div>
                <div class="clearfix text-center">
                    <p>尚未注册管理员账号？ <a href="{{ url('admin/register') }}">赶紧去注册一个</a></p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection