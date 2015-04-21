<?php
namespace framework\session\store;
use App;
class SessionMysql extends Store{
	private   $conn   =null;
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
		// 
		$this->host   = $config['host'];
		$this->user   = $config['user'];
		$this->passwd = $config['passwd'];
		$this->port   = $config['port'];
		$this->dbname = $config['dbname'];
		$this->table  = $config['tbname'];
		// 关闭php系统session
		ini_set('session.use_cookies', '0');
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
		$data = addslashes(serialize([]));
		$sql = 'INSERT INTO '.$this->table.'(sid,user_id,expire_time,last_active,data) VALUES(\''.
			(string)$sid.'\','.
			(int)$userId.','.
			(int)$expireTime.','.
			(int)$lastActive.',\''.
			addslashes(serialize([])).
			'\')';
		return $this->exec($sql);
	}
	/**
	 * 获取session内容
	 * @param  [type] $sid [session id]
	 * @return 
	 */
	public function fetch($sid=''){
		$sql = 'SELECT * FROM '.$this->table.' WHERE sid=\''.$sid.'\' AND expire_time>'.App::$time;
		if(!($data = $this->exec($sql))) return false;
		return unserialize($data['data']);
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
		// 清除函数 按一定的几率执行 1% 的概率
		mt_rand(0,99)==mt_rand(0,99)&&$this->clean();
		$sql = 'UPDATE '.$this->table.' SET data=\''.serialize($data).'\',expire_time='.(int)$expireTime.',last_active='.(int)$lastActive.' WHERE sid=\''.$sid.'\'';
		return $this->exec($sql);
	}
	/**
	 * 清除过期的session记录
	 * @return [type] [description]
	 */
	private function clean(){
		$sql = 'DELETE FROM '.$this->table.' WHERE expire_time<'.App::$time;
		return $this->exec($sql);
	}
	/**
	 * 删除当前用户的session记录
	 * @param  string $sid [session id]
	 * @return [type]      [description]
	 */
	public function destroy($sid=''){
		$sql = 'DELETE FROM '.$this->table.' WHERE sid=\''.$sid.'\'';
		return $this->exec($sql);
	}
	/**
	 * 检测用户是否在线
	 * @param  integer $userId [用户id]
	 * @return boolean
	 */
	public function isOnline($userId=0){
		$sql='SELECT COUNT(user_id) FROM '.$this->table.
		' WHERE user_id='.$userId.' AND expire_time>'.App::$time.
		' AND last_active>'.App::$time;
		return $this->exec($sql,true);
	}
	/**
	 * 统计在线用户总数
	 * @return 
	 */
	public function countOnline(){
		$sql='SELECT COUNT(*) FROM '.$this->table.
		' WHERE expire_time>'.App::$time.
		' AND last_active>'.App::$time;
		return $this->exec($sql,true);
	}
	/**
	 * 执行sql
	 * @param  string  $sql   [description]
	 * @param  boolean $count [description]
	 * @return [type]         [description]
	 */
	private function exec($sql='',$count=false){
		$this->connect();
		$res = mysql_query($sql);
		if($res==false){
			echo mysql_errno().':'.mysql_error();
			return false;
		}
		return $count?mysql_num_rows($res):mysql_fetch_assoc($res);
	}
	/**
	 * 数据库连接
	 * @return [type] [description]
	 */
	protected function connect(){
		$this->conn = mysql_connect($this->host,$this->user,$this->passwd) or exit('fail to connect your mysql!');
		mysql_select_db($this->dbname);
	}
	public function __destruct(){
		@mysql_close($this->conn);
	}
}