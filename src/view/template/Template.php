<?php
namespace framework\view\template;
use App;
/**
 * @author fantiq <fantiq@163.com>
 * @version 1.0.0
 * @copyright Copyright Right (c) 2015
 * @link [url] [description]
 *
 * 模版解析引擎
 */
class Template{
	protected $dispatch = null;
	protected $leftTag  = '<{';
	protected $rightTag = '}>';
	protected $vars     = [];
	protected $skin     = 'default';
	protected $tplExt   = 'php';
	protected $expire   = 0;
	protected $loopElem = ''; // 循环key
	protected $tplPath  = ''; // 模版文件路径
	protected $comPath  = ''; // 编译文件路径
	protected $cacPath  = ''; // 缓存文件路径
	protected $rootPath = '';
	protected $layoutContent = null;
	protected $layoutAssets  = '';
	protected $configs = [];
	/**
	 * 初始化一些参数
	 */
	public function __construct(){
		$this->configs = App::$config->get('view');
		$this->skin = trim($this->config('skin','default'),DS);
		$this->tplExt = $this->config('tpl_ext','php');
		$this->dispatch = App::getComponent('router')->getDispatch();
		$this->comPath = APP_PATH.DS.'views'.DS.'compile';
		$this->cacPath = APP_PATH.DS.'views'.DS.'cache';
		// 创建文件夹
		foreach([$this->comPath, $this->cacPath] as $dir){
			if(!file_exists($dir)){
				mkdir($dir, 0755);
				chmod($dir, 0755);
			}
		}
	}
	/**
	 * 显示模版
	 * @param  string $path 指定模版路径
	 * @return [type]       [description]
	 */
	public function display($path=''){
		// 设置路径
		$this->setTplPath($path);
		// 编译模版
		$this->compile();
		// 缓存
		if($this->expire>0){
			$out = $this->cacheFile();
		}else{
			ob_start();
			include($this->comPath);
			$out = ob_get_clean();
		}
		// 返回输出内容
		return $out;
	}
	/**
	 * 编译模版 将模版解析成php代码
	 * @return [type] [description]
	 */
	private function compile(){
		if(!is_file($this->tplPath)){
			exit('模版文件不存在');
		}
		// if(!is_file($this->comPath)||filemtime($this->comPath)<filemtime($this->tplPath)){
		if(1){ // this is debug condition
			$out = file_get_contents($this->tplPath); // 获取模版内容
			// 处理模版标签中的 layout css js 指定
			// css js 会注入到layout的模版文件中
			if(preg_match('/^@@([^@]+?)@@/',$out,$matches)){
				foreach(explode(PHP_EOL, trim($matches[1], PHP_EOL)) as $tags){
					if(stripos($tags, 'layout')!==false){
						$tags = preg_replace('/^\s*layout\s+/i', '', $tags);
						$tags = $this->rootPath.DS.'layout'.DS.trim($tags,DS).'.'.$this->tplExt;
						$this->layoutContent = file_get_contents($tags);
						continue;
					}
					if(stripos($tags, 'css')!==false){
						$tags = preg_replace('/^\s*css\s+/i', '', $tags);
						$this->layoutAssets.='<link rel="stylesheet" type="text/css" href="'.$tags.'">';
						continue;
					}
					if(stripos($tags, 'js')!==false){
						$tags = preg_replace('/^\s*js\s+/i', '', $tags);
						$this->layoutAssets.='<script type="text/javascript" src="'.$tags.'"></script>';
						continue;
					}
				}
				$out = str_replace($matches[0], '', $out);
			}
			// 解析请求模版的标签
			$pattern = '/'.preg_quote($this->leftTag).'(.+?)'.preg_quote($this->rightTag).'/i';
			$out = preg_replace_callback($pattern, [$this,'parseTplFile'], $out);
			// 生成最终的模版编译文件
			if($this->layoutContent!=null){
				$this->layoutContent = str_replace($this->leftTag.'tag-assets'.$this->rightTag, $this->layoutAssets, $this->layoutContent);
				$out = str_replace($this->leftTag.'tag-content'.$this->rightTag, $out, $this->layoutContent);
				unset($this->layoutContent);
				unset($this->layoutAssets);
			}
			if(!file_put_contents($this->comPath, $out)){
				exit('编译文件生成失败');
			}
			unset($out);
		}
	}
	/**
	 * 生成缓存文件
	 * 缓存文件不存在 或者编译文件被修改
	 * 缓存文件过期 都会新生成缓存文件
	 * @return [type] [description]
	 */
	private function cacheFile(){
		$isNewFile = false;
		// 文件不存在 或者 编译文件被修改 生成新的缓存文件
		if(!is_file($this->cacPath)||filemtime($this->cacPath)<filemtime($this->comPath)){
			$isNewFile = true;
		}else{
			// 检测缓存文件是否过期
			ob_start();
			include $this->cacPath;
			$out = ob_get_clean();
			if(preg_match('/^cache--(\d+)-->/', $out, $matches)){
				if($matches[1]<App::$time){
					$isNewFile = true;
				}
			}
		}
		// 创建新的缓存文件
		if($isNewFile){
			ob_start();
			include $this->comPath;
			$out = ob_get_clean();
			// 添加过期时间戳到缓存文件开头
			$out = 'cache--'.($this->expire*60+App::$time).'-->'.$out;
			if(!file_put_contents($this->cacPath, $out)){
				exit('生成缓存文件失败');
			}
		}
		return preg_replace('/^cache--\d+--\>/i', '', $out);
	}
	/**
	 * 生成静态文件
	 * @param  string $filename 文件路径
	 * @return [type]           
	 */
	private function cacheStaticFile($filename=''){
		if(!is_file($filename)){
			ob_start();
			include $this->comPath;
			$data = ob_get_clean();
			file_put_contents($filename, $data);
		}
	}
	/**
	 * 模版标签解析方法
	 * 主要是变量输出 判断 循环的解析
	 * @param  [type] $matches 匹配到的标签内容
	 * @return 
	 */
	private function parseTplFile($matches=[]){
		if(stripos($matches[1], 'layout')!==false){
			$matches = str_ireplace(['layout',' '], '', $matches[1]);
			$layoutPath = APP_PATH.DS.$this->skin.DS.'layout'.DS.$matches.'.'.$this->tplExt;
			if(is_file($layoutPath)){
				$this->layoutContent = file_get_contents($layoutPath);
			}
			return '';
		}
		if(preg_match('/^php.*/i', $matches[1])){ // 执行php代码
			return str_replace('=', ' echo ', preg_replace('/^php(.*)/i', '<?php \\1;?>', rtrim($matches[1],'; ')));
		}
		if(stripos($matches[1], 'include')!==false){ // 加载其他模版文件
			$matches[1] = preg_replace('/^\s*include\s+/i', '', $matches[1]);
			return '<?php include "'.$matches[1].'";?>';
		}
		if(stripos($matches[1], '/')===0){
			return '<?php }?>';
		}
		if(stripos($matches[1], 'else')!==false){
			return '<?php }else{?>';
		}
		/**
		 * form表单的hash字段放重复提交
		 */
		if(stripos($matches[1], 'form')!==false){
			$matches[1] = preg_replace('/^\s*form\s+/i', '', $matches[1]);
			$parts = explode('@', $matches[1]);
			if(isset($parts[1])){
				$parts[0] = strtolower($parts[0]); // 请求方法 get post upload
				switch ($parts[0]) {
					case 'get':
						$form = '<form action=\''.$parts[1].'\' method=\'get\'>';
						break;
					case 'post':
						$form = '<form action=\''.$parts[1].'\' method=\'post\'>';
						break;
					case 'upload':
						$form = '<form action=\''.$parts[1].'\' method=\'post\' enctype=\'multipart/form-data\'>';
						break;					
					default:
						$form = '<form action=\''.$parts[1].'\' method=\'post\'>';
						break;
				}
				if($keys = $this->config('form_hash_keys',null)){
					$form .= '<input type=\'hidden\' name=\''.$this->config('form_hash_name','__hash__').'\' value=\''.md5($keys).'\'>';
				}
				return $form;
			}
			return '<form action=\'#\' method=\'post\'>';
		}
		// if 条件判断解析
		// 
		if(stripos($matches[1], 'if')!==false){
			$str = str_ireplace(['neq','eq','lt','gt','lte','gte','if',' '], ['!=','==','<','>','<=','>=',''], $matches[1]);
			if(strpos($str, "'")!==false||strpos($str, "\"")!==false){ // 字符串的比较
				if(preg_match('/(\w+)(==|!=|>|<)([\042|\047]+\w+[\042|\047]+)/i', $str)){
					$str = preg_replace('/(\w+)(==|!=|>|<)([\042|\047]+\w+[\042|\047]+)/i', '<?php if($this->vars[\'${1}\']${2}${3}){?>', $str);
				}else{
					$str = preg_replace('/([\042|\047]+\w+[\042|\047]+)(==|!=|>|<)(\w+)/i', '<?php if(${1}${2}$this->vars[\'${3}\']){?>', $str);
				}
			}else{
				if(preg_match('/\w+(==|!=|>|<)\d+/i', $str)){
					$str = preg_replace('/(\w+)(==|!=|>|<)(\d+)/i', '<?php if($this->vars[\'${1}\']${2}${3}){?>', $str);
				}else{
					$str = preg_replace('/(\w+)(==|!=|>|<)(\w+)/i', '<?php if($this->vars[\'${1}\']${2}$this->vars[\'${3}\']){?>', $str);
				}
			}
			return $str;
		}
		/*
			foreach 循环 标签解析
			foreach list->li
			foreach data=k->v
		*/
		if(stripos($matches[1], 'foreach')!==false){
			$str = preg_replace('/^\s*foreach\s+/i', '', $matches[1]);
			$this->loopElem=substr($str, stripos($str, '->')+2);
			if(stripos($str, '=')!==false){
				return preg_replace('/(\w+)=(\w+)->(\w+)/i', '<?php foreach($this->vars[\'${1}\'] as $${2}=>$${3}){?>', $str);
			}else{
				return preg_replace('/(\w+)->(\w+)/i', '<?php foreach($this->vars[\'${1}\'] as $${2}){?>', $str);
			}
		}
		if(!empty($this->loopElem) && stripos($matches[1], $this->loopElem)!==false){
			$str = trim($matches[1]);
			if(stripos($str, '.')!==false){
				$elem = explode('.', $str);
				return '<?php echo $'.$this->loopElem.'[\''.end($elem).'\'];?>';
			}else{
				return '<?php echo $'.$str.';?>';
			}
		}
		return '<?php echo $this->vars[\''.$matches[1].'\'];?>';
	}
	/**
	 * 计算模版相关的路径
	 * 模版文件路径
	 * 要生成的编译文件路径
	 * 要生成的缓存文件路径
	 * @param string $path [description]
	 */
	private function setTplPath($path=''){
		$this->rootPath = APP_PATH.DS.'views'.DS.$this->skin;
		if($path == ''){ // 默认以 控制器为路径方法为文件名计算模版路径
			$m = $this->dispatch->getModuleName();
			$m = $m==null?'':$m.DS;
			$tplPath = $m.$this->dispatch->getControllerName();
			// 模版文件路径
			$this->tplPath = $this->rootPath.DS.$tplPath.DS.$this->dispatch->getActionName().'.'.$this->tplExt;
		}else{
			$this->tplPath = $this->rootPath.DS.trim($path,DS).'.'.$this->tplExt;
		}
		// 编译后的文件的路径
		$this->comPath = $this->comPath.DS.'compile'.md5($this->tplPath);
		// 缓存文件路径
		$this->cacPath = $this->cacPath.DS.'cache'.md5($this->tplPath);
	}
	/**
	 * 变量赋值
	 * @param  [type] $key [description]
	 * @param  [type] $val [description]
	 * @return [type]      [description]
	 */
	public function assign($key=null,$val=null){
		if($key==null) return $key;
		$this->vars[$key] = $val;
	}
	/**
	 * 模版标签分界符 左分界符
	 * @param string $tag 分界符
	 */
	public function setLeftTag($tag=''){
		$this->leftTag = $tag;
	}
	/**
	 * 模版标签分界符 右分界符
	 * @param string $tag 分界符
	 */
	public function setRightTag($tag=''){
		$this->rightTag = $tag;
	}
	/**
	 * 设置模版皮肤
	 * @param string $skin 皮肤
	 */
	public function setSkin($skin='default'){
		$this->skin = $skin;
	}
	/**
	 * 生成模版文件缓存
	 * @param  integer $exp 缓存文件失效时间 单位为 分钟
	 * @return 
	 */
	public function cache($exp=0){
		$this->expire = $exp;
	}
	/**
	 * 获取配置项的值
	 * 可返回默认值
	 * @param  string $key 配置项的key
	 * @param  string $def 默认值
	 * @return mixed
	 */
	protected function config($key=null,$def=null){
		return isset($this->configs[$key])?$this->configs[$key]:$def;
	}
}