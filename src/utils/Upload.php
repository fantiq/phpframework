<?php
namespace framework\utils;
use framework\http\Http;
use App;
/**
 * 
 * @author fantiq <fantiq.163.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2015, fantiq
 * @link URL description
 * 获取文件信息
 * 检测文件条件
 *      文件类型
 *      文件大小
 * 移动文件
 *      设置文件名称
 */
class Upload{
    protected $uploadFiles = []; // 上传文件
    protected $config = [
        'type'=>[],         // 允许的文件类型
        'size'=>2048,       // 允许的最大文件尺寸 单位kb 默认2M
        'path'=>'./',       // 上传文件路径
        'rename'=>false,    // 是否重命名
    ];
    protected $ext = '';  // 文件扩展名
    protected $errors = [];
    protected $info = [];
    /**
     * 设置配置参数 可以在初始化中设置
     * @param [type] $config [description]
     */
    public function __construct($conf = []) {
        if($conf!=[]){
            $this->setConfig($conf);
        }
    }
    /**
     * 设置配置参数
     * @param string $key 参数名
     * @param string $val 参数值
     */
    public function setConfig($key='',$val=''){
        if(is_array($key)){
            foreach($key as $var=>$cfg){
                $this->config[$var] = $cfg;
            }
        }else{
            $this->config[$key] = $val;
        }
    }
    /**
     * 开始上传文件
     * @param  string $field 表单字段 多字段可以是数组
     * @param  [type] $conf  配置参数
     * @return [type]        [description]
     */
    public function run($field='',$conf=[]){
        if($conf!=[]){
            $this->setConfig($conf);
        }
        // 处理上传的文件信息格式
        $this->getFileInfo($field);
        // 正式开始上传操作
        foreach($this->uploadFiles as $file){
            if($this->checkFile($file)&&$this->moveFile($file)){
                continue;
            }
            return false;
        }
    }
    /**
     * 获取上传的文件参数
     * @param  string $field 上传表单的文件字段
     * @return [type]        [description]
     */
    protected function getFileInfo($field=''){
        // 多个表单都要做上传处理
        $request = App::getComponent('http')->getHttpRequest();
        if(is_array($field)){
            foreach($field as $fi){
                if(($file = $request->files($fi,null))&&$file['error']===0){
                    $this->uploadFiles[] = $file;
                }
            }
        }elseif($tmp = $request->files($field,null)){
            // 表单上传字段是一个数组
           if(is_array($tmp['name'])){
                $i = $k =0;
                for($l=count($tmp['name']);$k<$l;$k++){
                    if($tmp['error'][$k]===0){
                        $this->uploadFiles[$i] = [
                            'name'=>$tmp['name'][$i],
                            'type'=>$tmp['type'][$i],
                            'tmp_name'=>$tmp['tmp_name'][$i],
                            'error'=>$tmp['error'][$i],
                            'size'=>$tmp['size'][$i]
                        ];
                        $i++;
                    }
                }
           }elseif($tmp['error']===0){ // 单文件上传
                $this->uploadFiles[] = $tmp;
           }
        }else{
            $this->uploadFiles = [];
        }
    }
    /**
     * 检查文件属性是否符合要求的那样 type size
     * @return [type] [description]
     */
    protected function checkFile($file=''){
        // 获取文件类型
        $this->ext = Http::getExt($file['type']);
        if(!$this->ext){
            $this->ext = explode('.', $file['name']);
            $this->ext = end($this->ext);
        }
        // 检测文件类型
        if(!in_array($this->ext, $this->config['type'])){
            $this->setError("格式错误");
            return false;
        }
        // 检测文件尺寸
        if($this->config['size']*1024<$file['type']){
            $this->setError("上传文件不能超过".$this->config['size'].'KB');
            return false;
        }
        return true;
    }
    /**
     * 将上传的临时存储文件移动到指定目录存储
     * @param  [type] $file 文件信息
     * @return [type]       [description]
     */
    protected function moveFile($file=[]){
        if($this->config['rename']){
            // 生成随机文件名
            $file['name'] = sha1(mt_rand(11111,99999).App::$time).'.'.$this->ext;
        }else{
            // 过滤文件名
            $file['name'] = App::getComponent('validate')->getFilter()->reviseFilename($file['name']);
        }
        /**
         * 计算生成文件的文件路径
         * /dir/foo/bar/
         * ./dir/foo/bar/
         * ../dir/foo/bar/
         * 上面这种形式的路径都可以被 realpath转换
         * 不能转换的自动添加 ./ 在路径前面
         */
        if(!preg_match('/^\s*\.{0,2}\/.*$/', $this->config['path'])){
            $this->config['path']='./'.$this->config['path'];
        }
        if($this->config['path']=realpath($this->config['path'])){
            $this->info['path']=$this->config['path'].DS.$file['name'];
        }else{
            $this->setError("你指定的文件路径不存在请创建这个文件路径".$this->config['path']);
            return false;
        }
        if(is_uploaded_file($file['tmp_name'])&&move_uploaded_file($file['tmp_name'], $this->info['path'])){
            return true;
        }
        $this->setError('移动上传的临时文件出现错误，新文件为'.$this->info['path']);
        return false;
    }
    /**
     * 设置错误信息
     * @param string $msg message
     */
    protected function setError($msg=''){
        $this->errors = $msg;
    }
    /**
     * 获取错误信息
     * @return [type] [description]
     */
    public function getError(){
        return $this->errors;
    }
    public function getInfo(){
        return $this->info;
    }
}