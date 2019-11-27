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



namespace eapie\source\request\express;
use eapie\error;
class admin_order extends \eapie\source\request\express {
	
    /**
     * 后台订单管理
     */

     
    	
	/**
	 * 获取订单详情
	 * 
	 * EXPRESSADMINORDERDETAILS
	 * {"class":"express/admin_order","method":"api_details"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_details($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_DETAILS_READ);
		object(parent::ERROR)->check($data, 'express_order_id', parent::TABLE_EXPRESS_ORDER, array('args'));
		
		return object(parent::TABLE_EXPRESS_ORDER)->find_details($data["express_order_id"]);
	}
	
	 
     
     
    /**
	 * 逻辑回收订单
	 * 
	 * SHOPADMINORDERTRASH
	 * {"class":"express/admin_order","method":"api_trash"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_trash($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_TRASH);
		object(parent::ERROR)->check($data, 'express_order_id', parent::TABLE_EXPRESS_ORDER, array('args'));
		
		//获取旧数据
		$old_data = object(parent::TABLE_EXPRESS_ORDER)->find($data['express_order_id']);
		if( empty($old_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		if( !empty($old_data["express_order_trash"]) ){
			throw new error("该订单已经在回收站");
		}
		
		//更新回收状态
		$update_data["express_order_trash"] = 1;
		$update_data['express_order_trash_time'] = time();
		if( object(parent::TABLE_EXPRESS_ORDER)->update( array(array('express_order_id=[+]', (string)$data['express_order_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['shop_order_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
    
     
	 
		
	/**
	 * 获取数据列表
	 * 
	 * EXPRESSADMINORDERLIST
	 * {"class":"express/admin_order","method":"api_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'id_desc' => array('order_id', true),
			'id_asc' => array('order_id', false),
			
			'user_id_desc' => array('user_id', true),
			'user_id_asc' => array('user_id', false),
			'user_nickname_desc' => array('user_nickname', true),
			'user_nickname_asc' => array('user_nickname', false),
			'user_phone_verify_list_desc' => array('user_phone_verify_list', true),
			'user_phone_verify_list_asc' => array('user_phone_verify_list', false),
			
			'update_time_desc' => array('express_order_update_time', true),
			'update_time_asc' => array('express_order_update_time', false),
			
			'insert_time_desc' => array('express_order_insert_time', true),
			'insert_time_asc' => array('express_order_insert_time', false),
			
			'rebate_money_desc' => array('express_order_rebate_money', true),
			'rebate_money_asc' => array('express_order_rebate_money', false),
			'money_desc' => array('express_order_money', true),
			'money_asc' => array('express_order_money', false),
			
			'pay_money_desc' => array('express_order_pay_money', true),
			'pay_money_asc' => array('express_order_pay_money', false),
			
			'state_desc' => array('express_order_state', true),
			'state_asc' => array('express_order_state', false),
			
			'pay_state_desc' => array('express_order_pay_state', true),
			'pay_state_asc' => array('express_order_pay_state', false),
			
			'shipping_state_desc' => array('express_order_shipping_state', true),
			'shipping_state_asc' => array('express_order_shipping_state', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('eo.express_order_id', false);
		
		$config["where"][] = array('[and] eo.express_order_trash=0');
		
		if(!empty($data['search'])){
			if( isset($data['search']['express_order_id']) && is_string($data['search']['express_order_id']) ){
				$config["where"][] = array('[and] eo.express_order_id=[+]', $data['search']['express_order_id']);
			}
			
			if( isset($data['search']['express_order_shipping_no']) && is_string($data['search']['express_order_shipping_no']) ){
				$config["where"][] = array('[and] eo.express_order_shipping_no=[+]', $data['search']['express_order_shipping_no']);
			}
			if( isset($data['search']['express_rider_phone']) && is_string($data['search']['express_rider_phone']) ){
				$config["where"][] = array('[and] eo.express_rider_phone=[+]', $data['search']['express_rider_phone']);
			}
			
			//骑手检索
			if( isset($data['search']['rider_user_id']) && is_string($data['search']['rider_user_id']) ){
                $config['where'][] = array('[and] eo.express_rider_user_id=[+]', $data['search']['rider_user_id']);
            }
			if( isset($data['search']['rider_user_nickname']) && is_string($data['search']['rider_user_nickname']) ){
                $config['where'][] = array('[and] rider.user_nickname LIKE "%[-]%"', $data['search']['rider_user_nickname']);
            }
			if( isset($data['search']['rider_user_phone']) && is_string($data['search']['rider_user_phone']) ){
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['rider_user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] eo.express_rider_user_id=[+]', $user_id);
            }
			
			
			if( isset($data['search']['user_id']) && is_string($data['search']['user_id']) ){
                $config['where'][] = array('[and] eo.user_id=[+]', $data['search']['user_id']);
            }
			if( isset($data['search']['user_nickname']) && is_string($data['search']['user_nickname']) ){
                $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $data['search']['user_nickname']);
            }
			
			if( isset($data['search']['user_phone']) && is_string($data['search']['user_phone']) ){
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] eo.user_id=[+]', $user_id);
            }
			
			//订单状态
			if( isset($data['search']['state']) && 
			(is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
			in_array($data['search']['state'], array("0", "1", "2","3", "4")) ){
				$config["where"][] = array('[and] eo.express_order_state=[+]', $data['search']['state']);
			}
			
			//支付状态
			if( isset($data['search']['pay_state']) && 
			(is_string($data['search']['pay_state']) || is_numeric($data['search']['pay_state'])) &&
			in_array($data['search']['pay_state'], array("0", "1")) ){
				$config["where"][] = array('[and] eo.express_order_pay_state=[+]', $data['search']['pay_state']);
			}
			
			//发货状态
			if( isset($data['search']['shipping_state']) && 
			(is_string($data['search']['shipping_state']) || is_numeric($data['search']['shipping_state'])) &&
			in_array($data['search']['shipping_state'], array("0", "1", "2")) ){
				$config["where"][] = array('[and] eo.express_order_shipping_state=[+]', $data['search']['shipping_state']);
			}
			
			
		}
		
		return object(parent::TABLE_EXPRESS_ORDER)->select_page($config);
	}
	
	
	
	
	
	/**
	 * 确认发货/确认收货
	 * 
	 * EXPRESSADMINORDERSHIPPING
	 * {"class":"express/admin_order","method":"api_shipping"}
	 * 
	 * @param	array	$data
	 */
	public function api_shipping( $data = array() ){
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_SHIPPING); //需要定义常量
		object(parent::ERROR)->check($data, 'express_order_id', parent::TABLE_EXPRESS_ORDER, array('args'));
		object(parent::ERROR)->check($data, 'express_order_shipping_state', parent::TABLE_EXPRESS_ORDER, array('args'));
		
		$old_data = object(parent::TABLE_EXPRESS_ORDER)->find($data['express_order_id']);
		if( empty($old_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		if( empty($old_data['express_order_state']) ){
			throw new error("该订单已取消");
		}
		
		if( empty($old_data['express_order_pay_state']) ){
			throw new error("该订单未支付");
		}
		
		$update_data = array();
		$update_data['express_order_shipping_state'] = $data['express_order_shipping_state'];
		
		$update_where = array();
		$update_where[] = array('express_order_id=[+]', (string)$data['express_order_id']);
		
		//状态。0未发货，等待发货；1确认收货; 2已发货，运送中
		if( $data['express_order_shipping_state'] == 2 ){
			if( $old_data['express_order_shipping_state'] != 0 ){
				throw new error("该订单已发货，请勿重复操作");
			}
			object(parent::ERROR)->check($data, 'express_order_shipping_no', parent::TABLE_EXPRESS_ORDER, array('args'));
			
			$update_data['express_order_shipping_no'] = $data['express_order_shipping_no'];
			$update_data['express_order_shipping_send_time'] = time();//确认发货时间
			$update_where[] = array('[and] express_order_shipping_state=0');
			
			if( !object(parent::REQUEST_APPLICATION)->kuaidi100_subscription_push($data['express_order_shipping_no'], $old_data['shipping_sign']) ){
				throw new error("该订单的快递服务平台订阅失败");
			}
			
			
		}else
		if( $data['express_order_shipping_state'] == 1 ){
			if( $old_data['express_order_shipping_state'] == 1 ){
				throw new error("该订单已收货，请勿重复操作");
			}
			if( $old_data['express_order_shipping_state'] != 2 ){
				throw new error("该订单未发货");
			}
			
			$update_data['express_order_shipping_take_time'] = time();//确认收货时间
			$update_where[] = array('[and] express_order_shipping_state=2');
		}else{
			throw new error("该订单配送状态异常");
		}
		
		
		$update_data["express_order_update_time"] = time();
		if( object(parent::TABLE_EXPRESS_ORDER)->update($update_where, $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['express_order_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	


    /**
     * 后台发货
     * Undocumented function
     * 
     * api: EXPRESSADMINORDERSENDGOODS
     * {"class":"express/admin_order","method":"api_self_send_goods"}
     * 
     * @return void
     */
   /* public function api_send_goods($input=array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_EXPRESS_ORDER_SEND); //需要定义常量
        
        //验证参数
        object(parent::ERROR)->check($input, 'express_order_id', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_id');
        object(parent::ERROR)->check($input, 'express_order_shipping_no', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_shipping_no');

        $where = array(
            array('express_order_id =[+]',$input['express_order_id'])
        );
        $express_order = object(parent::TABLE_EXPRESS_ORDER)->find_where($where);
        if(!$express_order){throw new error('订单参数错误');}

        if($express_order['express_order_shipping_state'] != 0 || $express_order['express_order_pay_state'] != 1){throw new error('订单状态异常');}

        $update_data = array(
            'express_order_shipping_no'=>$input['express_order_shipping_no']
        );

        db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
        $res = object(parent::TABLE_EXPRESS_ORDER)->update_one($express_order['express_order_id'],$update_data);
        if(!$res){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            throw new error('发货失败');
        }

        $express_order_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("express_order"), true);  

        $data = array(
            'conpany'=>$express_order['shipping_sign'],
            'number'=>$input['express_order_shipping_no'],
            'notify_url'=>"http://developer.eapie.eonfox.com/index.php/application/jiangyoukuaidi/data/APPLICATIONEXPRESSHUNDREDNOTIFYURL",
            'key'=>$express_order_config['key'],
            'customer'=>$express_order_config['customer']
        );

        $result = object(parent::PLUGIN_KUAIDI100)->express_hundred_curl($data);

        if(!$result || $result['errno'] != 0){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            throw new error('发货失败');
        }

        db(parent::DB_APPLICATION_ID)->query("COMMIT");//回滚
        return $express_order['express_order_id'];
    } */


    /**
     * 
     * 获取订单列表
     * Undocumented function
     * 
     * api: EXPRESSADMINORDEREXPRESSORDERLIST
     * {"class":"express/admin_order","method":"api_express_order_list"}
     *
     * @return void
     */
    /*public function api_express_order_list($input=array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_READ); //需要定义常量

        if(!isset($input['states']) || empty($input['states'])){throw new error('参数错误');}

        $config = array(
            'orderby'=>array(
                array('express_order_update_time',false),
            ),
            'where'=>array(
                array('express_order_trash =[+]',0),
            ),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
            'select'=>array()
        );

        switch($input['states']){
            case 1: //未支付
                array_push($config['where'],
                    array('express_order_pay_state = 0')
                );
                break;
            case 2: //已支付
                array_push($config['where'],
                    array('express_order_pay_state = 1')
                );
                break;
            case 3: //待发货    
                array_push($config['where'],
                    array('express_order_pay_state = 1'),
                    array('express_order_shipping_state = 0')
                );
                break;
            case 4: //已发货
                array_push($config['where'],
                    array('express_order_pay_state = 1'),
                    array('express_order_shipping_state = 2')
                );
                break;

            case 5: //已收货
                array_push($config['where'],
                    array('express_order_pay_state = 1'),
                    array('express_order_shipping_state = 1')
                );
                break;

            default:  //用户删除
                array_push($config['where'],
                    array('express_order_delete_state=1')
                );
                break;
        }

        $data = object(parent::TABLE_EXPRESS_ORDER)->select_page($config);
        return $data;
    }*/

    
	
    /**
     * 根据订单   设置抽奖
     * Undocumented function
     *
     * api: EXPRESSADMINORDERSETCOUPON
     * {"class":"express/admin_order","method":"api_set_coupon"}
	 * 
     * @param array $input
     * @return void
     */
    public function api_set_coupon($input=array())
    {

        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_SET); //需要定义常量

        //验证参数
        object(parent::ERROR)->check($input, 'express_order_id', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_id');
        
        $where = array(
            array('express_order_id =[+]',$input['express_order_id']),
            array('express_order_pay_money > 0',),
            array('express_order_trash = 0'),
            array('express_order_delete_state = 0'),
            array('express_order_state = 1')
        );
        $express_order = object(parent::TABLE_EXPRESS_ORDER)->find_where($where);
        if(empty($express_order)){throw new error('未找到相关订单');}

        $time = time();
        $user_info = object(parent::TABLE_USER)->find($express_order['user_id']);
        $coupon = array(
            'coupon_id'=>object(parent::TABLE_COUPON)->get_unique_id(),
            'coupon_name'=>'每日抽奖优惠券',
            'coupon_module'=>'express_coupon',
            'coupon_type'=>2,
            'coupon_limit_min'=>0,
            'coupon_limit_max'=>0,
            'coupon_discount'=>$express_order['express_order_pay_money'],
            'coupon_start_time'=>0,
            'coupon_end_time'=>0,
            'coupon_state'=>1,
            'coupon_insert_time'=>$time,
            'coupon_update_time'=>$time
        );

        $user_luck_draw_json = array(
            'user'=>$user_info,
            'coupon'=>$coupon,
            'user_coupon'=>'',
        );

        $luck_draw = array(
            'user_luck_draw_id'=>object(parent::TABLE_USER_LUCK_DRAW)->get_unique_id(),
            'user_id'=>$express_order['user_id'],
            'express_order_id'=>$express_order['express_order_id'],
            'user_luck_draw_json'=>$user_luck_draw_json,
            'user_luck_draw_insert_time'=>$time,
            'user_luck_draw_update_time'=>$time,
        );
        if(object(parent::TABLE_USER_LUCK_DRAW)->insert($luck_draw)){
            return $luck_draw['user_luck_draw_id'];
        }else{
            throw new error('操作失败');
        }

	}
	

	/**
	 * 导出
	 * Undocumented function
	 * 
	 * api:	EXPRESSADMINORDEREXPORT
	 * 
	 * {"class":"express/admin_order","method":"api_export"}
	 *
	 * @param array $input
	 * @return void
	 */
	public function api_export($input=array())
	{

		//检查权限
		// object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_EXPORT);	//验证快递单导出权限

		object(parent::ERROR)->check($input, 'express_order_when_start_time', parent::TABLE_EXPRESS_ORDER, array('args'));
		object(parent::ERROR)->check($input, 'express_order_when_end_time', parent::TABLE_EXPRESS_ORDER, array('args'));

		$start_time = cmd(array($input['express_order_when_start_time']), "time mktime");
		$end_time = cmd(array($input['express_order_when_end_time']), "time mktime");

		$config = array(
			'where' => array(
				array("[and] express_order_insert_time between []", $start_time),
				array("[and] []", $end_time),
				array("express_order_delete_state =[]",0),
				array("express_order_trash =[]",0)
			),
			'orderby' => array('express_order_insert_time',false),
			'select' => array(
				'eo.express_order_id AS order_id',
				'eo.express_order_money AS money',
				'eo.express_order_pay_money AS pay_money',
				'eo.user_address_name AS address_name',
				'eo.user_address_phone AS address_phone',
				'eo.user_address_province AS address_province',
				'eo.user_address_city AS address_city',
				'eo.user_address_district AS address_district',
				'eo.user_address_details AS address_details',
				'eo.express_order_get_name AS get_name',
				'eo.express_order_get_phone AS get_phone',
				'eo.express_order_get_province AS get_province',
				'eo.express_order_get_city AS get_city',
				'eo.express_order_get_district AS get_district',
				'eo.express_order_get_address AS get_details',
				"FROM_UNIXTIME(eo.express_order_insert_time,'%Y-%m-%d %T') AS insert_time",
				"u.user_name AS name",
				"rider.*",
			)
		);
		
		if(isset($input['state'])){
			switch($input['state']){
				case 0:	//未支付
					array_push($config['where'],array('express_order_pay_state = 0'));
					break;
				case 1:	//已支付
					array_push($config['where'],array('express_order_pay_state = 1'));
					break;
			}
		}else{
			array_push($config['where'],array('express_order_pay_state = 1'));
		}

		// return $config;
		$data = object(parent::TABLE_EXPRESS_ORDER)->select($config);
		return $data;
		

		object(parent::PLUGIN_EXCEL)->output("已取件订单导出", "Test", function ($obj) use($data) {
			$obj->setActiveSheetIndex(0)
				->setCellValue('A1', '订单号')
				->setCellValue('B1', '下单人')
				->setCellValue('C1', '寄件人')
				->setCellValue('D1', '寄件地址')
				->setCellValue('E1', '收货电话')
				->setCellValue('F1', '详细地址')
				->setCellValue('G1', '收货人')
				->setCellValue('H1', '收货地址')
				->setCellValue('I1', '收货电话')
				->setCellValue('J1', '详细地址')
				->setCellValue('K1', '订单金额')
				->setCellValue('L1', '订单金额')
				->setCellValue('M1', '时间')
				->setCellValue('N1', '接单人');
				// ->setCellValue('O1', '寄件地址')
				// ->setCellValue('P1', '收货电话')
				// ->setCellValue('Q1', '详细地址')
				// ->setCellValue('R1', '收货人')
				// ->setCellValue('S1', '收货地址')
				// ->setCellValue('T1', '收货电话')
				// ->setCellValue('U1', '详细地址')
				// ->setCellValue('V1', '寄件人')
				// ->setCellValue('W1', '寄件地址')
				// ->setCellValue('X1', '收货电话')
				// ->setCellValue('Y1', '详细地址')
				// ->setCellValue('Z1', '收货人');
			$i = 1;
			foreach($data as $k=>$v){
				$obj->setActiveSheetIndex($i)
					->setCellValue('A'.$i,$v['order_id'])
					->setCellValue('B'.$i,$v['order_id'])
					->setCellValue('C'.$i,$v['order_id'])
					->setCellValue('D'.$i,$v['order_id'])
					->setCellValue('E'.$i,$v['order_id'])
					->setCellValue('F'.$i,$v['order_id'])
					->setCellValue('G'.$i,$v['order_id'])
					->setCellValue('H'.$i,$v['order_id'])
					->setCellValue('I'.$i,$v['order_id'])
					->setCellValue('J'.$i,$v['order_id'])
					->setCellValue('K'.$i,$v['order_id'])
					->setCellValue('L'.$i,$v['order_id'])
					->setCellValue('M'.$i,$v['order_id'])
					->setCellValue('N'.$i,$v['order_id']);
					// ->setCellValue('O'.$i,$v['order_id'])
					// ->setCellValue('P'.$i,$v['order_id']);
				$i++;	
			}	
		});
		

		exit;

	}
}