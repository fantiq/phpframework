<?php
namespace framework\http;
/**
 * @author fantiq <fantiq@163.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2015
 * @link [url] [description]
 *
 * 处理http请求过来的数据
 * http请求有很多的用户输入数据
 * 这些数据要经过严格的过滤处理
 * 不能相信用户的数据，初始化的
 * 时候已经做了初步的过滤，其他
 * 的用户可以在controller里用verify
 * 过滤数据
 */
class Request{
	/**
	 * 存储http中get形式传递的值
	 * @var [type]
	 */
	protected $get    = [];
	/**
	 * 存储http请求中通过post形式传递的值
	 * @var [type]
	 */
	protected $post   = [];
	/**
	 * 上传文件的文件信息
	 * @var [type]
	 */
	protected $files  = [];
	/**
	 * http传递的cookie数据
	 * @var null
	 */
	protected $cookie = null;
	/**
	 * 用户的ip地址
	 * @var null
	 */
	protected $ip        = null;
	/**
	 * 主机的域名 domain
	 * @var null
	 */
	protected $hostName  = null;
	/**
	 * http的请求方法 GET POST PUT DELETE ... 8种
	 * @var null
	 */
	protected $reqMethod = null;
	/**
	 * 初步过滤用户通过http提交的数据
	 * 并销毁全局数组 消除不安全因素
	 * @param Cookie $cookie cookie对象
	 */
	public function __construct($cookie){
		$pattern = '/^[a-z0-1-_\.]+$/i';
		foreach(array('get','post','cookie','files') as $data){
			$inputData='_'.strtoupper($data);
			foreach($GLOBALS[$inputData] as $key=>$val){
				if(preg_match($pattern, $key)){
					$tmp = &$this->$data;
					$tmp[$key] = $val;
				}
			}
			unset($GLOBALS[$inputData]);
		}
		$cookie->init($this->cookie);
		$this->cookie = $cookie;
	}
	/**
	 * 获取HTTP请求的方式
	 * 返回大写字符串
	 * @param  integer $ref 是否需要重新获取
	 * @return string
	 */
	public function getMethod($ref=0){
		if($ref||$this->reqMethod==null||!$this->isAjaxRequest()){
			$this->reqMethod = $this->getServer('REQUEST_METHOD');
		}
		return $this->reqMethod;
	}
	/**
	 * 检测是否是AJAX请求
	 * @return boolean 
	 */
	public function isAjaxRequest(){
		if(strcasecmp($this->getServer('HTTP_X_REQUESTED_WITH'),'xmlhttprequest')==0){
			$this->reqMethod='AJAX';
			return true;
		}
		return false;
	}
	/**
	 * 获取访问者的IP
	 * @param  integer $ref 是否重新获取
	 * @return string 
	 */
	public function getClientIp($ref=0){
		if($ref||$this->ip==null){
			// 设置ip
			if($tmp = $this->getServer('HTTP_X_FORWARDED_FOR')){
				foreach(explode(',', $tmp) as $val){
					if(strtolower($val) !== 'unkonwn'){
						$ip = $val;
						break;
					}
				}
			}else{
				$tmp = $this->getServer('REMOTE_ADDR');
				$ip = $tmp!==null?$tmp:'0.0.0.0';
			}
			$tmp = strpos(':', $ip)===false?FILTER_FLAG_IPV4:FILTER_FLAG_IPV6;
			$this->ip = filter_var($ip,FILTER_VALIDATE_IP,array('flags'=>$tmp))?$ip:'0.0.0.0';
		}
		return $this->ip;
	}
	public function getHostName($ref=0){
		if($ref||self::$hostName==null){
			self::$hostName = $this->getServer('HTTP_HOST');
		}
		return self::$hostName;
	}
	/**
	 * $_SERVER数组数据
	 * @param  [type] $key 数组的key
	 * @return [type]      [description]
	 */
	public function getServer($key=null){
		if($key==null) return $_SERVER;
		if(isset($_SERVER[$key])||(($key = strtoupper($key))&&isset($_SERVER[$key]))){
			return $_SERVER[$key];
		}
		return null;
	}
	/**
	 * $_GET
	 * 对get请求的数据进行过滤检测
	 * 最后会销毁这个数组
	 * 也就是在controller model中是不能用$_GET的
	 * @param  [type] $key [key]
	 * @param  [type] $def [默认值]
	 * @return [type]      [description]
	 */
	public function get($key=null,$def=null){
		if($key==null) return $this->get;
		if(isset($this->get[$key])){
			return $this->get[$key];
		}
		return $def;
	}
	/**
	 * $_POST
	 * 过滤POST的数据，过滤的是key部分
	 * value部分用户用filter::XSS自行过滤
	 * @param  [type] $key [key]
	 * @param  [type] $def [默认值]
	 * @return [type]      [description]
	 */
	public function post($key=null,$def=null){
		if($key==null) return $this->post;
		if(isset($this->post[$key])){
			return $this->post[$key];
		}
		return $def;
	}
	/**
	 * 设置GET数组中的某一项的值
	 * @param string $key GET数组中的项
	 * @param string $val 修改成的值
	 */
	public function setGet($key=null,$val=null){
		if(isset($this->get[$key])){
			$this->get[$key] = $val;
			return true;
		}
		return false;
	}
	/**
	 * 设置POST数组中的某一项的值
	 * @param string $key POST数组中的项
	 * @param string $val 修改成的值
	 */
	public function setPost($key=null,$val=null){
		if(isset($this->post[$key])){
			$this->post[$key] = $val;
			return true;
		}
		return false;
	}
	/**
	 * 获取cookie对象
	 * @return [type] [description]
	 */
	public function cookie($key=null,$def=''){
		return $this->cookie->get($key,$def);
	}
	/**
	 * 设置COOKIE数组中的某一项的值
	 * @param string $key COOKIE数组中的项
	 * @param string $val 修改成的值
	 */
	public function setCookie($key='',$val=''){
		return $this->cookie->set($key,$val);
	}
	/**
	 * $_FILES
	 * 对上传的文件数据检测
	 * 最后会销毁这个数组
	 * @param  [type] $key [key]
	 * @param  [type] $def [默认值]
	 * @return [type]      [description]
	 */
	public function files($key=null,$def=null){
		if($key==null) return $this->files;
		if(isset($this->files[$key])){
			return $this->files[$key];
		}
		return $def;
	}
}