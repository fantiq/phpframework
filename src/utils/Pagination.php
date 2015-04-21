<?php
namespace framework\utils;
use App;
class Pagination{
	private $count=0; //记录总条数
	private $pageCount=0; //总页数
	private $listNum = 0; //每页显示条数
	private $page=0;//当前页数
	public function __construct($count=0,$list_num=0,$page=1){
		$this->count = $count<0?0:$count; // 总条数
		$this->listNum=$listNum<1?App::$config->get('application.page_lists',10):$listNum; // 每页显示的条数
		$this->pageCount = ceil($this->count/$this->listNum); // 总页数
		$this->page = $page<1?1:$page;
	}
	/**
	 * 设置每页显示的条数
	 * @param integer $listNum [description]
	 */
	public function setListNum($listNum=0){
		if(is_numeric($listNum)&&$listNum>0){
			$this->listNum=$listNum;
		}
	}
	/**
	 * 使用在sql语句的limit部分的参数，获取数据开始的行数
	 * @return [type] [description]
	 */
	public function getStart(){
		if($this->page>$this->page_count) $this->page=$this->page_count;
		$start = ($this->page-1)*$this->list_num;
		return $start<0?0:$start;
	}
	/**
	 * 使用在sql语句的limit部分的参数，获取显示的条数
	 * @return [type] [description]
	 */
	public function getListNum(){
		return $this->listNum;
	}
	/**
	 * 直接返回sql语句中limit部分的参数组合
	 * @return [type] [description]
	 */
	public function getLimit(){
		return $this->getStart().','.$this->listNum;
	}
	public function __set($key=null,$val=null){
		return false;
	}
	/**
	 * 计算页数
	 * @param  integer $offset 分页偏移量
	 * @return [type]          [description]
	 */
	public function show($offset=4){
		$this->page = $this->page>$this->pageCount?$this->pageCount:$this->page;
		$start = $this->page-$offset;
		$start = $start<1?1:$start;
		for($i=$start;$i<$this->page;$i++){
			echo '<b>'.$i.'</b><br>';
		}
		echo $this->page.'<br>';
		$end = $this->page + $offset;
		$end = $end>$this->pageCount?$this->pageCount:$end;
		for($i=$this->page+1;$i<=$end;$i++){
			echo '<b>'.$i.'</b><br>';
		}
	}
}
// $page = new page(1008,3,7);
// echo $page->get_start().'<br>';
// echo $page->get_lists().'<br>';
// $page->show(5);