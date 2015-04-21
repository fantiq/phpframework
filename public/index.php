<?php
//////////
// 入口文件 //
//////////


// 定义项目路径
define('APP_PATH', dirname(__DIR__).'/app');
include dirname(__DIR__).'/src/App.php';
$conf = '';
App::run();