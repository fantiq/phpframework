<?php
namespace framework\session\store;
use App;
class SessionFile extends Store{
	private   $conn   = null;
	protected $host   = '';
	protected $port   = 3306;
	protected $user   = '';
	protected $passwd = '';
	protected $dbname = '';
	protected $table  ='';
	/**
	 * 初始化参数
	 * @param [array] $config [配置数组]
	 */
	public function __construct($config=[]){
		ini_set('session.use_cookies', '1');
		session_start();
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
		return null;
	}
	/**
	 * 获取session内容
	 * @param  [type] $sid [session id]
	 * @return array 	session数据
	 */
	public function fetch($sid=''){
		return $_SESSION;
	}
	/**
	 * 更新数据到session记录
	 * @param  string  $sid        [session id]
	 * @param  integer $expireTime [过期时间]
	 * @param  integer $lastActive [最后活跃时间]
	 * @param  [type]  $data       [session数据]
	 * @return 
	 */
	public function update($sid='',$expireTime=0,$lastActive=0,$data=[]){
		$_SESSION = $data;
	}
	/**
	 * 清除过期的session记录
	 * @return [type] [description]
	 */
	// public function clean(){}
	/**
	 * 删除当前用户的session记录
	 * @param  string $sid [session id]
	 * @return [type]      [description]
	 */
	public function destroy($sid=''){
		session_unset();
		session_destroy();
	}
	/**
	 * 检测用户是否在线
	 * @param  integer $userId [用户id]
	 * @return boolean
	 */
	public function isOnline($userId=0){
		return null;
	}
	/**
	 * 统计在线用户总数
	 * @return 
	 */
	public function countOnline(){
		return null;
	}
	// private function exec($sql='',$count=false){}
	// protected function connect(){}
	// public function __destruct(){}
}