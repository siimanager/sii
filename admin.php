<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<'))
    die('require PHP > 5.3.0 !');
    
    // 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', False);

// 定义应用目录
define('APP_PATH', './App/');

// 定义应用名
define("APP_NAME", 'SII后台管理');

// 定义模块名 App/Admin 替换原来的Home
define('BIND_MODULE', 'Admin');

define('URL_CASE_INSENSITIVE', 'false');

//设置缓存路径
define('WEB_CACHE_PATH', '/App/');    

ini_set("session.save_handler", "user");//设置PHP的SESSION由用户定义

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单