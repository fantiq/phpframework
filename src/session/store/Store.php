<?php
namespace framework\session\store;
abstract class Store{
	abstract public function __construct();
	abstract public function fetch($sid);
	abstract public function create($sid,$userId,$expireTime,$lastActive);
	abstract public function update($sid='',$userId=0,$lastActive=0,$data=[]);
	abstract public function destroy($sid);
	abstract public function isOnline($userId);
	abstract public function countOnline();
}