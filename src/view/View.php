<?php
namespace framework\view;
use App;
class View{
	protected $engine = null;
	public function __construct(){
		$driver = App::$config->get('view.driver','template\Template');
		$driver = strpos($driver, '\\')!==false?$driver:$driver.'\\'.ucfirst($driver);
		$driver = __NAMESPACE__.'\\'.trim($driver,'\\');
		$this->engine = App::getContainer()->singleton($driver);
	}
	public function getInstance(){
		return $this->engine;
	}
}