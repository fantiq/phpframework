<?php
namespace framework\session\store;
class SessionRedis extends Store{
	protected $userTag = 'user_id_';
	protected static $store = null;
	/**
	 * 初始化参数
	 * @param [array] $config [配置数组]
	 */
	public function __construct($config=[]){
		if(self::$store==null){
			self::$store = App::getComponent('cache')->getRedis();
		}
		self::$store->connect($config['host'],$config['port']);
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
		self::$store->set($sid,[],$expireTime);
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
		self::$store->getset($sid,$data);
		if($userId>0){
			self::$store->getset($this->userTag.$userId,$lastActive);
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
		self::$store->del($sid);
		if($uid>0){
			self::$store->del($this->userTag.$uid);
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
	}
	// public function __destruct(){}
}