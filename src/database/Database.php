<?php
namespace framework\database;
use App;
class Database{
	protected static $config = [];
	protected static $instance = null;
	public static function getInstance(){
		if(self::$instance==null){
			self::getObjByDrive();
		}
		return self::$instance;
	}
	public function getORM($table = ''){
		return App::$container->instance(__NAMESPACE__.'\ORM',[$table]);
	}
	/**
	 * 获取数据库的驱动器
	 * @return [type] [description]
	 */
	protected static function getObjByDrive(){
		if(self::$config==[]){
			self::$config = App::$config->get('database');
		}
		if(self::$config['dsn']!=''){
			$info = parse_url(self::$config['dsn']);
			$info['dbname'] = trim($info['path'],'\/');
			$info['passwd'] = self::$config['passwd'];
			$info['chatset'] = self::$config['chatset'];
			$info['prefix'] = self::$config['prefix'];
			unset($info['path']);
		}else{
			$info = self::$config;
		}
		$info['scheme'] = strtolower(trim($info['scheme']));
		if(!empty($info['scheme'])){
			$part = explode('-', $info['scheme']);
			if($part[0]=='pdo'){
				$info['scheme'] = isset($part[1])?$part[1]:'mysql';
			}
			self::$instance = App::getContainer()->singleton('framework\database\driver\\'.$part[0].'\connect',[$info]);
		}else{
			exit('Exception:指定驱动');
		}
	}
}