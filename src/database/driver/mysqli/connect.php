<?php
namespace framework\database\driver\mysqli;
use framework\database\DatabaseException;
use mysqli;
class connect extends query{
	protected $dbh = null;
	protected $prefix = '';
	public function __construct($config=[]){
		$config['port'] = $config['port']==''?3306:$config['port'];
		$this->dbh = new mysqli($config['host'],$config['user'],$config['pass'],$config['dbname'],$config['port']);
		if(!$this->dbh){
			throw new DatabaseException('mysqli connect errors!', 1);
		}
		$this->prefix = $config['prefix'];
	}
}