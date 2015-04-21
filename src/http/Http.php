<?php
namespace framework\http;
use App;
/**
 * @author fantiq <fantiq.163.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2015
 * @link [url] [description]
 *
 * http组件的入口类，通过这个类来调用http的请求 reuest
 * 以及响应 respons 和cookie 相关的数据
 */
class Http{
	/**
	 * 静态变量存储http request
	 * @var null
	 */
	protected static $httpRequest = null;
	/**
	 * 静态变量存储http response
	 * @var null
	 */
	protected static $httpResponse = null;
	/**
	 * 静态变量存储cookie
	 * @var null
	 */
	protected static $httpCookie = null;
	/**
	 * 获取request对象
	 * @return Request
	 */
	protected static $mime = [];
	protected static $status = [];
	public function getHttpRequest(){
		if(self::$httpRequest==null){
			$args[] = $this->getHttpCookie();
			self::$httpRequest = App::getContainer()->singleton(__NAMESPACE__.'\Request',$args);
		}
		return self::$httpRequest;
	}
	/**
	 * 获取cookie对象
	 * @return Cookie
	 */
	public function getHttpResponse(){
		if(self::$httpResponse==null){
			self::$httpResponse = App::getContainer()->singleton(__NAMESPACE__.'\Response');
		}
		return self::$httpResponse;
	}
	/**
	 * 获取cookie对象
	 * @return Cookie
	 */
	public function getHttpCookie(){
		if(self::$httpCookie==null){
			self::$httpCookie = App::getContainer()->singleton(__NAMESPACE__.'\Cookie');
		}
		return self::$httpCookie;
	}
	public static function getMime($ext=''){
		if(self::$mime==[]){
			self::$mime = include __DIR__.DS.'mime.php';
		}
		return isset(self::$mime[$ext])?self::$mime[$ext]:null;
	}
	public static function getExt($mime=''){
		if(self::$mime==[]){
			self::$mime = include __DIR__.DS.'mime.php';
			self::$mime = array_flip(self::$mime);
		}
		return isset(self::$mime[$mime])?self::$mime[$mime]:null;
	}
	public static function getHttpStatus($code=0){
		if(self::$status==[]){
			self::$status = include __DIR__.DS.'status.php';
			self::$status = array_flip(self::$status);
		}
		return isset(self::$status[$code])?self::$status[$code]:null;
	}
}