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



namespace eapie\source\request\session;
use eapie\main;
use eapie\error;
class image_verify_code extends \eapie\source\request\session {
	
	
	
	/**
	 * 初始化变量
	 * 
	 * @param	void
	 * @return	void
	 */
	private function _init_var(){
		if( !isset($_SESSION['session_private']) || !is_array($_SESSION['session_private']) ){
			$_SESSION['session_private'] = array();
			}
		if( !isset($_SESSION['session_private']['verify_code']) || !is_array($_SESSION['session_private']['verify_code']) ){
			$_SESSION['session_private']['verify_code'] = array();
			}
		if( !isset($_SESSION['session_private']['verify_code']['image_verify_code']) || 
		!is_array($_SESSION['session_private']['verify_code']['image_verify_code']) ){
			$_SESSION['session_private']['verify_code']['image_verify_code'] = array();
			}
	}
	
	
	
	
	
	/**
	 * 显示验证码。获取数字验证码图片，输出到页面上
	 * 将验证码存入 $_SESSION['session_private']['verify_code']['image_verify_code'][$key] 并且用md5进行加密
	 * 
	 * $config = array(
	 * 	"length"=>"字符长度",
	 *  "height"=>"高",
	 *  "width"=>"宽"
	 * );
	 * 
	 * SESSIONIMAGEVERIFYCODESHOW
	 * [{"image_verify_key":"必须|验证码的键名称","bg_img":"选填|是否使用背景图片，默认false","bg_color":"选填|背景颜色，bg_color_rand为false有效。是一个rgb参数数组，如白色背景：[255, 255, 255]","bg_color_rand":"选填|背景颜色随机，默认为true","font":"字符类型的选择。默认default，是数字和英文。number|chinese|english","font_size":"选填|字体大小，默认23","font_ttf":"选填|验证码字体 默认5.ttf，其他值为1~6.ttf，中文篆体是z.ttf","height":"选填|高度，默认50","width":"宽度|默认宽度160"}]
	 * 
	 * @param	string		$data		
	 * @return	print
	 */
	public function api_show($data = array()){
		
		//检查是否已初始化
		object(parent::REQUEST_SESSION)->check();
		if( !isset($data["image_verify_key"]) ){
			throw new error("验证码键名称不存在");
		}
		if(!is_string($data["image_verify_key"]) && !is_numeric($data["image_verify_key"])){
			throw new error("验证码键名称有误");
		}
		
		//白名单
		$whitelist = array(
			'bg_img',
			'bg_color',
			'bg_color_rand',
			'font', 
			'font_size',
			'font_ttf',
			'height', 
			'width',
			);
		$config = cmd(array($data, $whitelist), 'arr whitelist');
		
		//默认值
		$default_config = array(
			'bg_img' => false,
			'length'=> 5,
			'font' => 'number', //number|chinese|english
			'height' => 50,
			'width' => 160,
			'font_size' => 23,
			'font_ttf' => '5.ttf',
			'code' => ''
			);
		$config = array_merge($default_config, $config);
		
		//获得验证码
		$config['code'] = object(parent::PLUGIN_VERIFYCODE)->code($config);
		
		//初始化变量
		$this->_init_var();
		//存进会话
		$_SESSION['session_private']['verify_code']['image_verify_code'][$data["image_verify_key"]] = $config['code'];
		
		//输出
		object(parent::PLUGIN_VERIFYCODE)->output($config);
		exit;
	}
	
	
	
	
	
	
	
	
	
	/**
	 * 检测验证码，无论是否正确都要生成一个临时的验证码
	 * 因为验证码保存在session中的，所以验证码检测工具也在这里
	 * 
	 * $data['image_verify_key']	键
	 * $data['image_verify_code']	验证码
	 * 
	 * @param	array		$data	数据
	 * @return	throw
	 */
	protected function _check_($data = array()){
		//检查是否已初始化
		object(parent::REQUEST_SESSION)->check();
		
		//初始化变量
		$this->_init_var();
		
		if( !isset($data['image_verify_key']) || 
		(!is_numeric($data['image_verify_key']) && !is_string($data['image_verify_key'])) || 
		$data['image_verify_key'] == '' ){
			//直接删除该验证码参数
			unset($_SESSION['session_private']['verify_code']['image_verify_code']);
			throw new error("缺少验证码键名称");
		}
		
		if( !isset($data['image_verify_code']) || 
		 (!is_numeric($data['image_verify_code']) && !is_string($data['image_verify_code'])) || 
		 $data['image_verify_code'] == '' ){
			//直接删除该验证码参数
			unset($_SESSION['session_private']['verify_code']['image_verify_code']);
			throw new error("验证码输入不合法");
		}
		
		
		//获取会话的验证码
		if( !isset($_SESSION['session_private']['verify_code']['image_verify_code'][$data['image_verify_key']]) ||
		!is_array($_SESSION['session_private']['verify_code']['image_verify_code'][$data['image_verify_key']]) ){
			//更新临时验证码
			$_SESSION['session_private']['verify_code']['image_verify_code'][$data['image_verify_key']] = array('temp_'.cmd(array(10), 'random autoincrement'));
			throw new error("验证码异常，刷新验证码重试");
		}
		
		
		//将数组拼接成字符串对比
		//将验证码转为字符串并且转为小写
		if( $data['image_verify_code'] !== implode($_SESSION['session_private']['verify_code']['image_verify_code'][$data['image_verify_key']]) ){
			//更新临时验证码
			$_SESSION['session_private']['verify_code']['image_verify_code'][$data['image_verify_key']] = array('temp_'.cmd(array(10), 'random autoincrement'));
			throw new error("验证码输入错误");
		}
		
		//更新临时验证码
		$_SESSION['session_private']['verify_code']['image_verify_code'][$data['image_verify_key']] = array('temp_'.cmd(array(10), 'random autoincrement'));
		
		return true;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>