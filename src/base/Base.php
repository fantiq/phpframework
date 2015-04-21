<?php
namespace framework\base;
use App;
class Base{
	protected static $http=null;
	protected static $router = null;
	protected static $session = null;
	protected static $config = null;
	protected static $validate = null;
	protected static $database = null;
	protected static $utils = null;
        protected  static $debug = null;
        public function __get($name=''){
		$func = 'get'.ucfirst($name);
		if(method_exists($this, $func)){
			return $this->$func();
		}
		return null;
	}
	public function model($model='',$args=[]){
		$model = App::$id.'\models\\'.ucfirst($model).'Model';
		return App::getContainer()->singleton($model,$args);
	}
	public function action(){}
	/**
	 * 获取http组件
	 * @return [type] [description]
	 */
	protected function getHttp(){
		if(self::$http==null){
			self::$http = App::getComponent('http');
		}
		return self::$http;
	}
        /**
         * 获取debug组件
         * @return type
         */
        public function getDebug(){
            if(self::$debug==null)
                self::$debug = App::getComponent('debug');
            return self::$debug;
        }
	/**
	 * 获取http中的请求数据对象
	 * 提供htttp input data的预处理以及访问 修改
	 * @return [type] [description]
	 */
	public function getRequest(){
		if(self::$http==null){
			$this->getHttp();
		}
		return self::$http->getHttpRequest();
	}
	/**
	 * 发送http返回数据的对象
	 * @return [type] [description]
	 */
	public function getResponse(){
		if(self::$http==null){
			$this->getHttp();
		}
		return self::$http->getHttpResponse();
	}
	/**
	 * 请求 响应中的cookie数据
	 * @return [type] [description]
	 */
	public function getCookie(){
		if(self::$http==null){
			$this->getHttp();
		}
		return self::$http->getHttpCookie();
	}
	/**
	 * 获取路由组件
	 * @return [type] [description]
	 */
	protected function getRouter(){
		if(self::$router==null){
			self::$router = App::getComponent('router');
		}
		return self::$router;
	}
	/**
	 * 获取url解释器
	 * @return [type] [description]
	 */
	public function getRoute(){
		if(self::$router==null){
			$this->getRouter();
		}
		return self::$router->getRoute();
	}
	/**
	 * 获取指派器 可以获取 访问的 module controller action
	 * @return [type] [description]
	 */
	public function getDispatch(){
		if(self::$router==null){
			$this->getRouter();
		}
		return self::$router->getDispatch();
	}
	/**
	 * 数据验证过滤组件
	 * @return [type] [description]
	 */
	protected function getValidate(){
		if(self::$validate==null){
			self::$validate = App::getComponent('validate');
		}
		return self::$validate;
	}
	/**
	 * 数据验证
	 * @return [type] [description]
	 */
	public function getVerify(){
		if(self::$validate==null){
			$this->getValidate();
		}
		return self::$validate->getVerify();
	}
	/**
	 * 数据过滤
	 * @return [type] [description]
	 */
	public function getFilter(){
		if(self::$validate==null){
			$this->getValidate();
		}
		return self::$validate->getFilter();
	}
	/**
	 * 获取配置组件
	 * @return [type] [description]
	 */
	public function getConfig(){
		if(self::$config==null){
			self::$config = App::getComponent('config');
		}
		return self::$config;
	}
	/**
	 * 获取session组件
	 * 使用session的时候在没有设置自动启动 auto_start
	 * 的时候需要你自行调用start启动
	 * @return [type] [description]
	 */
	public function getSession(){
		if(self::$session==null){
			self::$session = App::getComponent('session',[$this->getRequest()]);
		}
		return self::$session;
	}
	/**
	 * 获取database组件 并使用
	 * @return [type] [description]
	 */
	public function getDb(){
		if(self::$database==null){
			self::$database = App::getComponent('database')->getInstance();
		}
		return self::$database;
	}
	public function getUtils(){
		if(self::$utils==null){
			self::$utils = App::getComponent('utils');
		}
		return self::$utils;
	}
}