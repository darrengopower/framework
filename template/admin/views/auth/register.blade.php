@extends('admin::auth.layouts')
@section('content')
<div class="page page-auth clearfix">
    <div class="auth-container">
        <h1 class="site-logo h2 mb15"><a href="/"><span>Notadd</span>&nbsp;内容管理系统</a> - 注册管理员账号</h1>
        <div class="form-container">
            <p class="small">已经有账号了。<a href="{{ url('admin/login') }}">前往登录</a></p>
            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>喔！</strong>您的填写有误哦！<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form action="{{ url('admin/register') }}" class="form-horizontal" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group form-group-lg">
                    <input type="text" class="form-control" name="name" placeholder="用户名" value="{{ old('name') }}">
                </div>
                <div class="form-group form-group-lg">
                    <input type="email" class="form-control" name="email" placeholder="电子邮箱" value="{{ old('email') }}">
                </div>
                <div class="form-group form-group-lg">
                    <input type="password" class="form-control" name="password" placeholder="密码">
                </div>
                <div class="form-group form-group-lg">
                    <input type="password" class="form-control" name="password_confirmation" placeholder="确认密码">
                </div>
                <button type="submit" class="btn btn-lg btn-primary text-uppercase">注册</button>
            </form>
        </div>
    </div>
</div>
@endsection