<?php
namespace framework\config;
class Config{
	protected static $configs = [];
	/**
	 * 初始化配置文件
	 * 传递的配置文件路径为空的话
	 * 加载默认路径的配置文件
	 * @param string $conf 配置文件路径
	 */
	public function __construct($conf=''){
		if($conf==''){
			$conf = APP_PATH.'/config/configs.php';
		}
		if(self::$configs==[]){
			self::$configs = include $conf;
		}
	}
	public function get($key='',$def=null){
		if(empty($key)){
			return self::$configs;
		}
		if(strpos($key, '.')!==false){
			$key = explode('.', $key, 2);
			return isset(self::$configs[$key[0]][$key[1]])?self::$configs[$key[0]][$key[1]]:$def;
		}else{
			return isset(self::$configs[$key])?self::$configs[$key]:$def;
		}
	}
	public function set($key=null,$val=null){
		$key = explode('.', $key, 2);
		if(isset(self::$configs[$key[0]][$key[1]])&&$val!==null){
			self::$configs[$key[0]][$key[1]] = $val;
			return true;
		}
		return false;
	}
}