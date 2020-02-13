<?php define('THEMENAME','default'); if (THEME!=THEMENAME) exit;?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>HoChat</title>
    <link rel="stylesheet" href="https://cdn.hochat.space/theme/default/style/main.css">
</head>
<body>

<div style="height: 50px;"></div>


<!-- HoChat! -->
<!-- Base Example -->
<div class="hochat">
    <div class="hochat-login">
        <div class="hochat-login-title">发表评论</div>
        <form>
            <div class="hochat-login-input-group">
                <div class="hochat-login-input">
                    <label>昵称(必填)</label>
                    <input placeholder="输入您的昵称" type="text">
                </div>

                <div class="hochat-login-input">
                    <label>邮件(必填)</label>
                    <input placeholder="请输入您的邮箱地址" type="text">
                </div>

                <div class="hochat-login-input">
                    <label>网址</label>
                    <input placeholder="输入您的网站地址" type="text">
                </div>
            </div>
            <textarea placeholder="什么叫鸡巴话，都在这里说吧~" rows="3"></textarea>
            <button>发射</button>
        </form>

    </div>
	<?php foreach ($FRONT['comments'] as $comment): arraytofront('comment',$comment); ?>
    <div class="ho-chat">
        <img onerror="this.src='https://cdn.hochat.space/img/avatar.png'" src="https://cdn.v2ex.com/gravatar/<?php echo $FRONT['comment.mailmd5'];?>?d=https%3a%2f%2fcdn.hochat.space%2fimg%2favatar.png">
        <div class="ho-content">
            <div class="ho-name"><?php echo $FRONT['comment.username'];?></div>
            <div class="ho-info"><?php echo $FRONT['comment.website'];?></div>
            <div class="ho-msg">
                <?php echo $FRONT['comment.comment'];?>
                <div class="ho-time"><?php echo $FRONT['comment.time'];?></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<!-- Base Example -->
<script>
    window.addEventListener('load', function () {
        window.parent.postMessage(document.body.offsetHeight, "*");
    });
</script>
</body>
</html>
