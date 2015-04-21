<?php
namespace framework\utils;
class Image{
	private $img=[
		'filename'=>'',
		'w'=>0,
		'h'=>0,
		'mime'=>'',
		'type'=>null
		];
	// 生成的新资源的图片数据类型
	private $image=[
		'h'=>0,
		'w'=>0,
		'im'=>null
		];
	private $gdVersion=null;
	public function __construct($path=''){
		if(extension_loaded('gd')&&!empty($path)&&!($image_info = getimagesize($path))){
			$this->img['filename'] = $path;
			$this->img['w'] = $image_info[0];
			$this->img['h'] = $image_info[1];
			$this->img['mime'] = strtolower($image_info['mime']);
			$this->img['type'] = str_replace('image/', '', $this->img['mime']);

			$gd = gd_info();
			$this->gdVersion = preg_match('/2\.\d\.\d/i', $gd['GD Version'])?2:1;
		}
	}
	/**
	 * 验证码生成
	 * @param  integer $w     验证码宽度
	 * @param  integer $h     验证码高度
	 * @param  integer $n     随机字符串个数
	 * @param  array   $color 背景色
	 * @return [type]         [description]
	 */
	public function captcha($h=0,$n=4,$bgcolor=[255,255,255],$color=[0,0,0]){
		$this->imageNewSource($h,$n,$bgcolor);
		$this->createCode($n,$color);
		$this->interference($color);
		//输出
		header("Content-Type:image/png;");
		imagepng($this->image['im']);
		// imagedestroy($this->image['im']);
	}
	/**
	 * 图片缩放
	 * 0 以宽度为参照缩放
	 * 1 以高度为参照缩放
	 * other 以百分比为参照
	 * @param  integer $refer     参照数
	 * @param  string  $save_path 文件保存路径
	 * @param  integer $order     依据
	 * @return bool
	 */
	public function thumb($refer=100,$save_path='',$order=0){
		if($order==0){
			// 默认以宽度进行缩放计算
			$new_w = $refer;
			$new_h = $new_w*$this->img['h']/$this->img['w'];
		}elseif($order==1){
			// 依据高度作缩放尺寸计算
			$new_h = $refer;
			$new_w=$new_h*$this->img['w']/$this->img['h'];
		}else{
			$refer = (($refer/100)>1||($refer/100)<0)?1:($refer/100);
			$new_w = $this->img['w']*$refer;
			$new_h = $this->img['h']*$refer;
		}
		$src_image = $this->image_source();
		$dst_image = imagecreatetruecolor($new_w, $new_h);
		if(imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $new_w, $new_h, $this->img['w'], $this->img['h'])){
			$this->image_save($dst_image,$save_path);
		}
	}
	/**
	 * 图片截取
	 * @param  integer $start_x   采样开始x轴位置
	 * @param  integer $start_y   采样开始y轴位置
	 * @param  integer $x_len     在x轴上从tart_x开始取的长度
	 * @param  integer $y_len     在y轴上从tart_y开始取的长度
	 * @param  integer $dst_w     新文件的宽度
	 * @param  integer $dst_h     新文件的高度
	 * @param  string  $save_path 新文件保存路径
	 * @return bool
	 */
	public function crop($start_x=0,$start_y=0, $x_len=0, $y_len=0, $dst_w=0, $dst_h=0, $save_path=''){
		if($x_len>($dst_w-$start_x)) $x_len=$dst_w-$start_x;
		if($y_len>($dst_h-$start_y)) $y_len=$dst_h-$start_y;
		$src_image = $this->image_source();
		$dst_image = imagecreatetruecolor($dst_w, $dst_h);
		if(imagecopyresampled($dst_image, $src_image, 0, 0, $start_x, $start_y,$dst_w, $dst_h, $x_len, $y_len)){
			$this->image_save($dst_image,$save_path);
		}
	}
	/**
	 * 生成一个图片资源返回
	 * @param  string $path 资源路径
	 * @param  string $from 创建类型
	 * @return source
	 */
	private function image_source($path='',$from=''){
		$path = empty($path)?$this->img['filename']:$path;
		$from = empty($from)?$this->img['type']:$from;
		$create_func = 'imagecreatefrom'.$from;
		return $create_func($path);
	}
	/**
	 * 创建新的图片资源
	 * @param  integer $w 图片宽度
	 * @param  integer $h 图片高度
	 * @return void
	 */
	private function imageNewSource($h=0,$n=0,$color=[0,0,0]){
		//创建图片
		$this->image['h']=$h;
		$this->image['w']=$n*$h*3/5;
		$this->image['im'] = imagecreatetruecolor($this->image['w'], $h);
		// 填充颜色
		imagefill($this->image['im'], 0, 0, $this->getColor($this->image['im'],$color));
	}
	/**
	 * 存储生成的图片文件
	 * @param  string $save_path 保存的路径 文件名
	 * @param  source $im        要保存的资源
	 * @return bool
	 */
	private function image_save($im=null,$save_path=''){
		$save_path = empty($save_path)?$this->img['filename']:$save_path;
		$output_image = 'image'.$this->img['type'];
		$output_image($im, $save_path);
		imagedestroy($im);
	}
	/**
	 * 创建验证码随机字符
	 * @param  integer $n 	  验证码字符个数
	 * @param  array   $color 字体颜色
	 * @return [type]     [description]
	 */
	private function createCode($n=0,$color=[0,0,0]){
		//生成随机数
		$code='';
		$charlist = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$len = strlen($charlist)-1;
		for($i=0;$i<$n;$i++) $code.=$charlist[mt_rand(0,$len)];
		//写入图片
		$font_size=$this->gdVersion==2?$this->image['h']*2/11:$this->image['h'];
		$font_size=$this->image['h']*3/5; //1pt = 3/4 px
		$str_x = $this->image['h']*mt_rand(20,30)/100;
		$str_y = $this->image['h']*4/5;
		// 验证码字体
		$font_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR;
		$font_path.='roughtrad.ttf';
		// $font_path.='HotelOriental.ttf';
		// $font_path.='Comic.ttf';
		$im_color = $this->getColor($this->image['im'],$color);
		for($i=0;$i<$n;$i++){
			$angle=mt_rand(0,10);
			$pos = imagettftext($this->image['im'], $font_size, $angle, $str_x, $str_y, $im_color, $font_path, $code[$i]);
			$str_x=$pos[4]*mt_rand(95,105)/100;
		}
		// set session
	}
	/**
	 * 生成验证码上的干扰线
	 * @param  integer $mode 干扰线模式 1->曲线 2->点 3->曲线+点
	 * @return void
	 */
	private function interference($color=[0,0,0],$mode=1){
		$im_color = $this->getColor($this->image['im'],$color);
		for($i=0;$i<2;$i++){
			//干扰线
			$cx=mt_rand(0,1)?mt_rand(2,10):mt_rand(2,10)+$this->image['w'];
			$cy=mt_rand(2,5)-10;
			$cw=$this->image['w']*mt_rand(21,25)/10;
			$ch=$this->image['h']*mt_rand(12,14)/10;
			imagesetthickness($this->image['im'], 3);
			imagearc($this->image['im'], $cx, $cy, $cw, $ch, 0, 359.9, $im_color);
		}
	}
	/**
	 * 设置颜色值
	 * @param  source 	$im 图片资源
	 * @param  int 		$r  red
	 * @param  int 		$g  green
	 * @param  int 		$b  blue
	 * @return void
	 */
	private function getColor($im=null,$color=array(0,0,0)){
		list($r,$g,$b) = $color;
		return imagecolorallocate($im, $r, $g, $b);
	}
	public function __destruct(){
		// imagedestroy($this->image['im']);
	}
}