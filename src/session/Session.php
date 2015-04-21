<?php
namespace framework\session;
use App;
class Session{
	protected $sessionId    = null;
	protected $sessionName  = '';
	protected $sessionStore = null; // 驱动器实例化对象
	protected $userId       = 0;	// 用户id
	protected $data         = [];	// session数据
	protected $config       = [];
	protected $request 		= null;
	protected $isUpdate		= false;

	/**
	 * 初始化session数据
	 * 加载session store
	 * 获取session id
	 * @return 
	 */
	public function __construct($request=null){
		$this->config = App::$config->get('session');
		$this->request = $request;
		// 加载session驱动引擎  优先dsn
		if($this->config['dsn']!=''){
			$info = parse_url($this->config['dsn']);
			$tmp  = explode('/', trim($info['path'],'/'));
			$info['dbname'] = $tmp[0];
			$info['tbname'] = isset($tmp[1])?$tmp[1]:'session';
			$info['passwd'] = $this->config['passwd'];
		}else{
			$info = $this->config;
		}
		if($info['scheme']!=''){
			if(strpos($info['scheme'], '-')){
				$drive = explode('-', $info['scheme']);
				$info['scheme'] = isset($drive[1])?$drive[1]:'';
				$drive = $drive[0];
			}else{
				$drive = $info['scheme'];
			}
			$this->sessionStore = App::getContainer()->singleton(__NAMESPACE__.'\store\Session'.ucfirst($drive),[$info]);
		}else{
			throw new SessionException("session 驱动未指定", 1);
		}
		// 获取session id
		$this->sessionName = $this->config('sess_name','__SESSIONID__');
		if($this->request->cookie($this->sessionName,null)){
			$this->sessionId = $this->request->cookie($this->sessionName);
		}elseif($this->request->get('sid',null)){
			$this->sessionId = $this->request->get('sid');
		}else{
			$this->sessionId = false;
		}
	}
	/**
	 * 初始化session对象
	 * 尝试获取session数据
	 * 不能获取就创建session记录
	 * @param integer $userId 用户id
	 * @param int 	  $expire session过期时间
	 */
	public function start($expire = null,$userId=0){
 		// 创建session数据
		if($this->sessionId!==false&&($record=$this->sessionStore->fetch($this->sessionId))){
			$this->userId = $userId;
			$this->data   = $record;
		}else{ // 获取不到数据创建session
			if($expire>0){
				$expire = App::$time+$expire;
			}elseif($this->config['sess_expire']>0){
				$expire = App::$time+$this->config['sess_expire'];
			}else{
				$expire = 0;
			}
			$this->config['sess_expire'] = $expire;
			$this->create($userId);
		}
	}
	/**
	 * 创建session记录
	 * @param  integer $userId [用户id]
	 * @return array
	 */
	private function create($userId=0){
		$this->sessionId  = $this->createNewSid();
		$this->userId     =(int)$userId;
		$expireTime = $this->config['sess_expire'];
		$lastActive = App::$time+$this->config('alive_time',3600);
		// 设置客户端cookie
		if(setcookie($this->sessionName,$this->sessionId,$expireTime,$this->config('cookie_path','/'),$this->config('cookie_domain',HOST_NAME))){
			$this->sessionStore->create($this->sessionId, $this->userId, $expireTime, $lastActive);
		}

	}
	/**
	 * 更新session记录
	 * 更行数据函数等待基本全部执行结束执行
	 * @return [type] [description]
	 */
	public function update(){
		if(!$this->isUpdate) return null;
		return $this->sessionStore->update(
			$this->sessionId,
			$this->userId,
			App::$time+$this->config('alive_time',3306),
			$this->data
		);
	}
	/**
	 * 是否需要更新数据
	 * 需要更新数据的操作调用此方法
	 * 脚本结束时会执行此对象的 update 方法
	 * @return
	 */
	private function needUpdate(){
		$this->isUpdate = true;
	}
	/**
	 * 生成session id
	 * 算法同php
	 * md5 加密 ip+time+microtime+5位随机数
	 * @return string 
	 */
	private function createNewSid(){
		return md5($this->request->getClientIp().App::$time.App::$microTime.mt_rand(10000,99999));
	}
	/**
	 * 获取session数据
	 * @param  [type] $key [key]
	 * @param  [type] $def 默认值
	 * @return [type]      [description]
	 */
	public function get($key=null,$def=null){
		$this->needUpdate(); //这里主要更新的不是数据而是更新最后活跃时间
		if($key==null){
			return $this->data;
		}elseif(isset($this->data[$key])){
			return $this->data[$key];
		}else{
			return $def;
		}
	}
	/**
	 * 设置session值
	 * @param string $key key
	 * @param string $val val
	 */
	public function set($key=null,$val=''){
		$this->needUpdate();
		if(is_array($key)){
			$this->data = array_merge($this->data,$key);
			return true;
		}elseif($key!=null){
			$this->data[$key]=$val;
			return true;
		}
		return null;
	}
	/**
	 * 删除指定的session项
	 * @param  [type] $key 键
	 * @return boolean
	 */
	public function del($key=null){
		$this->needUpdate();
		if(is_array($key)){
			foreach($key as $v){
				if(isset($this->data[$key])){
					unset($this->data[$v]);
				}
			}
		}elseif(isset($this->data[$key])){
			unset($this->data[$key]);
		}else{
			return false;
		}
	}
	/**
	 * 删除当前用户的session记录
	 * @return [type] [description]
	 */
	public function destroy(){
		if($this->sessionStore->destroy($this->sessionId)){
			return setcookie($this->sessionName,'',App::$time-3600,$this->config('cookie_path','/'),$this->config('cookie_domain',HOST));
		}
		return false;
	}
	/**
	 * 检查用户是否在线
	 * @param  integer $userId 用户id
	 * @return boolean         [description]
	 */
	public function isOnline($userId=0){
		$this->sessionStore->isOnline($userId);
	}
	/**
	 * 当前在线用户数
	 * @return [type] [description]
	 */
	public function countOnline(){
		return $this->sessionStore->countOnline();
	}
	protected function config($key=null,$def=''){
		return isset($this->config[$key])?$this->config[$key]:$def;
	}
}