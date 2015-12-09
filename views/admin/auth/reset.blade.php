@extends('admin::auth.layouts')
@section('content')
    <div class="page page-auth clearfix">
        <div class="auth-container">
            <h1 class="site-logo h2 mb15"><a href="{{ url() }}"><span>Notadd</span>&nbsp;内容管理系统</a> - 重置密码</h1>
            <div class="form-container">
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
                <form action="{{ url('admin/password/reset') }}" class="form-horizontal" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group form-group-lg">
                        <input type="email" class="form-control" name="email" placeholder="电子邮箱" value="{{ old('email') }}">
                    </div>
                    <div class="form-group form-group-lg">
                        <input type="password" class="form-control" name="password" placeholder="密码">
                    </div>
                    <div class="form-group form-group-lg">
                        <input type="password" class="form-control" name="password_confirmation" placeholder="确认密码">
                    </div>
                    <button type="submit" class="btn btn-lg btn-primary text-uppercase">重置密码</button>
                </form>
            </div>
        </div>
    </div>
@endsection