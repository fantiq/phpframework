<?php
namespace framework\base;
use ReflectionClass;
class Container{
	protected static $singletonClass = [];
	protected static $instance=null;
	/**
	 * 实例化当前类
	 * @return 
	 */
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance = new self;
		}
		return self::$instance;
	}
	/**
	 * IoC思想的 实例化 类
	 * @param  string $className 类名
	 * @param  [type] $args      参数
	 * @return 
	 */
	public function instance($className='',$args=[]){
		$ref = new ReflectionClass($className);
		return $ref->newInstanceArgs($args);
	}
	/**
	 * 单例class
	 * @param  string $className 类名
	 * @param  [type] $args      参数
	 * @return 
	 */
	public function singleton($className='',$args=[]){
		if(!isset(self::$singletonClass[$className])){
			self::$singletonClass[$className] = $this->instance($className,$args);
		}
		return self::$singletonClass[$className];
	}
	public function reject(){}
}