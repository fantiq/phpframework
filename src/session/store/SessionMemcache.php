<?php
namespace framework\session\store;
use App;
class SessionMemcache extends Store{
	protected $host   = '127.0.0.1';
	protected $port   = 11211;
	protected $userTag = 'user_id_';
	protected static $store = null;
	/**
	 * 初始化参数
	 * @param [array] $config [配置数组]
	 */
	public function __construct($config=[]){
		// 
		$this->host   = $config['host'];
		$this->port   = $config['port'];
		// 关闭php系统session
		ini_set('session.use_cookies', '0');
		if(self::$store==null){
			self::$store = App::getComponent('cache')->getMemcache();
		}
		self::$store->connect($this->host,$this->port) or exit('无法连接Memcache!!!');
	}
	/**
	 * 创建一条session记录
	 * @param  [type] $sid         [session id]
	 * @param  [type] $user_id     [用户id]
	 * @param  [type] $expire_time [过期时间]
	 * @param  [type] $last_active [最后活跃时间]
	 * @return 
	 */
	public function create($sid=0,$userId=0,$expireTime=0,$lastActive=0){
		$rt = self::$store->set($sid,[],MEMCACHE_COMPRESSED,$expireTime);
		if($userId>0){
			self::$store->set($this->userTag.$userId,$lastActive);
		}
	}
	/**
	 * 获取session内容
	 * @param  [type] $sid [session id]
	 * @return 
	 */
	public function fetch($sid=''){
		return self::$store->get($sid);
	}
	/**
	 * 更新数据到session记录
	 * @param  string  $sid        [session id]
	 * @param  integer $expireTime [过期时间]
	 * @param  integer $lastActive [最后活跃时间]
	 * @param  [type]  $data       [session数据]
	 * @return 
	 */
	public function update($sid='',$userId=0,$lastActive=0,$data=[]){
		self::$store->replace($sid,$data);
		if($userId>0){
			self::$store->replace($this->userTag.$userId,$lastActive);
		}
	}
	/**
	 * 清除过期的session记录
	 * cache db 可以设置过期时间 不用手动清除
	 * @return [type] [description]
	 */
	// protected function clean(){}
	/**
	 * 删除当前用户的session记录
	 * @param  string $sid [session id]
	 * @return [type]      [description]
	 */
	public function destroy($sid='',$uid=0){
		self::$store->delete($sid);
		if($uid>0){
			self::$store->delete($this->userTag.$uid);
		}
	}
	/**
	 * 检测用户是否在线
	 * @param  integer $userId [用户id]
	 * @return boolean
	 */
	public function isOnline($userId=0){
		return self::$store->get($this->userTag.$userId);
	}
	/**
	 * 统计在线用户总数
	 * @return 
	 */
	public function countOnline(){
		$stat = self::$store->getStats();
		return $stat['curr_items'];
	}
	// private function exec($sql='',$count=false){}
	// protected function connect(){}
	// public function __destruct(){}
}