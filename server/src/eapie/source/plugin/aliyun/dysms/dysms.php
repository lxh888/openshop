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



namespace eapie\source\plugin\aliyun\dysms;
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\SendBatchSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;
use Aliyun\Core\Exception\ClientException;
class dysms {
	
	/**
	 * AccessKey ID
	 * 
	 * @var	string
	 */
	private $_access_key = "";
	
	
	/**
	 * AccessKey Secret
	 * 
	 * @var	string
	 */
	private $_secret_key = "";
	
	
	/**
     * 储存AcsClient
     *
     * @return DefaultAcsClient
     */
	static private $_acs_client = null;
	
	
	
	public function  __construct (){
		require_once __DIR__."/autoload.php";
		// 加载区域结点配置
		Config::load();
   	}
	
	
	
	
	
	/**
     * 取得AcsClient
     *
	 * @param	array	$config
     * @return DefaultAcsClient
     */
	public function client($config) {
		if( empty($config["id"]) || !is_string($config["id"]) ){
			return $this->_error(1);
		}
		if( empty($config["secret"]) || !is_string($config["secret"]) ){
			return $this->_error(2);
		}
		
		//产品名称:云通信短信服务API产品,开发者无需替换	 $product = "Dysmsapi";
		if( empty($config["product"]) || !is_string($config["product"]) ){
			return $this->_error(3);
		}
		//产品域名,开发者无需替换		$domain = "dysmsapi.aliyuncs.com";
		if( empty($config["domain"]) || !is_string($config["domain"]) ){
			return $this->_error(4);
		}
		// 暂时不支持多Region		$region = "cn-hangzhou";
		if( empty($config["region"]) || !is_string($config["region"]) ){
			return $this->_error(5);
		}
		// 服务结点		$endPointName = "cn-hangzhou";
		if( empty($config["end_point_name"]) || !is_string($config["end_point_name"]) ){
			return $this->_error(6);
		}
		

        //TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
        //$accessKeyId = "yourAccessKeyId"; // AccessKeyId
        //$accessKeySecret = "yourAccessKeySecret"; // AccessKeySecret
		
		
        if( self::$_acs_client == null ) {
            //初始化acsClient,暂不支持region化
            $profile = DefaultProfile::getProfile($config["region"], $config["id"], $config["secret"]);
            // 增加服务结点
            DefaultProfile::addEndpoint($config["end_point_name"], $config["region"], $config["product"], $config["domain"]);
            // 初始化AcsClient用于发起请求
            self::$_acs_client = new DefaultAcsClient($profile);
        }
		
        return $this;
    }
	
	
	
	
	/**
     * 发送短信
	 * $config = array(
	 * 		"phone_numbers"=>,//接收的手机号码
	 * 		"template_param"=>, //模板的替换参数,短信模板中字段的值
	 * 		"sign_name"=>	//短信签名
	 * 		"template_code"=>	//模板CODE
	 * 		"out_id"=> //流水号
	 * 		"sms_up_extend_code"=> //上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
	 * )
	 * 
	 * @param	array	$data
     * @return 	stdClass
     */
    public function send($config) {
    	if( empty(self::$_acs_client) ){
    		return $this->_error(10);
    	}
		
     	// if( empty($config["phone_numbers"]) || !is_numeric($config["phone_numbers"]) ){
		// 	return $this->_error(7);
		// }
		if( empty($config["sign_name"]) || !is_string($config["sign_name"]) ){
			return $this->_error(8);
		}
		if( empty($config["template_code"]) || !is_string($config["template_code"]) ){
			return $this->_error(9);
		}
		if( empty($config["template_param"]) || !is_array($config["template_param"]) ){
			$config["template_param"] = array();
		}
		if( empty($config["out_id"]) || !is_string($config["out_id"]) ){
			$config["out_id"] = "";
		}
		if( empty($config["sms_up_extend_code"]) || !is_string($config["sms_up_extend_code"]) ){
			$config["sms_up_extend_code"] = "";
		}
		
		
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();
        //可选-启用https协议
        //$request->setProtocol("https");
        // 必填，设置短信接收号码
        $request->setPhoneNumbers($config["phone_numbers"]);
        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName($config["sign_name"]);
        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode($config["template_code"]);

        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        if( !empty($config["template_param"]) ){
        	$request->setTemplateParam(json_encode($config["template_param"], JSON_UNESCAPED_UNICODE));
        }
        
		
        // 可选，设置流水号
        if($config["out_id"] != ""){
        	$request->setOutId($config["out_id"]);
        }
       
        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
        if($config["sms_up_extend_code"] != ""){
        	$request->setSmsUpExtendCode($config["sms_up_extend_code"]);
        }

        // 发起访问请求
        try{
        	$acsResponse = self::$_acs_client->getAcsResponse($request);
        }catch ( ClientException $e ) {
			/*printexit($e->getMessage(), $e->getErrorMessage(), $e->getErrorType());
			exit;*/
			$acsResponse = $e->getMessage();
        }
        
        return $this->_response($acsResponse);
    }
	
	
	
	/**
	 * 错误提示
	 * 
	 * @param	int			$errno		错误码
	 * @param	string		$message	附加的错误信息
	 * @return	array
	 */
	private function _error( $errno = 0 , $message = '') {
		$return = array();
		switch( $errno ){
			case 1: $return = array('errno'=>1, 'error'=>'AccessKey ID 异常!'); break;
			case 2: $return = array('errno'=>2, 'error'=>'AccessKey Secret 异常!'); break;
			case 3: $return = array('errno'=>3, 'error'=>'product 产品名称异常!'); break;
			case 4: $return = array('errno'=>4, 'error'=>'domain 产品域名异常!'); break;
			case 5: $return = array('errno'=>5, 'error'=>'region 异常!'); break;
			case 6: $return = array('errno'=>6, 'error'=>'endPointName 服务结点!'); break;
			
			case 7: $return = array('errno'=>7, 'error'=>'手机号码异常!'); break;
			case 8: $return = array('errno'=>8, 'error'=>'短信签名异常!'); break;
			case 9: $return = array('errno'=>9, 'error'=>'模板CODE异常!'); break;
			
			case 10: $return = array('errno'=>10, 'error'=>'初始化AcsClient异常!'); break;
			default: $return = array('errno'=>'default', 'error'=>'未知错误'); break;
		}
		return $return;
	}	
	
	
	
	
	/**
	 * 响应数据
	 * 
	 * $acs_response->Message = "";//消息
	 * $acs_response->RequestId = "";//响应ID,排查问题使用
	 * $acs_response->Code = "";
	 * $acs_response->BizId = "";//通过 BizId 查询当前这条短信信息（解决用session存验证码，后期增加服务器压力问题）
	 * 
	 * @param	array		$acs_response
	 * @return	array
	 */
	private function _response($acs_response){
		
		$response = array(
			"errno" => 0,//错误码
			"error" => "",//错误信息
			"RequestId" => "",
			"BizId" => ""
			);
		
		if( is_object($acs_response) ){
			$response["RequestId"] = $acs_response->RequestId;
			if( $acs_response->Code == "OK" ){
				$response["BizId"] = $acs_response->BizId;
			}else{
				$response["errno"] = $acs_response->Code;
				$response["error"] = $acs_response->Message;
			}
		}else{
			$response["errno"] = 1;
			$response["error"] = $acs_response;
		}
		
		return $response;
	}
	
	
	
	
	
	
}
?>