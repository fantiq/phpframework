<?php
namespace framework\base;
use App;
class Controller extends Base{
	private $_view      = null;
	protected $verify = null;
	public function __construct(){
		$this->verify = $this->validate->getVerify([$this->request]);
		$this->_view = App::getComponent('view')->getInstance();
	}
	protected function getView(){
		return $this->_view;
	}
	public function cache($expire=0){
		$this->_view->cache($expire);
	}
	public function assign($key=null,$val=null){
		$this->_view->assign($key,$val);
	}
	public function display($path=''){
		$this->response->setBody($this->_view->display($path));
	}
	public function render($path='',$data=[]){
		if($data!=[]){
			foreach($data as $key=>$val){
				if($key!=null){
					$this->_view->assign($key,$val);
				}
			}
		}
		// $this->response->setBody($this->_view->display($path));
		return $this->_view->display($path);
	}
	public function renderAjax($data=[]){
		return json_encode($data);
	}
	public function forward($m=null,$c='',$a='',$args=[]){
		$this->redirect();
	}
	public function redirect($url=''){
		header('Location:'.$url);
		exit();
	}
	
}