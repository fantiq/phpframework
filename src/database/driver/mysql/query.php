<?php
namespace framework\database\driver\mysql;
use framework\database\DatabaseException;
use framework\database\driver\DB;
/**
 * @author fantiq <fantiq@163.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2015
 * @link [url] [description]
 *
 * mysql 数据库的sql操作
 * sql的拼接使用了方法的封装 方便操作
 * sql字符串中的值都进行了转义防止sql注入
 * 方法的连贯操作
 * 
 */
class query extends DB{
	// protected $stmt = null;	// PDOStatement
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
                if($data == []){
                    return false;
                }
		$this->symbol = 'insert';
		$tmp = ($multi = isset($data[0])&&is_array($data[0]))?$data[0]:$data;
		$sql = 'insert into '.$this->getTablename($tablename).'('.implode(',', array_keys($tmp)).') values';
		if($multi){
			foreach($data as $d){
				$tmp = '(';
				foreach($d as $value){
					$tmp .= '?,';
					$this->params[] = $value;
				}
				$sql.=rtrim($tmp,',').'),';
			}
			$sql = rtrim($sql,',');
		}else{
			foreach($data as $value){
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
	 * $db->where(['id'=>5])->update('tbname',['name'=>'fantasy'])
	 * @param  string $tablename 表名称
	 * @param  [type] $data      要更新的数据
	 * @param  [type] $cond      更新的条件
	 * @return 
	 */
	public function update($tablename='',$data=[], $cond=null){
                if($data==[]){
                    return false;
                }
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
	 * $cond = ['name'=>'fantasy'] 
	 * where name=?
	 * params = ['fantasy']
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
			foreach($cond as $field=>$data){
				if(is_array($data)){
					$data[2] = isset($data[2])?trim($data[2]):'=';
					$data[3] = isset($data[3])?strtoupper($data[3]):' AND ';
					$sql.=trim($data[0]).$data[2].'? '.$data[3].' ';
					$this->params[] = is_string($data[1])?trim($data[1]):$data[1];
				}else{
					$sql.=$field.'=? AND ';
					$this->params[] = is_string($data)?trim($data):$data;
				}
			}
			$sql = rtrim($sql,'AND|OR|NOT| ');
		}elseif($params!=[]&&is_string($cond)){
			$sql .= $cond;
			if(isset($params[0])){
				$this->params = $params;
			}else{
				foreach($params as $field=>$value){
					$this->params[':'.$field] = $value;
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
	 * 统计总条数 count 
	 * $this->select()->from('users')->where(['id',10,'>'])->count();
	 * @param  string $field [description]
	 * @return [type]        [description]
	 */
	public function count($field=''){
		$field = $field=''?'COUNT(*)':'COUNT('.$field.')';
		$this->select($field);
		return $this->one();
	}
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
	 * 不设置$offset 值 默认将第一个参数作为offset值 不设置开始位置
	 * @param  integer $start  开始的行数
	 * @param  integer $offset 取的条数
	 * @return 
	 */
	public function limit($start=0,$offset=0){
		if($offset<1){
			$this->queryString['select']['limit']=' LIMIT '.$start;
		}else{
			$this->queryString['select']['limit']=' LIMIT '.$start.','.$offset;
		}
		return $this;
	}
        /**
	 * 直接执行sql语句
	 * @param  string  $sql sql字符串
	 * @return 
	 */
	public function query($sql=''){
            if(stripos($sql, 'select')!==false){
                    $res = mysql_query($sql);
                    if(is_resource($res)){
                            $datas = [];
                            while($row = mysql_fetch_array($res)) $datas[] = $row;
                            return $datas;
                    }else{
                            $this->error();
                            // return $res;
                    }
            }else{
                    if(!mysql_query($sql)){
                            $this->error();
                    }
                    return true;
            }
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
	 * 错误显示
	 * @return [type] [description]
	 */
	public function error(){
		exit(mysql_errno().':'.mysql_error());
	}
}