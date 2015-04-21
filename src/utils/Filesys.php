<?php
namespace framework\utils;
class Filesys{
	/**
	 * 创建文件夹 \支持迭代创建功能
	 * @param  string  $path 路径
	 * @param  integer $mode 模式
	 * @return bool          创建是否成功
	 */
	public function createDir($path='',$mode=0){
		$mode=$mode==0?755:$mode;
		if(is_dir($path)||mkdir($path,$mode,true)){
			if(IS_WIN){
				chmod($path, $mode);
			}
			return true;
		}
		return false;
	}
	/**
	 * 创建一个空文件，文件路径不存在会创建，文件存在会写入内容
	 * @param  string $filename 文件路径
	 * @param  string $data     写入文件的数据
	 * @return 
	 */
	public function createFile($filename='',$data=''){
		$path = dirname($filename);
		$data = (string)$data;
		if($this->createDir($path)){
			return empty($data)?touch($filename):file_put_contents($filename, $data, FILE_APPEND);
		}
		return false;
	}
	/**
	 * 删除指定目录下面的所有文件以及文件夹
	 * @param  string $path 目录
	 * @return void
	 */
	public function deleteDir($path=''){
		if(!is_dir($path)) return false;
		$this->dir_iterator($path);
	}
	/**
	 * 简单的目录删除方法
	 * 删除指定目录下面的文件
	 * @param  string $path 路径
	 * @return void
	 */
	public function delDir($path=''){
		if(is_dir($path)){
			foreach(scandir($path) as $file){
				if(is_file($file)){
					unlink($path.DIRECTORY_SEPARATOR.$file);
				}
			}
			return true;
		}
		return false;
	}
	/**
	 * 读取指定目录下面的所有文件
	 * @param  string $path 目录
	 * @return array        文件以及目录列表
	 */
	public function getDirList($path=''){
		if(!is_dir($path)) return false;
		$data = [];
		return $this->dir_iterator($path,false,$data);
	}
	/**
	 * 目录迭代器
	 * @param  string  $path   目录
	 * @param  boolean $is_del 是否要删除文件
	 * @param  array   $data   搜集文件的数组
	 * @return [type]          [description]
	 */
	private function dir_iterator($path='',$is_del=true,&$data=array()){
		$dh = opendir($path);
		while(($file = readdir($dh))!==false){
			if($file=='.'||$file=='..') continue;
			$filename = $path.DIRECTORY_SEPARATOR.$file;
			if(is_dir($filename)){
				if($is_del){
					$this->dir_iterator($filename);
				}else{
					$this->dir_iterator($filename,$is_del,$data[$filename]);
				}
			}else{
				if($is_del){
					unlink($filename);
				}else{
					$data[$path][] = $file;
					// $data[] = $filename;
				}
			}
		}
		closedir($dh);
		if($is_del){
			rmdir($path); //删除目录
		}else{
			return $data;
		}
	}
}