<h2>停一下！</h2>
<p>安装过程出现问题，请在安装之前解决才能继续安装。</p>
<div class="Errors">
    <?php foreach($errors as $error): ?>
        <div class="Error">
            <h3 class="Error-message"><?php echo $error['message']; ?></h3>
            <?php if(!empty($error['detail'])): ?>
                <p class="Error-detail"><?php echo $error['detail']; ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>