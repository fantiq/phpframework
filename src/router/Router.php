<?php
namespace framework\router;
use App;
/**
 * @author fantiq <fantiq@163.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2015 
 * @link [url] [description]
 */
class Router{
	protected static $route    = null;
	protected static $dispatch = null;
	public function __construct(){
	}
	public function getRoute($request=null){
		if(self::$route==null){
			$args = [$request,$this->getDispatch()];
			self::$route = App::$container->singleton(__NAMESPACE__.'\Route',$args);
		}
		return self::$route;
	}
	public function getDispatch(){
		if(self::$dispatch==null){
			self::$dispatch = App::$container->singleton(__NAMESPACE__.'\Dispatch');
		}
		return self::$dispatch;
	}
}