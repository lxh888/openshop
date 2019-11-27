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



namespace eapie\source\request\user;
use eapie\main;
use eapie\error;
class trade_detail extends \eapie\source\request\user {

	//交易明细
	
	
	
	/**
	 * 查询用户交易明细数据列表
	 * USERTRADEDETAIL
	 * 
	 * {"class":"user/trade_detail","method":"api"}
	 * 
	 * [{"type":"交易类型","method":"交易方式，如：weixinpay|alipay|merchant_credit|merchant_money|user_credit|user_money|user_money_annuity|user_money_earning|user_money_help|user_money_service|user_money_share","merchant":"1包括商家用户数据|0不包括商家用户数据，默认0"}]
	 * 
	 * @param [arr] $array [请求参数]
     * @return array
	 */
	public function api($input = array()){
		//检测登录
        object(parent::REQUEST_USER)->check();
		
		//查询配置
        $config = array(
            'where' => array(),
            'order' => array(),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
        );
		
		//字段
        $config['select'] = array('o.*');
		
		//排序
        $config['orderby'][] = array('o.order_pay_time', true);
        $config['orderby'][] = array('o.order_insert_time', true);
        $config['orderby'][] = array('o.order_id', false);
		
		//条件
        $config['where'][] = array('o.order_state = 1');
        $config['where'][] = array('[and] o.order_pay_state = 1');
		//用户收入，支出方式
        $user_method = array(
            parent::PAY_METHOD_WEIXINPAY,
            parent::PAY_METHOD_ALIPAY,
            parent::PAY_METHOD_USER_CREDIT,
            parent::PAY_METHOD_USER_MONEY,
            parent::PAY_METHOD_USER_MONEY_ANNUITY,
            parent::PAY_METHOD_USER_MONEY_EARNING,
            parent::PAY_METHOD_USER_MONEY_HELP,
            parent::PAY_METHOD_USER_MONEY_SERVICE,
            parent::PAY_METHOD_USER_MONEY_SHARE,
        );
		
		
		//商家收入，支出方式
        $merchant_method = array(
            parent::PAY_METHOD_MERCHANT_CREDIT,
            parent::PAY_METHOD_MERCHANT_MONEY
        );
		
		// 是否指定交易类型
        if( !empty($input['type']) && is_string($input['type']) ){
            $config['where'][] = array('[and] o.order_type = [+]', $input['type']);
        }

        // 是否指定交易方式
        if( !empty($input['method']) && in_array($input['method'], $user_method) ){
            $user_method = array($input['method']);
        }
		
		
		if( !empty($input['merchant']) ){
			//要查询商家用户
            $mch_ids = object(parent::TABLE_MERCHANT_USER)->get_mch_ids($_SESSION['user_id']);//查询用户所属的商家ID
		}
		
		//用户收入、支出
	   	$user_method_in = "'".implode("','", $user_method)."'";
			
		//判断是否存在商家数据
		if( !empty($mch_ids) ){
			//商家收入、商家支出
	        $merchant_method_in = "'".implode("','", $merchant_method)."'";
			$merchant_ids_in = "'".implode("','", $mch_ids)."'";
			
			//当需要查询商家用户的交易信息，那么操作用户是用户ID
			$config['where'][] = array('[and] (((o.order_action_user_id="'.$_SESSION['user_id'].'" AND (o.order_plus_method in('.$user_method_in.') AND o.order_plus_account_id in('.$merchant_ids_in.') )) OR (o.order_minus_method in('.$user_method_in.') AND o.order_minus_account_id in('.$merchant_ids_in.')) ) OR ((o.order_plus_method in('.$user_method_in.') AND o.order_plus_account_id="'.$_SESSION['user_id'].'") OR (o.order_minus_method in('.$user_method_in.') AND o.order_minus_account_id="'.$_SESSION['user_id'].'")))');
		}else{
	        $config['where'][] = array('[and] ((o.order_plus_method in('.$user_method_in.') AND o.order_plus_account_id="'.$_SESSION['user_id'].'") OR (o.order_minus_method in('.$user_method_in.') AND o.order_minus_account_id="'.$_SESSION['user_id'].'"))');
		}
		
		
		//查询数据
        $list_data = object(parent::TABLE_ORDER)->select_page($config);
		if( empty($list_data['data']) ){
			return $list_data;
		}
			
		$type_list = object(parent::TABLE_ORDER)->get_type();
		$method_list = object(parent::TABLE_ORDER)->get_method();
		$data = array();
		foreach($list_data['data'] as $key => $value){
			$temp_data = array();
			
			$temp_data['comment'] = $value["order_comment"];
			$temp_data['time'] = date('Y-m-d H:i', $value['order_pay_time']);
			$temp_data['timestamp'] = $value['order_pay_time'];
			//获得交易类型
			$temp_data['type'] = $value["order_type"];
			if( isset($type_list[$value["order_type"]]) ){
				$temp_data["type_name"] = $type_list[$value["order_type"]];
			}else{
				$temp_data["type_name"] = '';
			}
			
			//如果是商家
			if( !empty($mch_ids) && $value["order_action_user_id"] == $_SESSION['user_id']){
				//如果是增加
				if( in_array($value["order_plus_account_id"], $mch_ids) ){
					$temp_son_data = $temp_data;
					$temp_son_data['value'] = $value['order_plus_value'];
					$temp_son_data['sign'] = '+';//交易符号+，-
					$temp_son_data['merchant_id'] = $value["order_plus_account_id"];//是否是商家
					$temp_son_data['transaction_id'] = $value['order_plus_transaction_id'];
					$temp_son_data['method'] = $value["order_plus_method"];
					if( isset($method_list[$value["order_plus_method"]]) ){
						$temp_son_data["method_name"] = $method_list[$value["order_plus_method"]];
					}else{
						$temp_son_data["method_name"] = '';
					}
					//搜集
					$data[] = $temp_son_data;
				}
				
				//如果是减少
				if( in_array($value["order_minus_account_id"], $mch_ids) ){
					$temp_son_data = $temp_data;
					$temp_son_data['value'] = $value['order_minus_value'];
					$temp_son_data['sign'] = '-';//交易符号+，-
					$temp_son_data['merchant_id'] = $value["order_minus_account_id"];//是否是商家
					$temp_son_data['transaction_id'] = $value['order_minus_transaction_id'];
					$temp_son_data['method'] = $value["order_minus_method"];
					if( isset($method_list[$value["order_minus_method"]]) ){
						$temp_son_data["method_name"] = $method_list[$value["order_minus_method"]];
					}else{
						$temp_son_data["method_name"] = '';
					}
					//搜集
					$data[] = $temp_son_data;
				}
				
			}
			
			//如果是用户
			if( $value["order_plus_account_id"] == $_SESSION['user_id'] ){
				$temp_son_data = $temp_data;
				$temp_son_data['value'] = $value['order_plus_value'];
				$temp_son_data['sign'] = '+';//交易符号+，-
				$temp_son_data['merchant_id'] = '';//是否是商家
				$temp_son_data['transaction_id'] = $value['order_plus_transaction_id'];
				$temp_son_data['method'] = $value["order_plus_method"];
				if( isset($method_list[$value["order_plus_method"]]) ){
					$temp_son_data["method_name"] = $method_list[$value["order_plus_method"]];
				}else{
					$temp_son_data["method_name"] = '';
				}
				//搜集
				$data[] = $temp_son_data;
			}
			
			//如果是用户
			if( $value["order_minus_account_id"] == $_SESSION['user_id'] ){
				$temp_son_data = $temp_data;
				$temp_son_data['value'] = $value['order_minus_value'];
				$temp_son_data['sign'] = '-';//交易符号+，-
				$temp_son_data['merchant_id'] = '';//是否是商家
				$temp_son_data['transaction_id'] = $value['order_minus_transaction_id'];
				$temp_son_data['method'] = $value["order_minus_method"];
				if( isset($method_list[$value["order_minus_method"]]) ){
					$temp_son_data["method_name"] = $method_list[$value["order_minus_method"]];
				}else{
					$temp_son_data["method_name"] = '';
				}
				//搜集
				$data[] = $temp_son_data;
			}
			
		}

		//重新赋值
		$list_data['data'] = $data;

		return $list_data;
		
	}








    /**
     * 查询用户交易明细数据列表
     *
     * api: [旧] USERSELFTRADEDETAILLIST
     * api: USERTRADEDETAILSELFLIST
     * req: {
     *  type        [str] [可选] [交易类型]
     *  method      [str] [可选] [交易方式]
     * }
     *
     * 若商家ID合法，用户合法，同时查询商家交易明细
     *
     * @param [arr] $array [请求参数]
     * @return array
     */
    public function api_self_list($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //查询配置
        $config = array(
            'where' => array(),
            'order' => array(),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
        );

        //字段
        $config['select'] = array(
            'o.order_type AS type',
            'o.order_comment AS comment',
            'o.order_plus_method',
            'o.order_plus_account_id',
            'o.order_plus_value',
            'o.order_minus_method',
            'o.order_minus_account_id',
            'o.order_minus_value',
            'o.order_insert_time AS time',

            'o.order_id',
            'o.order_plus_transaction_id',
            'o.order_minus_transaction_id',
            'plus_uc.user_credit_value as plus_user_credit_value',
            'minus_uc.user_credit_value as minus_user_credit_value',
        );

        //条件
        $config['where'][] = array('order_state = 1');
        $config['where'][] = array('[and] order_pay_state = 1');

        //排序
        $config['orderby'][] = array('order_pay_time', true);
        $config['orderby'][] = array('order_insert_time', true);
        $config['orderby'][] = array('order_id', false);

        //——用户——
        $user_id = $_SESSION['user_id'];
        //用户收入，支出方式
        $user_method = array(
            'weixinpay',
            'alipay',
            'user_credit',
            'user_money',
            'user_money_annuity',
            'user_money_earning',
            'user_money_help',
            'user_money_service',
            'user_money_share',
        );

        // 是否指定交易类型
        if (!empty($input['type']) && is_string($input['type'])) {
            $config['where'][] = array('[and] order_type = [+]', $input['type']);
        }

        // 是否指定交易方式
        if (!empty($input['method']) && in_array($input['method'], $user_method)) {
            $user_method = array($input['method']);
        }

        $user_method_str = "'".implode("','", $user_method)."'";
        //用户收入
        $config['where_user_plus'][] = array('order_plus_method in([-])', $user_method_str, true);
        $config['where_user_plus'][] = array('[and] order_plus_account_id=[+]', $user_id);
		//$config['where_user_plus'][] = array('[and] order_state=1');
		//$config['where_user_plus'][] = array('[and] order_pay_state=1');
		
        //用户支出
        $config['where_user_minus'][] = array('[or] order_minus_method in([-])', $user_method_str, true);
        $config['where_user_minus'][] = array('[and] order_minus_account_id=[+]', $user_id);
		//$config['where_user_minus'][] = array('[and] order_state=1');
		//$config['where_user_minus'][] = array('[and] order_pay_state=1');

        //是否查询商家交易明细
        if (empty($input['method'])) {
            //查询用户所属的商家ID
            $mch_ids = object(parent::TABLE_MERCHANT_USER)->get_mch_ids($user_id);
        } else {
            $mch_ids = null;
        }

        //拼接商家where查询条件
        //商家收入，支出方式
        $mch_method = array(
            'merchant_credit',
            'merchant_money'
        );
        $mch_method_str = "'".implode("','", $mch_method)."'";
        if ($mch_ids) {
            $mch_ids_str = "'".implode("','", $mch_ids)."'";
            //商家支出
            $config['where_mch_minus'][] = array('[or] order_minus_method in([-])', $mch_method_str, true);
            $config['where_mch_minus'][] = array('[and] order_minus_account_id in([-])', $mch_ids_str, true);
			//$config['where_mch_minus'][] = array('[and] order_state=1');
			//$config['where_mch_minus'][] = array('[and] order_pay_state=1');
			
            //商家收入
            $config['where_mch_plus'][] = array('[or] order_plus_method in([-])', $mch_method_str, true);
            $config['where_mch_plus'][] = array('[and] order_plus_account_id in([-])', $mch_ids_str, true);
			//$config['where_mch_plus'][] = array('[and] order_state=1');
			//$config['where_mch_plus'][] = array('[and] order_pay_state=1');
        }

        //查询数据
        $data = object(parent::TABLE_ORDER)->select_trade_detail_page($config);

        //格式化数据
        $output = array();
        foreach ($data['data'] as &$v) {
            $trade = 0;             //交易
            $sign = '+';            //交易符号+，-
            $is_merchant = false;   //是否是商家

            //增加和减少是否同一身份
            if ($v['order_plus_account_id'] === $v['order_minus_account_id']) {
                array_push($output, array(
                    'time'   => date('Y-m-d H:i:s', $v['time']),
                    'type'   => $this->_get_trade_type($v['type']),
                    'method' => $this->_get_trade_method($v['order_plus_method']),
                    'comment'=> $v['comment'],
                    'trade'  => $v['order_plus_value'],
                    'sign'   => '+',
                    'is_merchant' => false,
                ));
                array_push($output, array(
                    'time'   => date('Y-m-d H:i:s', $v['time']),
                    'type'   => $this->_get_trade_type($v['type']),
                    'method' => $this->_get_trade_method($v['order_minus_method']),
                    'comment'=> $v['comment'],
                    'trade'  => $v['order_minus_value'],
                    'sign'   => '-',
                    'is_merchant' => false,
                ));
                continue;
            }

            //
            if (
                (
                	!empty($user_method)
					&& is_array($user_method)
                    && in_array($v['order_plus_method'], $user_method)
                    && $v['order_plus_account_id'] === $user_id
                    && !empty($mch_method)
					&& is_array($mch_method)
                    && in_array($v['order_minus_method'], $mch_method)
					&& !empty($mch_ids)
					&& is_array($mch_ids)
                    && in_array($v['order_plus_account_id'], $mch_ids)
                )
                ||
                (
                	!empty($mch_method)
					&& is_array($mch_method)
                    && in_array($v['order_plus_method'], $mch_method)
					&& !empty($mch_ids)
					&& is_array($mch_ids)
                    && in_array($v['order_plus_account_id'], $mch_ids)
					&& !empty($user_method)
					&& is_array($user_method)
                    && in_array($v['order_minus_method'], $user_method)
                    && $v['order_minus_account_id'] === $user_id
                )

            ) {
                array_push($output, array(
                    'time'   => date('Y-m-d H:i:s', $v['time']),
                    'type'   => $this->_get_trade_type($v['type']),
                    'method' => $this->_get_trade_method($v['order_plus_method']),
                    'comment'=> $v['comment'],
                    'trade'  => $v['order_plus_value'],
                    'sign'   => '+',
                    'is_merchant' => false,
                ));
                array_push($output, array(
                    'time'   => date('Y-m-d H:i:s', $v['time']),
                    'type'   => $this->_get_trade_type($v['type']),
                    'method' => $this->_get_trade_method($v['order_minus_method']),
                    'comment'=> $v['comment'],
                    'trade'  => $v['order_minus_value'],
                    'sign'   => '-',
                    'is_merchant' => false,
                ));
                continue;
            }

            //用户数据
            if (in_array($v['order_plus_method'], $user_method) && $v['order_plus_account_id'] === $user_id) {
                $trade = $v['order_plus_value'];
                $sign  = '+';//增加
            } elseif (in_array($v['order_minus_method'], $user_method) && $v['order_minus_account_id'] === $user_id) {
                $trade = $v['order_minus_value'];
                $sign  = '-';//减少
            } elseif ($mch_ids) {
                //商家数据
                $is_merchant = true;
                if (in_array($v['order_plus_method'], $mch_method) && in_array($v['order_plus_account_id'], $mch_ids)) {
                    $trade = $v['order_plus_value'];
                    $sign  = '+';//增加
                } elseif (in_array($v['order_minus_method'], $mch_method) && in_array($v['order_minus_account_id'], $mch_ids)) {
                    $trade = $v['order_minus_value'];
                    $sign  = '-';//减少
                }
            }

            //判断交易方式
            $method = ($sign === '+') ? $v['order_plus_method'] : $v['order_minus_method'];

            array_push($output, array(
                'type'      => $this->_get_trade_type($v['type']),
                'method'    => $this->_get_trade_method($method),
                'comment'   => $v['comment'],
                'trade'     => $trade,
                'sign'      => $sign,
                'time'      => date('Y-m-d H:i:s', $v['time']),
                'timestamp'   => $v['time'],
                'is_merchant' => $is_merchant,
            ));
        }

        $data['data'] = $output;
        return $data;
    }


    //===========================================
    // 私有方法
    //===========================================


    /**
     * 获取交易类型所对应的中文描述
     * 
     * @param  string $key 英文键
     * @return string
     */
    private function _get_trade_type($key = '')
    {
        $type = object(parent::TABLE_ORDER)->get_type();
        return array_key_exists($key, $type) ? $type[$key] : '未知';
    }


    /**
     * 获取交易方式所对应的中文描述
     * 
     * @param  string $key 英文键
     * @return string
     */
    private function _get_trade_method($key = '')
    {
        $method = object(parent::TABLE_ORDER)->get_method();
        return array_key_exists($key, $method) ? $method[$key] : '未知';
    }


    /**
     * 获取用户收入明细--列表
     * api: USERTRADEDETAILSELFINCOMELIST
     * {"class":"user/trade_detail","method":"api_self_income_list"}
     */
    public function api_self_income_list($data=array()){
        //检测登录
        object(parent::REQUEST_USER)->check();

        //查询配置
        $config = array(
            'where' => array(),
            'orderby' => array(),
            'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_USER),
        );
		
		//字段
        $config['select'] = array('o.*');
		
		//排序
        $config['orderby'][] = array('o.order_insert_time', true);
		
		//条件
        $config['where'][] = array('o.order_state = 1');
        $config['where'][] = array('[and] o.order_pay_state = 1');
        $config['where'][] = array('[and] o.order_plus_value > 0');
        $config['where'][] = array('[and] o.order_minus_value = 0');
		//用户收入，支出方式
        $user_method = array(
            parent::PAY_METHOD_WEIXINPAY,
            parent::PAY_METHOD_ALIPAY,
            parent::PAY_METHOD_USER_CREDIT,
            parent::PAY_METHOD_USER_MONEY,
            parent::PAY_METHOD_USER_MONEY_ANNUITY,
            parent::PAY_METHOD_USER_MONEY_EARNING,
            parent::PAY_METHOD_USER_MONEY_HELP,
            parent::PAY_METHOD_USER_MONEY_SERVICE,
            parent::PAY_METHOD_USER_MONEY_SHARE,
        );
		
		
		// //商家收入，支出方式
        // $merchant_method = array(
        //     parent::PAY_METHOD_MERCHANT_CREDIT,
        //     parent::PAY_METHOD_MERCHANT_MONEY
        // );
		
		// 是否指定交易类型
        if( !empty($data['type']) && is_string($data['type']) ){
            $config['where'][] = array('[and] o.order_type = [+]', $data['type']);
        }

        // 是否指定交易方式
        if( !empty($data['method']) && in_array($data['method'], $user_method) ){
            $user_method = array($data['method']);
        }
		
		
		// if( !empty($data['merchant']) ){
		// 	//要查询商家用户
        //     $mch_ids = object(parent::TABLE_MERCHANT_USER)->get_mch_ids($_SESSION['user_id']);//查询用户所属的商家ID
		// }
		
		//用户收入、支出
	   	$user_method_in = "'".implode("','", $user_method)."'";
			
		// //判断是否存在商家数据
		// if( !empty($mch_ids) ){
		// 	//商家收入、商家支出
	    //     $merchant_method_in = "'".implode("','", $merchant_method)."'";
		// 	$merchant_ids_in = "'".implode("','", $mch_ids)."'";
			
		// 	//当需要查询商家用户的交易信息，那么操作用户是用户ID
		// 	$config['where'][] = array('[and] (((o.order_action_user_id="'.$_SESSION['user_id'].'" AND (o.order_plus_method in('.$user_method_in.') AND o.order_plus_account_id in('.$merchant_ids_in.') )) OR (o.order_minus_method in('.$user_method_in.') AND o.order_minus_account_id in('.$merchant_ids_in.')) ) OR ((o.order_plus_method in('.$user_method_in.') AND o.order_plus_account_id="'.$_SESSION['user_id'].'") OR (o.order_minus_method in('.$user_method_in.') AND o.order_minus_account_id="'.$_SESSION['user_id'].'")))');
		// }else{
	        $config['where'][] = array('[and] ((o.order_plus_method in('.$user_method_in.') AND o.order_plus_account_id="'.$_SESSION['user_id'].'") OR (o.order_minus_method in('.$user_method_in.') AND o.order_minus_account_id="'.$_SESSION['user_id'].'"))');
		// }
		
		
		//查询数据
        $list_data = object(parent::TABLE_ORDER)->select_page($config);
		if( empty($list_data['data']) ){
			return $list_data;
		}
			
        $type_list = object(parent::TABLE_ORDER)->get_type();
        $method_list = object(parent::TABLE_ORDER)->get_method();
        $data = array();
        
		foreach($list_data['data'] as $key => $value){
            $shop_order = array();
            if(!empty($value['order_sign'])){
                $shop_order = object(parent::TABLE_SHOP_ORDER)->find_details($value['order_sign']);
            }
            
			$temp_data = array();
			if(!empty($shop_order)){
                //用户
                $temp_data['user'] = isset($shop_order['user_compellation']) && !empty($shop_order['user_compellation'])?$shop_order['user_compellation']:
                isset($shop_order['user_nickname']) && !empty($shop_order['user_nickname'])?$shop_order['user_nickname'] : '匿名用户';

                $temp_data['goods'] = !empty($shop_order['shop_order_goods'][0]['shop_order_goods_name'])?$shop_order['shop_order_goods'][0]['shop_order_goods_name']:'未知商品';
            }
			$temp_data['comment'] = $value["order_comment"];
			$temp_data['time'] = date('Y-m-d H:i', $value['order_pay_time']);
			$temp_data['timestamp'] = $value['order_pay_time'];
			//获得交易类型
			$temp_data['type'] = $value["order_type"];
			if( isset($type_list[$value["order_type"]]) ){
				$temp_data["type_name"] = $type_list[$value["order_type"]];
			}else{
				$temp_data["type_name"] = '';
			}
			
			//如果是用户
			if( $value["order_plus_account_id"] == $_SESSION['user_id'] ){
				$temp_son_data = $temp_data;
				$temp_son_data['value'] = $value['order_plus_value'];
				$temp_son_data['sign'] = '+';//交易符号+，-
				$temp_son_data['merchant_id'] = '';//是否是商家
				$temp_son_data['transaction_id'] = $value['order_plus_transaction_id'];
				$temp_son_data['method'] = $value["order_plus_method"];
				if( isset($method_list[$value["order_plus_method"]]) ){
					$temp_son_data["method_name"] = $method_list[$value["order_plus_method"]];
				}else{
					$temp_son_data["method_name"] = '';
				}
				//搜集
				$data[] = $temp_son_data;
			}
			
		}

		//重新赋值
		$list_data['data'] = $data;

		return $list_data;
		
    }

}