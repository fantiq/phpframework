<?php
use framework\base\Container;
/**
 * @author fantiq <fantiq@sina.com>
 */
// define('APP_PATH', dirname(__DIR__).'/app');
// App::run();
class App{
	public static $id          = null;
	public static $config      = null;
	public static $time        = null;
	public static $microTime   = null;
	public static $container   = null;
	protected static $hook = null;
	protected static $components  = [];
	protected static $loadedFiles = [];
	/**
	 * 框架启动程序，需要在项目入口文件中调用
	 * @param  string $conf 配置文件路径 默认选择 app/config/configs.php
	 * @return 
	 */
	public static function run($conf=''){
		self::init($conf);
		$router = self::getComponent('router');
		$http   = self::getComponent('http');
		$request  = $http->getHttpRequest();
		$response = $http->getHttpResponse();
		$route = $router->getRoute($request);
		$dispatch = $router->getDispatch();
		App::$hook->preRoute();
		// 解析url
		$route->resolve();
		App::$hook->preController();
		// 调用用户的controller类
		$dispatch->run();
		// 发送内容到client
		$response->send();
	}
	/**
	 * 初始化框架
	 *
	 * 定义常量以及设置一些配置项
	 * @param  string $conf 配置文件路径
	 * @return 
	 */
	protected static function init($conf=''){
		self::$microTime = microtime(true);
		define('BASE_PATH', __DIR__);
		define('DS', DIRECTORY_SEPARATOR);
		// 请求方式 http or cli
		if(isset($_SERVER['argc'])){
			define('IS_CLI', true);
		}else{
			define('IS_CLI', false);
			$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
			define('HOST_NAME', $http_type.$_SERVER['HTTP_HOST']);
		}
		// 定义css js img 资源的路径
		define('ASSET_PATH', HOST_NAME.'/oa1024/public/');
		// 注册自动加载方法
		spl_autoload_register([__CLASS__, 'autoLoadHandler']);
		// Ioc容器
		self::$container = Container::getInstance();
		App::$config = self::getComponent('config');
		App::$id = App::$config->get('application.id');
		self::loadHooks();
		// session
		if(App::$config->get('session.auto_start',false)==true){
			App::getComponent('session')->start();
		}
		
		self::$time = (int)self::$microTime;
		// 服务器操作系统
		define('IS_WIN', stripos(PHP_OS, 'WIN')===false?false:true);
		defined('APP_PATH') or define('APP_PATH', BASE_PATH);
		// 错误处理函数
		set_error_handler([__CLASS__,'errorHandler']);
		// 异常处理函数
		set_exception_handler([__CLASS__,'exceptionHandler']);
		// 注册脚本结束调用的方法
		register_shutdown_function([__CLASS__, 'shutdown']);
	}
	protected static function loadHooks(){
            // hook
            $hook = App::$config->get('application.hooks','');
            App::import(APP_PATH.DS.$hook['file']);
            App::$hook = new $hook['class'];
            $hook = ['preSystem','preRoute','preController','preResponse','endSystem'];
            foreach($hook as $method){
                    if(!method_exists(App::$hook, $method)){
                            exit("钩子程序需要有这几个方法".print_r($hook,true).".但是系统检查到你的钩子类中缺少方法".$method);
                    }
            }
	}
	/**
	 * 加载组件
	 *
	 * 加载组件 component/Component.php 
	 * 并且以单例的形式返回实例化对象
	 * @param  string $comp 组件名称
	 * @param  [type] $args 实例化参数
	 * @return Object 		实例化组件
	 */
	public static function getComponent($comp='',$args=[]){
		if(!in_array($comp, self::$components)){
			self::$components[$comp] = self::$container->singleton('framework\\'.strtolower($comp).'\\'.ucfirst($comp),$args);
		}
		return self::$components[$comp];
	}
	public static function getContainer(){
		return self::$container;
	}
	public static function errorHandler(){}
	public static function exceptionHandler($e){
		echo $e->getMessage();
		echo '<br>'.$e->getLine();
		echo '<br>'.$e->getFile();
		// print_r($e);
	}
	/**
	 * 文件单次加载 优化的 include_once()
	 * @param  string $path 文件路径
	 * @return
	 */
	public static function import($path=''){
		if(!in_array($path, self::$loadedFiles)){
			if(is_file($path)){
				include $path;
				self::$loadedFiles[] = $path;
				return true;
			}
			exit("加载文件<b>{$path}</b>不存在！");
		}
		return false;
	}
	/**
	 * class 自动加载方法
	 * @param  string $className 类名称
	 * @return 
	 */
	protected static function autoLoadHandler($className=''){
		$className = str_replace(['\\','_'], DS, ltrim($className, '\\'));
		list($vender,$filepath) = explode('\\', $className, 2);
		if($vender=='framework'){
			$filepath=BASE_PATH.DS.$filepath.'.php';
		}elseif($vender==App::$id){
			$filepath=APP_PATH.DS.$filepath.'.php';
		}
		self::import($filepath);
	}
	public static function shutdown(){
		// session -> update
		if(in_array('session', array_keys(self::$components))){
			self::$components['session']->update();
		}
	}
}