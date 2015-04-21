<?php
namespace framework\router;
use App;
/**
 * @author fantiq <fantiq@163.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2015
 * @link [url] [description]
 *
 * 处理请求中的 module controller action args这四个属性
 * 存储了用户请求的这四个值
 * 对这四个值进行检测是否符合要求
 * 检测通过后会根据mvc规则运行这个class的方法并返回运行结果
 */
class Dispatch{
	protected $controller        = null;
	protected $moduleName        = null;
	protected $controllerName    = null;
	protected $actionName        = null;
	protected $argsArray = [];
	protected $baseControllerName = 'framework\base\Controller';
	protected static $moduleMaps = [];
	/**
	 * 赋值moduleMaps 为模块名的列表赋值
	 */
	public function __construct(){
		if(self::$moduleMaps==[]){
			self::$moduleMaps = App::$config->get('router.modules');
		}
	}
	/**
	 * 执行请求的方法
	 * @return mixed 返回执行的结果
	 */
	public function run(){
		return call_user_func_array([$this->controller,$this->getActionName()], $this->getArgs());
	}
	/**
	 * 调用一个请求
	 * @param  null   $m    模块名称
	 * @param  string $c    控制器名称
	 * @param  string $a    方法名称
	 * @param  array  $args 参数
	 * @return mixed       返回运行结果
	 */
	public function forward($m=null,$c='',$a='',$args=[]){
		if($m!=null) $this->handleModule($m);
		if($this->handleController($c)&&$this->handleAction($a)){
			$this->setControllerName($c);
			$this->setActionName($a);
			$this->setArgs($args);
			return $this->run();
		}
		return false;

	}
	/**
	 * 检测模块是否合法
	 * 在moduleMaps中是否声明过
	 * @param  string $m 模块名
	 * @return boolean
	 */
	public function handleModule($m=null){
		if(in_array($m = $this->tranSegment($m), self::$moduleMaps)){
			$this->setModuleName($m);
			return true;
		}
		$this->setModuleName(null);
		return false;
	}
	/**
	 * 检测控制器是否合法
	 * 控制器类是否存在以及是否继承了框架的controller基础类
	 * @param  string $c 控制器名称
	 * @return boolean
	 */
	public function handleController($c=null){
		$c = $this->tranSegment($c);
		$className = $c.'Controller';
		$path = $this->moduleName==null?'':'/'.$this->moduleName;
		$path = APP_PATH.'/controllers'.$path.'/'.$className.'.php';
		if(is_file($path)){
			$this->controller = App::getContainer()->singleton($this->getControllerNamespace().$className);
			if(!is_subclass_of($this->controller, $this->baseControllerName)){
				throw new RouterException("控制器未继承框架的Controller类");
			}
			$this->setControllerName($c);
			return true;
		}
		return false;
	}
	/**
	 * 检测请求的方法是否合法
	 * 请求方法在请求的控制器类中是否存在
	 * @param  string $a 方法名
	 * @return boolean
	 */
	public function handleAction($a=null){
		if(method_exists($this->controller, $a)){
			$this->setActionName($a);
			return true;
		}
		return false;
	}
	/**
	 * 返回控制器的命名空间
	 * 命名空间的开始单词是在configs中设置的app id
	 * @return string
	 */
	protected function getControllerNamespace(){
		$m = $this->getModuleName();
		if($m==null){
			return App::$id.'\controllers\\';
		}else{
			$m = str_replace('/', '\\', $m);
			return App::$id.'\controllers\\'.$m.'\\';
		}
	}

	/**
	 * 转义请求url中的片段
	 * 首字母大写
	 * 下划线 '_' 转义成 '/'
	 * admin 			 -> Admin
	 * admin-access 	 -> AdminAccess
	 * admin_access_user -> Admin/Access/User
	 * @param  string $module [description]
	 * @return [type]         [description]
	 */
	private function tranSegment($module=''){
		if(strpos($module, '_')){
			return str_replace(' ', '/', ucwords(str_replace('_', ' ', $module)));
		}elseif(strpos($module, '-')){
			return str_replace(' ','',ucwords(str_replace('-', ' ', $module)));
		}else{
			return ucfirst($module);
		}
	}
	/**
	 * 获取模块名称
	 * @return string
	 */
	public function getModuleName(){
		return $this->moduleName;
	}
	/**
	 * 获取控制器名称
	 * @return string
	 */
	public function getControllerName(){
		return $this->controllerName;
	}
	/**
	 * 获取方法名称
	 * @return string
	 */
	public function getActionName(){
		return $this->actionName;
	}
	/**
	 * 获取请求的参数
	 * @return array
	 */
	public function getArgs(){
		return $this->argsArray;
	}
	/**
	 * 设置模块名称
	 * @param string $m 模块名
	 */
	public function setModuleName($m=null){
		$this->moduleName = $m;
	}
	/**
	 * 设置控制器名称
	 * @param string $c 控制器名
	 */
	public function setControllerName($c=null){
		$this->controllerName = $c;
	}
	/**
	 * 设置方法名称
	 * @param string $a 方法名
	 */
	public function setActionName($a=null){
		$this->actionName = $a;
	}
	/**
	 * 设置参数
	 * @param array $args 参数
	 */
	public function setArgs($args=[]){
		$this->argsArray = $args;
	}
}