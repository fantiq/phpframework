<?php
namespace framework\database\driver\mysql;
/**
 * @author fantiq <fantiq@163.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2015
 * @link [url] [description]
 *
 * mysql驱动 数据库的连接层
 */
class connect extends query{
	// protected $dbh = null;
	protected $prefix = '';
	public function __construct($config=[]){
		$config['port'] = $config['port']==''?3306:$config['port'];
		if(!mysql_connect($config['host'].':'.$config['port'],$config['user'],$config['passwd'])){
			exit('cannot access the db by drive mysql');
		}
		mysql_select_db($config['dbname']);
		$this->prefix = $config['prefix'];
	}
	/**
	 * 查询一条记录
	 * @param string $method 结果集数据形式
	 * @return 
	 */
	public function one($method=null){
		$res = $this->exec();
		if(is_resource($res)){
			if($method==null){
				return mysql_fetch_assoc($res);
			}elseif(strtolower($method)=='obj'){
				return mysql_fetch_object($res);
			}elseif(strtolower($method)=='num'){
				return mysql_fetch_row($res,MYSQL_NUM);
			}
			return mysql_fetch_array($res);
		}else{
			$this->error();
			// return $res;
		}
	}
	/**
	 * 查询所有记录
	 * @param string $method 结果集数据形式
	 * @return 
	 */
	public function all($method=null){
		$res = $this->exec();
		if(is_resource($res)){
			$datas = [];
			if($method==null){
				while($row = mysql_fetch_assoc($res)) $datas[] = $row;
			}elseif(strtolower($method)=='obj'){
				while($row = mysql_fetch_object($res)) $datas[] = $row;
			}elseif(strtolower($method)=='num'){
				while($row = mysql_fetch_row($res)) $datas[] = $row;
			}else{
				while($row = mysql_fetch_array($res)) $datas[] = $row;
			}
			return $datas;
		}else{
			return $res;
		}
	}
        /**
	 * 拼接出来sql语句并且进行预处理
	 * 参数绑定是通过execute参数的形式
	 * @return 
	 */
	public function exec(){
                if(empty($this->symbol)){
                    return false;
                }
		switch ($this->symbol) {
			case 'select':
				$this->sql=$this->queryString['select']['field'].
				$this->queryString['select']['from'].
				$this->queryString['select']['join'].
				$this->queryString['where'].
				$this->queryString['select']['group'].
				$this->queryString['select']['order'].
				$this->queryString['select']['limit'];
				break;
			case 'insert':
				$this->sql=$this->queryString['insert'];
				break;
			case 'update':
				$this->sql=$this->queryString['update'].$this->queryString['where'];
				break;
			case 'delete':
				$this->sql=$this->queryString['delete'].$this->queryString['where'];
				break;
			default:
				return false;
		}
		// 替换参数
		if(strpos($this->sql, '?')!==false){
			$this->sql = preg_replace_callback('/\?/', function($match=[]){
				static $i=-1;
				$i++;
				if(preg_match('/^`(.+)`$/', $this->params[$i], $match)){
					return $match[1];
				}elseif(is_string($this->params[$i])){
					return '\''.mysql_real_escape_string($this->params[$i]).'\'';
				}
				return $this->params[$i];
			}, $this->sql);
		}else{
			foreach($this->params as $key=>$val){
				if(preg_match('/^`(.+)`$/', $val, $match)){
					$val = $match[1];
				}elseif(is_string($val)){
					$val = '\''.mysql_real_escape_string($val).'\'';
				}
				$this->sql = str_replace($key, $val, $this->sql);
			}
		}
		// echo $this->sql;
		$rt = mysql_query($this->sql);
		if(!$rt){
			$this->error();
		}
		return $rt;
	}
}