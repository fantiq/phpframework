<?php
namespace framework\router;
use App;
/**
 * @author fantiq <fantiq@163.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2015 
 * @link [url] [description]
 *
 * 路由解析URL 通过url分析出来请求中的 module controller action args
 * 这些信息，并通过Dispatch检测这些参数的合法性
 * 依赖 Dispatch Request 类
 */
class Route{
	protected $request  = null;
	protected $segments = '';
	protected $dispatch = null;
	protected static $config = [];
	public function __construct($httpRequest=null,$dispatch=null){
		$this->request  = $httpRequest;
		$this->dispatch = $dispatch;
		if(self::$config==[]){
			self::$config = App::$config->get('router');
		}
	}
	/**
	 * 解析URL请求
	 * 
	 * 分析出 请求url中的module controller action args
	 * 并依赖Dispatch检测请求参数的合法性
	 * @return null
	 */
	public function resolve(){
		$this->getQueryString(); // 获取url请求中的有用信息
		if(trim($this->segments,'?')==''){
			$this->defaultRouter(); // 空请求调用默认路由
		}else{
			// 根据配置使用不同的方式解析url请求字符串
			switch ($this->config('urlmode' ,0)) {
				case 0:
					$this->autoParseUrl();
					break;
				case 1:
					$this->queryParseUrl();
					break;
				case 2:	
					$this->pathsParseUrl();
					break;
				case 3:
					$this->segments = $this->request->get('r','');
					$this->pathsParseUrl();
					break;
				default:
					# code...
					break;
			}
		}
	}
	/**
	 * 自动解析判断url
	 * @return 
	 */
	protected function autoParseUrl(){
		if($this->segments[0]=='?'){
			if($seg = $this->request->get('r')){
				$this->segments = $seg;
				$this->pathsParseUrl();
			}else{
				$this->queryParseUrl();
			}
		}else{
			$this->pathsParseUrl();
		}
	}
	/**
	 * 以QueryString 的形式解析url
	 * ?m=modelName&c=controllerName&a=actionName&args=1&......
	 * 
	 * @return 
	 */
	protected function queryParseUrl(){
		if($m = $this->request->get('m')){
			$this->dispatch->handleModule($m);
		}
		$c = $this->request->get('c',$this->config('defaultController','Index'));
		$a = $this->request->get('a',$this->config('defaultAction','index'));
		if(!$this->dispatch->handleController($c)||!$this->dispatch->handleAction($a)){
			throw new RouterException("请求的文件不存在", 404, 'http');
		}
	}
	/**
	 * 以PathInfo的形式解析url
	 * /modelName/controllerName/actionName/arg1/arg2/...
	 * 
	 * @return 
	 */
	protected function pathsParseUrl(){
		if($pos = strpos($this->segments, '?')){
			$this->segments = substr($this->segments, 0, $pos);
		}
		$parts = explode('/', $this->segments);
		if($this->dispatch->handleModule($parts[0])){
			array_shift($parts);
		}
		$c = isset($parts[0])?$parts[0]:$this->config('defaultController','Index');
		$a = isset($parts[1])?$parts[1]:$this->config('defaultAction','index');
		if(!$this->dispatch->handleController($c)||!$this->dispatch->handleAction($a)){
			throw new RouterException("控制器{$c} <br> 方法{$a}", 404, 'http');
		}
		$this->dispatch->setArgs(array_slice($parts, 2));
	}
	protected function regexParseUrl(){}
	/**
	 * 默认的路由 Index/index
	 * 可以在配置文件的router模块配置
	 * @return 
	 */
	protected function defaultRouter(){
		$this->dispatch->setModuleName($this->config('defaultModule',null));
		$c = $this->config('defaultController','Index');
		$a = $this->config('defaultAction','index');
		if(!$this->dispatch->handleController($c)||!$this->dispatch->handleAction($a)){
			throw new RouterException("文件未找到--!", 404, 'http');
		}
	}
	/**
	 * 对url进行解析，解析出url最后的参数部分的字符串
	 *
	 * 即使请求的文件错写成 abc.php  服务器rewrite到正确的index.php
	 * 这种情况也是能正常解析出来的
	 * @return 
	 */
	protected function getQueryString(){
		$file = $this->request->getServer('SCRIPT_NAME');
		$uri  = $this->request->getServer('REQUEST_URI');
		$this->segments = strpos($uri, $file)===false?dirname($file):$file;
		$this->segments = str_replace($this->segments, '', $uri);
		if(($pos = strpos($this->segments, '.php'))!==false){
			$this->segments = substr($this->segments, $pos+4);
		}
		$this->segments = trim($this->segments, '/');
	}
	/**
	 * 获取配置项
	 * @param  [type] $key 配置项的键名
	 * @param  [type] $def 不存在时候返回的默认值
	 * @return string
	 */
	public function config($key=null,$def=null){
		return isset(self::$config[$key])?self::$config[$key]:$def;
	}
}