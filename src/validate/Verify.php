<?php
namespace framework\validate;
class Verify{
	protected $request = null;
	protected $errorMsg = [];
	protected static $rules = [];
	/**
	 * 传递http-input数据对象 request
	 * 并复制给属性 request 
	 * 主要是在 form 方法中使用到
	 * @param [type] $request Http-Request
	 */
	public function __construct($request=null){
		$this->request = $request;
	}
	/**
	 * 表单自动验证方法
	 *
	 * 项目/config/rules.php 中配置有不同场景的字段验证规则
	 * 在多个验证规则的时候当验证失败则停止验证返回错误信息
	 * @param  string $scene 使用的一套验证规则 场景
	 * @return boolean 返回验证结果
	 */
	public function form($scene=''){
		if(self::$rules==[]){ // 加载表单验证配置文件
			self::$rules = include APP_PATH.DS.'config'.DS.'rules.php';
		}
		if(!isset(self::$rules[$scene])){ // 请求的场景是否存在
			exit('自动验证的配置文件中不存在\''.$scene.'\'这个场景名称');
		}
		// 遍历验证规则
		foreach(self::$rules[$scene] as $field=>$data){
			$errors = explode('|',$data[1]); //  错误提示信息
			// 数据初步过滤处理
			if(isset($data[2])){
				foreach(explode('|', $data[2]) as $filter){
					$this->request->setPost($field, $this->runValidate($field,$filter));
				}
			}
			// 验证
			foreach(explode('|', $data[0]) as $k=>$validate){
				if(!$this->runValidate($field,$validate)){
					// 存储错误信息 并 返回false
					$this->errorMsg = ['msg'=>isset($errors[$k])?$errors[$k]:'','field'=>$field];
					return false;
				}
			}
		}
		return true;
	}
	/**
	 * 数据过滤
	 * xss跨域攻击html标签过滤
	 * @param  string $str 要过滤的字符串
	 * @return string 	过滤后的字符串
	 */
	public function xss($str=''){
		return Filter::removeXss($str);
	}
	/**
	 * 字符串修正 过滤
	 * @param  string  $str 要修正的字符串
	 * @param  integer $len 字符串最大长度
	 * @param  string  $tag 截取后的拼接
	 * @return string 		截取后的字符串
	 */
	public function cut($str='',$len=10,$tag=''){
		return Filter::reviseString($str,$len,$tag);
	}
	/**
	 *	表单自动验证的入口方法
	 * 	所有的过滤方法、验证方法都要通过这个方法调度返回
	 *
	 * @param  string $validate 验证规则的字符串
	 * @return mixed 	执行结果
	 */
	// number:1,10|string:(alpha,num)|in:linux|required|...
	protected function runValidate($field='',$validate=''){
		// 参数解析
		$validate = explode(':', $validate);
		$validate = $validate[0]; // 过滤器
		$args = [];
		if(isset($validate[1])){
			$args = preg_match('/^\(([^\)]+)\)$/', $$validate[1], $args)?[$args[1]]:explode(',', $validate[1]);
			// if(preg_match('/^\(([^\)]+)\)$/', $$validate[1], $args)){
			// 	$args = [$args[1]];
			// }else{
			// 	$args = explode(',', $validate[1]);
			// }
		}
		array_unshift($args, $this->request->post($field)); // 参数
		if(function_exists($validate)){ // 如果是php函数则会调用php函数进行处理
			return call_user_func_array($validate, $args);
		}
		return call_user_func_array([$this, $validate], $args);
	}
	/**
	 * 返回错误信息
	 * @return [type] [description]
	 */
	public function getError(){
		return $this->errorMsg;
	}
	/**
	 * 输入内容不能为空
	 * 空数组返回
	 * null=='' 返回true （非严格比较）
	 * @param  string $var [description]
	 * @return bool 空数据则返回 false
	 */
	public function required($var=''){
		$var = str_replace(' ', '', Filter::removeInvisibleChars($var));
		return is_array($var)?count(array_filter($var)):Filter::removeInvisibleChars($var)!='';
	}
	/**
	 * alpha 只能是字母
	 * num   只能是数字
	 * zh    只能是汉字
	 * @param  string $str  验证字符串
	 * @param  string $rule 验证规则
	 * @return bool
	 */
	public function string($str='',$rule=''){
		$patterns = ['alpha'=>'a-z','num'=>'0-9','zh'=>'\x{4e00}-\x{9fa5}'];
		if($rule==''){
			$rule = implode('', $patterns);
		}else{
			$allowed = '';
			foreach(explode(',', $rule) as $opt){
				if(isset($patterns[$opt])){
					$allowed .= $patterns[$opt];
				}else{
					$allowed .= preg_quote($opt);
				}
			}
		}
		return preg_match('/^['.$allowed.']+$/iu', $str);
	}
	/**
	 * 字段数据匹配
	 * @param  string $var   需要与其他字段输入值匹配的字段字段值
	 * @param  string $field 需要匹配的字段
	 * @return boolean
	 */
	public function match($var='',$field=''){
		return $var==$this->request->post($field,null);
	}
	/**
	 * 验证字符串长度范围
	 * @param  string $var   要验证的数据
	 * @param  string $range 验证长度范围
	 * @return boolean
	 */
	public function len($var='',$min=null,$max=null){
		$len = mb_strlen($var,'utf-8');
		return $this->number($len,$min,$max);
	}
	/**
	 * 数据是否在列表中出现
	 *
	 * @param  string $var  数据
	 * @param  string $list 列表 列表形式以','隔开 e.g. a,b,c,d....
	 * @return boolean 
	 */
	public function in($var='',$list=''){
		return in_array($var, explode(',', $list));
	}
	/**
	 * 验证数字长度范围
	 * @param  integer $num   要验证的数字
	 * @param  string  $min 最小范围 不设置范围用null or false
	 * @param  string  $max 最大范围 不设置范围用null or false
	 * @return boolean
	 */
	public function number($num=0,$min=null,$max=null){
		if(is_numeric($num)){
			if($min){
				return $max?$num>$min&&$num<$max:$num>$min;
			}else{
				return $num<$max;
			}
		}
		return false;
	}
	/**
	 * 验证邮箱格式
	 * @param  string $email 邮箱
	 * @return boolean
	 */
	public function email($email=''){
		return filter_var($email,FILTER_VALIDATE_EMAIL);
	}
	/**
	 * 验证URL格式是否合法
	 * @param  string $url url
	 * @return boolean
	 */
	public function url($url=''){
		if(strlen($url)>1000){
			return false;
		}
		$pattern = '/^(https?:\/\/)?([a-z0-9\-]+\.){1,}(.*)?$/i';
		return preg_match($pattern, strval($url));
	}
	/**
	 * ip格式判断 支持 ipv4 and ipv6
	 * @param  string $ip ip字符串
	 * @return boolean
	 */
	public function ip($ip=''){
		$options=strpos($ip, ':')===true?['flags'=>FILTER_FLAG_IPV6]:[];
		return filter_var($ip,FILTER_VALIDATE_IP,$options);
	}
	/**
	 * 验证手机号 目前的方法只验证中国的手机号
	 * @param  integer $num 手机号
	 * @return 
	 */
	public function phone($num=0){
		return preg_match('/1[358]\d{9}/', $num)?$num:false;
	}
	/**
	 * 身份证号码合法性验证
	 *		区域    生日       同日生人标号，男性为基数
	 *     411102 1991-03-19              009              		 4 = 前十七位的矫正算法(算法如下)
	 * @param  [type] $num 身份证号码
	 * @return boolean
	 */
	public function id($num){
		if(!preg_match('/^\d{17}[\dXx]$/', $num)) return false;
		$sum=0;
		$coef = [7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2];
		$rema = [1,0,'X',9,8,7,6,5,4,3,2];
		for($i=0,$len=count($coef);$i<$len;$i++) $sum+=$num[$i]*$coef[$i];
		return $rema[$sum%11]==$num[$len]?$num:false;
	}
}