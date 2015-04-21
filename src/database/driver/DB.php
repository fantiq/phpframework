<?php
namespace framework\database\driver;
abstract class DB{
	// SELECT * FROM cloud_user_distribute AS dis 						查询的字段以及表
	// LEFT JOIN cloud_company AS com ON dis.cloud_company_id=com.id 	连接的表
	// WHERE com.member_grade>0 										查询条件
	// ORDER BY dis.distribute_time DESC 								排序规则 (ORDER BY 与 GROUP BY 只能出现一个)
	// GROUP BY dis.inner_user_id 										分组规则 (ORDER BY 与 GROUP BY 只能出现一个)
	// LIMIT 0,10 														提取数据条数
	
	// abstract protected function __construct();
	abstract protected function insert($tablename='',$data=null);
	abstract protected function update($tablename='',$data=null);
	abstract protected function delete($tablename='',$cond=null);
	abstract protected function select($fields='');
	abstract protected function from($tablename='');
	abstract protected function join($method='LEFT',$tablename='',$on='');
	abstract protected function where($cond=null); // $cond 可数组可字符串
	abstract protected function order($field='',$method='DESC');
	abstract protected function group($field='');
	abstract protected function limit($start=0,$offset=10);
	abstract protected function exec(); // 组合成SQL语句执行
	abstract protected function query($sql=''); // 直接执行sql字符串

}