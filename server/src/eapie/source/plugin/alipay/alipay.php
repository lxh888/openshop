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



namespace eapie\source\plugin\alipay;
class alipay {
	
	
	public function  __construct (){
		require_once __DIR__.'/alipay-sdk-PHP-3.3.1/AopSdk.php';
   	}
	
	
    /**
     * 成功
     * 
     * @param  mixed $data 输出数据
     * @return array
     */
    private function _success($data = null){
        return array('errno' => 0, 'data'  => $data);
    }


    /**
     * 失败
     *
     * @param  mixed $errno 错误码或错误信息
     * @return array
     */
    private function _error($errno = null){
        //不是数字，那么就是错误信息
        if (!is_int($errno) )
            return array('errno'=>110, 'error'=> $errno);

        $return = array();
        switch ($errno) {
            case 1:
                $return = array('errno'=>1, 'error'=>'appId 异常!');
                break;
            case 2:
                $return = array('errno'=>2, 'error'=>'rsaPrivateKey 异常!');
                break;
            case 3:
                $return = array('errno'=>3, 'error'=>'alipayrsaPublicKey 异常!');
                break;
			case 4:
                $return = array('errno'=>4, 'error'=>'交易类型异常!');
                break;	
            default:
                $return = array('errno'=>'default', 'error'=>"[{$errno}]未知错误");
                break;
        }

        return $return;
    }
	
	
	
	/**
     * 获取aop实例
     * 
     * req: {
     *  id                      [str] [必填] [支付宝分配给开发者的应用ID]
     *  rsa_private_key         [str] [必填] [开发者私钥，一行字符串]
     *  alipayrsa_public_key    [str] [必填] [支付宝公钥，一行字符串]
     * }
     * 
     * @return object
     */
    private function _aop_client($config = array()){
        if (empty($config['id']) || (!is_string($config['id']) && !is_numeric($config['id'])))
            return $this->_error(1);

        if (empty($config['rsa_private_key']) || !is_string($config['rsa_private_key']))
            return $this->_error(2);

        if (empty($config['alipayrsa_public_key']) || !is_string($config['alipayrsa_public_key']))
            return $this->_error(3);

        //获取SDK实例
        $aop = new \AopClient();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $config['id'];
        $aop->rsaPrivateKey = $config['rsa_private_key'];
        $aop->alipayrsaPublicKey = $config['alipayrsa_public_key'];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset = 'UTF-8';
        $aop->format = 'json';

        return $aop;
    }
	
	
	
	/**
	 * 获取 input 数据
	 * 
	 * @return	array
	 */
	public function input_get_array(){
		//获取输入流
        $input_stream = file_get_contents('php://input');
        parse_str($input_stream, $params);
		if( !is_array($params) ){
			return false;
		}
		return $params;
	}
	
	
	
	/**
     * 检测签名
     *
     * req: {
     *  alipayrsa_public_key [str] [必填] [支付宝公钥，一行字符串]
     * }
     * 
     * @return bool
     */
    public function check_sign($input_get_array = array(), $config = array()){
    	if( empty($input_get_array) || empty($config['alipayrsa_public_key']) ){
    		return false;
    	}
		
        $aop = new \AopClient();
        $aop->alipayrsaPublicKey = $config['alipayrsa_public_key'];
        return $aop->rsaCheckV1($input_get_array, $config['alipayrsa_public_key'], 'RSA2');
    }
	
	
	
		
	/**
	 * 获取安全验证的随机数
	 * 
	 * @return	string
	 */
	public function get_passback_params(){
		return cmd(array(24), 'random string');
	}
	
	
	
	
	/**
     * 单笔转账到支付宝账户
	 * 文档：https://docs.open.alipay.com/api_28/alipay.fund.trans.toaccount.transfer
     *
     * req: {
     *  out_biz_no          [str] [必填] [商户转账唯一订单号。发起转账来源方定义的转账单据ID，用于将转账回执通知给来源方。不同来源方给出的ID可以重复，同一个来源方必须保证其ID的唯一性。只支持半角英文、数字，及“-”、“_”。 ]
     *  amount              [dec] [必填] [转账金额，单位元。只支持2位小数]
     *  payee_type          [str] [必填](ALIPAY_LOGONID) [收款方账户类型。1、ALIPAY_USERID：支付宝账号对应的支付宝唯一用户号。以2088开头的16位纯数字组成。2、ALIPAY_LOGONID：支付宝登录号，支持邮箱和手机号格式。]
     *  payee_account       [str] [必填] [收款方账户。与payee_type配合使用。付款方和收款方不能是同一个账户。]
     *  payee_real_name     [str] [可选] [收款方真实姓名。如果本参数不为空，则会校验该账户在支付宝登记的实名是否与收款方真实姓名一致。]
     *  payer_show_name     [str] [可选] [付款方姓名（最长支持100个英文/50个汉字）。显示在收款方的账单详情页。如果该字段不传，则默认显示付款方的支付宝认证姓名或单位名称。]
     *  remark              [str] [可选] [转账备注（支持200个英文/100个汉字）。当付款方为企业账户，且转账金额达到（大于等于）50000元，remark不能为空。收款方可见，会展示在收款用户的收支详情中。 ]
     *  ext_param_order_title     [str] [可选] [转账标题]
     * }
     * 
     * @param  array  $config [description]
     * @return array
     */
	public function transfer($config = array()){
		//白名单
        $whitelist = array(
            'out_biz_no',
            'payee_type',
            'payee_account',
            'amount',
            'payer_show_name',
            'payee_real_name',
            'ext_param_order_title',
            'remark',
        );
        $app = cmd(array($config, $whitelist), 'arr whitelist');
        
        //是否定义收款方账户类型
        if (!isset($app['payee_type']))
            $app['payee_type'] = 'ALIPAY_LOGONID';

        if (empty($app['payee_account']) || !is_string($app['payee_account']))
            return $this->_error('请填写支付宝账号');

        if (empty($app['payee_real_name']) || !is_string($app['payee_real_name']))
            return $this->_error('请填写真实姓名');

        if (empty($app['out_biz_no']) || (!is_string($app['out_biz_no']) && !is_numeric($config['out_biz_no']))) {
            return $this->_error('请填写商户转账唯一订单号');
        } else {
            $app['out_biz_no'] = strval($app['out_biz_no']);
        }

        $app['ext_param'] = array();
        if (isset($app['ext_param_order_title'])) {
            $app['ext_param']['order_title'] = $app['ext_param_order_title'];
            unset($app['ext_param_order_title']);
        }

        //请求参数
        $biz_content = cmd(array($app), 'json encode');
		//调用SDK
        $aop = $this->_aop_client($config);
        if (is_array($aop) && !empty($aop['errno'])) {
            return $aop;
        }
        $request = new \AlipayFundTransToaccountTransferRequest ();
        $request->setBizContent($biz_content);
        $result = $aop->execute ($request);
        $responseNode = str_replace('.', '_', $request->getApiMethodName()) . '_response';
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            return $this->_success(array(
                'transaction_id' => $result->$responseNode->order_id
            ));
        } else {
            return $this->_error('['.$result->$responseNode->sub_code.']'.$result->$responseNode->sub_msg);
        }
	}
	
	
	
	/**
	 * 支付
	 * 
	 * @param	array	$config
	 * @param	string	$trade_type
	 * @return	string	| array
	 */
	public function pay($config = array(), $trade_type){
		$trade_type = strtoupper($trade_type);
		if( $trade_type == "APP"){
			return $this->_pay_app($config);
		}
		if( $trade_type == "WAP" ){
			return $this->_pay_wap($config);
		} else {
			return $this->_error(4);
		}
	}
	
	
	
	
	/**
     * app支付
     * 文档：https://docs.open.alipay.com/api_1/alipay.trade.app.pay/
     *
     * req: {
     *  out_trade_no        [str](64)   [必填] [商户网站唯一订单号]
     *  subject             [str](256)  [必填] [商品的标题/交易标题/订单标题/订单关键字等]
     *  total_amount        [dec](9)    [必填] [订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]]
     *  passback_params     [str](512)  [可选] [公用回传参数，如果请求时传递了该参数，则返回给商户时会回传该参数。支付宝只会在同步返回（包括跳转回商户网站）和异步通知时将该参数原样返回。本参数必须进行UrlEncode之后才可以发送给支付宝]
     * }
     * 
     * @return void
     */
    private function _pay_app( $config = array() ){
        //白名单
        $whitelist = array(
           'out_trade_no',
           'subject',
           'total_amount',
           'passback_params',
       );
        //请求参数
        $biz_content = cmd(array($config, $whitelist), null, 'arr whitelist | json encode');
        //$biz_content = cmd(array($biz_content), 'json encode');
		
        //调用SDK
        $aop = $this->_aop_client($config);
        if (is_array($aop) && !empty($aop['errno'])) {
            return $aop;
        }
        $request = new \AlipayTradeAppPayRequest();
        $request->setNotifyUrl($config['notify_url']);//异步地址传值方式
        $request->setBizContent($biz_content);
		if( !is_object($aop) ){
			return $this->_error('支付配置异常');
		}
		
        $result = $aop->sdkExecute($request);
        return $this->_success($result);
    }
	
	
	
	
    /**
     * 网页支付
     * 文档：https://docs.open.alipay.com/api_1/alipay.trade.wap.pay/
     * 
     * req: {
     *  out_trade_no        [str](64)   [必填] [商户网站唯一订单号]
     *  subject             [str](256)  [必填] [商品的标题/交易标题/订单标题/订单关键字等]
     *  total_amount        [dec](9)    [必填] [订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]]
     *  quit_url            [str](400)  [必填] [用户付款中途退出返回商户网站的地址]
     *  product_code        [str](64)   [必填] [销售产品码，商家和支付宝签约的产品码]
     *  passback_params     [str](512)  [可选] [公用回传参数，如果请求时传递了该参数，则返回给商户时会回传该参数。支付宝只会在同步返回（包括跳转回商户网站）和异步通知时将该参数原样返回。本参数必须进行UrlEncode之后才可以发送给支付宝]
     * }
     *
     * @return array
     */
    private function _pay_wap($config = array()){
        //白名单
        $whitelist = array(
            'out_trade_no',
            'subject',
            'total_amount',
            'quit_url',
            'product_code',
            'passback_params',
        );

        //请求参数
        $biz_content = cmd(array($config, $whitelist), null, 'arr whitelist | json encode');
        //$biz_content = cmd(array($biz_content), '');
        //调用SDK
        $aop = $this->_aop_client($config);
    }
	
	
	
	
	
	
	
}
?>