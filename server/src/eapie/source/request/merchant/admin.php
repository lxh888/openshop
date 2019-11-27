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



namespace eapie\source\request\merchant;

use eapie\main;
use eapie\error;

class admin extends \eapie\source\request\merchant
{

    // 操作数据 =====================================

    /**
     * 商家——添加
     * 
     * api: MERCHANTADMINADD
     * req: {
     *  merchant_name           [str] [必填] [商户名称]
     *  merchant_info           [str] [必填] [商户简介]
     *  merchant_address        [str] [必填] [商户地址]
     *  merchant_longitude      [dec] [必填] [商户坐标经度]
     *  merchant_latitude       [dec] [必填] [商户坐标纬度]
     *  merchant_logo_image_id  [str] [可选] [商户logo图片ID]
     *  merchant_state          [int] [可选] [商户状态，0 审核失败，1已认证，2等待审核（默认0）]
     * }
     * 
     * @param  [arr] $input [请求参数]
     * @return [str] [商家ID]
     */
    public function api_add($input = array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_ADD);

        //校验数据
        object(parent::ERROR)->check($input, 'merchant_name', parent::TABLE_MERCHANT, array('args', 'length'));
        object(parent::ERROR)->check($input, 'merchant_info', parent::TABLE_MERCHANT, array('args'));
        object(parent::ERROR)->check($input, 'merchant_phone', parent::TABLE_MERCHANT, array('args'));
		
        object(parent::ERROR)->check($input, 'merchant_province', parent::TABLE_MERCHANT, array('args'));
        object(parent::ERROR)->check($input, 'merchant_city', parent::TABLE_MERCHANT, array('args'));
        object(parent::ERROR)->check($input, 'merchant_district', parent::TABLE_MERCHANT, array('args'));
		object(parent::ERROR)->check($input, 'merchant_address', parent::TABLE_MERCHANT, array('args'));
		
       
		
		object(parent::ERROR)->check($input, 'merchant_longitude', parent::TABLE_MERCHANT, array('args'));
        object(parent::ERROR)->check($input, 'merchant_latitude', parent::TABLE_MERCHANT, array('args'));
		
        if (isset($input['merchant_state']))
            object(parent::ERROR)->check($input, 'merchant_state', parent::TABLE_MERCHANT, array('args'));

        //白名单
        $whitelist = array(
            'merchant_name',
            'merchant_info',
            
            'merchant_longitude',
            'merchant_latitude',
            'merchant_state',
            
            'merchant_province',
            'merchant_city',
            'merchant_district',
			'merchant_address',
			
			'merchant_phone',
        );
        $insert_data = cmd(array($input, $whitelist), 'arr whitelist');

        //格式化数据
        $insert_data['merchant_id'] = object(parent::TABLE_MERCHANT)->get_unique_id();
        $insert_data['merchant_insert_time'] = time();
        $insert_data['merchant_update_time'] = time();

        //插入数据，记录日志
        if (object(parent::TABLE_MERCHANT)->insert($insert_data)) {
            object(parent::TABLE_ADMIN_LOG)->insert($input, $insert_data);
            return $insert_data['merchant_id'];
        } else {
            throw new error('添加失败');
        }
    }

    /**
     * 商家——编辑
     * 
     * api: MERCHANTADMINEDIT
     * req: {
     *  merchant_id             [str] [必填] [商户ID]
     *  merchant_name           [str] [可选] [商户名称]
     *  merchant_info           [str] [可选] [商户简介]
     *  merchant_address        [str] [可选] [商户地址]
     *  merchant_longitude      [dec] [可选] [商户坐标经度]
     *  merchant_latitude       [dec] [可选] [商户坐标纬度]
     *  merchant_logo_image_id  [str] [可选] [商户logo图片ID]
     *  merchant_state          [int] [可选] [商户状态，0 审核失败，1已认证，2等待审核（默认0）]
     * }
     * 
     * @param  [arr] $input [请求参数]
     * @return [str] [商家ID]
     */
    public function api_edit($input = array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_EDIT);

        //校验数据
        object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
        if (isset($input['merchant_name']))
            object(parent::ERROR)->check($input, 'merchant_name', parent::TABLE_MERCHANT, array('args', 'length'));
        if (isset($input['merchant_info']))
            object(parent::ERROR)->check($input, 'merchant_info', parent::TABLE_MERCHANT, array('args'));
        if (isset($input['merchant_address']))
            object(parent::ERROR)->check($input, 'merchant_address', parent::TABLE_MERCHANT, array('args'));
        if (isset($input['merchant_longitude']))
            object(parent::ERROR)->check($input, 'merchant_longitude', parent::TABLE_MERCHANT, array('args'));
        if (isset($input['merchant_latitude']))
            object(parent::ERROR)->check($input, 'merchant_latitude', parent::TABLE_MERCHANT, array('args'));
        /*if (isset($input['merchant_logo_image_id']))
            object(parent::ERROR)->check($input, 'merchant_logo_image_id', parent::TABLE_MERCHANT, array('args'));*/
        if (isset($input['merchant_state']))
            object(parent::ERROR)->check($input, 'merchant_state', parent::TABLE_MERCHANT, array('args'));

		if (isset($input['merchant_license_name']))
        object(parent::ERROR)->check($input, 'merchant_license_name', parent::TABLE_MERCHANT, array('args'));
		if (isset($input['merchant_license_number']))
        object(parent::ERROR)->check($input, 'merchant_license_number', parent::TABLE_MERCHANT, array('args'));
		if (isset($input['merchant_license_address']))
        object(parent::ERROR)->check($input, 'merchant_license_address', parent::TABLE_MERCHANT, array('args'));
		if (isset($input['merchant_license_operator']))
        object(parent::ERROR)->check($input, 'merchant_license_operator', parent::TABLE_MERCHANT, array('args'));
		
		if (isset($input['merchant_province']))
        object(parent::ERROR)->check($input, 'merchant_province', parent::TABLE_MERCHANT, array('args'));
		if (isset($input['merchant_city']))
        object(parent::ERROR)->check($input, 'merchant_city', parent::TABLE_MERCHANT, array('args'));
		if (isset($input['merchant_district']))
        object(parent::ERROR)->check($input, 'merchant_district', parent::TABLE_MERCHANT, array('args'));
		
		if (isset($input['merchant_phone']))
		object(parent::ERROR)->check($input, 'merchant_phone', parent::TABLE_MERCHANT, array('args'));
		if (isset($input['merchant_tel']))
		object(parent::ERROR)->check($input, 'merchant_tel', parent::TABLE_MERCHANT, array('args'));
		if (isset($input['merchant_email']))
		object(parent::ERROR)->check($input, 'merchant_email', parent::TABLE_MERCHANT, array('args'));
		
        //查询原始数据
        $original = object(parent::TABLE_MERCHANT)->find($input['merchant_id']);
        if( empty($original) ) throw new error('ID有误，数据不存在');

		$merchant_json = array();
		if( !empty($original["merchant_json"]) ){
			$merchant_json = cmd(array($original["merchant_json"]), "json decode");
		}
		if( !is_array($merchant_json)) {
			$merchant_json = array();
		}
		
		
		//用户消费赠送商家积分配置
		if( isset($input["config_rmb_consume_user_credit"]) ){
			if( empty($merchant_json["config_rmb_consume_user_credit"]) || !is_array($merchant_json["config_rmb_consume_user_credit"]) ){
				$merchant_json["config_rmb_consume_user_credit"] = array();
			}
			if( isset($input["config_rmb_consume_user_credit"]["ratio_credit"]) && $input["config_rmb_consume_user_credit"]["ratio_credit"] !== "" ){
				object(parent::ERROR)->check( $input["config_rmb_consume_user_credit"], 'ratio_credit', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[ratio_credit]" );
				$merchant_json["config_rmb_consume_user_credit"]["ratio_credit"] = $input["config_rmb_consume_user_credit"]["ratio_credit"];
			}
			if( isset($input["config_rmb_consume_user_credit"]["ratio_rmb"]) && $input["config_rmb_consume_user_credit"]["ratio_rmb"] !== "" ){
				object(parent::ERROR)->check( $input["config_rmb_consume_user_credit"], 'ratio_rmb', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[ratio_rmb]" );
				$merchant_json["config_rmb_consume_user_credit"]["ratio_rmb"] = $input["config_rmb_consume_user_credit"]["ratio_rmb"];
			}
			if( isset($input["config_rmb_consume_user_credit"]["algorithm"]) && $input["config_rmb_consume_user_credit"]["algorithm"] !== "" ){
				object(parent::ERROR)->check( $input["config_rmb_consume_user_credit"], 'algorithm', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[algorithm]" );
				$merchant_json["config_rmb_consume_user_credit"]["algorithm"] = $input["config_rmb_consume_user_credit"]["algorithm"];
			}
			if( isset($input["config_rmb_consume_user_credit"]["state"]) ){
				object(parent::ERROR)->check( $input["config_rmb_consume_user_credit"], 'state', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[state]" );
				$merchant_json["config_rmb_consume_user_credit"]["state"] = $input["config_rmb_consume_user_credit"]["state"];
			}
			
			//状态开启，检查所有的数据
			if( !empty($merchant_json["config_rmb_consume_user_credit"]["state"]) ){
				object(parent::ERROR)->check( $merchant_json["config_rmb_consume_user_credit"], 'ratio_credit', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[ratio_credit]" );
				object(parent::ERROR)->check( $merchant_json["config_rmb_consume_user_credit"], 'ratio_rmb', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[ratio_rmb]" );
				object(parent::ERROR)->check( $merchant_json["config_rmb_consume_user_credit"], 'algorithm', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[algorithm]" );
			}
			
			if( empty($merchant_json["config_rmb_consume_user_credit"]) ){
				unset($merchant_json["config_rmb_consume_user_credit"]);
			}
		}
		
		
		//支付宝提现配置
		if( isset($input["config_withdraw_alipay"]) ){
			if( empty($merchant_json["config_withdraw_alipay"]) || !is_array($merchant_json["config_withdraw_alipay"]) ){
				$merchant_json["config_withdraw_alipay"] = array();
			}
			if( isset($input["config_withdraw_alipay"]["account"]) ){
				if(!is_string($input["config_withdraw_alipay"]["account"]) &&
				!is_numeric($input["config_withdraw_alipay"]["account"])){
					throw new error('支付宝提现账号信息不合法');
				}	
				$merchant_json["config_withdraw_alipay"]["account"] = $input["config_withdraw_alipay"]["account"];
			}
			if( isset($input["config_withdraw_alipay"]["realname"]) ){
				if(!is_string($input["config_withdraw_alipay"]["realname"]) &&
				!is_numeric($input["config_withdraw_alipay"]["realname"])){
					throw new error('支付宝提现真实姓名不合法');
				}
				$merchant_json["config_withdraw_alipay"]["realname"] = $input["config_withdraw_alipay"]["realname"];
			}
			
			if( empty($merchant_json["config_withdraw_alipay"]) ){
				unset($merchant_json["config_withdraw_alipay"]);
			}
		}
		
		//微信提现配置
		if( isset($input["config_withdraw_weixinpay"]) ){
			if( empty($merchant_json["config_withdraw_weixinpay"]) || !is_array($merchant_json["config_withdraw_weixinpay"]) ){
				$merchant_json["config_withdraw_weixinpay"] = array();
			}
			if( isset($input["config_withdraw_weixinpay"]["openid"]) ){
				if(!is_string($input["config_withdraw_weixinpay"]["openid"]) &&
				!is_numeric($input["config_withdraw_weixinpay"]["openid"])){
					throw new error('微信提现openid不合法');
				}
				$merchant_json["config_withdraw_weixinpay"]["openid"] = $input["config_withdraw_weixinpay"]["openid"];
			}
			if( isset($input["config_withdraw_weixinpay"]["trade_type"]) ){
				if( !empty($input["config_withdraw_weixinpay"]["trade_type"]) ){
					object(parent::ERROR)->check( $input["config_withdraw_weixinpay"], 'trade_type', parent::TABLE_ORDER, array('args'), 'weixin_trade_type' );
				}
				$merchant_json["config_withdraw_weixinpay"]["trade_type"] = $input["config_withdraw_weixinpay"]["trade_type"];
			}
			if( empty($merchant_json["config_withdraw_weixinpay"]) ){
				unset($merchant_json["config_withdraw_weixinpay"]);
			}
		}
		
		$input["merchant_json"] = "";
		if( !empty($merchant_json) && is_array($merchant_json) ){
			$input["merchant_json"] = cmd(array($merchant_json), "json encode");
		}
		
        //白名单
        $whitelist = array(
            'merchant_name', 
            'merchant_info', 
            
            'merchant_longitude',
            'merchant_latitude',
            //'merchant_logo_image_id',
            'merchant_state',
            
            'merchant_license_name',
            'merchant_license_number',
            'merchant_license_address',
            'merchant_license_operator',
            
            'merchant_province',
            'merchant_city',
            'merchant_district',
            'merchant_address',
            
            'merchant_phone',
            'merchant_tel',
            'merchant_email',
			
            "merchant_json"
        );
        $update_data = cmd(array($input, $whitelist), 'arr whitelist');

        //过滤不需要更新的数据
        if( !empty($update_data) ){
        	 foreach ($update_data as $k => &$v) {
	            if (isset($original[$k]) && $original[$k] == $v)
	                unset($update_data[$k]);
	        }
        }
		
        if (empty($update_data)){
        	throw new error('没有需要更新的数据');
        }
		
		
        //格式化数据
        $update_data['merchant_update_time'] = time();

        //更新数据，记录日志
        if (object(parent::TABLE_MERCHANT)->update(array(array('merchant_id=[+]', $input['merchant_id'])), $update_data)) {
            // 是否审核通过
            if (isset($update_data['merchant_state']) && $update_data['merchant_state'] == 1) {
                // 查询商家用户
                $merchant_user = object(parent::TABLE_MERCHANT_USER)->find_merchant($input['merchant_id']);
                if (!$merchant_user) {
                    // 添加申请人为商家用户
                    object(parent::TABLE_MERCHANT_USER)->insert(array(
                        'merchant_user_id' => object(parent::TABLE_MERCHANT_USER)->get_unique_id(),
                        'user_id' => $original['merchant_user_id'],
                        'merchant_id' => $input['merchant_id'],
                        'merchant_user_name' => '',
                        'merchant_user_info' => '',
                        'merchant_user_state' => 1,
                        'merchant_user_sort' => 0,
                        'merchant_user_insert_time' => time(),
                        'merchant_user_update_time' => time(),
                    ));
                }
            }

            object(parent::TABLE_ADMIN_LOG)->insert($input, $update_data);
            return $input['merchant_id'];
        } else {
            throw new error('操作失败');
        }
    }



	/**
	 * 设为自营\取消自营  编辑自营
	 * 
	 * MERCHANTADMINEDITSELF
	 * {"class":"merchant/admin","method":"api_edit_self"}
	 * 
	 * @param	array	$data
	 * @return bool
	 */
	public function api_edit_self($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_EDIT);
		//校验数据
        object(parent::ERROR)->check($data, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
		object(parent::ERROR)->check($data, 'merchant_self', parent::TABLE_MERCHANT, array('args'));
		
		//获取旧数据
		$old = object(parent::TABLE_MERCHANT)->find($data['merchant_id']);
		if( empty($old) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'merchant_self', 
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		if( !empty($update_data) ){
			foreach($update_data as $key => $value){
				if( isset($old[$key]) ){
					if($old[$key] == $value){
						unset($update_data[$key]);
					}
				}
			}
		}
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		$update_data['merchant_update_time'] = time();
		//更新
		if( object(parent::TABLE_MERCHANT)->update( array(array('merchant_id=[+]', (string)$data['merchant_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['merchant_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	
	






    /**
     * 商家——删除
     *
     * api: MERCHANTADMINREMOVE
     * req: {
     *  merchant_id     [str] [必填] [商家ID]
     * }
     * 
     * @param  array  $input [请求数据]
     * @return [str] [商家ID]
     */
    public function api_remove($input = array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_REMOVE);

        //校验数据
        object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));

        //查询旧数据
        $original = object(parent::TABLE_MERCHANT)->find($input['merchant_id']);
        if (empty($original))
            throw new error('数据不存在');
		
        //判断依赖
        if( (bool)object(parent::TABLE_MERCHANT_USER)->find_merchant( $input['merchant_id'] ) ) 
        throw new error('该商家下存在商家用户，请先清理商家用户才能删除该商家');
		
        //删除数据，记录日志
        if (object(parent::TABLE_MERCHANT)->delete($input['merchant_id'])) {
            //logo存在，那么要删除旧图片
            if( !empty($original["merchant_logo_image_id"]) ){
            	object(parent::REQUEST_APPLICATION)->qiniu_image_remove(array( "image_id" => $original["merchant_logo_image_id"] ));
            }
            object(parent::TABLE_ADMIN_LOG)->insert($input, $original);
            return $input['merchant_id'];
        } else {
            throw new error('删除失败');
        }
    }   

    /**
     * 商家以七牛云的方式上传LOGO图片
     * MERCHANTADMINLOGOQINIUUPLOAD
     * 
     * 前台以 file 键名称请求
     * {"class":"merchant/admin","method":"api_logo_qiniu_upload"}
     * 
     * @param  array  $data
     * @return image_id
     */
    public function api_logo_qiniu_upload($data = array()) {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_EDIT);
		object(parent::ERROR)->check($data, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
		//查询原始数据
        $original = object(parent::TABLE_MERCHANT)->find($data['merchant_id']);
        if( empty($original) ) throw new error('ID有误，数据不存在');
		
        $response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();
        //更新分类头像
        $update_data['merchant_logo_image_id'] = $response['image_id'];
        $update_data['merchant_update_time'] = time();
        $update_where = array(array('merchant_id=[+]', $data['merchant_id']));
        if ( !object(parent::TABLE_MERCHANT)->update($update_where, $update_data) ){
        	object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
            throw new error ('更新失败');
        }
        
        //删除旧图片
        if (!empty($original['merchant_logo_image_id'])) {
            //请求七牛云
            $response['image_id'] = $original['merchant_logo_image_id'];
			object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
        }
		
        //插入操作日志
        object(parent::TABLE_ADMIN_LOG)->insert($data, $original);
        return $update_data['merchant_logo_image_id'];
    }





    /**
     * 商家以七牛云的方式上传营业执照图片
     * MERCHANTADMINLICENSEIMAGEQINIUUPLOAD
     * 
     * 前台以 file 键名称请求
     * {"class":"merchant/admin","method":"api_license_image_qiniu_upload"}
     * 
     * @param  array  $data
     * @return image_id
     */
    public function api_license_image_qiniu_upload($data = array()) {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_EDIT);
		object(parent::ERROR)->check($data, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
		//查询原始数据
        $original = object(parent::TABLE_MERCHANT)->find($data['merchant_id']);
        if( empty($original) ) throw new error('ID有误，数据不存在');
		
        $response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();
		
        //更新分类头像
        $update_data['merchant_license_image_id'] = $response['image_id'];
        $update_data['merchant_update_time'] = time();
        $update_where = array(array('merchant_id=[+]', $data['merchant_id']));
        if ( !object(parent::TABLE_MERCHANT)->update($update_where, $update_data) ){
        	object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
            throw new error ('更新失败');
        }
        
        //删除旧图片
        if (!empty($original['merchant_license_image_id'])) {
            //请求七牛云
            $response['image_id'] = $original['merchant_license_image_id'];
			object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
        }
        
		//插入操作日志
        object(parent::TABLE_ADMIN_LOG)->insert($data, $original);
        return $update_data['merchant_license_image_id'];
    }







    // 查询数据 =====================================

    /**
     * 商家-后台-数据列表
     *
     * api: MERCHANTADMINLIST
     * req: {
     *  search   [arr] [可选] [搜索、筛选]
     *  sort     [arr] [可选] [排序]
     *  size     [int] [可选] [每页的条数]
     *  page     [int] [可选] [当前页数，如果是等于 all 那么则查询所有]
     *  start    [int] [可选] [开始的位置，如果存在，则page无效]
     * }
     *  
     * limit的分页算法是：当前页数-1 * page_size
     * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
     *
     * @param  [arr] $input [请求参数]
     * @return array(
     *  row_count   [int] [数据总条数]
     *  limit_count [int] [已取出条数]
     *  page_size   [int] [每页的条数]
     *  page_count  [int] [总页数]
     *  page_now    [int] [当前页数]
     *  data        [arr] [数据]
     * )
     * 
     * @param  [arr] $input [请求参数]
     * @return array
     */
    public function api_list($data = array()) {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_READ);

        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
        );

        //排序
        $config["orderby"] = object(parent::REQUEST)->orderby($data, array(
            'id_desc' => array('merchant_id', true),
            'id_asc' => array('merchant_id', false),
            'name_desc' => array('merchant_name', true),
            'name_asc' => array('merchant_name', false),
            'state_desc' => array('merchant_state', true),
            'state_asc' => array('merchant_state', false),
            'insert_time_desc' => array('merchant_insert_time', true),
            'insert_time_asc' => array('merchant_insert_time', false),
            'update_time_desc' => array('merchant_update_time', true),
            'update_time_asc' => array('merchant_update_time', false),
            'self_desc' => array('merchant_self', true),
            'self_asc' => array('merchant_self', false),
            
            'merchant_user_count_desc' => array('merchant_user_count', true),
            'merchant_user_count_asc' => array('merchant_user_count', false),
        ));

        //避免排序重复
        $config["orderby"][] = array('merchant_id', false);

        //搜索
        if (!empty($data['search'])) {
        	if (isset($data['search']['merchant_id']) && is_string($data['search']['merchant_id'])) {
                $config['where'][] = array('[and] m.merchant_id=[+]', $data['search']['merchant_id']);
            }

        	if (isset($data['search']['merchant_name']) && is_string($data['search']['merchant_name'])) {
                $config['where'][] = array('[and] m.merchant_name LIKE "%[-]%"', $data['search']['merchant_name']);
            }
			
			if( isset($data['search']['merchant_state']) && 
			(is_string($data['search']['merchant_state']) || is_numeric($data['search']['merchant_state'])) &&
			in_array($data['search']['merchant_state'], array("0", "1", "2", "3")) ){
				$config["where"][] = array('[and] m.merchant_state=[+]', $data['search']['merchant_state']);
			}
        }

        //查询数据
        $data = object(parent::TABLE_MERCHANT)->select_page($config);
        return $data;
    }

    /**
     * 商家-后台-数据详情
     * 
     * api: MERCHANTADMINGET
     * req: {
     *  merchant_id     [str] [必填] [商户ID]
     * }
     * 
     * @param  [arr] $input [请求参数]
     * @return [type]
     */
    public function api_get($input = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_EDIT);
        //校验数据
        object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
        //查询数据
        $data = object(parent::TABLE_MERCHANT)->find_join($input['merchant_id']);
        if( empty($data) ) throw new error('数据不存在');
		if( !empty($data['merchant_json']) ){
			$data['merchant_json'] = cmd(array($data['merchant_json']), "json decode");
		}
		
		//获取商家分类
		$data['merchant_type'] = object(parent::TABLE_MERCHANT_TYPE)->select(array(
			"where" => array(
				array('merchant_id=[+]', $input['merchant_id'])
			)
		));
		
        return $data;
    }

    /**
     * 员工列表数据
     * 
     * MERCHANTADMINMERCHANTCASHIERLIST
     * {"class":"merchant/admin","method":"api_merchant_cashier_list"}
     */
    public function api_merchant_cashier_list($data){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CASHIER_LIST);

        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
        );

        //排序
        $config["orderby"] = object(parent::REQUEST)->orderby($data, array(
            'cashier_id_desc' => array('merchant_cashier_id', true),
            'cashier_id_asc' => array('merchant_cashier_id', false),
            'id_desc' => array('merchant_id', true),
            'id_asc' => array('merchant_id', false),
            'user_id_desc' => array('user_id', true),
            'user_id_asc' => array('user_id', false),
            'cashier_action_user_desc' => array('merchant_cashier_action_user', true),
            'cashier_action_user_asc' => array('merchant_cashier_action_user', false),
            'cashier_name_desc' => array('merchant_cashier_name', true),
            'cashier_name_asc' => array('merchant_cashier_name', false),
            'cashier_state_desc' => array('merchant_cashier_state', true),
            'cashier_state_asc' => array('merchant_cashier_state', false),
            
            'cashier_sort_desc' => array('merchant_cashier_sort', true),
            'cashier_sort_asc' => array('merchant_cashier_sort', false),
            'cashier_update_time_desc' => array('merchant_cashier_update_time', true),
            'cashier_update_time_asc' => array('merchant_cashier_update_time', false),
            'cashier_insert_time_desc' => array('merchant_cashier_insert_time_desc', true),
            'cashier_insert_time_asc' => array('merchant_cashier_insert_time_desc', false),
        ));

        //避免排序重复
        $config["orderby"][] = array('merchant_cashier_id', false);

        //搜索
        if (!empty($data['search'])) {
            if (isset($data['search']['merchant_cashier_id']) && is_string($data['search']['merchant_cashier_id'])) {
                $config['where'][] = array('[and] mc.merchant_cashier_id=[+]', $data['search']['merchant_cashier_id']);
            }

            if (isset($data['search']['cashier_name_desc']) && is_string($data['search']['cashier_name_desc'])) {
                $config['where'][] = array('[and] mc.cashier_name_desc LIKE "%[-]%"', $data['search']['cashier_name_desc']);
            }
            
            if( isset($data['search']['merchant_cashier_state']) && 
            (is_string($data['search']['merchant_cashier_state']) || is_numeric($data['search']['merchant_cashier_state'])) &&
            in_array($data['search']['merchant_cashier_state'], array("0", "1", "2")) ){
                $config["where"][] = array('[and] mc.merchant_cashier_state=[+]', $data['search']['merchant_cashier_state']);
            }
        }

        //查询数据
        $data = object(parent::TABLE_MERCHANT_CASHIER)->select_page($config);
        return $data;
    }

    /**
     * 审核员工数据
     * 
     * {"merchant_cashier_id":"收银员ID"}
     * MERCHANTADMINMERCHANTCASHIERSTATE
     * {"class":"merchant/admin","method":"api_merchant_cashier_state"}
     */
    public function api_merchant_cashier_state($data){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CASHIER_STATE);

        if(!isset($data['merchant_cashier_id']))
            throw new error("收银员ID为空");
        // 操作人ID
        $user_id = $_SESSION['user_id'];

        $merchant_cashier = object(parent::TABLE_MERCHANT_CASHIER)->find_by_merchant_cashier_id($data['merchant_cashier_id']);

        if(empty($merchant_cashier) || (int)$merchant_cashier['merchant_cashier_state'] !== 2)
            throw new error("未找到员工数据或员工已通过审核");
        
        $update_data = array(
            'user_id' => $user_id,
            'merchant_cashier_state' => 1,
            'merchant_cashier_update_time' => time()
        );

        $update_where = array(
            array('merchant_cashier_id=[+]',$data['merchant_cashier_id'])
        );

        return object(parent::TABLE_MERCHANT_CASHIER)->update( $update_where,$update_data );
    }



    // 检测 =======================================

   /**
     * 检查商家编辑的权限
     * 
     * MERCHANTADMINEDITCHECK
     * {"class":"merchant/admin","method":"api_edit_check"}
     * 
     * @param   void
     * @return  bool
     */
    public function api_edit_check()
    {
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_EDIT);
        return true;
    }






}