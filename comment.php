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

define('FULLDIR', $_SERVER['HTTP_REFERER']);
define('SITE', parse_url(FULLDIR, PHP_URL_HOST));
require_once 'include.php';

$AUTH = $DB->FetchResult($DB->SelectData('auth', array('key' => $_GET['key'])), MYSQLI_ASSOC, false);

if ($AUTH['key'] != $_GET['key']) {
    echo 'Ho! Key not found!';
    exit;
}

if ($AUTH['site'] != SITE) {
    echo 'Ho! The key don\'t match the site: ' . SITE . '<br>It\'s only for ' . $AUTH['site'];
    exit;
}

define('PRO', $AUTH['pro'] == 1 ? true : false);

$DB->SelectData('comment', array('site' => SITE));
?>
<script>
    window.addEventListener('load',function () {
        window.parent.postMessage(document.body.offsetHeight,"*");
    });
</script>
<body>

</body>
