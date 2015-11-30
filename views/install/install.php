<h2>安装 Notadd</h2>
<p>如果您在安装过程中遇到问题，可以联系作者获取帮助。</p>
<form method="post">
    <div id="error" style="display:none"></div>
    <div class="FormGroup">
        <div class="FormField">
            <label>网站标题</label> <input name="forumTitle">
        </div>
    </div>
    <div class="FormGroup">
        <div class="FormField">
            <label>MySQL Host</label> <input name="mysqlHost" value="localhost">
        </div>
        <div class="FormField">
            <label>MySQL Database</label> <input name="mysqlDatabase">
        </div>
        <div class="FormField">
            <label>MySQL Username</label> <input name="mysqlUsername">
        </div>
        <div class="FormField">
            <label>MySQL Password</label> <input type="password" name="mysqlPassword">
        </div>
        <div class="FormField">
            <label>表前缀(例：not_)</label> <input type="text" name="tablePrefix">
        </div>
    </div>
    <div class="FormGroup">
        <div class="FormField">
            <label>管理员用户名</label> <input name="adminUsername">
        </div>
        <div class="FormField">
            <label>管理员Email</label> <input name="adminEmail">
        </div>
        <div class="FormField">
            <label>管理员密码</label> <input type="password" name="adminPassword">
        </div>
        <div class="FormField">
            <label>确认密码</label> <input type="password" name="adminPasswordConfirmation">
        </div>
    </div>
    <div class="FormButtons">
        <button type="submit">开始安装</button>
    </div>
</form>
<script src="http://cdn.bootcss.com/jquery/2.1.4/jquery.min.js"></script>
<script>
    $(function () {
        $('form :input:first').select();
        $('form').on('submit', function (e) {
            e.preventDefault();
            var $button = $(this).find('button').text('正在安装...').prop('disabled', true);
            $.post('', $(this).serialize()).done(function (data) {
                console.log(data);
                //window.location.reload();
            }).fail(function (data) {
                $('#error').show().text('Something went wrong:\n\n' + data.responseJSON.error);
                $button.prop('disabled', false).text('开始安装');
            });
            return false;
        });
    });
</script>
