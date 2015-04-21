<?php
namespace framework\http;
/**
 * @author fantiq <fantiq@163.com>
 * @version 1.0.0
 * @copyright  Copyright (c) 2015
 * @link [url] [description]
 */
class Cookie
{
	protected $expire = 0;
	protected $path   = '/';
	protected $domain = HOST_NAME;
	protected $cookie = [];
	public function init($cookie=[]){
		$this->cookie = $cookie;
	}
	/**
	 * 修改cookie中的数据
	 * @param string $name  cookie中的键
	 * @param string $value 要设置的值
	 */
	public function set($name=null,$value=null){
		if(is_array($name)){
			foreach($name as $key=>$val){
				setcookie($key,$val,$this->expire,$this->path,$this->domain);
			}
		}else{
			setcookie($name,$value,$this->expire,$this->path,$this->domain);
		}
	}
	/**
	 * 获取cookie数据
	 * @param  string $key 键
	 * @param  string $def 默认值
	 * @return mixed
	 */
	public function get($key=null,$def=null){
		if($key==null){
			return $this->cookie;
		}
		if(isset($this->cookie[$key])){
			return $this->cookie[$key];
		}
		return $def;
		
	}
	/**
	 * 删除cookie文件
	 * @return void
	 */
	public function del(){
		if(!empty($this->cookie)){
			foreach($this->cookie as $key=>$val){
				$this->rm($key);
			}
		}
	}
	/**
	 * 清空指定cookie项
	 * @param  string $key cookie项
	 * @return void
	 */
	public function rm($key=''){
		if(isset($this->cookie[$key])){
			unset($this->cookie[$key]);
			$this->expire=time()-3600;
			$this->set($key,null);
		}
	}
	/**
	 * 设置过期时间
	 * @param integer $val 过期时间戳
	 */
	public function setExpire($val=0){
		$this->expire=is_numeric($val)?$val:0;
	}
	/**
	 * 设置cookie可访问路径
	 * @param string $val 路径
	 */
	public function setPath($val='/'){
		$this->path=$val;
	}
	/**
	 * 设置cookie域
	 * @param [type] $val 域
	 */
	public function setDomain($val=null){
		if($val !== null) $this->domain=$val;
	}
}