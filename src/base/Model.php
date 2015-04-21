<?php
namespace framework\base;
use App;
/**
 * 数据验证操作 最好在这里操作
 * controller只负责操作dispatch
 */
class Model extends Base{
	public function orm($tbname=''){
		if($tbname=='') return null;
		return App::getComponent('database')->getORM($tbname);
	}
}