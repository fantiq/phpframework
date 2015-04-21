<?php
use framework\base\Base;
class Hooks extends Base{
	public function __construct(){
		$this->preSystem();
	}
	/**
	 * 框架开始执行之前 初始化之前
	 * @return [type] [description]
	 */
	public function preSystem(){}
	/**
	 * 路由开始解析之前
	 * @return [type] [description]
	 */
	public function preRoute(){
		// echo 'hello';
	}
	/**
	 * 路由解析之后 调用用户控制器之前
	 * @return [type] [description]
	 */
	public function preController(){
		// echo "你请求的控制器是：".$this->dispatch->getControllerName()."<br>";
		// echo "你请求的控制器方法是：".$this->dispatch->getActionName()."<br>";
	}
	/**
	 * 发送内容到用户浏览器之前
	 * @return [type] [description]
	 */
	public function preResponse(){}
	/**
	 * 框架结束
	 * @return [type] [description]
	 */
	public function endSystem(){}
}