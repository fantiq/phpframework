<?php
namespace framework\utils;
use App;
class Utils{
	protected static $loadedClass = [];
	public function getImage(){
		if(!isset(self::$loadedClass['Image'])){
			self::$loadedClass['Image'] = App::$container->singleton(__NAMESPACE__.'\Image');
		}
		return self::$loadedClass['Image'];
	}
	public function getFilesys(){
		if(!isset(self::$loadedClass['Filesys'])){
			self::$loadedClass['Filesys'] = App::$container->singleton(__NAMESPACE__.'\Filesys');
		}
		return self::$loadedClass['Filesys'];
	}
	public function getUpload(){
		if(!isset(self::$loadedClass['Upload'])){
			self::$loadedClass['Upload'] = App::$container->singleton(__NAMESPACE__.'\Upload');
		}
		return self::$loadedClass['Upload'];
	}
	public function getPagination($count=0,$listNum=0,$page=1){
		if(!isset(self::$loadedClass['Pagination'])){
			$args = [$count,$listNum,$page];
			self::$loadedClass['Pagination'] = App::$container->singleton(__NAMESPACE__.'\Pagination',$args);
		}
		return self::$loadedClass['Pagination'];
	}
}