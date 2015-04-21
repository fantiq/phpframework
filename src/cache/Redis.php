<?php
namespace framework\cache;
class Redis{
	protected static $instance = null;
	public function __construct($config=[]){
		if(self::$instance == null){
			self::$instance = new Redis;
		}
		if(!self::$instance->connect($config['host'],$config['port'])){
			throw new CacheException('驱动'.$config['scheme'].'连接失败!!!', 1);
		}
	}
	public function getInstance(){}
}