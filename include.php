<?php

define('ROOT', __DIR__ . '/');
define('CLASSDIR', ROOT . 'class/');
define('DOMAIN', 'api.hochat.space');

require_once(CLASSDIR . 'output.class.php');
require_once(CLASSDIR . 'error.class.php');

//Init MySQL Connection
require_once(CLASSDIR . 'db.class.php');
$DB = new DB(
    'cdb-35uiv8mc.cd.tencentcdb.com', 'hochat', 'EZ6PzTd-_yd8TNj', 'hochat',
    'hochat_', 10103
);