<?php
namespace framework\http;
class Response{
	protected $raws=[];
	protected $body = '';
	public function setBody($context=''){
		$this->body = $context;
	}
	public function getBody(){
		return $this->body;
	}
	public function addBody($context=''){
		$this->body.=$context;
	}
	public function getRaws(){
		return $this->raws;
	}
	public function addRaws($raw=[]){
		$this->raws[] = $raw;
	}
	public function setHeader($code=0){
		return 'HTTP/1.1 '.$code.' '.Http::getHttpStatus($code);
	}

	public function send(){
        echo $this->body;
    }
}