@extends('admin::auth.layouts')
@section('content')
    <div class="page page-auth clearfix">
        <div class="auth-container">
            <h1 class="site-logo h2 mb15"><a href="/"><span>Notadd</span>&nbsp;内容管理系统</a> - 重置密码</h1>
            <div class="form-container">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
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
                <form action="{{ url('admin/password/email') }}" class="form-horizontal" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group form-group-lg">
                        <input type="email" class="form-control" name="email" placeholder="输入电子邮箱地址" value="{{ old('email') }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block text-uppercase btn-lg">发送重置密码链接到邮箱</button>
                </form>
            </div>
        </div>
    </div>
@endsection