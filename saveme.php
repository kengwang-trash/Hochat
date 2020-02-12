<?php

/**
 * 救灾模式
 *
 * 当网页遇到无法挽回的错误时,即可通过此页面查看错误日志
 *
 * 日志内容可能很长,加载需要一段时间
 *
 * 建议先清除掉日志,再引发错误之后查看
 */
define('PASSWORD', 'saveme'); //SaveMe密码,在安装后修改
if (!$_GET['P'] == PASSWORD) exit;
$file_path = "error.log";
if ($_GET['d'] == 1) {
    echo file_get_contents($file_path);
    exit;
}
if ($_GET['c'] == 1) {
    file_put_contents($file_path, '');
    echo '清空日志成功';
}
?>
<h1>救援模式 - SaveMe</h1>
<table border="1">
    <tr>
        <th>错误编号</th>
        <th>错误信息</th>
        <th>错误文件</th>
        <th>错误位置</th>
    </tr>

    <?php
    if (file_exists($file_path)) {
        $file_contents = file($file_path);
        for ($i = 0; $i < count($file_contents); $i++) { //逐行读取文件内容
            $arr = json_decode($file_contents[$i], true);
            echo '<tr>';
            echo '<th>';
            echo $arr['n'];
            echo '</th>';
            echo '<th>';
            echo $arr['m'];
            echo '</th>';
            echo '<th>';
            echo $arr['f'];
            echo '</th>';
            echo '<th>';
            echo $arr['p'];
            echo '</th>';
            echo '</tr>';
        }
    }
    echo '</table>';
    ?>
    您还可以<a href="saveme.php?P=<?php echo $_GET['P']; ?>&d=1">点我</a>此处查看全部日志<br />
    您还可以<a href="saveme.php?P=<?php echo $_GET['P']; ?>&c=1">点我</a>清除全部日志