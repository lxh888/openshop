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



namespace eapie\source\request;
use eapie\main;
use eapie\error;
class test extends main {



	public function api($input = array())
	{
		$res = object(parent::TABLE_SHOP_GOODS)->select(array(
            'limit' => array(1)
        ));

        return $res;
	}
	

	/**
	 * 
	 *  {"class":"test","method":"api_wangaho"}
	 */
	public function api_wangaho(){
		
		// $data = object(parent::TABLE_USER_COUPON)->register_coupon(array('user_id'=>'266bb5b65915584882232284'));
		// $data = object(parent::TABLE_USER_COUPON)->member_goods_coupon(array('user_id'=>'266bb5b65915584882232284'));


		$input = array(
			'money' =>100,
			'credit' =>100
		);

		$req = array(
            // 'user_id' =>'3b5115155e15625819309779',
            'user_id' =>'7884d730ae15626356461147',
            'orderby' => array()
        );

        $req['orderby'][] = array('uc.user_coupon_insert_time', false);
        if(isset($input['money']) && is_numeric($input['money'])){
            $req['money'] = (int)$input['money'];
        }
        if(isset($input['credit']) && is_numeric($input['credit'])){
            $req['credit'] = (int)$input['credit'];
        }
        
        $req['select'] = array(
            "uc.user_coupon_id AS id",
            "uc.coupon_id",
            "uc.user_coupon_json AS coupon_info",
            "uc.user_coupon_expire_time AS expire_time",
            "uc.user_coupon_insert_time AS insert_time",
            "uc.user_coupon_state AS state",
            "uc.user_coupon_number AS number",
            "uc.user_coupon_use_number AS use_number",
            "c.coupon_name AS name",
            "c.coupon_info AS info",
            "c.coupon_type AS type",
            "c.coupon_discount AS discount",
            "c.coupon_start_time AS start_time",
            "c.coupon_end_time AS end_time",
            "c.coupon_state AS c_state",
            "c.coupon_limit_min AS min",
            "c.coupon_limit_max AS max",
            "c.coupon_property AS property",
        );

        //查询数据
        $data = object(parent::TABLE_USER_COUPON)->select_available_join_coupon($req);


		// $user_id = '3b5115155e15625819309779';

		printexit($data);



		$data = object(parent::TABLE_API)->find_join('USERSIGNUP', 'emshop_test');
		$data2 = object(parent::TABLE_API)->find_join('USERWITHDRAWSELFADD', 'emshop_test');
		printexit($data, $data2);
		
	}

	/**
	 * E麦商城奖励规则测试
	 * 
	 * {"class":"test","method":"api_test_reward_rule"}
	 * TESTTESTREWARDRULE
	 * 
	 * @return array $data 身份奖励规则
	 */
	public function api_test_reward_rule($data = array()){
		return object(parent::TABLE_USER_RECOMMEND)->xlt_distribution_money_reward('946336136e0b15712135743039');     // 分销奖励发放
		// return object(parent::TABLE_USER_RECOMMEND)->xlt_goods_user_recommend('979577b42db915619470891502');             // 身份升级
		// return object(parent::TABLE_SHOP_ORDER)->xlt_distribution_reward('946336136e0b15712135743039');          // 新建身份升级
		// return object(parent::TABLE_USER_RECOMMEND)->xlt_shop_goods_reward_money('4c5470cc058b15711202329319');
	}

	/**
	 * 锁表测试
	 * 
	 * 1TESTLOCKTABLE
	 * 
	 * @param	string		$command	命令
	 * @param	array		$tables		表列表，是一个索引数据
	 * @return	bool
	 */
	public function api_lock_table($command = "", $tables = array())
	{
		if (empty($command) || !is_string($command) || !preg_match("/^[bsiudc]{1,6}$/i", $command)) {
			throw new error("操作命令为空或不合法");
		}
		if (empty($tables)) {
			throw new error("表数据为空");
		}

		db(parent::DB_APPLICATION_ID)->call("table", $tables)->lock($command);
		sleep(20);
		db(parent::DB_APPLICATION_ID)->lock('c');

		return "执行完毕";
	}




	/**
	 * 用户积分测试
	 * 
	 * 1TESTUSERCREDITPLUS
	 * 
	 * @param	string		$command	命令
	 * @param	array		$tables		表列表，是一个索引数据
	 * @return	bool
	 */
	public function api_user_credit_plus()
	{

		$args = array(
			"merchant_id" => "merchant_idxxxx",
			"merchant_credit_plus" => 1000,
			"merchant_credit_type" => "recharge"
		);
		$user_now_data = object(parent::TABLE_MERCHANT_CREDIT)->find_now_data($args['merchant_id']); //查询用户当前积分
		if (!empty($user_now_data)) {
			$args["merchant_credit_value"] = $user_now_data['merchant_credit_value'] + $args["merchant_credit_plus"];
			$args["merchant_credit_join_id"] = $user_now_data['merchant_credit_id'];
		} else {
			$args["merchant_credit_value"] = $args["merchant_credit_plus"];
		}
		$bool = object(parent::TABLE_MERCHANT_CREDIT)->insert_plus($args);
		return $bool;
	}

	/**
	 * 拼团成功接口测试
	 * TESTADDGROUPORDERTEST
	 */
	public function api_add_group_order_test($data){
		$return = array();
		$config = array();
		$config['select'] = array('*');

		$result = object(parent::TABLE_USER)->select($config);
		foreach ($result as $key => &$value) {
			$update_data = array();
			$cache = cmd(array($value['user_json']), 'json decode');
			if(isset($cache['address']['province']))
				$update_data['user_register_province'] = $cache['address']['province'];
			if(isset($cache['address']['city']))
				$update_data['user_register_city'] = $cache['address']['city'];
			if(isset($cache['address']['area']))
				$update_data['user_register_area'] = $cache['address']['area'];
			if(!empty($update_data)){
				$update_where = array( array('user_id=[+]',$value['user_id']));
				$return[] = object(parent::TABLE_USER)->update($update_where,$update_data);
			}
			
		}
		return $return;
		
	}




	/**
	 * 用户积分测试
	 * 
	 * 1TESTUSERCREDITMINUS
	 * 
	 * @param	string		$command	命令
	 * @param	array		$tables		表列表，是一个索引数据
	 * @return	bool
	 */
	public function api_user_credit_minus()
	{

		$args = array(
			"merchant_id" => "merchant_idxxxx",
			"merchant_credit_minus" => 800,
			"merchant_credit_type" => "purchase"
		);
		$user_now_data = object(parent::TABLE_MERCHANT_CREDIT)->find_now_data($args['merchant_id']); //查询用户当前积分
		if (!empty($user_now_data)) {
			$args["merchant_credit_value"] = $user_now_data['merchant_credit_value'] - $args["merchant_credit_minus"];
			$args["merchant_credit_join_id"] = $user_now_data['merchant_credit_id'];
		}

		$bool = object(parent::TABLE_MERCHANT_CREDIT)->insert_minus($args);



		return $bool;
	}



	/**
	 * 转换率
	 * 
	 * TESTCONVERSIONRATIO
	 * 
	 * 
	 */
	public function api_test_conversion_ratio()
	{

		$yesterday_first = cmd(array(time()), "time day_first"); //获取当天的最初时间戳
		$anteayer_first = cmd(array($yesterday_first - 1), "time day_first"); //前天
		$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("user_credit_conversion_user_money_share"), true);
		$yesterday_sum = object(parent::TABLE_ORDER)->find_timestamp_merchant_credit_rmb_all_sum($yesterday_first);
		$anteayer_sum = object(parent::TABLE_ORDER)->find_timestamp_merchant_credit_rmb_all_sum($anteayer_first);
		//获得转换率
		$conversion_ratio = object(parent::PROJECT_CLZY)->conversion_ratio($config, $anteayer_sum, $yesterday_sum);

		printexit("上一天：" . $anteayer_sum, "今天：" . $yesterday_sum, $conversion_ratio);
		exit;
	}





	/**
	 * 事务
	 * 
	 * 1TESTWORK
	 * 
	 * 
	 */
	public function api_work($action = 0)
	{
		exit;

		/*$day_first = cmd(array( time() ), "time day_first");//获取当天的最初时间戳
		$yesterday_first = cmd(array( $day_first - 1 ), "time day_first");//昨天
		$anteayer_first = cmd(array( $yesterday_first - 1 ), "time day_first");//前天
		
		//获取及时的
		$yesterday_sum = object(parent::TABLE_ORDER)->find_timestamp_merchant_credit_rmb_all_sum($yesterday_first);
		$anteayer_sum = object(parent::TABLE_ORDER)->find_timestamp_merchant_credit_rmb_all_sum($anteayer_first);*/

		$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("user_credit_conversion_user_money_share"), true);
		//获得转换率
		$conversion_ratio = object(parent::PROJECT_CLZY)->conversion_ratio($config, '9600', '22400');

		printexit($conversion_ratio);

		exit;
		$start_event = object(parent::PROJECT_CLZY)->get_event_url("parent_recommend_user_credit", "25c006ce0f2e9e6999ad9515541055574907");
		//$bool = object(parent::PLUGIN_HTTP_CURL)->request_get(array("url"=>$start_event['data'], "timeout"=>2));

		printexit($start_event);
		/*$start_event = object(parent::PROJECT_CLZY)->get_event_url("parent_recommend_user_credit", "bffb95cb1f6b35ff13c60615541056577878");
		$bool = object(parent::PROJECT_CLZY)->socket_event($start_event['data']);*/
		$bool = object(parent::PROJECT_CLZY)->socket_event("parent_recommend_user_credit", "e8c8b06344aac144b1847a15541042043239");
		printexit($bool);


		$bool = (bool)db(parent::DB_SYSTEM_ID)
			->table('shop_goods_sku')
			->where(array('shop_goods_sku_stock>=3'))
			->data(array('shop_goods_sku_stock = [-]', 'shop_goods_sku_stock - 3', true))
			->update(function ($p) {
				printexit($p);
			});



		printexit($bool);

		exit;
		//object(parent::TABLE_SHOP_GOODS_WHEN)->update_state_clear();

		object(parent::PLUGIN_EXCEL)->output("测试文件", "Test", function ($obj) {

			$obj->setActiveSheetIndex(0)
				->setCellValue('A1', '排序')
				->setCellValue('B1', '用户名称')
				->setCellValue('C1', '用户注册时间')
				->setCellValue('D1', '全部订单数')
				->setCellValue('E1', '已付款订单数')
				->setCellValue('F1', '未付款订单数')
				->setCellValue('G1', '全部订单总金额')
				->setCellValue('H1', '已付款订单总金额')
				->setCellValue('I1', '未付款订单总金额')
				->setCellValue('J1', '专属顾问');
		});



		exit;

		$data = object(parent::PROJECT_CLZY)->get_event_url("parent_recommend_user_credit", "abcdefg12345678");
		$s = object(parent::PLUGIN_HTTP_CURL)->request_get(array("url" => $data['data'], "timeout_ms" => 1));

		//file_get_contents($data['data']);
		printexit($s);

		$data = object(parent::PROJECT_CLZY)->start_event_parent_recommend_user_credit("abc123");
		printexit($data);

		$data = object(parent::TABLE_MERCHANT_USER)->select_all_user_id("fe99bedb1df73693fdedbd15513345586506");
		printexit($data);




		$DATA = object(parent::TABLE_USER)->find_join_parent("48a7da97d5155143168865");
		printexit($DATA);

		// 建立socket连接到内部推送端口
		$client = stream_socket_client('tcp://127.0.0.1:21', $errno, $errmsg, 1);
		// 推送的数据，包含uid字段，表示是给这个uid推送
		$data = array('uid' => 'uid1', 'percent' => '88%');
		// 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
		fwrite($client, json_encode($data) . "\n");
		// 读取推送结果
		echo fread($client, 8192);
		exit;


		$DATA = object(parent::TABLE_USER_IDENTITY)->update_state_clear();
		printexit($DATA);

		/*$DATA = object(parent::TABLE_SHOP_ORDER_GOODS)->insert_batch(array(
			array("shop_order_goods_id"=>"1234","shop_order_goods_time"=>time()),
			array("shop_order_goods_id"=>"abc","shop_order_goods_time"=>time())
		));*/

		/*$DATA = object(parent::TABLE_USER_CREDIT)->find_now_all_sum();
		
		printexit($DATA);*/


		/*$a = array();
		foreach($a as $v){
		
		};
		
		printexit("没有报错");
		
		
		$DATA = object(parent::TABLE_APPLICATION)->get_table_list();
		
		printexit($DATA);*/
		//
		/*$config_value = array(
			"mch_id" => "1275430601",
			"pay_key" => "DZ7bc8vNre6PCsRN8EMnxQ4uNVxpDLtj",
			"spbill_create_ip" => "192.168.0.2",
			"ssl_cert" => "-----BEGIN CERTIFICATE-----
MIIEbDCCA9WgAwIBAgIEAO2z2zANBgkqhkiG9w0BAQUFADCBijELMAkGA1UEBhMC
Q04xEjAQBgNVBAgTCUd1YW5nZG9uZzERMA8GA1UEBxMIU2hlbnpoZW4xEDAOBgNV
BAoTB1RlbmNlbnQxDDAKBgNVBAsTA1dYRzETMBEGA1UEAxMKTW1wYXltY2hDQTEf
MB0GCSqGSIb3DQEJARYQbW1wYXltY2hAdGVuY2VudDAeFw0xNzA0MzAwNDA1MjBa
Fw0yNzA0MjgwNDA1MjBaMIGbMQswCQYDVQQGEwJDTjESMBAGA1UECBMJR3Vhbmdk
b25nMREwDwYDVQQHEwhTaGVuemhlbjEQMA4GA1UEChMHVGVuY2VudDEOMAwGA1UE
CxMFTU1QYXkxMDAuBgNVBAMUJ+e7temYs+W4guS8mOeLkOe9kee7nOenkeaKgOac
iemZkOWFrOWPuDERMA8GA1UEBBMIMTA2NTQ4MDgwggEiMA0GCSqGSIb3DQEBAQUA
A4IBDwAwggEKAoIBAQDF86BnZWkUd5CnstkgqkNOCB5TH28ODj7Yaog15OjaVPSK
iTgX9LLhtSpNU84pf8afcv4+FyRhEHi1P4SDrZFPIDJbAyQZ5bGe5oqAdy0wJeRk
ElPgVOgz28GFTZyBRu1MGFgOP5E11SKgTNn6+lUMuySi48DIr9GU/NGNY7ksfN4W
oaU9UV2vtiHbnMxIeGB+dH4GsWdALkDt0FuWm6U+sLajdf5DpBeEStV3uLstatCl
zZkymQ2pOnVOOnWwBjDnSeuKByJNbYDdIyWUSyjiw7qKBGEmg6thxiVMih4kfmFC
A3njSh7MbA5FtdlV/QlJ5y8qXa8A1CY5qOha/IFjAgMBAAGjggFGMIIBQjAJBgNV
HRMEAjAAMCwGCWCGSAGG+EIBDQQfFh0iQ0VTLUNBIEdlbmVyYXRlIENlcnRpZmlj
YXRlIjAdBgNVHQ4EFgQUX/qgC7VnQWacJgF4mP47AizRvjcwgb8GA1UdIwSBtzCB
tIAUPgUm9iJitBVbiM1kfrDUYqflhnShgZCkgY0wgYoxCzAJBgNVBAYTAkNOMRIw
EAYDVQQIEwlHdWFuZ2RvbmcxETAPBgNVBAcTCFNoZW56aGVuMRAwDgYDVQQKEwdU
ZW5jZW50MQwwCgYDVQQLEwNXWEcxEzARBgNVBAMTCk1tcGF5bWNoQ0ExHzAdBgkq
hkiG9w0BCQEWEG1tcGF5bWNoQHRlbmNlbnSCCQC7VJcrvADoVzAOBgNVHQ8BAf8E
BAMCBsAwFgYDVR0lAQH/BAwwCgYIKwYBBQUHAwIwDQYJKoZIhvcNAQEFBQADgYEA
Bj8BcD7RHaq3CqU7Pxg/J1d1a67iRzEanMNCzvQR3UdDC0BcfmP3zN8z0hHLgUHD
JwOXPXb46BYw4Gr6+lUINQw3kSf9cqfd0++GZ9xlv4sbGdaovR9EKXkWNEGmWP/x
/QmLLmgwu1MwDQXLz60fbZQAZoUs4yGIUywIbeu92Z0=
-----END CERTIFICATE-----",
			"ssl_key" => "-----BEGIN PRIVATE KEY-----
MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDF86BnZWkUd5Cn
stkgqkNOCB5TH28ODj7Yaog15OjaVPSKiTgX9LLhtSpNU84pf8afcv4+FyRhEHi1
P4SDrZFPIDJbAyQZ5bGe5oqAdy0wJeRkElPgVOgz28GFTZyBRu1MGFgOP5E11SKg
TNn6+lUMuySi48DIr9GU/NGNY7ksfN4WoaU9UV2vtiHbnMxIeGB+dH4GsWdALkDt
0FuWm6U+sLajdf5DpBeEStV3uLstatClzZkymQ2pOnVOOnWwBjDnSeuKByJNbYDd
IyWUSyjiw7qKBGEmg6thxiVMih4kfmFCA3njSh7MbA5FtdlV/QlJ5y8qXa8A1CY5
qOha/IFjAgMBAAECggEANCwe9cFzrZJJzPlFYuedL57SJ0Rcp863X6DMX7ACczD6
9txtrVqwHu75xEG3T5a+yd2sBr9gtjh6KAMisPX5G2q3+ubcB/tTEjde/7bpcQw7
ouh1tOWMAccEvVaUTjpcZrbv1mmIozCWnLtEpHKAIgfdAxWWBQ7Z5TOxI1vKPWAB
FKYSC7k96yQRwmRxYpCLI9m4wNyDjkLP7uK94VMhFnnXKik/H7UKCRW0ntjMP0Uv
rJQiwZjw6ML1FP/uvOgenPmrxiTgTTCsTr61o5W8CGOU2cOtogZK1UhBZHeYOCLn
YuUE7pC0O5CGYe4WbXI+QSDqOqmHi2yJklsbWLDMwQKBgQDlxPLgFJXGlajfPQq2
lcHIsRYq2EvbBPNxKgDUZTPU5/8IAQkJFKmVA4IWpnEhOpMacL8TcPsRFQoPNLW4
SWBUNRodqnLjjKQXwOZhV06sXavqNEFdIiZb5/FLHIYCC9IgucI6yWsr8uFA2ljA
p1KgcWtzzAsNPzxgI9AUMEL5QwKBgQDcjMz6wRYml1yr8bKXEaRDlaEg04a5tHkw
hQjUL6gSHon/UakvceCXi/pLtfk6PBhzLRnL0XrakcpBjVuZ5IXx9mcauNe3vAPM
F6SFe4ZLyYbGEul8EfEET57V0czgkKzaG8pEfLdhSmBuNCYuE1z5p4NJBGxblJDl
oCjdmI9FYQKBgEk1LDFLPcFtE8Y0+8HbC0g3bBmwmtKozCvlNyh1KkOOu4pAUjGr
fLC20m8HDwqIUDBfdfHq0IPL0U2w/Kk/78pEtRJ4qWxo9it/Uaf4Gl/+5HSVu7HB
3LjxuMa2ytfCDmxQ41q5rETveOeh6h9P8JqgKJ1XiLnqyQDUyEp2ahBjAoGANBx8
rl8DGdk3x1TTishbVLC9IqF4Ota+r80vOduMzcMzfgVQgWpZ09T6LftwSOc8K7Kj
Xro/WfeKn5SD6UfKslIzKGg2aC5fg1Cuye9W2v9h/dkoG+2tUgRyFjl9PC5S+TIJ
x8bkGXPWdxORVd+zXzXKWm1WIQlodykxnrJWX+ECgYBbiF8+9ylPEp1zG15ahfu9
ePUVNBbvyHpitgseVqatRYe321wkREjWwNcwf31ueERBww+n1diXDL1UrKocA2of
Db8Z45aarR/k3qglvpbz8zY8DS0uGLx9qabgZGrDM6HR8+e2rX4jWNFgfUzQsbdj
CLmzsUL70VIYUuI3VtXo+Q==
-----END PRIVATE KEY-----",
		);
		
		
		
		$bool = object(parent::TABLE_CONFIG)->update(array(
			array("config_id=[+]", "weixin_pay_access")
		), array(
			"config_value" => cmd(array($config_value), "json encode")
		));
		
		printexit($bool);*/
		/*$day_first = cmd(array( time() ), "time day_first");
		$yesterday_first = cmd(array( $day_first - 1 ), "time day_first");//昨天
		$anteayer_first = cmd(array( $yesterday_first - 1 ), "time day_first");//前天
		
		$yesterday_sum = object(parent::TABLE_ORDER)->find_timestamp_merchant_credit_all_sum($yesterday_first);
		$anteayer_sum = object(parent::TABLE_ORDER)->find_timestamp_merchant_credit_all_sum($anteayer_first);
		
		$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("project_clzy"), true);
		$data = object(parent::PROJECT_CLZY)->conversion_rate($config, $anteayer, $yesterday);
		printexit($data);*/

		/*$day_first = cmd(array( time() ), "time day_first");
		$day_first2 = cmd(array( $day_first ), "time day_first");
		
		$day_first3 = cmd(array( $day_first - 1 ), "time day_first");
		
		printexit( $day_first, $day_first2, $day_first3 );
		
		
		
		$application = object(parent::MAIN)->api_application();
		$notify_url = http(function($http) use ($application){
			$http["path"] = array();
			$http["path"][] = "index.php";
			$http["path"][] = "temp";
			$http["path"][] = "application";
			$http["path"][] = $application["application_id"];
			$http["path"][] = "data";
			$http["path"][] = "PROJECTLCZYEVENTSYSTEMTRANSITION";
		 * $http["path"] = array()
			return http($http);
		});
		
		
		printexit( $notify_url );
		
		
		$bool = object(parent::TABLE_EVENT)->clzy_system_transition();*/
		printexit($bool);


		$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("project_clzy"), true);

		$yesterday = object(parent::TABLE_ORDER)->find_yesterday_merchant_credit_all_sum();
		$day = object(parent::TABLE_ORDER)->find_day_merchant_credit_all_sum();

		$data2 = object(parent::PROJECT_CLZY)->conversion_rate($config, $yesterday, $day);



		printexit($config, array($yesterday, $day), $data2);


		/*$config = array(
			"data" => "这是测试",
			"level" => "H",
			"size" => 6,
			"margin" => 1,
		);
		object(parent::PLUGIN_PHPQRCODE)->output($config);*/



		/*db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");	
		if($action == 1){
			$lock_id = object(parent::TABLE_LOCK)->start("merchant_id", "111111111111", "merchant_credit");
			sleep(20);
			db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
		}else{
			$lock_id = object(parent::TABLE_LOCK)->start("merchant_id", "222222222222", "merchant_credit");
			sleep(10);
			db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
		}*/


		//$lock_id = object(parent::TABLE_LOCK)->start("merchant_id", "XXXXXXSSSSSSSSSS23333", "merchant_credit");
		//sleep(20);
		//db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
		//db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	}






	const TEST_ARRAY = array(
		"user",
		__CLASS__
	);


	/**
	 * 锁测试
	 * 
	 * 1TESTLOCK
	 * 
	 * @param	void
	 * @return	bool
	 */
	public function api_lock()
	{

		printexit(self::TEST_ARRAY, basename(str_replace('\\', '/', "tset2")), basename(str_replace('\\', '/', __CLASS__)), __METHOD__, __NAMESPACE__, __FUNCTION__);

		$lock_id = object(parent::TABLE_LOCK)->start("merchant_id", "XXXXXX", "merchant_credit");
		$lock_id2 = object(parent::TABLE_LOCK)->start("merchant_id", "XXXXXX", "merchant_credit2");
		$lock_id3 = object(parent::TABLE_LOCK)->start("merchant_id", "XXXXXX", "merchant_credit3");
		$lock_id4 = object(parent::TABLE_LOCK)->start("merchant_id", "XXXXXX", "merchant_credit4");
		sleep(10);
		$bool = object(parent::TABLE_LOCK)->close($lock_id); //关闭
		$bool24 = object(parent::TABLE_LOCK)->close(array($lock_id2, $lock_id4)); //关闭

		printexit(array($lock_id, $lock_id2, $lock_id3, $lock_id4), $bool, $bool24);
	}
}