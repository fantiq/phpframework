<?php
namespace framework\validate;
use App;
/**
 * Verify 数据格式的验证返回boolean
 * Filter 过滤数据 返回过滤后的结果
 * XssFilter 过滤xss返回过滤后的结果
 */
class Validate{
	protected static $xss=null;
	protected static $filter=null;
	protected static $verify=null;
	public function getXssFilter(){
		if(!isset(self::$xss)){
			self::$xss = App::getContainer()->singleton(__NAMESPACE__.'\XssFilter');
		}
		return self::$xss;
	}
	public function getFilter(){
		if(!isset(self::$filter)){
			self::$filter = App::getContainer()->singleton(__NAMESPACE__.'\Filter');
		}
		return self::$filter;
	}
	public function getVerify($args=[]){
		if(!isset(self::$verify)){
			self::$verify = App::getContainer()->singleton(__NAMESPACE__.'\Verify', $args);
		}
		return self::$verify;
	}
}