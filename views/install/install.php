<h2>安装</h2>
<p>如果您在安装过程中遇到问题，可以联系作者获取帮助。</p>
<form autocomplete="off" method="post">
    <div class="form-group">
        <div class="form-field">
            <label>网站标题</label>
            <input name="title">
        </div>
    </div>
    <div class="form-group">
        <div class="form-field">
            <label>数据库服务器</label>
            <input name="host" value="localhost">
        </div>
        <div class="form-field">
            <label>数据库名</label>
            <input name="database">
        </div>
        <div class="form-field">
            <label>数据库用户名</label>
            <input name="username">
        </div>
        <div class="form-field">
            <label>数据库密码</label>
            <input type="password" name="password">
        </div>
        <div class="form-field">
            <label>数据库表前缀(例：not_)</label>
            <input type="text" name="prefix">
        </div>
    </div>
    <div class="form-group">
        <div class="form-field">
            <label>管理员用户名</label>
            <input name="admin_username">
        </div>
        <div class="form-field">
            <label>管理员Email</label>
            <input name="admin_email">
        </div>
        <div class="form-field">
            <label>管理员密码</label>
            <input type="password" name="admin_password">
        </div>
        <div class="form-field">
            <label>确认密码</label>
            <input type="password" name="admin_password_confirmation">
        </div>
    </div>
    <div id="error" style="display:none"></div>
    <div>
        <button type="submit">开始安装</button>
    </div>
</form>
<script src="http://cdn.bootcss.com/jquery/2.1.4/jquery.min.js"></script>
<script>
    $(function () {
        $('form :input:first').select();
        $('form').on('submit', function (e) {
            e.preventDefault();
            $('#error').hide().text('');
            var $button = $(this).find('button').text('正在安装...').prop('disabled', true);
            $.post("", $(this).serialize()).done(function (data) {
                //window.location.reload();
            }).fail(function (data) {
                $("#error").append("<p>安装操作有误：</p>");
                $.each(data.responseJSON, function(key, value) {
                    $("#error").append("<p>" + value + "</p>");
                });
                $button.prop('disabled', false).text('开始安装');
                $("#error").show();
                $("body").animate({
                    scrollTop: $("body").outerHeight()
                }, 1000);
            });
            return false;
        });
    });
</script>
