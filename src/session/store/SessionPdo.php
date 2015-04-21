<?php
namespace framework\session\store;
use App;
use PDO;
class SessionPdo extends Store{
	private   $conn   = null;
	protected $table  = '';
	protected $config = [];
	protected $dbhand = null; // PDO 操作句柄
	/**
	 * 初始化参数
	 * @param [array] $config [配置数组]
	 */
	public function __construct($config=[]){
		// 
		$this->table  = $config['tbname'];
		$config['port'] = $config['port']==''?3306:$config['port'];
		$config['charset'] = $config['charset']==''?'utf8':str_replace('-', '', $config['charset']);
		$this->config = $config;
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
	public function update($sid='',$userId=0,$lastActive=0,$data=[]){
		mt_rand(0,99)==mt_rand(0,99)&&$this->clean(); // Session GC
		$sql = 'UPDATE '.$this->table.' SET data=\''.serialize($data).'\',user_id='.(int)$userId.',last_active='.(int)$lastActive.' WHERE sid=\''.$sid.'\'';
		return $this->exec($sql);
	}
	/**
	 * 清除过期的session记录
	 * @return [type] [description]
	 */
	public function clean(){
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
		if($stmt = $this->dbhand->query($sql)){
			if(strpos($sql, 'SELECT')!==false){
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				return $stmt->fetch();
			}
			return $stmt;
		}
		exit($this->dbhand->errorCode().':'.$this->dbhand->errorInfo());
	}
	/**
	 * 数据库连接
	 * @return [type] [description]
	 */
	protected function connect(){
		$dsn = strtolower($this->config['scheme']).'://host='.$this->config['host'].';port='.$this->port.';dbname='.$this->config['dbname'].';charset='.$this->config['charset'].';';
		$this->dbhand = new PDO($dsn,$this->config['user'],$this->config['passwd']);
		if(!$this->dbhand){
			exit('pdo数据库连接失败');
		};
	}
	public function __destruct(){
		$this->conn = null;
	}
}