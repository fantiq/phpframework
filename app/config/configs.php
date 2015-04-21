<?php
/**
 * 每个配置的选项可以拆分成小的数组 存储在文件中
 * 通过include 加载模块的配置文件
 */
return [
	// 全局的应用程序配置项
	'application'=>[
		'id'=>'app',												// 应用程序的id，项目的命名空间会用到
		'timezone'=>'RPC', 											// 设置时区
		'hooks'=>['file'=>'config/hooks.php','class'=>'Hooks'], 	// 指定钩子程序的位置
	],
	// 路由配置
	'router'=>[
	# 0 自动识别url
	# 1 ?m=Admin&c=Access&a=login&arg1=1....
	# 2 Admin/Access/login/arg1/arg2...
	# 3 ?r=Admin/Access/login/arg1/arg2...
	# 4 
		'urlmode'=>0,
		'defaultModule'=>'Index',			// 默认模型
		'defaultController'=>'Index',		// 默认控制器
		'defaultAction'=>'index',			// 默认方法
		'modules'=>['Index','Blog','Flow','Project','User','Knowlegde',],	// 存在的模块名
		'regex'=>[							// 正则匹配url规则
			'pattern'=>'Index',
		],
	],
	// 数据库配置
	'database'=>[
		'dsn'=>'pdo-mysql://root@127.0.0.1/phpframework',
		// 'dsn'=>'mysql://root@127.0.0.1/phpframework', 	// dsn形式
		'scheme'=>'pdo-mysql',					// 数据库类型（pdo类型的要以 pdo-模型 的形式指定）
		'host'=>'127.0.0.1',					// 地址
		'port'=>3306,							// 端口
		'dbname'=>'test',						// 数据库名称
		'user'=>'root',							// 帐号
		'passwd'=>'321321',						// 密码
		'charset'=>'utf8',						// 数据表编码
		'prefix'=>''							// 数据表前缀
	],
	// 内存缓存
	'cache'=>[
		'dsn'=>'memcache://127.0.0.1:11211', 	// dsn字符串形式定义
		'scheme'=>'memcache',					// 缓存类型
		'host'=>'127.0.0.1',					// 地址
		'port'=>11211,							// 端口
	],
	// session
	'session'=>[
		'auto_start'=>false,	// 自动加载
		'passwd'=>'321321',	// 连接store的密码
		'dsn'=>'pdo-mysql://root:@127.0.0.1:3306/phpframework/session',
		// 'dsn'=>'memcache://127.0.0.1:11211', 	// 字符串形式
		'scheme'=>'pdo-mysql', 					// 存储session数据库的模型
		'host'=>'127.0.0.1',					// 地址
		'port'=>3306,							// 端口号
		'user'=>'root',							// 用户名
		'dbname'=>'phpframework',				// 存储session的数据库名称
		'tbname'=>'session',				// 存储session的数据表名称
		'charset'=>'utf8',						// 编码方式
		'prefix'=>'',							// 表前缀
		'sess_name'=>'__SESSIONID__',			
		'sess_expire'=>3600*24,		// 默认session过期时间
		'alive_time'=>3600,			// 用户活跃时间间隔 这个时间内没有任何操作视为下线
		'cookie_path'=>'/',			// cookie 路径
		'cookie_domain'=>'',		// cookie 域名
	],
	// 模版配置
	'view'=>[
		'drive'=>'template',			// 模板引擎
		'skin'=>'default',				// 默认皮肤
		'tpl_ext'=>'php',				// 模版文件后缀
		'form_hash_name'=>'__hash__',	// 表单hash字段名
		'form_hash_keys'=>'fantasy',	// 表单hash的key
	],
	/////////////
	// 加载用户数据 //
	/////////////	
	'datas'=>include 'datas.php',
	'alias'=>[],
];