说明：
入口文件： public/index.php
配置文件：app/config/configs.php
控制器 ： app/controller/IndexController.php
模型 ：	  app/models/UsersModel.php

src 是框架源代码
App.php 是入口文件需要调用的 App::run()
	self::init($conf); // 初始化一些参数

	.....声明一些需要用到的组件.....

	App::$hook->preRoute(); // 钩子
	// 解析url后面的参数分析请求的是那个控制器的那个方法
	$route->resolve();
	App::$hook->preController();// 钩子
	// 通过上面的分析(以及对这些请求参数的合法性检测)调用用户的controller类
	$dispatch->run();
	// 发送内容到client
	$response->send();
router 路由解析模块
	Route.php 		分析URL并通过Dispatch类检测合法性
	Dispatch.php 	通过Route分析的结果调用请求的控制器的方法 call_user_func_array()
http http交互模块
	Request.php 	预处理http传递的参数 get post cookie files
	Response.php 	响应http 头信息 状态码 数据
	Cookie.php 		cookie操作
validate
	Filter.php 		过滤数据 比如文件名的合法性
	XssFilter.php 	XSS过滤
	Verify.php   	验证，邮箱 url 手机号 字符长度等
database
	Database.php 	数据库 驱动在配置文件中设置
	ORM.php 		提供ORM操作的api
session
	Session.php 	session操作 API: start get set del destroy ...
view
	template/Template 模版解析引擎
cache
	memory DB 未封装
config 
	Config.php 配置
utils 工具类
	目前的 图片 验证码 文件系统 都可以用 上传还没有写好
