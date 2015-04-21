<?php
namespace framework\validate;
use App;
class Filter{
	public static function removeInvisibleChars($str=''){
		$pattern = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // S 加快分析速度
		$count = 0;
		do{
			$str = preg_replace($pattern, '', $str, -1, $count);
		}while($count>0);
		return $str;
	}
	public static function reviseString($str='',$len=10, $tag=''){
		if($len<1) return '';
		return (mb_strlen($str,'utf-8')>$len)?mb_substr($str, 0, $len, 'utf-8').$tag:$str;
	}
	public static function reviseFilename($filename=''){
		// ><)(&$?;=
		$blackList = ["/","./","../","<!--","-->","<",">","'",'"','&','$','#','{','}','[',']','=',';','?',"%20","%22","%3c","%253c","%3e","%0e","%28","%29","%2528","%26","%24","%3f","%3b","%3d"];
		$filename = self::removeInvisibleChars($filename);
		return stripslashes(str_replace($blackList, '', $filename));
	}
	public static function removeXSS($str=''){
		return XssFilter::getInstance()->clean($str);
	}
}