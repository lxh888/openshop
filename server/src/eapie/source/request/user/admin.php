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
class admin extends \eapie\source\request\user {




	/**
	 * 根据权限 获取 资金账户导出选项
	 * 
	 * USERADMINACCOUNTEXCELOPTION
	 * {"class":"user/admin","method":"api_account_excel_option"}
	 * 
	 * @param	void
	 * @return	array
	 */
	public function api_account_excel_option(){
		$account_excel_option = array();
		
		if( object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MONEY_ANNUITY_EXCEL, true) ){
			$account_excel_option[] = array(
				'api'=>'USERADMINMONEYANNUITYEXCEL',
				'title'=>'用户养老基金Excel导出'
			);
		}
		if( object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MONEY_HELP_EXCEL, true) ){
			$account_excel_option[] = array(
				'api'=>'USERADMINMONEYHELPEXCEL',
				'title'=>'用户扶贫基金Excel导出'
			);
		}
		if( object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MONEY_SERVICE_EXCEL, true) ){
			$account_excel_option[] = array(
				'api'=>'USERADMINMONEYSERVICEEXCEL',
				'title'=>'用户服务费Excel导出'
			);
		}
		if( object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MONEY_EXCEL, true) ){
			$account_excel_option[] = array(
				'api'=>'USERADMINMONEYEXCEL',
				'title'=>'用户钱包Excel导出'
			);
		}
		
		return empty($account_excel_option)? '' : $account_excel_option;
	}



    /**
     * 易淘添加推荐人
     * @param string $user_id
     *  USERADMINADDPARENTUSERID
	 * {"class":"user/admin","method":"api_add_parent_user_id"}
     */
    public function api_add_parent_user_id($data){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADD_PARENT_USER);
        
        $user_recommend_data = object(parent::TABLE_USER_RECOMMEND)->select_by_recommend_id($data['user_id']);
        // 没有下级！TODO 修改下级关系链
        if( !empty($user_recommend_data) )
            throw new error('用户已有下级');

        $user_data = object(parent::TABLE_USER)->find($data['user_id']);
        
        if(empty($user_data))
            throw new error('用户不存在');
        if(!empty($user_data['user_parent_id']))
            throw new error('用户已有邀请人');

        if(empty($user_data['user_json']))
            throw new error('用户未填写地址');

        $user_json = cmd(array($user_data['user_json']), 'json decode');
        
        if( empty($user_json['address']) )
            throw new error('用户未填写地址');

        $user_update = array();
        $region_data = object(parent::TABLE_AGENT_REGION)->find_province_city_district($user_json['address']['province'],$user_json['address']['city'],$user_json['address']['area']);
        if( !empty($region_data) ){
            $where = array(
                array('agent_region_id=[+]',$region_data['agent_region_id']),
                array('[AND]agent_user_state=1')
            );
            $user_parent_user = object(parent::TABLE_AGENT_USER)->find_where($where);
            if ( !empty($user_parent_user) ) {
                $user_update["user_parent_id"]  = $user_parent_user['user_id'];
            }
        }

        $user_update["user_update_time"] = time();
        $result = array();
        $result['insert_parent_id'] = object(parent::TABLE_USER)->update_user($data['user_id'],$user_update);
        // 更新用户邀请人成功，查询推荐关系并插入表
        if($result){
            // 成为新会员，查询出邀请人ID
            $recommend_user_id = $user_update["user_parent_id"];

            // 初始化数据
            $recommend_data = array();
            if($recommend_user_id !== false){
                
                // 循环次数
                $time = 0;
                // 循环赋值数组
                while (true) {
                    $recommend_data[$time] = array(
                        'user_id' => $data['user_id'],
                        'user_recommend_id' => object(parent::TABLE_USER_RECOMMEND)->get_unique_id(),
                        'user_recommend_user_id' => $recommend_user_id,
                        'user_recommend_level' => $time + 1,
                        'user_recommend_update_time' => time(),
                        'user_recommend_insert_time' => time(),
                    );
                    // 插入数据
                    $result['insert_recommend'][] = object(parent::TABLE_USER_RECOMMEND)->insert($recommend_data[$time]);
                    $time++;
                    //继续查询下级
                    $recommend_user_id = object(parent::TABLE_USER)->find_recommend_user_id($recommend_user_id);
                    if($recommend_user_id === false){
                        break;
                    }
                }
            }
            return $result;
        } else {
            throw new error('用户推荐人更新失败');
        }
    }




    /**
     * 查——全部用户列表
     * 需要判断浏览权限
     * 
     * api: USERADMINLIST
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
     */
    public function api_list($data = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_READ);
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
        );
		
        //排序
        $config["orderby"] = object(parent::REQUEST)->orderby($data, array(
            'id_desc' => array('user_id', true),
            'id_asc' => array('user_id', false),
            'parent_id_desc' => array('user_parent_id', true),
            'parent_id_asc' => array('user_parent_id', false),
            'nickname_desc' => array('user_nickname', true),
            'nickname_asc' => array('user_nickname', false),
            'sex_desc' => array('user_sex', true),
            'sex_asc' => array('user_sex', false),
            'compellation_desc' => array('user_compellation', true),
            'compellation_asc' => array('user_compellation', false),
            'register_time_desc' => array('user_register_time', true),
            'register_time_asc' => array('user_register_time', false),
            'update_time_desc' => array('user_update_time', true),
            'update_time_asc' => array('user_update_time', false),
            'phone_login_count_desc' => array('user_phone_login_count', true),
            'phone_login_count_asc' => array('user_phone_login_count', false),
            'phone_verify_count_desc' => array('user_phone_verify_count', true),
            'phone_verify_count_asc' => array('user_phone_verify_count', false),
            
			'user_recommend_count_desc' => array('user_recommend_count', true),
            'user_recommend_count_asc' => array('user_recommend_count', false),
        ));

        //避免排序重复
        $config["orderby"][] = array('user_id', false);

        //搜索
        if (!empty($data['search'])) {
            if (isset($data['search']['user_id']) && is_string($data['search']['user_id'])) {
                $config['where'][] = array('[and] u.user_id=[+]', $data['search']['user_id']);
            }
			if (isset($data['search']['user_nickname']) && is_string($data['search']['user_nickname'])) {
                $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $data['search']['user_nickname']);
            }

			if (isset($data['search']['user_phone']) && is_string($data['search']['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] u.user_id=[+]', $user_id);
            }
			
            if (isset($data['search']['user_parent_id']) && is_string($data['search']['user_parent_id'])) {
                $config['where'][] = array('[and] u.user_parent_id=[+]', $data['search']['user_parent_id']);
            }
			if (isset($data['search']['user_parent_phone']) && is_string($data['search']['user_parent_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_parent_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "-";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] u.user_parent_id=[+]', $user_id);
            }
			if (isset($data['search']['user_parent_nickname']) && is_string($data['search']['user_parent_nickname'])) {
                $config['where'][] = array('[and] parent_u.user_nickname LIKE "%[-]%"', $data['search']['user_parent_nickname']);
            }
			
        }

        //查询数据
        $data = object(parent::TABLE_USER)->select_page($config);
        return $data;
    }

    /**
     * 查一某个用户的信息
     *
     * api: USERADMINGET
     * req: {
     *  user_id [str] [必填] [用户ID]
     * }
     *
     * @param  [arr] $input [请求参数]
     * @return [arr]
     */
    public function api_get($input = array())
    {
        //检测权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_READ);
        //校验数据
        object(parent::ERROR)->check($input, 'user_id', parent::TABLE_USER, array('args'));
        //查询数据
        $get_data = object(parent::TABLE_USER)->find_join($input['user_id']);
        if (empty($get_data)) throw new error('用户不存在');
        return $get_data;
    }

    /**
     * 查——用户推荐人信息(单条)
     *
     * api: USERADMINPARENTGET
     * req: {
     *  user_id [str] [必填] [用户ID]
     * }
     *
     * @param  [arr] $input [请求参数]
     * @return [arr]
     */
    public function api_parent_get($input = array())
    {
        //检测权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_READ);

        //校验数据
        object(parent::ERROR)->check($input, 'user_id', parent::TABLE_USER, array('args'));

        //查询数据
        $get_data = object(parent::TABLE_USER)->find_self_parent($input['user_id'], array(
            'parent_u.user_id',
            'parent_u.user_nickname',
            'parent_u.user_compellation',
            'parent_up.user_phone_id as user_phone',
        ));

        if (empty($get_data))
            throw new error('用户父级数据不存在');

        return $get_data;
    }



	
	/**
     * 编辑用户的权限检测
     *	
     * api: USERADMINEDITCHECK
     * req: {
     *  user_id [str] [必填] [用户ID]
     * }
     *
     * @param  [arr] $input [请求参数]
     * @return [arr]
     */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_EDIT);
		return true;
	}
	
	


    /**
     * 改——用户基本信息
     * 
     * api: USERADMINEDIT
     * req: {
     *  user_id             [str] [必填] [用户ID]
     *  user_nickname       [str] [可选] [昵称]
     *  user_compellation   [str] [可选] [姓名]
     *  user_state          [int] [可选] [状态，0封禁，1正常]
     *  user_sex            [int] [可选] [性别，0未知，1男，2女]
     *  user_phone_sort     [int] [可选] [手机排序]
     *  user_phone_type     [int] [可选] [手机类型，0联系手机号，1登录手机号]
     *  user_parent         [str] [可选] [推荐人的ID或登录手机号]
     *  user_parent_id      [str] [可选] [若为空字符串，则删除推荐人]
     * }
     *
     * @param  [arr] $input [请求参数]
     * @return [str] [用户ID]
     */
    public function api_edit($input = array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_EDIT);
        
        //校验数据
        object(parent::ERROR)->check($input, 'user_id', parent::TABLE_USER, array('args'));
        if (isset($input['user_parent_id']))
            object(parent::ERROR)->check($input, 'user_parent_id', parent::TABLE_USER, array('format'));
        if (isset($input['user_nickname']))
            object(parent::ERROR)->check($input, 'user_nickname', parent::TABLE_USER, array('args'));
        if (isset($input['user_compellation']))
            object(parent::ERROR)->check($input, 'user_compellation', parent::TABLE_USER, array('args'));
        if (isset($input['user_wechat']))
            object(parent::ERROR)->check($input, 'user_wechat', parent::TABLE_USER, array('args'));
        if (isset($input['user_qq']))
            object(parent::ERROR)->check($input, 'user_qq', parent::TABLE_USER, array('args'));
        if (isset($input['user_email']))
            object(parent::ERROR)->check($input, 'user_email', parent::TABLE_USER, array('args'));
        if (isset($input['user_sex']))
            object(parent::ERROR)->check($input, 'user_sex', parent::TABLE_USER, array('args'));
        if (isset($input['user_state']))
            object(parent::ERROR)->check($input, 'user_state', parent::TABLE_USER, array('args'));

        //查询原始数据
        $original = object(parent::TABLE_USER)->find($input['user_id']);
        if (empty($original))
            throw new error('ID有误，数据不存在');
        
        //白名单
        $whitelist = array(
            'user_parent_id',
            'user_nickname', 
            'user_compellation', 
            'user_wechat',
            'user_qq',
            'user_email',
            'user_sex',
            'user_state'
        );
        $update_data = cmd(array($input, $whitelist), 'arr whitelist');

        //判断推荐人
        if (!empty($input['user_parent_id'])) {
            object(parent::ERROR)->check($input, 'user_parent_id', parent::TABLE_USER, array('exists_id'));
            if ($input['user_parent_id'] === $input['user_id'])
                throw new error('推荐人不能设为自己');
            $update_data['user_parent_id'] = $input['user_parent_id'];
        }

        //判断推荐人的ID或者是登录手机号
        if (!empty($input['user_parent'])) {
            object(parent::ERROR)->check($input, 'user_parent', parent::TABLE_USER, array('format'));
            $user_parent_data = object(parent::TABLE_USER)->find_id_or_phone($input['user_parent']);
            if (empty($user_parent_data['user_id']))
                throw new error('推荐人无效');
            if ($user_parent_data['user_id'] === $input['user_id'])
                throw new error('推荐人不能设为自己');
            $update_data['user_parent_id'] = $user_parent_data['user_id'];
        }

        //不能封禁自己
        if (
            isset($update_data['user_state'])
            && empty($update_data['user_state'])
            && $input['user_id'] === $_SESSION['user_id']
        ){
            throw new error('不能封禁自己');
        }

        //过滤不需要更新的数据
        foreach ($update_data as $key => $value) {
            if (isset($original[$key]) && $original[$key] == $value)
                unset($update_data[$key]);
        }
        if (empty($update_data))
            throw new error('没有需要更新的数据');

        //格式化数据
        $update_data['user_update_time'] = time();

        //更新数据，记录日志
        if (object(parent::TABLE_USER)->update( array(array('user_id=[+]', $input['user_id'])), $update_data)) {
            object(parent::TABLE_ADMIN_LOG)->insert($input, $update_data);
            return true;
        } else {
            throw new error('操作失败');
        }
    }
    
	
	
	
    /**
     * 改——用户登录密码
     * 
     * api: USERADMINPASSWORDEDIT
     * req: {
     *  user_id                 [str] [必填] [用户ID]
     *  user_password           [str] [必填] [新密码]
     *  user_confirm_password   [str] [必填] [确认新密码]
     * }
     *
     * @param  [arr] $input [请求参数]
     * @return [str] [用户ID]
     */
    public function api_password_edit($input = array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_EDIT);

        //校验数据
        object(parent::ERROR)->check($input, 'user_id', parent::TABLE_USER, array('args'));
        object(parent::ERROR)->check($input, 'user_password', parent::TABLE_USER, array('args'));
        object(parent::ERROR)->check($input, 'user_confirm_password', parent::TABLE_USER, array('args'));
        if ($input['user_password'] !== $input['user_confirm_password'])
            throw new error ('两次密码输入不一致');

        //查询原始数据
        $original = object(parent::TABLE_USER)->find_password($input['user_id']);
        if (empty($original))
            throw new error('ID有误，数据不存在');

        $update_data = array();
        //获得用户密码
        $update_data['user_left_password'] = md5($input['user_password'].$input['user_id']);
        $update_data['user_right_password'] = md5($input['user_id'].$input['user_password']);
        
        //过滤不需要更新的数据
        foreach ($update_data as $key => $value) {
            if (isset($original[$key]) && $original[$key] == $value)
                unset($update_data[$key]);
        }
        if (empty($update_data))
            throw new error('没有需要更新的数据');

        //更新时间
        $update_data['user_update_time'] = time();
        if (object(parent::TABLE_USER)->update( array(array('user_id=[+]', $input['user_id'])), $update_data)) {
            //插入操作日志
            object(parent::TABLE_ADMIN_LOG)->insert($input, $update_data);
            return $input['user_id'];
        } else {
            throw new error('操作失败');
        }
    }

    /**
     * 手动录入用户资料
	 * 
     * USERADMINADD
     * {"class":"user/admin","method":"api_add"}
	 * 
     * @param string user_name [用户名称] [选填]
     * @param string password [登录密码] [选填]
     * @param int level [*等级][对应身份ID] [选填]
     * @param string recommend_user_id [邀请人ID] [选填]
     * @param int gender [性别] [选填]
     * @param string phone [办公电话] [选填]
     * @param string address [办公地址] [选填] 
     * @param string mobile_phone [登录手机] [选填]
     * @param int user_money [麦豆数量 - 用户钱包余额] [选填]
     * @param int credit [积分] [选填]
     */
    public function api_add( $input = array() ){
    	object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_ADD);
		//数据检测 
		object(parent::ERROR)->check($input, 'user_password', parent::TABLE_USER, array('args'));
		object(parent::ERROR)->check($input, 'user_nickname', parent::TABLE_USER, array('args'));
		object(parent::ERROR)->check($input, 'user_sex', parent::TABLE_USER, array('args'));
		object(parent::ERROR)->check($input, 'user_wechat', parent::TABLE_USER, array('args'));
		object(parent::ERROR)->check($input, 'user_qq', parent::TABLE_USER, array('args'));
		object(parent::ERROR)->check($input, 'user_email', parent::TABLE_USER, array('args'));
		
		object(parent::ERROR)->check($input, 'user_phone', parent::TABLE_USER_PHONE, array('args', 'length'), 'user_phone_id');
		//获取旧数据
		$user_phone_data = object(parent::TABLE_USER_PHONE)->find($input['user_phone']);
		//判断手机是否已经存在用户并且已经认证
		if( !empty($user_phone_data["user_id"]) && !empty($user_phone_data["user_phone_state"]) ){
			throw new error("登录手机号已经被认证登记");
		}
		
		//白名单
		$whitelist = array(
			'user_nickname', 
			'user_sex',
			'user_wechat',
			'user_qq',
            'user_email',
            'user_register_province',
            'user_register_city',
            'user_register_area'
		);
		$insert_data = cmd(array($input, $whitelist), 'arr whitelist');
		
		$insert_data['user_id'] = object(parent::TABLE_USER)->get_unique_id();
		// 加密用户密码
        $insert_data["user_left_password"] = md5($input['user_password'].$insert_data['user_id']);
        $insert_data["user_right_password"] = md5($insert_data['user_id'].$input['user_password']);
		//是否已回收。0正常；1已回收
		$insert_data['user_trash'] = 0;
		$user_json_data = array();
		
		if( isset($input['work_phone']) && $input['work_phone'] != '' ){
            $user_json_data['work_phone'] = $input['work_phone'];
        } 
		if(isset($input['work_address'])){
            $user_json_data['work_address'] = $input['work_address'];
        } 
		$insert_data['user_json'] = cmd(array($user_json_data), 'json encode');
		
		if( isset($input['user_parent']) && $input['user_parent'] != '' ){
        	//判断推荐人的ID或者是登录手机号
        	$user_data = object(parent::TABLE_USER)->find_id_or_phone($input['user_parent']);
			if( empty($user_data['user_id']) ){
				throw new error ('用户推荐人不合法');
			}
            $insert_data['user_parent_id'] = $user_data['user_id'];
        }
		
		$insert_data['user_update_time'] = time();
        $insert_data['user_register_time'] = time();
		
		if( !object(parent::TABLE_USER)->insert($insert_data) ){
			throw new error ('用户添加失败');
		}
		
		//添加用户手机号
		if( empty($user_phone_data["user_phone_id"]) ){
			$insert_user_phone = array(
	            'user_phone_id' => $input['user_phone'],
	            'user_id' => $insert_data['user_id'],
	            'user_phone_type' => 1,
	            'user_phone_state' => 1,
	            'user_phone_insert_time' => time(),
	            'user_phone_update_time' => time(),
	        );
	        if( !object(parent::TABLE_USER_PHONE)->insert($insert_user_phone) ){
				object(parent::TABLE_USER)->remove($insert_data['user_id']);//删除
	        	throw new error ('用户登录手机号登记失败');
	        }
			
		}else{
			//更新手机信息
			$update_where = array();
			$update_where[] = array("user_phone_id=[+]", $input['user_phone']);
			$update_where[] = array('[and] user_phone_state=0');
			$update_user_phone = array(
				"user_id" => $insert_data['user_id'],
				"user_phone_state" => 1,
				"user_phone_type" => 1,
				"user_phone_update_time" => time(),
			);
			
			if( !object(parent::TABLE_USER_PHONE)->update($update_where, $update_user_phone) ){
				object(parent::TABLE_USER)->remove($insert_data['user_id']);//删除
				throw new error ("用户登录手机号更新失败");
			}
		}
		
		
		//添加管理员数据
		if( isset($input['admin_id']) && $input['admin_id'] != '' ){
			object(parent::ERROR)->check($input, 'admin_id', parent::TABLE_ADMIN, array('args', 'exists_id'));
			$insert_admin_user = array(
                'user_id' => $insert_data['user_id'],
                'admin_id' => $input['admin_id'],
                'admin_user_state' => 1,
                'admin_user_insert_time' => time(),
                'admin_user_update_time' => time(),
            );
            if( !object(parent::TABLE_ADMIN_USER)->insert($insert_admin_user) ){
            	throw new error ('用户添加成功，但管理员设置失败');
            }
		}
		
		
		if( isset($insert_data['user_parent_id']) && object(parent::TABLE_USER_RECOMMEND)->verification_distribution() ){
            // 邀请人ID作为直接推荐，继续查询
            $recommend_data = array();
            $recommend_user_id = $insert_data['user_parent_id'];
            $time = 0;
            while( true ){
                $recommend_data[$time] = array(
                    'user_recommend_id' => object(parent::TABLE_USER_RECOMMEND)->get_unique_id(),
                    'user_id' => $insert_data['user_id'],
                    'user_recommend_user_id' => $recommend_user_id,
                    'user_recommend_level' => $time + 1,
                    'user_recommend_update_time' => time(),
                    'user_recommend_insert_time' => time(),
                );
                object(parent::TABLE_USER_RECOMMEND)->insert($recommend_data[$time]);
                // 判断是否需要更新身份
                object(parent::TABLE_USER_RECOMMEND)->need_updata_admin_id($recommend_user_id);
                $time = $time + 1;
                //继续查询下级
                $recommend_user_id = object(parent::TABLE_USER)->find_recommend_user_id($recommend_user_id);
                if($recommend_user_id === false){
                    break;
                }
            }
        }

		
		if( isset($input['user_money']) ){
            $insert_user_money = array(
                'admin_user_id' => $_SESSION["user_id"],
                'user_id' => $insert_data['user_id'],
                'value' => $input['user_money'],
                'type' => parent::TRANSACTION_TYPE_ADMIN_PLUS,
            );
            
			if( !object(parent::TABLE_USER_MONEY)->insert_admin($insert_user_money) ){
				throw new error ('用户添加成功，用户钱包赠送失败');
			}
        }
		
		
        if( isset($input['user_credit']) ){
            $insert_user_credit = array(
                'admin_user_id' => $_SESSION["user_id"],
                'user_id' => $insert_data['user_id'],
                'value' => $input['user_credit'],
                'type' => parent::TRANSACTION_TYPE_ADMIN_PLUS,
            );
			
            if( !object(parent::TABLE_USER_CREDIT)->insert_admin($insert_user_credit) ){
            	throw new error ('用户添加成功，用户积分赠送失败');
            }
        }

    }


	
	/**
     * 用户以七牛云的方式上传LOGO图片
	 * USERADMINLOGOQINIUUPLOAD
	 * 
	 * 前台以 file 键名称请求
	 * {"class":"user/admin","method":"api_logo_qiniu_upload"}
	 * 
     * @param  array  $data
     * @return image_id
     */
    public function api_logo_qiniu_upload($data = array()) {
    	//检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_EDIT);
		object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args'));
		//查询原始数据
        $original = object(parent::TABLE_USER)->find($data['user_id']);
        if (empty($original)) throw new error('ID有误，数据不存在');
		
		$response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();
		//更新分类头像
        $update_data['user_logo_image_id'] = $response['image_id'];
        $update_data['user_update_time'] = time();
        $update_where = array(array('user_id=[+]', $data['user_id']));
        if ( !object(parent::TABLE_USER)->update($update_where, $update_data) ){
        	object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
        	throw new error ('更新失败');
        }
		
		//删除旧图片
        if (!empty($original['user_logo_image_id'])) {
            //请求七牛云
            $response['image_id'] = $original['user_logo_image_id'];
			object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
        }
		
		//插入操作日志
        object(parent::TABLE_ADMIN_LOG)->insert($data, $original);
		return $update_data['user_logo_image_id'];
    }
	
	




    
	/**
	 * 获取数据列表(根据积分或预付款数量)
	 *
     * ------Mr.Zhao------2019.07.04-----
     *  
	 * $request = array(
     *  'type' => str //类型，积分、预付款
	 * 	'min_value' => int //最小值
	 * 	'max_value' => int //最大值
	 * );
	 * 
	 * 
	 * 返回的数据：
	 * 
	 * USERADMINEXCEL
	 * {"class":"user/admin","method":"api_excel"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_excel($data = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_EXCEL);


        //检测数据
        if (
            empty($data['type']) ||
            !is_string($data['type']) ||
            !in_array($data['type'], array('credit', 'money'))
        ) {
            throw new error('导出条件有误（积分或预付款）');
        }

        $config = array(
            'orderby' => array(),
            'where' => array(),
            // 'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
        );


        // 排序
        $config["orderby"][] = array('user_id', false);
        $config["orderby"][] = array('user_' . $data['type'] . '_value', true);
        $config["orderby"][] = array('user_' . $data['type'] . '_time', true);

        $table = array(
            'credit' => object(parent::TABLE_USER_CREDIT),
            'money' => object(parent::TABLE_USER_MONEY)
        );

        if (
            isset($data['min_value']) &&
            is_numeric($data['min_value']) &&
            (int) $data['min_value'] >= 0
        ) {
            $sql_join_now_value = $table[$data['type']]->sql_join_user_now_value("u");
            $config['where'][] = array('[and] (' . $sql_join_now_value . ') > []', ((int) $data['min_value'] - 1));
        }

        if (
            isset($data['max_value']) &&
            is_numeric($data['max_value']) &&
            (int) $data['max_value'] >= 0
        ) {
            $sql_join_now_value = $table[$data['type']]->sql_join_user_now_value("u");
            $config['where'][] = array('[and] (' . $sql_join_now_value . ') < []', ((int) $data['max_value'] + 1));
        }

        $res_data = $table[$data['type']]->select_user($config);
        // return $res_data;


        // 整理成Excel数据导出

        if (empty($res_data)) {
            throw new error('没有数据');
        }

        // 获取积分单位进制和精度配置
        $credit_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("credit"), true);
        // 单位进制
        $scale = $credit_config['scale'] ? $credit_config['scale'] : 1;
        // 精度
        $precision = $credit_config['precision'] ? $credit_config['precision'] : 2;

        // 把用户数据整理成索引数组，便于Excel数据处理
        $excel_datas = array();
        foreach ($res_data as $key => $value) {
            // $number = $data['type'] == 'credit' ? $value['user_credit_value'] / 100 : $value['user_money_value'];
            $date = !empty($value['user_'.$data['type'].'_time'])?date("Y-m-d", $value['user_'.$data['type'].'_time']):'--';
            $number = $data['type'] == 'credit' ? $value['user_credit_value'] / $scale : $value['user_money_value'];
            $excel_datas[$key] = array();
            $excel_datas[$key][] = $key;
            $excel_datas[$key][] = !empty($value['user_compellation'])?$value['user_compellation']:'';
            $excel_datas[$key][] = !empty($value['user_nickname'])?$value['user_nickname']:'';
            $excel_datas[$key][] = !empty($value['user_phone_verify_list'])?' ' . $value['user_phone_verify_list']:'';
            $excel_datas[$key][] = !empty($number)?$number:'';
            $excel_datas[$key][] = $date;
            // $excel_datas[$key][] = number_format($value['occurrence']/100,2);
            // $excel_datas[$key][] = number_format($value['balance']/100,2);
        }

        // 表格标题
        $str = $data['type'] == 'credit' ? '积分' : '预付款';
        $min = !empty($data['min_value']) && $data['min_value'] > 0 ? $data['min_value']/100 : '0';
        $max = !empty($data['max_value']) ? $data['max_value'] / 100 : '';
        $title = $str . '范围（' . $min . '---' . $max . '）用户列表';
        // 文件名称
        $fileName = $title . date('_YmdHis');
        // 表头
        $cellName = array('序号', '姓名', '昵称', '手机', $str . '数量', '注册时间');
        // 表头有几行占用
        $topNumber = 2;

        $cellKey = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM',
            'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'
        );


        object(parent::PLUGIN_EXCEL)->output($fileName, $title, function ($obj) use ($excel_datas, $title, $cellName, $topNumber, $cellKey) {

            //处理表头标题
            $obj->getActiveSheet()->mergeCells('A1:' . $cellKey[count($cellName) - 1] . '1'); //合并单元格（如果要拆分单元格是需要先合并再拆分的，否则程序会报错）
            $obj->setActiveSheetIndex(0)->setCellValue('A1', $title);
            $obj->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $obj->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
            $obj->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $obj->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            // $obj->getActiveSheet()->getColumnDimension('A')->setWidth(50);

            //处理表头
            foreach ($cellName as $k => $v) {
                $obj->setActiveSheetIndex(0)->setCellValue($cellKey[$k] . $topNumber, $v); //设置表头数据
                $obj->getActiveSheet()->freezePane($cellKey[$k + 1] . ($topNumber + 1)); //冻结窗口
                $obj->getActiveSheet()->getStyle($cellKey[$k] . $topNumber)->getFont()->setBold(true); //设置是否加粗
                $obj->getActiveSheet()->getStyle($cellKey[$k] . $topNumber)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直居中
                if ($k > 0) {
                    $obj->getActiveSheet()->getColumnDimension($cellKey[$k])->setWidth(16);        //设置表格宽度
                }
                // $obj->getActiveSheet()->getColumnDimension($cellKey[$k])->setAutoSize(true);   //内容自适应
            }

            foreach ($excel_datas as $k => $excel_data) {
                foreach ($excel_data as $k1 => $val) {
                    $obj->getActiveSheet()->setCellValue($cellKey[$k1] . ($k + 1 + $topNumber), $val);
                    if (strlen($val) > 16) {
                        $obj->getActiveSheet()->getColumnDimension($cellKey[$k1])->setWidth(strlen($val));        //设置表格宽度
                    }
                }
            }
        });



        exit;
		
    }
    
    /**
     * 删除邀请关系，删除会员身份
     * 
     * USERADMINRESETUSER
	 * {"class":"user/admin","method":"api_reset_user"}
     */
    public function api_reset_user($data){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_RESET_USER_RECOMMEND);

        if(!isset($data['user_id']))
            throw new error("需要重置的用户ID不能为空");
            
        $user_id = $data['user_id'];

        $result = array();

        $result[] = object(parent::TABLE_USER_RECOMMEND)->remove($user_id);
        $result[] = object(parent::TABLE_ADMIN_USER)->remove($user_id);

        return $result;
    }



    /**
     * E麦补发393会员优惠券
     * 
     * USERADMINREPLACEMENTCOUPON
	 * {"class":"user/admin","method":"api_replacement_coupon"}
     * 
     * @param string $user_id
     */
    public function api_replacement_coupon($data){
        // TODO 检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_REPLACEMENT_COUPON);
        return object(parent::TABLE_USER_COUPON)->member_goods_coupon(array('user_id' => $data['user_id']));
    }


    /**
     * 设置
     * api: USERADMINSELFSETTINGS
     * req: {
     *  
     *}
     *
     * @return json
     }
     */
    public function api_self_settings($input = array())
    {
        $user_id = $_SESSION['user_id'];

        // 查询原始数据
        $original = object(parent::TABLE_ADMIN_USER)->find($user_id);
        $admin_user_json = cmd(array($original['admin_user_json']), 'json decode');
        if (empty($admin_user_json)) {
            $admin_user_json = array();
        }

        // 检查设置

        $update_where = array(
            array('user_id=[+]', $user_id),
        );

        $update_data = array(
            'admin_user_json' => cmd(array($admin_user_json), 'json encode'),
            'admin_user_update_time' => time(),
        );

        // 更新数据
        $res = object(parent::TABLE_ADMIN_USER)->update($update_where, $update_data);

        if ($res) {
            // 记录日志
            object(parent::TABLE_ADMIN_LOG)->insert($input, $update_data);
            return $input;
        } else {
            throw new error('操作失败');
        }
    }

}