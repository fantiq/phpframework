<?php
namespace framework\database\driver\pdo;
use framework\database\DatabaseException;
use PDO;
use PDOException;
class connect extends query{
	protected $dbh = null;
	protected $prefix = '';
	/**
	 * 分析配置文件中的参数 初始化PDO
	 * @param [type] $config [description]
	 */
	public function __construct($config=[]){
		if(!extension_loaded('PDO')){
			throw new DatabaseException("php未开启PDO扩展", 1);
		}
		$config['charset'] = $config['charset']==''?'utf8':$config['charset'];
		$config['port'] = $config['port']==''?3306:$config['port'];
		$dsn = $config['scheme'].':host='.$config['host'].';port='.$config['port'].';dbname='.$config['dbname'].';charset='.$config['charset'];
		try {
			$this->dbh = new PDO($dsn,$config['user'],$config['passwd']);
			$this->prefix = $config['prefix'];
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	/**
	 * 直接执行sql语句
	 * @param  string  $sql sql字符串
	 * @return 
	 */
	public function query($sql=''){
		if(stripos($sql, 'select')!==false){
			$stmt = $this->dbh->query($sql);
			$stmt->setFetchMode(PDO::FETCH_BOTH);
			return $stmt->fetchAll();
		}else{
			return is_bool($this->dbh->exec($sql))?false:true;
		}
	}
	/**
	 * 查询一条记录
	 * @param string $method 结果集数据形式
	 * @return 
	 */
	public function one($method=null){
		$this->exec();
		$this->stmt->setFetchMode($this->parseMethod($method));
		return $this->stmt->fetch();
	}
	/**
	 * 查询所有记录
	 * @param string $method 结果集数据形式
	 * @return 
	 */
	public function all($method=null){
		$this->exec();
		$this->stmt->setFetchMode($this->parseMethod($method));
		return $this->stmt->fetchAll();
	}
	private function parseMethod($method=''){
		if($method==null){
			return PDO::FETCH_ASSOC;
		}elseif(strtolower($method)=='obj'){
			return PDO::FETCH_OBJ;
		}elseif(strtolower($method)=='num'){
			return PDO::FETCH_NUM;
		}
		return PDO::FETCH_BOTH;
	}
	// 增加事务处理支持
}