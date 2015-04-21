<?php
namespace framework\validate;
use framework\validate\Filter;
class XssFilter{
	private $context='';
	protected static $instance = null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance = new self;
		}
		return self::$instance;
	}
	public function clean($context=''){
		// -------------------preprocess--------------------------
		// 过滤不可显示的字符
		$this->context = Filter::removeInvisibleChars($context);
		// 解析URL
		$this->context = rawurldecode($this->context);
		// 解码HTML实体
		$this->decodeHtmlEntity();
		// 过滤不可显示的字符
		$this->context = Filter::removeInvisibleChars($context);
		// 处理关键字中间空格或者制表符的绕过情况
		foreach(array('javascript', 'expression', 'vbscript', 'script', 'base64','applet', 'alert', 'document', 'write', 'cookie', 'window') as $word){
			//  正则表达式中 \s 代表任意的空白符，包括空格，制表符(Tab),换行符
			$pattern='\s*';
			for($i=0,$len=strlen($word);$i<$len;$i++){
				$pattern.=$word[$i].'\s*';
			}
			$this->context = preg_replace_callback('/'.$pattern.'/i', array($this,'removeKeywordSapce'), $this->context);
		}
		// -------------------filter----------------------
		$this->neverAllowed();
		if(preg_match('/<\s*a[^>]*?(>|$)/i', $this->context)){
			$this->context = preg_replace_callback('/<\s*a([^>]*?)(>|$)/i', array($this,'removeLinkAttr'), $this->context);
		}
		if(preg_match('/<\s*img[^>]*?(>|$)/i', $this->context)){
			$this->context = preg_replace_callback('/<\s*img([^>]*?)\/*(>|$)/i', array($this,'removeImgAttr'), $this->context);
		}
		if(preg_match('/<\s*script[^>]*?[>|$]/i', $this->context) || preg_match('/<\s*xss[^>]*?[>|$]/i', $this->context)){
			$this->context = preg_replace('/<(\/*)(script|xss)(.*?)>/si', '[removed]', $this->context);
		}
		$this->removeAttrs();
		// 处理特殊标签
		$this->context = str_replace(array('<?','?>','<!','-->'), array('&lt;?','?&gt;','&lt;!','--&gt;'), $this->context);
		// 处理HTML
		$blackList = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';
		$this->context = preg_replace_callback('#<(/*\s*)('.$blackList.')([^><]*)([><]*)#is', array($this, 'removeEvalHtml'), $this->context);
		// 处理PHP
		return preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $this->context);
	}
	/**
	 * html_entity_decode php自带的解析html标签的函数不能解析实体没分号的情况（&#98 ）
	 * 但是大多的浏览器是可以解析这样的字符串的
	 * 为了防止浏览器解析XSS代码，也为了后面对字符串的安全检测过滤处理，这里对ASCII范围可显示的字符做修正操作
	 * 用户可以通过 &#47; 这种形式代替 引号来绕过检测
	 *
	 * 这没有将所有可能的不带分号结尾实体全部修复转化，只是转换了会引起XSS的字符
	 */
	public function decodeHtmlEntity(){
		$this->context = html_entity_decode($this->context);// 解码HTML实体
		$this->context = str_replace(array('&quot','&amp','&lt','&gt','&nbsp'), array('"','&','<','>',' '), $this->context);
		$this->context = preg_replace('/\&\#0*(3[2-9]|[4-9][0-9]|1[0-1][0-9]|12[0-6])/e', 'chr(\\1)', $this->context);
		$this->context = preg_replace('/\&\#x([2-6][0-9a-f]|7[0-9a-e])/ei', 'chr(hexdec("\\1"))', $this->context);
	}
	private function removeKeywordSapce($matches=array()){
		return preg_replace('/\s/i', '', $matches[0]);
	}
	private function removeLinkAttr($matches=array()){
		// $matches[0] 要替换的内容
		// $matches[1] 属性
		$matches[1] = str_replace(array('<','>','/*','*/'), '', $matches[1]);
		return str_replace($matches[1], 
			preg_replace('/\s*href\s*\=\s*([\042|\047]*)\s*(alert\(|javascript\:|vbscript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|data\s*:).*?\\1($|\;|\s)(.*)/si', " href='###' $4", $matches[1])
			, $matches[0]);
	}
	private function removeImgAttr($matches=array()){
		// $matches[0] 要替换的内容
		// $matches[1] 属性
		$matches[1] = str_replace(array('<','>','/*','*/'), '', $matches[1]);
		return str_replace($matches[1],
			preg_replace('/\s*src\s*\=\s*([\042|\047]*)\s*(alert\(|javascript\:|vbscript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|data\s*:).*?\\1($|\;|\s)(.*)/si', " src='###' $4", $matches[1])
			, $matches[0]);
	}
	private function removeAttrs(){
		$attr = array();
		$blackList = array('on\w*', 'xmlns', 'style', 'formaction');
		preg_match_all('/('.implode('|', $blackList).')\s*\=\s*(\042|\047)([^\\2]*?)(\\2)/i', $this->context, $matches);
		foreach($matches as $match){
			if(count($match)>0) $attr[] = preg_quote($match[0],'/');
		}
		preg_match_all('/('.implode('|', $blackList).')\s*\=\s*([^\s>]*)/i', $this->context, $matches);
		foreach($matches as $match){
			if(count($match)>0) $attr[] = preg_quote($match[0],'/');
		}
		if(count($attr)>0){
			$this->context = preg_replace('/(<?)(\/?[^><]+?)([^A-Za-z<>\-])(.*?)('.implode('|', $attr).')(.*?)([\s><]?)([><]*)/i', '$1$2 $4$6$7$8', $this->context);
		}
	}
	private function removeEvalHtml($matches=array()){
		return '&lt;'.$matches[1].$matches[2].$matches[3].str_replace(array('<','>'), array('&lt;','&gt;'), $matches[4]);
	}
	private function neverAllowed(){
		$this->context = preg_replace('/(javascript\s*:|vbscript\s*:|expression\s*\(|Redirect\s+302)/i', '[removed]', $this->context);
		$this->context = preg_replace("/([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?/i", '[removed]', $this->context);
	}
}