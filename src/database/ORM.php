<?php
namespace framework\database;
/**
 * @author fantiq <fantiq@163.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2015
 * @link [url] [description]
 *
 * 这个类作为所有的表的一个映射
 */
class ORM{
	protected static $db = null;
	protected $tablename = '';
	protected $limit = 10;
	protected $datas = [];
	protected $primaryKey = 'id';
	public function __construct($tablename=''){
		$this->tablename = $tablename;
		if(self::$db==null){
			self::$db = Database::getInstance();
		}
	}
	public function setLimit($num){
		$this->limit = $num<1?1:$num;
	}
	public function setPrimaryKey($key=''){
		$this->primaryKey = $key;
	}
	public function get($field=''){
		$field = empty($field)?'*':$field;
		return self::$db->select($field)->from($this->tablename)->limit($this->limit)->all('obj');
	}
	public function getOne($field=''){
		$field = empty($field)?'*':$field;
		return self::$db->select($field)->from($this->tablename)->limit($this->limit)->one('obj');
	}
	public function getWhere($condition=[]){
		return self::$db->select()->from($this->tablename)->where($condition)->limit($this->limit)->all('obj');
	}
	public function getBy($rank='',$field=''){
		if(empty($field)) return false;
		$field = explode(' ', $field);
		$field[1] = isset($field[1])?$field[1]:'';
		if(strcasecmp($rank, 'order')){
			return self::$db->select()->from($this->tablename)->where($condition)->order($field[0],$field[1])->limit($this->limit)->all('obj');
		}else{
			return self::$db->select()->from($this->tablename)->where($condition)->group($field)->limit($this->limit)->all();
		}
	}
	public function getWhereBy($condition=[],$rank='',$field){
		$rank = strtolower($rank);
		if(strcasecmp($rank, 'order')){
			return self::$db->select()->from($this->tablename)->where($condition)->order($field)->limit($this->limit)->all('obj');
		}else{
			return self::$db->select()->from($this->tablename)->where($condition)->group($field)->limit($this->limit)->all('obj');
		}
	}
	/**
	 * 条添加数据 insert 
	 */
	public function add(){
		return self::$db->insert($this->tablename,$this->datas);
	}
	/**
	 * 更新数据
	 * @param  [type] $condition 跟新数据时候的条件
	 * @return [type]            [description]
	 */
	public function save($condition=[]){
		if($condition = $this->resolve($condition)){
			return self::$db->update($this->tablename,$this->datas,$condition);
		}
		return false;
	}
	/**
	 * 删除数据
	 * @param  [type] $condition 删除的条件
	 * @return [type]            [description]
	 */
	public function delete($condition=[]){
		if($condition = $this->resolve($condition)){
			return self::$db->delete($this->tablename,$condition);
		}
		return false;
	}
	/**
	 * 解析条件
	 * 空数组则不能执行
	 * 数组则按照where条件中的解释执行
	 * 不是数组是值的话 sql将这个值作为主键值 当作条件
	 * 不设置条需要设置这个值为false 防止错误的删除 更新数据
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	protected function resolve($condition=[]){
		if($condition==[]) return false;
		if(!is_array($condition)){
			if($condition===false) $condition = []; // 条件部分设置为false说明是不要条件
			else $condition = [$this->primaryKey=>$condition];
		}
		return $condition;
	}
	/**
	 * 通过魔术方法来收集用户操作的一些字段
	 * @param string $key 字段
	 * @param string $val 值
	 */
	public function __set($key='',$val=''){
		$this->datas[$key] = $val;
	}
}