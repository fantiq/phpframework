<?php
namespace framework\router;
use Exception;
class RouterException extends Exception{
	protected $tag = '';
	// 
	public function __construct($msg='',$code=0,$tag=''){
		$this->message = $msg;
		$this->code = $code;
		$this->tag = $tag;
	}
}