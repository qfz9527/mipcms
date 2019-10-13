<?php

if (!version_compare(PHP_VERSION,'5.4.0','ge')) {
    header("Content-type: text/html; charset=utf-8");
    echo '您当前使用的PHP版本为：' . PHP_VERSION . '，本网站系统最低要求PHP5.4，我们建议您使用PHP7.0版本';
    exit;
}

define('MIP_HOST',false);

define('SITE_HOST',false);

define('BAIDU',false);

define('APP_PATH', __DIR__ . '/../app/');

define('SITE_PATH', __DIR__ . '/');

defined('MIP_ROOT') or define('MIP_ROOT', __DIR__ . '/');

define('PUBLIC_PATH', __DIR__ . '/../public/');
 
define('VENDOR_PATH',__DIR__ . '/../system/vendor/');
// 加载框架引导文件
require __DIR__ . '/../system/thinkphp/start.php';
