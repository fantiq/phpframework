<?php
namespace framework\database\driver\mysqli;
use framework\database\DatabaseException;
use framework\database\driver\DB;
/**
 * @author fantiq <fantiq@163.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2015
 * @link [url] [description]
 *
 * mysqli是一个畸形的pdo 不建议使用 灵活度不高
 * stmt模式下的占位符只支持 ?
 */
class query extends DB{
	protected $stmt = null;	// mysqli_stmt
	protected $symbol = '';	// 操作信号
	protected $params = [];	// 参数
	protected $sql='';		// sql语句
	protected $queryString = [	// 执行的sql结构数组
		'select'=>[
			'field'=>'*',
			'from'=>'',
			'join'=>'',
			'order'=>'',
			'group'=>'',
			'limit'=>''
		],
		'insert'=>'',
		'update'=>'',
		'delete'=>'',
		'where'=>'',
	];
	/**
	 * 查询数据 select * 
	 * @param  string $fields 查询字段
	 * @return 
	 */
	public function select($fields=''){
		$this->symbol = 'select';
		$fields = $fields==''?'* ':$fields.' ';
		$this->queryString['select']['field'] = 'SELECT '.$fields;
		return $this;
	}
	/**
	 * from table 
	 * @param  string $tablename 表名称
	 * @return 
	 */
	public function from($tablename=''){
		$this->queryString['select']['from']='FROM '.$this->getTablename($tablename);
		return $this;
	}
	/**
	 * 表连接 
	 * left join tab on ... 
	 * right join tab on ...
	 * ..
	 * @param  string $method    连接形式 left right inner
	 * @param  string $tablename 要连接的表名称
	 * @param  string $on        连接条件
	 * @return 
	 */
	public function join($method='LEFT',$tablename='',$on=''){
		$method = strtoupper($method)=='LEFT'?' LEFT':' RIGHT';
		$this->queryString['select']['join']=$method.' JOIN '.$this->getTablename($tablename).' ON '.$on;
		return $this;
	}
	/**
	 * 结果排序 order by field DESC/ASC
	 * @param  string $field  要排序的字段
	 * @param  string $method 排序方法 DESC降序 ASC升序
	 * @return 
	 */
	public function order($field='',$method='DESC'){
		$method = $method=='DESC'?'DESC':'ASC';
		$this->queryString['select']['order'] = ' ORDER BY '.$field.' '.$method;
		return $this;
	}
	/**
	 * 分组查询条件
	 * @param  string $field 分组字段
	 * @return 
	 */
	public function group($field=''){
		$this->queryString['select']['limit']=' GROUP BY '.$field;
		return $this;
	}
	/**
	 * 取部分查询结果
	 * @param  integer $start  开始的行数
	 * @param  integer $offset 取的条数
	 * @return 
	 */
	public function limit($start=0,$offset=10){
		$this->queryString['select']['limit']=' LIMIT '.$start.','.$offset;
		return $this;
	}
	/**
	 * insert 添加写入数据到库
	 * 单条插入
	 * insert('tbname',['name'=>'fantasy','sex'=>1])
	 * 多条插入
	 * insert('tbname',[
	 * 		['name'=>'fantasy','sex'=>1],
	 * 		['name'=>'fantiq','sex'=>0]
	 * 	])
	 * @param  string $tablename 表名称
	 * @param  [type] $data      写入数据
	 * @return 
	 */
	public function insert($tablename='',$data=[]){
		if($data == []) return false;
		$this->symbol = 'insert';
		$tmp = ($multi = isset($data[0])&&is_array($data[0]))?$data[0]:$data;
		$tmp = array_keys($data);
		$sql = 'insert into '.$this->getTablename($tablename).'('.implode(',', $tmp).') values';
		$tmp = '(';
		if($multi){
			$i=1;
			foreach($data as $d){
				foreach($d as $field=>$value){
					$tmp .= '?,';
					$this->params[] = $value;
				}
				$sql.=rtrim($tmp,',').'),';
				$i++;
			}
			$sql = rtrim($sql,',');
		}else{
			foreach($data as $field=>$value){
				$tmp .= '?,';
				$this->params[] = $value;
			}
			$sql.=rtrim($tmp,',').')';
		}
		$this->queryString['insert']=$sql;
		$this->exec();
	}
	/**
	 * 更新数据表
	 * 若第三个参数 更新条件 会覆盖where指定的条件
	 * $db->update('tbname',['name'=>'fantasy'],['id'=>5])
	 * $db->where()->update('tbname',['name'=>'fantasy'])
	 * @param  string $tablename 表名称
	 * @param  [type] $data      要更新的数据
	 * @param  [type] $cond      更新的条件
	 * @return 
	 */
	public function update($tablename='',$data=[], $cond=null){
		if($data==[]) return false;
		$this->symbol = 'update';
		$tmp = '';
		foreach($data as $field=>$value){
			$tmp.=$field.'=?,';
			$this->params[]=$value;
		}
		$this->queryString['update'] = 'UPDATE '.$this->getTablename($tablename).' SET '.trim($tmp, ',');
		if($cond!=null){
			$this->where($cond);
		}
		$this->exec();
	}
	/**
	 * 删除表
	 * @param  string $tablename 表名称
	 * @param  [type] $cond      删除条件
	 * @return
	 */
	public function delete($tablename='',$cond=[]){
		$this->symbol = 'delete';
		$this->queryString['delete'] = 'DELETE FROM '.$this->getTablename($tablename);
		if($cond!=[]){
			$this->where($cond);
		}
		return $this->exec();
	}
	/**
	 * 指定条件
	 * 
	 * $cond = ['name','fantasy'] 
	 * where name=:name
	 * params = [':name'=>'fantasy']
	 * ------------------------------
	 * $cond = [
	 *  	['id',100,'>','OR'],
	 *  	['title','fanyilong','='],
	 *  ]
	 * where id>? or title=?
	 * params = [100,'fantasy']
	 * @param  string $cond   条件
	 * @param  [type] $params 参数
	 * @return 
	 */
	public function where($cond='',$params=[]){
		$sql = ' WHERE ';
		if(is_array($cond)){
			if(isset($cond[0])&&is_array($cond[0])){
				foreach($cond as $data){
					$data[2] = isset($data[2])?trim($data[2]):'=';
					$data[3] = isset($data[3])?strtoupper($data[3]):' AND ';
					$sql.=trim($data[0]).$data[2].'? '.$data[3];
					$this->params[] = is_string($data[1])?trim($data[1]):$data[1];
				}
			}else{
				$cond[0] = trim($cond[0]);
				$cond[2] = isset($cond[2])?trim($cond[2]):'=';
				$cond[3] = isset($cond[3])?strtoupper($cond[3]):' AND ';
				$sql.=$cond[0].$cond[2].'? '.$cond[3];
				$this->params[] = is_string($cond[1])?trim($cond[1]):$cond[1];
			}
			$sql = rtrim($sql,'AND|OR|NOT| ');
		}elseif($params!=[]&&is_string($cond)){
			$sql .= $cond;
			if(isset($params[0])){
				$this->params = $params;
			}else{
				foreach($params as $field=>$value){
					$this->params[] = $value;
				}
			}
		}elseif($params==[]){
			$sql .= preg_replace_callback('/([\w\-\_]+)\s*(>|<|=|!=|like)+\s*([\042|\047]*)(.+?)\\3(\s+|$)/', function($matches=[]){
				$this->params[] = $matches[4];
				return $matches[1].$matches[2].'? ';
			}, $cond);
		}
		$this->queryString['where'] = $sql;
		return $this;
	}
	/**
	 * 转换数据表
	 * {tablename} 形式的表会直接按照用户输入处理
	 * 其他情况会自动添加表前缀
	 * @param  string $tablename 表名称
	 * @return 
	 */
	private function getTablename($tablename=''){
		if(preg_match('/^\{(.+)\}$/', $tablename, $matches)){
			return $matches[1];
		}
		if($this->prefix!=''){
			$tablename='_'.$tablename;
		}
		return $this->prefix.$tablename;
	}
	/**
	 * 拼接出来sql语句并且进行预处理
	 * 参数绑定是通过execute参数的形式
	 * @return 
	 */
	public function exec(){
		if(empty($this->symbol)) return false;
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
				break;
		}
		// 替换参数
		if(strpos($this->sql, '?')!==false){
			$this->sql = preg_replace_callback('/\?/', function($matches=[]){
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
	/**
	 * 查询一条记录
	 * @return 
	 */
	public function one(){
		$res = $this->exec();
		if(is_resource($res)){
			return mysql_fetch_assoc($res);
		}else{
			$this->error();
			// return $res;
		}
	}
	/**
	 * 查询所有记录
	 * @return 
	 */
	public function all(){
		$res = $this->exec();
		if(is_resource($res)){
			while($row = mysql_fetch_assoc($res)){
				$data[] = $row;
			}
			return $data;
		}else{
			return $res;
		}
	}
	/**
	 * 直接执行sql语句
	 * @param  string  $sql sql字符串
	 * @param  boolean $one 是否查询一条
	 * @return 
	 */
	public function query($sql='',$one=false){
		$res = $this->dbh->query($sql);
		if(stripos($sql, 'select')!==false){
			return $res;
		}elseif(is_resource($res)){
			return $one?$res->fetch_assoc():$res->fetch_all(MYSQLI_ASSOC);
		}
		return false;
	}
	/**
	 * 错误显示
	 * @return [type] [description]
	 */
	public function error(){
		exit($this->dbh->errno.':'.$this->dbh->error);
	}
}