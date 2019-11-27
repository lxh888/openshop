<?php
/** ---- eapie ----
 * 优狐积木框架，让开发就像组装积木一样简单！
 * 解放千千万万程序员！这只是1.0版本，后续版本势如破竹！
 * 
 * QQ群：523668865
 * 开源地址 https://gitee.com/lxh888/openshop
 * 官网 http://eonfox.com/
 * 后端框架文档 http://cao.php.eonfox.com
 * 
 * 作者：绵阳市优狐网络科技有限公司
 * 电话/微信：18981181942
 * QQ：294520544
 */



namespace eapie\source\plugin\verifycode;
class verifycode {
	
	
	/*验证码类*/
	
	
	/**
	 * 合并后的配置信息
	 * 
	 * @var	array
	 */
	private $_config;
	
	
	/**
	 * 验证码图片实例
	 * 
	 * @var	resource
	 */
    private $_image   = NULL;
	
	
	
	
	/**
	 * 获取验证码的字符数组
	 * 
	 * $config = array(
	 * 		'font' => 'default', 字符类型的选择。默认default，是数字和英文。number|chinese|english
	 * 		'length' => '1', 字符的长度，默认为1  //用 mb_strwidth 统计
	 * )
	 * 
	 * @return	array
	 */
	public function code($config = array()){
		$font = array(
			'default' => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
			'number' => '12345678901234567890123456789012345678901234567890',
			'chinese' => '之乎者也于与以为由夫今且盖而则矣焉乎哉耶与夫诸耳尔已於',
			'english' => 'abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY'
		);
		if( empty($config['font']) || !isset($font[$config['font']]) ){
			$config['font'] = 'default';
		}
		
		if( empty($config['length']) || !is_numeric($config['length'])){
			$config['length'] = 1;
		}
		
		$code = array();
		for($i = 0; $i < $config['length']; $i ++){
			$code[$i] = iconv_substr($font[$config['font']], floor(mt_rand(0,mb_strlen($font[$config['font']])-1)), 1, mb_internal_encoding());
		}
		
		return $code;
	}
	
	
	/**
	 * 打印验证码
	 * 
	 * @return	print
	 */
	public function output($config = array()){
		$default_config = array(
			'code' => '',//验证码数组
			'bg_img' => false,//使用背景图片 
			'bg_color' =>  array(243, 251, 254), //背景颜色。
			'bg_color_rand' => true, // 背景颜色随机
			'font_size' => 25,// 验证码字体大小(px)
			'font_ttf' =>  NULL, // 验证码字体，不设置随机获取。设置字体为绝对路径
			'curve' => true, // 是否画混淆曲线
			'noise'	=>  false, // 是否添加杂点
			'line' =>  2,//干扰线段的数量。为0则是关闭了干扰线
			'height' =>  0, // 验证码图片高度
        	'width' =>  0, // 验证码图片宽度
        	
		);
		
		$this->_config = array_merge($default_config, $config);
		
		// 图片宽(px)
        if( empty($this->_config['width']) ){
			$this->_config['width'] = $this->_config['length']*$this->_config['font_size']*1.5 + $this->_config['length']*$this->_config['font_size']/2;
			}
        // 图片高(px) 
        if( empty($this->_config['height']) ){
			$this->_config['height'] = $this->_config['font_size'] * 2.5;
			}
			
        // 建立一幅图像
        $this->_image = imagecreatetruecolor($this->_config['width'], $this->_config['height']); 
		
		//先判断背景图片
		if( !empty($this->_config['bg_img']) ){
			$this->_background();//使用背景图片
		}else{
			
			// 设置背景，判断是否随机背景
			if( !empty($this->_config['bg_color_rand']) ){
				//画布的随机颜色	
				$color = imagecolorallocate(
					$this->_image,
					mt_rand(230,255),
					mt_rand(230,255),
					mt_rand(230,255)
					);
				}else{
					//否则是设置的背景颜色
					$color = imagecolorallocate(
						$this->_image, 
						$this->_config['bg_color'][0], 
						$this->_config['bg_color'][1], 
						$this->_config['bg_color'][2]
						); 
					}
			//imagefill()区域填充。 (画布资源,x,y,颜色)	
			imagefill($this->_image, 0, 0, $color);
			
		}
		
        if( !empty($this->_config['noise']) ) $this->_write_noise();// 绘杂点
        if( !empty($this->_config['curve']) ) $this->_write_curve();// 绘干扰线
        
        //imageline()画干扰线段
		if ( !empty($this->_config['line']) ){
			for($i = 0; $i < $this->_config['line']; $i++){
				//画布的随机颜色
				$colorline = imagecolorallocate(
					$this->_image,
					mt_rand(0,255),
					mt_rand(0,255),
					mt_rand(0,255)
				);
				imageline(
					$this->_image,
					mt_rand(0, $this->_config['width']),
					mt_rand(0,$this->_config['height']),
					mt_rand(0,$this->_config['width']),
					mt_rand(0,$this->_config['height']),
					$colorline
					); 
			}
		}
		
		//验证码字体，不设置随机获取
		$ttf_path = dirname(__FILE__).'/code/ttfs/';
        if( empty($this->_config['font_ttf']) ){
			//验证码使用随机字体
			$dir = scandir($ttf_path);
			$ttfs = array();
			foreach($dir as $file){
                if($file!='.' && substr($file, -4) == '.ttf') {
                    $ttfs[] = $file;
					}
				}
			//array_rand从数组中随机取出一个或多个单元
            $this->_config['font_ttf'] = $ttf_path.$ttfs[array_rand($ttfs)];
        	}else{
        		$this->_config['font_ttf'] = $ttf_path.$this->_config['font_ttf'];
        		} 
		
		//循环验证码字符
		if(!empty($this->_config['code']) && is_array($this->_config['code'])){
			$x = 0;//验证码第N个字符的左边距
			foreach($this->_config['code'] as $key => $value){
				//随机颜色
				$color = imagecolorallocate(
					$this->_image, 
					mt_rand(1,150), 
					mt_rand(1,150), 
					mt_rand(1,150)
				);
				$x += mt_rand($this->_config['font_size']*1.2, $this->_config['font_size']*1.6);
				//添加文字
                imagettftext(
	                $this->_image,
	                $this->_config['font_size'], //字体的尺寸。
	                mt_rand(-40, 40), //角度制表示的角度，0 度为从左向右读的文本。
	                $x, //x 
	                $this->_config['font_size'] *1.6, //y
	                $color, //颜色
	                $this->_config['font_ttf'], //TrueType 字体的路径
	                $value //UTF-8 编码的文本字符串
					);
			}
		}
		
		header('Cache-Control: private, max-age=0, no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);		
		// 这三个消息头，共同来控制，要不要缓存该页面。
		// 因为每个浏览器的规格不一样，所以需要三个消息头来控制，做到兼容。
		// 通过 header  来禁用缓存( 比如 ajax  技术，就要禁用缓存) 。
		header("Expires: -1");
		header("Cache-Control: no_cache");
		header("Pragma: no-cache");
        header("content-type: image/png");
        // 输出图像到页面
        imagepng($this->_image);
        imagedestroy($this->_image);
	}



    /**
     * 画杂点
     * 往图片上写不同颜色的字母或数字
     */
    private function _write_noise() {
        $codeSet = '2345678abcdefhijkmnpqrstuvwxyz';
        for($i = 0; $i < 10; $i++){
            //杂点颜色
            $noiseColor = imagecolorallocate($this->_image, mt_rand(200,225), mt_rand(200,225), mt_rand(200,225));
            for($j = 0; $j < 5; $j++) {
                // 绘杂点
                imagestring($this->_image, 5, mt_rand(-10,$this->_config['width']),  mt_rand(-10, $this->_config['height']), $codeSet[mt_rand(0, 29)], $noiseColor);
            }
        }
    }
	
	
    /**
     * 绘制背景图片
     * 注：如果验证码输出图片比较大，将占用比较多的系统资源
     */
	private function _background(){
        $path = dirname(__FILE__).'/code/bgs/';
		$dir = scandir($path);
		$bgs = array();	
		foreach($dir as $file){
			if($file!='.' && substr($file, -4) == '.jpg') {
				$bgs[] = $path . $file;
				}
			}
        $gb = $bgs[array_rand($bgs)];

        list($width, $height) = @getimagesize($gb);
        // Resample
        $bgImage = @imagecreatefromjpeg($gb);
        @imagecopyresampled($this->_image, $bgImage, 0, 0, 0, 0, $this->_config['width'], $this->_config['height'], $width, $height);
        @imagedestroy($bgImage);
		}
	
	
    /** 
     * 画一条由两条连在一起构成的随机正弦函数曲线作干扰线(你可以改成更帅的曲线函数) 
     *      
     *      高中的数学公式咋都忘了涅，写出来
     *		正弦型函数解析式：y=Asin(ωx+φ)+b
     *      各常数值对函数图像的影响：
     *        A：决定峰值（即纵向拉伸压缩的倍数）
     *        b：表示波形在Y轴的位置关系或纵向移动距离（上加下减）
     *        φ：决定波形与X轴位置关系或横向移动距离（左加右减）
     *        ω：决定周期（最小正周期T=2π/∣ω∣）
     *
     */
    private function _write_curve() {
        $px = $py = 0;
        
        // 曲线前部分
        $A = mt_rand(1, $this->_config['height']/2);                            // 振幅
        $b = mt_rand(-$this->_config['height']/4, $this->_config['height']/4);   // Y轴方向偏移量
        $f = mt_rand(-$this->_config['height']/4, $this->_config['height']/4);   // X轴方向偏移量
        $T = mt_rand($this->_config['height'], $this->_config['width']*2);       // 周期
        $w = (2* M_PI)/$T;
                        
        $px1 = 0;  // 曲线横坐标起始位置
        $px2 = mt_rand($this->_config['width']/2, $this->_config['width'] * 0.8);  // 曲线横坐标结束位置

        for ($px=$px1; $px<=$px2; $px = $px + 1) {
            if ($w!=0) {
                $py = $A * sin($w*$px + $f)+ $b + $this->_config['height']/2;  // y = Asin(ωx+φ) + b
                $i = (int) ($this->_config['font_size']/5);
                while ($i > 0) {
					$color = imagecolorallocate($this->_image, mt_rand(1,150), mt_rand(1,150), mt_rand(1,150));//随机颜色
                    imagesetpixel($this->_image, $px + $i , $py + $i, $color);  // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多				
                    $i--;
                }
            }
        }
        
        // 曲线后部分
        $A = mt_rand(1, $this->_config['height']/2);                  // 振幅		
        $f = mt_rand(-$this->_config['height']/4, $this->_config['height']/4);   // X轴方向偏移量
        $T = mt_rand($this->_config['height'], $this->_config['width']*2);  // 周期
        $w = (2* M_PI)/$T;		
        $b = $py - $A * sin($w*$px + $f) - $this->_config['height']/2;
        $px1 = $px2;
        $px2 = $this->_config['width'];

        for ($px=$px1; $px<=$px2; $px=$px+ 1) {
            if ($w!=0) {
                $py = $A * sin($w*$px + $f)+ $b + $this->_config['height']/2;  // y = Asin(ωx+φ) + b
                $i = (int) ($this->_config['font_size']/5);
                while ($i > 0) {		
					$color = imagecolorallocate($this->_image, mt_rand(1,150), mt_rand(1,150), mt_rand(1,150));//随机颜色
                    imagesetpixel($this->_image, $px + $i, $py + $i, $color);	
                    $i--;
                }
            }
        }
    }
	
	
	
	
	




	
}
?>