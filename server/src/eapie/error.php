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



namespace eapie;
class error extends \Exception {
	
	
	
	
	
	/**
	 * 重定义构造器使 message 变为必须被指定的属性
	 * 确保所有变量都被正确赋值
	 * 当$x 为int 那么就是 $code，而$y即$message
	 * 当$x 为string	那么就是$message， $code 等于-1。为自定义错误信息
	 * 
	 * @param	int		$code
	 * @param	string	$message
	 * @return	void
	 */
    public function __construct($x = NULL, $y = ''){
    	
		if(is_integer($x)){
			$code = $x;
			$message = $y;
		}else
		if(is_string($x)){
			$code = -1;
			$message = $x;
		}else{
			$code = 1;
			$message = $y;
		}
		
        parent::__construct($message, $code);
    }
	
	
	
	
	
	/**
	 * 关于错误与错误类型：
	 * 
	 * 最好是系统开发完成后，再对错误码进行序列排序，这样有利于整体化。
	 * 
	 * 像某些插件，则不需要错误码，直接在代码中写错误信息即可。以至以后扩展再作打算。
	 * throw new error("自定义的错误") 没有注明错误码，传入的是字符串，这时候系统自动设为错误码为 -1 即自定义错误信息
	 */
	
	
	
	
	
	/**
	 * 数据检测
	 * 
	 * 例如：
	 * object(parent::ERROR)->check(
	 * 		要检测的数据$data, 
	 * 		要检测的键'user_id', 
	 * 		要调用的数据表检测组parent::TABLE_USER,
	 * 		判断项 array('exist', 'max_length') 
	 * 		判断项的键 '编码'//如果为空，那么则与检测的数据键一致
	 * 		是否返回错误信息，默认false 直接throw new error
	 * )
	 * 
	 * 'password' => array(
	 * 		//20014{"zh_cn":"登录密码不合法","en_us":""}
	 * 		//20015{"zh_cn":"登录密码不能为空","en_us":""}
	 * 		//20016{"zh_cn":"登录密码数据类型有误","en_us":""}
	 * 		'args'=>array('exist'=>array(20014), '!null'=>array(20015), 'echo'=>array(20016) ),//参数检测
	 * 		
	 * 		//20017{"zh_cn":"登录密码的字符长度太多","en_us":""}
	 * 		//20018{"zh_cn":"登录密码的字符长度太多","en_us":""}
	 * 		'length' => array('>=length'=>array(6, 20017), '<=length'=>array(36, 20018)),//字符长度检测
	 * 		),
	 * 
	 * 
	 * //没有判断参数，只写错误信息，那么第二个参数即是 额外的错误信息
	 * 'exist'=>array("数据必须存在")	不存在，返回false, 则触发错误信息
	 * '!exist'=>array("数据不能存在")	存在，返回false, 则触发错误信息
	 * 'null'=>array("数据必须为空")	不为空，返回false, 则触发错误信息。是判断可echo 能输出的变量
	 * '!null'=>array("数据不能为空")	为空，返回false, 则触发错误信息。是判断可echo 能输出的变量
	 * 
	 * //在数据存在的情况下检测。可输出的数据类型		is_string || is_numeric
	 * 'echo'=>array("数据必须是可echo输出")	不能输出，返回false, 则触发错误信息
	 * '!echo'=>array("数据是echo不能输出的")	能输出，返回false, 则触发错误信息
	 * 
	 * //在数据存在的情况下检测。
	 * 'match'=>array('/^([a-zA-Z0-9_]+)@([^\.]+)\.(\w+)$/iu', "xx格式不合法" ) 没匹配到, 则触发错误信息。这里也是要先判断是可echo输出，不然会直接报错
	 * '!match'=>array('/^([a-zA-Z0-9_]+)@([^\.]+)\.(\w+)$/iu', "xx格式合法" ) 匹配到, 则触发错误信息
	 * 
	 * //在数据不为空的情况下检测。任何字符是根据所占字节来判断长度 函数 strlen。这里也是要先判断是可echo输出，不然会直接报错
	 * 'length'=>array(36, "xx字符长度必须等于36" )	//不等于限制字数返回false
	 * '!length'=>array(36, "xx字符长度必须不等于36" )	//等于限制字数, 则触发错误信息
	 * '>length'=>array(36, "xx字符太少" ) //必须大于限制字数，小于限制字数返回false
	 * '<length'=>array(36, "xx字符太多" )	//必须小于限制字数，大于限制字数返回false
	 * 
	 * //在数据不为空的情况下检测。一个汉字占2个字符，一个字母占一个 函数 mb_strwidth。这里也是要先判断是可echo输出，不然会直接报错。
	 * 'width'=>array(36, "xx字符长度必须等于36" )	//不等于限制字数返回false
	 * '!width'=>array(36, "xx字符长度不等于36" )	//等于限制字数, 则触发错误信息
	 * '>width'=>array(36, "xx字符太少" ) //必须大于限制字数，小于限制字数返回false
	 * '<width'=>array(36, "xx字符太多" )	//必须小于限制字数，大于限制字数返回false
	 * 
	 * //执行对象方法。自动传入将被检测的数据
	 * 'method'=>array( array('类名称、对象标识', '方法名称'), "这是错误信息") //对象方法返回false, 则触发错误信息
	 * '!method'=>array( array('类名称、对象标识', '方法名称'), "这是错误信息") //对象方法返回true, 则触发错误信息
	 * 如：'!method'=>array(array(parent::TABLE_USER_EMAIL, 'find_exists_email'), "电子邮箱地址已经注册")
	 * 
	 * 
	 * @param	array	$data				要检测的数据
	 * @param	string	$data_key			要检查的数据键
	 * @param	string	$class_name			类名称
	 * @param	array	$check				检查项
	 * @param	string	$check_key			检查项的键。如果为空，那么则与检测的数据键$data_key一致
	 * @param	bool	$is_error_return	是否返回错误信息，默认false 直接throw new error
	 * @return throw | true
	 */
	public function check($data,  $data_key, $class_name, $check = array(), $check_key = NULL, $is_error_return = FALSE ){
		//检查项的键。如果为空，那么则与检测的数据键$data_key一致
		if( !isset($check_key) ) $check_key = $data_key;
		
		if( !isset($check_key) || 
		(!is_string($check_key) && !is_numeric($check_key)) ){
			throw new error ("要检查的数据键名称不合法");
		}
		
		//检查对象或类是否具有该属性 或者 没有对应数据检测键
		if( property_exists(object($class_name), 'check') ){
			if( !empty(object($class_name)->check[$check_key]) ){
				$class_check = object($class_name)->check[$check_key];
			}
		}
		
		if(!isset($class_check) || 
		!is_array($class_check) || 
		empty($check) || 
		!is_array($check)){
			throw new error ("数据键名称为 “".$check_key."” 检测异常");
		}
		
		
		foreach($check as $type){
			$type = trim($type);
			if(isset($class_check[$type])){
				foreach($class_check[$type] as $c_key => $c_value){
					$err = $this->_check_submethod($data, $data_key, $class_check[$type], $c_key);
					if( isset($err[0]) ){
						//是否返回错误信息，默认false 直接throw new error
						if( empty($is_error_return) ){
							throw new error ($err[0], $err[1]);
						}else{
							return $err;
						}
						
					}
				}
			}else{
				throw new error ("数据键名称为 “".$check_key."” 中没有 “".$type."” 检测项");
			}
		}
		
		
		
		return true;
	}
	
	
	
	
	
	/**
	 * 细节检查
	 * 
	 * @param	array	$data			要检测的数据
	 * @param	string	$data_key		要检查的数据键
	 * @param	array	$check_list		检查项列表
	 * @param	string	$type			检查项的类型
	 * @return throw | true
	 */
	private function _check_submethod($data, $data_key, $check_list, $type){
		
		if($type == 'exist' && isset($check_list['exist'])){
			if( !isset($data[$data_key]) ){
				$err = array(
					(isset($check_list['exist'][0])? $check_list['exist'][0] : ''),
					(isset($check_list['exist'][1])? $check_list['exist'][1] : '')
					);
				return $err;
			}
		}
		if($type == '!exist' && isset($check_list['!exist'])){
			if( isset($data[$data_key]) ){
				$err = array(
					(isset($check_list['!exist'][0])? $check_list['!exist'][0] : ''),
					(isset($check_list['!exist'][1])? $check_list['!exist'][1] : '')
					);
				return $err;
			}
		}
		if($type == 'null' && isset($check_list['null'])){
			if( isset($data[$data_key]) && trim($data[$data_key])=='' ){
				$err = array(
					(isset($check_list['null'][0])? $check_list['null'][0] : ''),
					(isset($check_list['null'][1])? $check_list['null'][1] : '')
					);
				return $err;
			}
		}
		if($type == '!null' && isset($check_list['!null'])){
			if( !isset($data[$data_key]) || trim($data[$data_key]) == '' ){
				$err = array(
					(isset($check_list['!null'][0])? $check_list['!null'][0] : ''),
					(isset($check_list['!null'][1])? $check_list['!null'][1] : '')
					);
				return $err;
			}
		}
		
		
		/**
		 * //在数据存在的情况下检测。可输出的数据类型		is_string || is_numeric
		 * 'echo'=>array("数据必须是可echo输出")	不能输出，返回false, 则触发错误信息
		 * '!echo'=>array("数据是echo不能输出的")	能输出，返回false, 则触发错误信息
		 */
		if($type == 'echo' && isset($check_list['echo']) && isset($data[$data_key])){
			if( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ){
				$err = array(
					(isset($check_list['echo'][0])? $check_list['echo'][0] : ''),
					(isset($check_list['echo'][1])? $check_list['echo'][1] : '')
					);
				return $err;
			}
		}
		if($type == '!echo' && isset($check_list['!echo']) && isset($data[$data_key])){
			if( (is_string($data[$data_key]) || is_numeric($data[$data_key])) ){
				$err = array(
					(isset($check_list['!echo'][0])? $check_list['!echo'][0] : ''),
					(isset($check_list['!echo'][1])? $check_list['!echo'][1] : '')
					);
				return $err;
			}
		}
		
		
		/**
		 * //在数据存在的情况下检测。
		 * 'match'=>array('/^([a-zA-Z0-9_]+)@([^\.]+)\.(\w+)$/iu', "xx格式不合法" ) 没匹配到, 则触发错误信息。这里也是要先判断是可echo输出，不然会直接报错
		 * '!match'=>array('/^([a-zA-Z0-9_]+)@([^\.]+)\.(\w+)$/iu', "xx格式合法" ) 匹配到, 则触发错误信息
		 */
		if($type == 'match' && isset($check_list['match']) && isset($data[$data_key]) ){
			if( ( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ) ||
			!isset($check_list['match'][0]) ||
			!preg_match($check_list['match'][0], $data[$data_key]) ){
				$err = array(
					(isset($check_list['match'][1])? $check_list['match'][1] : ''),
					(isset($check_list['match'][2])? $check_list['match'][2] : '')
					);
				return $err;
			}
		}
		if($type == '!match' && isset($check_list['!match']) && isset($data[$data_key])){
			if( ( is_string($data[$data_key]) || is_numeric($data[$data_key]) && preg_match($check_list['!match'][0], $data[$data_key]) ) ||
			!isset($check_list['!match'][0]) ){
				$err = array(
					(isset($check_list['!match'][1])? $check_list['!match'][1] : ''),
					(isset($check_list['!match'][2])? $check_list['!match'][2] : '')
					);
				return $err;
			}
		}
		
		
		/**
		 * //在数据不为空的情况下检测。任何字符是根据所占字节来判断长度 函数 strlen。这里也是要先判断是可echo输出，不然会直接报错
		 * 'length'=>array(36, "xx字符长度必须等于36" )	//不等于限制字数返回false
		 * '!length'=>array(36, "xx字符长度必须不等于36" )	//等于限制字数, 则触发错误信息
		 * '>length'=>array(36, "xx字符太少" ) //必须大于限制字数，小于限制字数返回false
		 * '<length'=>array(36, "xx字符太多" )	//必须小于限制字数，大于限制字数返回false
		 * '>=length'=>array(36, "xx字符太少" ) //必须大于等于限制字数，小于限制字数返回false
		 * '<=length'=>array(36, "xx字符太多" )	//必须小于等于限制字数，大于限制字数返回false
		 */
		if($type == 'length' && isset($check_list['length']) && isset($data[$data_key]) ){
			if( ( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ) || 
			!isset($check_list['length'][0]) ||
			strlen($data[$data_key]) != $check_list['length'][0] ){
				$err = array(
					(isset($check_list['length'][1])? $check_list['length'][1] : ''),
					(isset($check_list['length'][2])? $check_list['length'][2] : '')
					);
				return $err;
			}
			
		}
		if($type == '!length' && isset($check_list['!length']) && isset($data[$data_key]) ){
			if( ( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ) || 
			!isset($check_list['!length'][0]) ||
			strlen($data[$data_key]) == $check_list['!length'][0] ){
				$err = array(
					(isset($check_list['!length'][1])? $check_list['!length'][1] : ''),
					(isset($check_list['!length'][2])? $check_list['!length'][2] : '')
					);
				return $err;
			}
			
		}
		if($type == '>length' && isset($check_list['>length']) && isset($data[$data_key]) ){
			if( ( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ) || 
			!isset($check_list['>length'][0]) ||
			strlen($data[$data_key]) <= $check_list['>length'][0] ){
				$err = array(
					(isset($check_list['>length'][1])? $check_list['>length'][1] : ''),
					(isset($check_list['>length'][2])? $check_list['>length'][2] : '')
					);
				return $err;
			}
			
		}
		if($type == '<length' && isset($check_list['<length']) && isset($data[$data_key]) ){
			if( ( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ) || 
			!isset($check_list['<length'][0]) ||
			strlen($data[$data_key]) >= $check_list['<length'][0] ){
				$err = array(
					(isset($check_list['<length'][1])? $check_list['<length'][1] : ''),
					(isset($check_list['<length'][2])? $check_list['<length'][2] : '')
					);
				return $err;
			}
			
		}
		
		if($type == '>=length' && isset($check_list['>=length']) && isset($data[$data_key]) ){
			if( ( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ) || 
			!isset($check_list['>=length'][0]) ||
			strlen($data[$data_key]) < $check_list['>=length'][0] ){
				$err = array(
					(isset($check_list['>=length'][1])? $check_list['>=length'][1] : ''),
					(isset($check_list['>=length'][2])? $check_list['>=length'][2] : '')
					);
				return $err;
			}
			
		}
		if($type == '<=length' && isset($check_list['<=length']) && isset($data[$data_key]) ){
			if( ( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ) || 
			!isset($check_list['<=length'][0]) ||
			strlen($data[$data_key]) > $check_list['<=length'][0] ){
				$err = array(
					(isset($check_list['<=length'][1])? $check_list['<=length'][1] : ''),
					(isset($check_list['<=length'][2])? $check_list['<=length'][2] : '')
					);
				return $err;
			}
			
		}
		
		
		/**
		 * //在数据不为空的情况下检测。一个汉字占2个字符，一个字母占一个 函数 mb_strwidth。这里也是要先判断是可echo输出，不然会直接报错。
		 * 'width'=>array(36, "xx字符长度必须等于36" )	//不等于限制字数返回false
		 * '!width'=>array(36, "xx字符长度不等于36" )	//等于限制字数, 则触发错误信息
		 * '>width'=>array(36, "xx字符太少" ) //必须大于限制字数，小于限制字数返回false
		 * '<width'=>array(36, "xx字符太多" )	//必须小于限制字数，大于限制字数返回false
		 * '>=width'=>array(36, "xx字符太少" ) //必须大于等于限制字数，小于限制字数返回false
		 * '<=width'=>array(36, "xx字符太多" )	//必须小于等于限制字数，大于限制字数返回false
		 */
		if($type == 'width' && isset($check_list['width']) && isset($data[$data_key]) ){
			if( ( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ) || 
			!isset($check_list['width'][0]) ||
			mb_strwidth($data[$data_key]) != $check_list['width'][0] ){
				$err = array(
					(isset($check_list['width'][1])? $check_list['width'][1] : ''),
					(isset($check_list['width'][2])? $check_list['width'][2] : '')
					);
				return $err;
			}
			
		}
		if($type == '!width' && isset($check_list['!width']) && isset($data[$data_key]) ){
			if( ( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ) || 
			!isset($check_list['!width'][0]) ||
			mb_strwidth($data[$data_key]) == $check_list['!width'][0] ){
				$err = array(
					(isset($check_list['!width'][1])? $check_list['!width'][1] : ''),
					(isset($check_list['!width'][2])? $check_list['!width'][2] : '')
					);
				return $err;
			}
			
		}
		if($type == '>width' && isset($check_list['>width']) && isset($data[$data_key]) ){
			if( ( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ) || 
			!isset($check_list['>width'][0]) ||
			mb_strwidth($data[$data_key]) <= $check_list['>width'][0] ){
				$err = array(
					(isset($check_list['>width'][1])? $check_list['>width'][1] : ''),
					(isset($check_list['>width'][2])? $check_list['>width'][2] : '')
					);
				return $err;
			}
			
		}
		if($type == '<width' && isset($check_list['<width']) && isset($data[$data_key]) ){
			if( ( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ) || 
			!isset($check_list['<width'][0]) ||
			mb_strwidth($data[$data_key]) >= $check_list['<width'][0] ){
				$err = array(
					(isset($check_list['<width'][1])? $check_list['<width'][1] : ''),
					(isset($check_list['<width'][2])? $check_list['<width'][2] : '')
					);
				return $err;
			}
			
		}
		if($type == '>=width' && isset($check_list['>=width']) && isset($data[$data_key]) ){
			if( ( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ) || 
			!isset($check_list['>=width'][0]) ||
			mb_strwidth($data[$data_key]) < $check_list['>=width'][0] ){
				$err = array(
					(isset($check_list['>=width'][1])? $check_list['>=width'][1] : ''),
					(isset($check_list['>=width'][2])? $check_list['>=width'][2] : '')
					);
				return $err;
			}
			
		}
		if($type == '<=width' && isset($check_list['<=width']) && isset($data[$data_key]) ){
			if( ( !is_string($data[$data_key]) && !is_numeric($data[$data_key]) ) || 
			!isset($check_list['<=width'][0]) ||
			mb_strwidth($data[$data_key]) > $check_list['<=width'][0] ){
				$err = array(
					(isset($check_list['<=width'][1])? $check_list['<=width'][1] : ''),
					(isset($check_list['<=width'][2])? $check_list['<=width'][2] : '')
					);
				return $err;
			}
			
		}
		
		
		/**
		 * //执行对象方法。自动传入将被检测的数据
		 * 'method'=>array( array('类名称、对象标识', '方法名称'), "这是错误信息") //对象方法返回false, 则触发错误信息
		 * '!method'=>array( array('类名称、对象标识', '方法名称'), "这是错误信息") //对象方法返回true, 则触发错误信息
		 */
		if($type == 'method' && isset($check_list['method']) && isset($data[$data_key]) ){
			if( !isset($check_list['method'][0][0]) || 
			!class_exists($check_list['method'][0][0]) ||
			 !isset($check_list['method'][0][1]) ||
			 !method_exists(object($check_list['method'][0][0]), $check_list['method'][0][1]) ||
			 !call_user_func_array(array( object($check_list['method'][0][0]), $check_list['method'][0][1]), array($data[$data_key]) ) ){
				$err = array(
					(isset($check_list['method'][1])? $check_list['method'][1] : ''),
					(isset($check_list['method'][2])? $check_list['method'][2] : '')
					);
				return $err;
			}
		}
		if($type == '!method' && isset($check_list['!method']) && isset($data[$data_key]) ){
			if( !isset($check_list['!method'][0][0]) || 
			!class_exists($check_list['!method'][0][0]) ||
			 !isset($check_list['!method'][0][1]) ||
			 !method_exists(object($check_list['!method'][0][0]), $check_list['!method'][0][1]) ||
			 call_user_func_array(array( object($check_list['!method'][0][0]), $check_list['!method'][0][1]), array($data[$data_key]) ) ){
				$err = array(
					(isset($check_list['!method'][1])? $check_list['!method'][1] : ''),
					(isset($check_list['!method'][2])? $check_list['!method'][2] : '')
					);
				return $err;
			}
		}
		
		
		/**
		 * 执行闭包函数
		 * 检测的数据时第一个参数
		 * 'closure'=>array(array(function(){}, array("第二个参数", "第三个参数", ...)), "这是错误信息"); //闭包函数返回空或者false、或者闭包函数不存在，则触发错误信息
		 */
		/*if($type == 'closure' && isset($check_list['closure']) && isset($data[$data_key]) ){
			$err = array(
			(isset($check_list['closure'][1])? $check_list['closure'][1] : ''),
			(isset($check_list['closure'][2])? $check_list['closure'][2] : '')
			);
			
			if( !isset($check_list['closure'][0][0]) || 
			gettype($check_list['closure'][0][0]) != 'object' || 
			get_class($check_list['closure'][0][0]) != 'Closure' ){
				return $err;
			}
			  
			//参数
			if( empty($check_list['closure'][0][1]) || !is_array($check_list['closure'][0][1])){
				$check_list['closure'][0][1] = array();
			}  
			array_unshift($check_list['closure'][0][1], $data[$data_key]);  
			$return = call_user_func_array($check_list['closure'][0][0], $check_list['closure'][0][1] );
			if( empty($return) ){
				return $err;
			} 
		}*/
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
}
?>