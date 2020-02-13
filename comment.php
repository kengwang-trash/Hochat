<?php

/* --------------------------  这个就是用户要引用的  ----------------------------------------*/
header('Access-Control-Allow-Origin:*');
if (empty($_SERVER['HTTP_REFERER'])) {
    echo 'Ho! How are you?<br>Seems using the wrong way~<br> --Hochat';
    exit;
}

if (empty($_GET['key'])) {
    echo 'Ho! Where\'s your key?<br> --Hochat';
    exit;
}

define('REALDIR', $_SERVER['HTTP_REFERER']);
if (empty($_GET['path'])) {
    $PATH = parse_url(REALDIR, PHP_URL_PATH);
    if (substr($PATH, -1) == "/") {
        $PATH .= 'index.html';
    }
    define('PATH', $PATH);
} else {
    define('PATH', $_GET['path']);
}
define('SITE', parse_url(REALDIR, PHP_URL_HOST));
require_once 'include.php';
require_once CLASSDIR . 'theme.class.php';
$AUTH = $DB->FetchResult(
    $DB->SelectData('auth', ['key' => $_GET['key']]),
    MYSQLI_ASSOC,
    false
);

if ($AUTH['key'] != $_GET['key']) {
    echo 'Ho! Key not found!';
    exit;
}

if ($AUTH['site'] != SITE) {
    echo 'Ho! The key don\'t match the site: ' . SITE . '<br>It\'s only for '
        . $AUTH['site'];
    exit;
}

define('PRO', $AUTH['pro'] == 1 ? true : false);

if (empty($_AUTH['theme'])) {
    $THEME = 'default';
} else {
    $THEME = $_AUTH['theme'];
}
define('THEME', $THEME);
$themefile = file_get_contents('theme/' . THEME . '.theme');
$search = array(
    '<%= ',
    '<%=',
    '=%>',
    '<* loop',
    '*>',
    '<* if',
    '<%',
    '%>'
);

$replace = array(
    '<%=',
    '<?php echo $FRONT[\'',
    '\']; ?>',
    '<?php foreach(',
    '): ?>',
    '<?php if (',
    '$FRONT[\'',
    '\']'
);
str_replace('<=', '<?php echo ', $themefile);
//select ,`cid`,`username`,`comment`,`site`,`time` from `hochat_comment`;
$comments = $DB->FetchResult($DB->SelectData('comment', ['site' => SITE . PATH]), MYSQLI_ASSOC, true);


?>


