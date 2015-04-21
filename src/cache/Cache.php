<?php
namespace framework\cache;
use Memcache;
class Cache{
	protected static $memcache=null;
	protected static $redis=null;
	protected static $config = [];
	public function __construct(){
		// if(self::$config == []){
		// 	self::$config = App::$config->get('cache');
		// 	if(isset(self::$config['dsn'])){
		// 		self::$config = parse_url(self::$config['dsn']);
		// 	}
		// }
	}
	public function getMemcache(){
		if(self::$memcache==null){
			self::$memcache = new Memcache;
		}
		return self::$memcache;
	}
	public function getRedis(){
		if(self::$redis==null){
			self::$redis = new Redis;
		}
		return self::$redis;
	}
}