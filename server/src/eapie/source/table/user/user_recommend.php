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



namespace eapie\source\table\user;
use eapie\main;
class user_recommend extends main {
    
    
    /**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "user");
    
    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
        'user_phone_id' => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少手机号码的参数"),
                    '!null'=>array("手机号码的参数不能为空"),
                    'match'=>array('/^[0-9]{11}$/iu', "手机格式不合法"),
                    ),
            //字符长度检测
            'length' => array(
                    'length'=>array(11, "手机号码的字符长度太多")
                    ),
        ),
    );

    /**
     * 获取一个id号
     * 
     * @param   void
     * @return  string
     */
    public function get_unique_id(){
        return cmd(array(10), 'random autoincrement');
    }

    /**
	 * 更新数据
	 * 
	 * @param	array		$where
	 * @param	array		$data
	 * @param	array		$call_data
	 * @return	bool
	 */
	public function update($where = array(), $data = array(), $call_data = array()){
		if( empty($where) || (empty($data) && empty($call_data)) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('user_recommend')
		->call('where', $where)
		->call('data', $call_data)
		->update($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
    }	
    
    /**
     * 单个用户所有推荐数据
     * @param string $user_id [用户ID]
     * @return array $data [单个用户所有推荐数据]
     */
    public function find($user_id){
        $result = db(parent::DB_APPLICATION_ID)
        ->table('user_recommend')
        ->where(array('user_id=[+]', (string)$user_id))
        ->find();

        return $result;
    }

    /**
     * 单个用户所有推荐数据
     * @param string $user_id [用户ID]
     * @return array $data [单个用户所有推荐数据]
     */
    public function find_count($where){
        $result = db(parent::DB_APPLICATION_ID)
        ->table('user_recommend')
        ->call('where', $where)
        ->find('count(*) as count');

        return $result;
    }

    /**
     * 单个用户所有推荐数据
     * @param string $user_id [用户ID]
     * @return array $data [单个用户所有推荐数据]
     */
    public function select_by_recommend_id($user_id){
        $result = db(parent::DB_APPLICATION_ID)
        ->table('user_recommend')
        ->where(array('user_recommend_user_id=[+]', (string)$user_id))
        ->select();

        return $result;
    }

    /**
     * 插入新数据
     * 
     * @param   array       $data           数据
     * @param   array       $call_data      数据
     * @return  bool
     */
    public function insert($data = array(), $call_data = array()){
        if (empty($data) && empty($call_data))
            return false;
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_recommend')
            ->call('data', $call_data)
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }

    /**
     * 查询是否有分销及推荐人功能
     */
    public function verification_recommend(){
        $data = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("shop_recommend_reward"), true);
        if (empty($data))
            return false;

        if((int)$data['is_open'] !== 1)
            return false;

        return true;
    }

    /**
     * 查询是否有易淘商城分销及推荐人功能
     */
    public function verification_yitao_distribution(){
        $data = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("shop_distribution_yitao"), true);
        if (empty($data))
            return false;

        if(1 !== (int)$data['is_open'])
            return false;

        return true;
    }

    /**
     * 查询是否有分销及推荐人功能
     */
    public function verification_distribution(){
        $data = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("shop_distribution_reward"), true);
        if (empty($data))
            return false;
        
        if(1 !== (int)$data['is_open'])
            return false;

        return true;
    }

    /**
     * 商城购物邀请关系查询
     * @param string $shop_order_id 业务流水ID
     */
    public function goods_user_recommend($shop_order_id){
        // 查询订单
        $shop_order = object(parent::TABLE_SHOP_ORDER)->find($shop_order_id);

        // 判断是否为会员
        $user_id = $shop_order['user_id'];
        $admin_user = object(parent::TABLE_ADMIN_USER)->find_exists_user($user_id);

        // 已经存在会员身份
        if(!empty($admin_user))
            return false;
        
        // 查询订单商品
        $shop_goods = object(parent::TABLE_SHOP_ORDER_GOODS)->find($shop_order_id);
        // 订单数据不存在
        if(!isset($shop_goods['shop_order_id']))
            return false;

        // 判断是否为会员商品或是积分商品
        $shop_goods_index = (int)$shop_goods['shop_goods_index'];
        $shop_goods_property = (int)$shop_goods['shop_goods_property'];
        if($shop_goods_index !== 1)
            return false;
        if($shop_goods_property !== 0)
            return false;

        // 更新会员身份
        $admin_user_insert_data = array(
            'user_id' => $user_id,
            'admin_id' => 'member',
            'admin_user_state' => 1,
            'admin_user_insert_time' => time(),
            'admin_user_update_time' => time()
        );
        object(parent::TABLE_ADMIN_USER)->insert($admin_user_insert_data);

        // 成为新会员，赠送优惠券   ---Mr.Zhao---
        $recommend_data['coupon'] = object(parent::TABLE_USER_COUPON)->member_goods_coupon(array('user_id' => $user_id));

        // 查询邀请关系
        $user_invite_data = object(parent::TABLE_USER_RECOMMEND)->find($user_id);

        // 判断是否已经在recommend表
        if ( !empty($user_invite_data) ){
            // 非会员邀请关系
            if($user_invite_data['user_recommend_state'] == 1){
                $config = array();
                $config['where'] = array(
                    array('user_id=[+]',$user_id)
                );
                $data = $this->select($config);
                foreach ($data as $key => $value) {
                    // 更新关系链中的身份
                    $user_recommend_updata_where = array( array('user_id=[+]',$value['user_id']) );
                    $user_recommend_updata_data = array('user_recommend_state' => 0);
                    $this->update($user_recommend_updata_where,$user_recommend_updata_data);
                }
            }
        } else {
            // 添加邀请关系链
            $recommend_data = $this->insert_recommend_by_user_id($user_id,0);
        }
        // 发放分销奖励
        $money_result = $this->distribution_money_reward($shop_order_id);
        return array('recommend' => $recommend_data, 'money' => $money_result);
    }

    

    public function xlt_shop_goods_reward_money($shop_order_id){
        // 查询订单
        $shop_order = object(parent::TABLE_SHOP_ORDER)->find($shop_order_id);

        // 查询订单商品
        $shop_goods = object(parent::TABLE_SHOP_ORDER_GOODS)->find($shop_order_id);

        $goods = object(parent::TABLE_SHOP_GOODS)->find($shop_goods['shop_goods_id']);

        $user = object(parent::TABLE_USER)->find($shop_order['user_id']);

        $user_admin_id = object(parent::TABLE_ADMIN_USER)->find($shop_order['user_id']);

        // 消费者非分销身份
        if( empty($user_admin_id) || !in_array($user_admin_id['admin_id'],array('bronze', 'silver', 'gold', 'platinum', 'king')) )
            return false;

        if( empty($user['user_parent_id']) )
            return false;

        $user_parent_admin_id = object(parent::TABLE_ADMIN_USER)->find($user['user_parent_id']);

        // 直属上级非分销身份
        if( empty($user_parent_admin_id) || !in_array($user_parent_admin_id['admin_id'],array('bronze', 'silver', 'gold', 'platinum', 'king')) )
            return false;

        // 直推奖励
        if( !isset($goods['shop_goods_reward_money_parent']) && (int)$goods['shop_goods_reward_money_parent'] <= 0 )
            return false;

        $insert_user_money_data = array();
        $insert_user_money_data = array(
            'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
            'order_plus_method' => parent::PAY_METHOD_USER_MONEY,
            'order_plus_account_id' => $user['user_parent_id'],
            'order_plus_value' => $goods['shop_goods_reward_money_parent'],
            'order_plus_transaction_id' => '',
            'order_sign' => $shop_order_id,
            'order_state' => 1,
            'order_pay_state' => 1,
            'order_pay_time' => time(),
            'order_insert_time' => time(),
            'order_type' => parent::TRANSACTION_TYPE_RECOMMEND_SALES_REWARD,
        );

        object(parent::TABLE_USER_MONEY)->reward_money($insert_user_money_data);

        if( !isset($goods['shop_goods_reward_money_king']) && (int)$goods['shop_goods_reward_money_king'] <= 0 )
            return false;

        // 上级是否有王者
        $recommend = $this->select_superiro($shop_order['user_id']);

        foreach( $recommend as $key => $value){

            $recommend_user_admin = object(parent::TABLE_ADMIN_USER)->find($value['user_id']);

            
            if(isset($recommend_user_admin['admin_id']) && $recommend_user_admin['admin_id'] == "king"){

                if($value['user_id'] == $shop_order['user_id']){
                    return false;
                }
                $insert_user_money_data = array(
                    'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
                    'order_plus_method' => parent::PAY_METHOD_USER_MONEY,
                    'order_plus_account_id' => $value['user_id'],
                    'order_plus_value' => $goods['shop_goods_reward_money_king'],
                    'order_plus_transaction_id' => '',
                    'order_sign' => $shop_order_id,
                    'order_state' => 1,
                    'order_pay_state' => 1,
                    'order_pay_time' => time(),
                    'order_insert_time' => time(),
                    'order_type' => parent::TRANSACTION_TYPE_RECOMMEND_SALES_REWARD,
                );

                object(parent::TABLE_USER_MONEY)->reward_money($insert_user_money_data);
            }
        }
    }

    /**
     * 喜乐淘，成为会员
     * @param string $shop_order_id 业务流水ID
     */
    public function xlt_goods_user_recommend($shop_order_id){
        // 查询订单
        $shop_order = object(parent::TABLE_SHOP_ORDER)->find($shop_order_id);

        // 查询订单商品
        $shop_goods = object(parent::TABLE_SHOP_ORDER_GOODS)->find($shop_order_id);
        
        // 查询身份
        $admin_user = object(parent::TABLE_ADMIN_USER)->find_exists_user($shop_order['user_id']);
        $user_id = $shop_order['user_id'];
        
        // 订单异常
        if(empty($shop_order))
            return false;
        
        // 订单数据不存在
        if(!isset($shop_goods['shop_order_id']))
            return false;

            // 判断是否为会员商品、积分商品
        if((int)$shop_goods['shop_goods_index'] !== 1)
            return false;
        if((int)$shop_goods['shop_goods_property'] !== 0)
            return false;

        // 不存在会员身份
        if(empty($admin_user)){
            // 新增会员身份
            $admin_user_insert_data = array(
                'user_id' => $user_id,
                'admin_id' => 'bronze',
                'admin_user_state' => 1,
                'admin_user_insert_time' => time(),
                'admin_user_update_time' => time()
            );
            object(parent::TABLE_ADMIN_USER)->insert($admin_user_insert_data);

            $recommend_data = null;
            // 查询邀请关系
            $user_invite_data = $this->find($user_id);

            // 判断是否已经在recommend表
            if ( !empty($user_invite_data) ){
                // 非会员邀请关系
                if($user_invite_data['user_recommend_state'] == 1){
                    $config = array();
                    $config['where'] = array(
                        array('user_id=[+]',$user_id)
                    );
                    $data = $this->select($config);
                    foreach ($data as $key => $value) {
                        // 更新关系链中的身份
                        $user_recommend_updata_where = array( array('user_id=[+]',$value['user_id']) );
                        $user_recommend_updata_data = array('user_recommend_state' => 0);
                        $recommend_data = $this->update($user_recommend_updata_where,$user_recommend_updata_data);
                    }
                }
            } else {
                // 添加邀请关系链
                return $this->insert_recommend_by_user_id($user_id,0);
            }
        } else {
            if(isset($admin_user) && $admin_user['admin_id'] == ''){
                // 更新身份
                $admin_user_update_data = array(
                    'admin_id' => 'bronze',
                    'admin_user_update_time' => time()
                );
                $admin_user_update_where = array(
                    array('user_id=[+]',$user_id)
                );
                object(parent::TABLE_ADMIN_USER)->update($admin_user_update_where,$admin_user_update_data);
            }
        }
        
    }

    /**
     * 喜乐淘,发放分销奖金
     * @param string $shop_order_id 业务订单ID
     */
    public function xlt_distribution_money_reward($shop_order_id){
        // 查询订单
        $shop_order = object(parent::TABLE_SHOP_ORDER)->find($shop_order_id);

        // 查询身份
        $user = object(parent::TABLE_USER)->find($shop_order['user_id']);

        if(isset($user['user_reward_money_is_send']) && (int)$user['user_reward_money_is_send'] == 1){
            return false;
        }
        
        $result = array();

        // 计算订单的奖金
        $rmb_award = object(parent::TABLE_SHOP_ORDER)->xlt_distribution_reward($shop_order_id);
        
        // 插入用户钱包
        for ($i = 0; $i < count($rmb_award); $i++) { 
            $plus_money = (int)$rmb_award[$i]['rmb_award'];
            
            // 判断是否有奖金
            if (isset($plus_money) && $plus_money > 0) {
                // 初始化数组
                $insert_user_money_data[$i] = array(
                    'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
                    'order_plus_method' => parent::PAY_METHOD_USER_MONEY,
                    'order_plus_account_id' => $rmb_award[$i]['user_id'],
                    'order_plus_value' => $plus_money,
                    'order_plus_transaction_id' => '',
                    'order_sign' => $shop_order_id,
                    'order_state' => 1,
                    'order_pay_state' => 1,
                    'order_pay_time' => time(),
                    'order_insert_time' => time(),
                    'order_type' => parent::TRANSACTION_TYPE_RECOMMEND_SALES_REWARD,
                );
                $result['distribution_money_reward'][$i] = object(parent::TABLE_USER_MONEY)->reward_money($insert_user_money_data[$i]);
            }
        }

        $update_user = array(
            'user_reward_money_is_send' => 1,
            'user_update_time' => time()
        );
        $update_user_where = array(
            array('user_id=[+]',$shop_order['user_id'])
        );

        object(parent::TABLE_USER)->update($update_user_where,$update_user);

        return $result;
    }

    /**
     * 喜乐淘身份升级
     */
    public function xlt_level_up($shop_order_id){
        // 查询订单
        $shop_order = object(parent::TABLE_SHOP_ORDER)->find($shop_order_id);
        $user_id = $shop_order['user_id'];
        $xlt_level = array(
            'bronze',
            'silver',
            'gold',
            'platinum',
            'king'
        );

        $user = object(parent::TABLE_USER)->find($user_id);

        $count = object(parent::TABLE_USER)->find_son_num($user_id)['count'];

        $user_admin_id = object(parent::TABLE_ADMIN_USER)->find($user_id)['admin_id'];

        $xlt_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("xlt_distribution"), true);

        if( !isset($xlt_config[$user_admin_id]) ){
            return false;
        }

        if( $user_admin_id == 'king' ){
            return false;
        }

        // 等级映射
        $now_admin = 0;
        if($user_admin_id == 'bronze'){
            $now_admin = 0;
        } elseif($user_admin_id == 'silver'){
            $now_admin = 1;
        } elseif($user_admin_id == 'gold'){
            $now_admin = 2;
        } elseif($user_admin_id == 'platinum'){
            $now_admin = 3;
        } else {
            $now_admin = 4;
        }

        $distribute = $xlt_config[$user_admin_id];

        $num_condition = 0;
        for($i = 0; $i <= $now_admin; $i++){
            $num_condition += $xlt_config[$xlt_level[$i]]['level_up_condition'];
        }

        if($count >= $num_condition && $user['user_cumulative_money'] >= $distribute['money']){
            $admin_data = array(
                'admin_id' => $xlt_level[$now_admin + 1],
                'admin_user_update_time' => time()
            );
            $admin_where = array(
                array('user_id=[+]',$user_id)
            );
            $result['admin'] = object(parent::TABLE_ADMIN_USER)->update($admin_where,$admin_data);

            $money_reward = array(
                'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
                'order_plus_method' => parent::PAY_METHOD_USER_MONEY,
                'order_plus_account_id' => $user_id,
                'order_plus_value' => $distribute['level_up_award'],
                'order_plus_transaction_id' => '',
                'order_sign' => $shop_order_id,
                'order_state' => 1,
                'order_pay_state' => 1,
                'order_pay_time' => time(),
                'order_insert_time' => time(),
                'order_type' => parent::TRANSACTION_TYPE_RECOMMEND_SALES_REWARD,
            );
            $result['money'] = object(parent::TABLE_USER_MONEY)->reward_money($money_reward);
            
            // 累计金额清零
            $update_user_data = array(
                'user_update_time' => time(),
                'user_reward_money_is_send' => 0,
                'user_cumulative_money' => 0
            );
            $update_where = array(
                array('user_id=[+]',$user_id)
            );
            return object(parent::TABLE_USER)->update($update_where,$update_user_data);
        } else{
            // 积累金额
            $update_user_data = array(
                'user_update_time' => time(),
                'user_cumulative_money' => $user['user_cumulative_money'] + $shop_order['shop_order_pay_money']
            );
            $update_where = array(
                array('user_id=[+]',$user_id)
            );
            return object(parent::TABLE_USER)->update($update_where,$update_user_data);
        }
    }


    /**
     * 发放分销奖金
     * @param string $shop_order_id 业务订单ID
     */
    public function distribution_money_reward($shop_order_id){
        $result = array();
        // 计算订单的奖金
        $rmb_award = object(parent::TABLE_SHOP_ORDER)->distribution_reward($shop_order_id);
        // 插入用户钱包
        for ($i = 0; $i < count($rmb_award); $i++) { 
            $plus_money = (int)$rmb_award[$i]['rmb_award'];
            
            // 判断是否有奖金
            if (isset($plus_money) && $plus_money > 0) {
                // 初始化数组
                $insert_user_money_data[$i] = array(
                    'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
                    'order_plus_method' => parent::PAY_METHOD_USER_MONEY,
                    'order_plus_account_id' => $rmb_award[$i]['user_id'],
                    'order_plus_value' => $plus_money,
                    'order_plus_transaction_id' => '',
                    'order_sign' => $shop_order_id,
                    'order_state' => 1,
                    'order_pay_state' => 1,
                    'order_pay_time' => time(),
                    'order_insert_time' => time(),
                    'order_type' => parent::TRANSACTION_TYPE_RECOMMEND_SALES_REWARD,
                );
                $result['distribution_money_reward'][$i] = object(parent::TABLE_USER_MONEY)->reward_money($insert_user_money_data[$i]);
            }
        }
        
        // 更新身份
        foreach($rmb_award as $value){
            $result['update_admin_id'] = $this->recomend_update_admin_id($value['user_id']);
        }
        return $result;
    }

    /**
     * 查询身份是否为分销身份ID
     * @param string user_id
     * @return int $code 0没有身份 1身份为非分销身份 2分销身份
     */
    public function ve_admin_id($user_id){
        // 查询原身份ID
        $admin = object(parent::TABLE_ADMIN_USER)->find($user_id);
        if(empty($admin)){
            return 0;
        }
        $admin_id = $admin['admin_id'];
        // 查询分销配置
        $config = object(parent::TABLE_SHOP_ORDER)->distribution_reward_rule();
        if(!isset($config[$admin_id])){
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * 查询是否有身份ID
     * @param string user_id
     */
    public function _has_admin_id($user_id) {
        // 查询原身份ID
        $admin_id = object(parent::TABLE_ADMIN_USER)->find($user_id);

        $result = array();
        // 没有身份
        if(empty($admin_id)){
            $result['has_admin_id'] = false;
            $result['admin_id'] = '';
        } else {
            $result['has_admin_id'] = true;
            $result['admin_id'] = $admin_id['admin_id'];
        }
        return $result;
    }

    /**
     * 查询身份是否为分销身份ID
     * @param string user_id
     */
    public function _is_distribution_admin_id($admin_id) {
        // 查询分销配置
        $config = object(parent::TABLE_SHOP_ORDER)->distribution_reward_rule();

        $result = array();
        // 非分销身份ID
        if(!isset($config[$admin_id])){
            return false;
        } else {
            return true;
        }
    }

    /**
     * (无条件)商城购物邀请关系查询
     * @param string $shop_order_id 业务流水ID
     */
    public function no_condition_goods_user_recommend($shop_order_id){
        // 分销奖励新增会员
        $shop_order = object(parent::TABLE_SHOP_ORDER)->find($shop_order_id);
        
        $user_id = $shop_order['user_id'];
        $user_invite_data = object(parent::TABLE_USER_RECOMMEND)->find($user_id);

        $has_admin_id = $this->_has_admin_id($user_id);
        if($has_admin_id['has_admin_id']) {
            // 已经有身份ID
        } else {
            // 无身份ID，插入新的身份ID
            $admin_user_insert_data = array(
                'user_id' => $user_id,
                'admin_id' => 'member',
                'admin_user_state' => 1,
                'admin_user_insert_time' => time(),
                'admin_user_update_time' => time()
            );
            object(parent::TABLE_ADMIN_USER)->insert($admin_user_insert_data);
        }

        // 判断是否已经为本平台会员
        if ( !empty($user_invite_data) ) 
            return $user_invite_data['user_recommend_id'];
            
        // 成为新会员，查询出邀请人ID
        $recommend_user_id = object(parent::TABLE_USER)->find_recommend_user_id($user_id);

        // 初始化数据
        $recommend_data = array();
        if($recommend_user_id !== false){
            
            // 循环次数
            $time = 0;
            // 循环赋值数组
            while (true) {
                $recommend_data[$time] = array(
                    'user_id' => $user_id,
                    'user_recommend_id' => object(parent::TABLE_USER_RECOMMEND)->get_unique_id(),
                    'user_recommend_user_id' => $recommend_user_id,
                    'user_recommend_level' => $time + 1,
                    'user_recommend_update_time' => time(),
                    'user_recommend_insert_time' => time(),
                );
                // 插入数据
                $this->insert($recommend_data[$time]);
                $time++;
                //继续查询下级
                $recommend_user_id = object(parent::TABLE_USER)->find_recommend_user_id($recommend_user_id);
                if($recommend_user_id === false){
                    break;
                }
            }
        }
        return $recommend_data;
    }

    /**
     * 查询会员上级推荐人ID及对应的等级
     * @param string $user_id 消费会员ID
     * @return array $data
     *  {"user_id":"邀请人ID","level":"等级"}
     */
    public function select_reward_user($user_id){
        $config = array();
        $config['where'][] = array('user_id=[+]',$user_id);
        $config['select'] = array(
            'user_recommend_user_id as user_id',
            'user_recommend_level as level',
        );

        $data = $this->select($config);
        return $data;
    }


    /**
     * （新）查询会员上级推荐人UserId及对应的等级
     * @param string $user_id 根节点的用户ID
     * @return array $data
     * {"user_id":"邀请人ID","level":"等级"}
     */
    public function select_superiro($user_id){
        $data = array();
        $level = 1;
        $user_data = object(parent::TABLE_USER)->find($user_id);
        if(empty($user_data) || empty($user_data['user_parent_id']) || $user_data['user_parent_id'] == '')
            return null;
        $data[] = array('user_id' => $user_data['user_parent_id'], 'level' => $level);
        for ($i = 0; $i < 6; $i++) { 
            $where = array(
                array('user_parent_id=[+]',$user_data['user_parent_id'])
            );
            $user_data = object(parent::TABLE_USER)->find($user_data['user_parent_id']);
            if(empty($user_data) || empty($user_data['user_parent_id']) || $user_data['user_parent_id'] == ''){
                return $data;
            }
            $level++;
            $data[] = array('user_id' => $user_data['user_parent_id'], 'level' => $level);
        }
        return $data;
    }

    /**
     * 获取单个用户多条推荐关系数据
     *  $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认不限制
     * );
     * 
     * @param   array   $config
     * @return  array
     */
    public function select($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select  = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            
            return db(parent::DB_APPLICATION_ID)
                ->table('user_recommend')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    } 

    /**
     * 查询条数
     * @param array $where [查询条件]
     * @return int $count 下级数量
     */
    public function select_count($where){
        $counts = db(parent::DB_APPLICATION_ID)
            ->table('user_recommend')
            ->call('where', $where)
            ->find('count(*) as count');

        return $counts;
    }

    /**
     * 判断是否需要更新会员角色
     * @param string $user_id [判断是否需要更新的用户ID]
     * @return boolean $result [是否需要更新]
     *  $result['admin_id'] [更新的身份ID]
     *  $result['update'] [是否更新成功]
     *  如果返回false 则不需要更新 无操作
     */
    public function need_updata_admin_id($user_id) {
        // 判断是否有分销功能
        if(!$this->verification_distribution())
            return false;
        // 取分销配置，查询身份更新条件
        $config = array();
        $cache = object(parent::TABLE_SHOP_ORDER)->distribution_reward_rule();

        foreach ($cache as $key => &$value) {
            if(isset($cache[$key]['condition'])){
                $config[$key]['condition'] = $cache[$key]['condition'];
                $config[$key]['admin_id'] = $cache[$key]['admin_id'];
            }
        }
        unset($cache);
        
        // 查询原身份ID
        $admin_id = object(parent::TABLE_ADMIN_USER)->find($user_id);
        
        // 没有身份
        if(empty($admin_id))
            return false;

        $admin_id = $admin_id['admin_id'];
        
        // 非分销身份
        if( !isset($config[$admin_id]) )
            return false;
        
        // 身份为总监和区域代理直接返回
        if($admin_id == 'chief_inspector' || $admin_id == 'area_agent')
            return false;

        // 下一等级级身份ID
        $next_admin_id = '';
        switch ($admin_id) {
            case 'member':
                $next_admin_id = 'shop_manager';
                break;
            case 'shop_manager':
                $next_admin_id = 'chief_inspector';
                break;
        }
        
        // 查询下级条数
        $where = array(
            array('user_recommend_user_id=[+]',$user_id),
            array('user_recommend_level=1')
        );
        $user_count = $this->select_count($where);
        $user_count = (int)$user_count['count'];

        // 无下级，或身份ID不存在
        if($user_count === 0)
            return false;

        // 初始化更新数据及更新条件
        $result = null;
        $update_where = array(
            array('user_id=[+]',$user_id)
        );
        $update_data = array(
            'admin_user_update_time' => time()
        );
        // 循环判断身份ID
        foreach ($config as $key => $value) {
            // 身份ID不对应
            if($value['admin_id'] !== $next_admin_id)
                continue;
            // 身份ID对应条件无人数
            if(!isset($value['condition']['num']))
                continue;
            // 下级人数为满足身份升级条件
            if($value['condition']['num'] > $user_count)
                continue;
            $update_data['admin_id'] = $value['admin_id'];
        }
        if(isset($update_data['admin_id'])){
            // 更新身份
            $result = object(parent::TABLE_ADMIN_USER)->update($update_where,$update_data);
            return array('admin_id'=> $update_data['admin_id'], 'update' => $result);
        }
        return false;
    }


    /**
     * 全新模型，更新分校身份
     * 
     * 模型说明: 现仅用于E麦商城编辑分销关系链
     * 
     * @param $user_id 用户ID
     * @auther terrell
     * @return boolean $result {"result":true(false)}
     */
    public function recomend_update_admin_id($user_id){
        // 判断是否有分销功能
        if(!$this->verification_distribution())
            return false;

        // 查询原身份ID,不用考虑非分销身份、无身份和身份ID为区域代理和总监的情况
        $admin_id = object(parent::TABLE_ADMIN_USER)->find($user_id);
        if( empty($admin_id) )
            return false;
        $admin_id = $admin_id['admin_id'];
        if( !isset($config[$admin_id]) )
            return false;
        if( $admin_id == 'chief_inspector' || $admin_id == 'area_agent' )
            return false;

        // 查询关系链表下级人数
        $where = array(
            array('user_recommend_user_id=[+]',$user_id),
            array('user_recommend_level = 1'),                // 直推
        );
        $user_count = $this->select_count($where);
        $user_count = (int)$user_count['count'];
        $new_admin_id = '';
        //TODO 身份升级的人数条件应该直接放到配置中
        if($user_count <= 0){
            $new_admin_id = '';
        } elseif($user_count < 3){
            $new_admin_id = 'member';
        } elseif($user_count >= 3 && $user_count < 100){
            $new_admin_id = 'shop_manager';
        } elseif( $user_count >= 100 ){
            $new_admin_id = 'chief_inspector';
        }

        // 是否需要升级身份
        if($new_admin_id == $admin_id){
            return false;
        } else{
            $update_where = array(
                array('user_id=[+]',$user_id)
            );
            $update_data = array(
                'admin_user_update_time' => time(),
                'admin_id' => $new_admin_id
            );
            $result = object(parent::TABLE_ADMIN_USER)->update($update_where,$update_data);
            return $result;
        }
    }

    /**
     * 生成用户ID对应的分销关系链
     * @param $user_id [用户ID]
     * @param $is_member [代表即将插入关系链表的用户是否为会员]
     * @author terrell
     * 
     */
    public function insert_recommend_by_user_id($user_id,$is_member = 1){
        // 判断是否有分销功能
        if(!$this->verification_distribution())
            return false;
        $recommend = $this->find($user_id);
        
        // 已存在邀请关系链
        if(!empty($recommend))
            return false;
        
        // 查询出邀请人ID
        $recommend_user_id = object(parent::TABLE_USER)->find_recommend_user_id($user_id);

        // 初始化数据
        $recommend_data = array();
        if($recommend_user_id !== false){
            // 循环次数
            $time = 0;
            // 循环赋值数组
            while (true) {
                $recommend_data[$time] = array(
                    'user_id' => $user_id,
                    'user_recommend_id' => object(parent::TABLE_USER_RECOMMEND)->get_unique_id(),
                    'user_recommend_user_id' => $recommend_user_id,
                    'user_recommend_state' => $is_member,
                    'user_recommend_level' => $time + 1,
                    'user_recommend_update_time' => time(),
                    'user_recommend_insert_time' => time(),
                );
                // 插入数据
                $this->insert($recommend_data[$time]);
                $time++;
                //继续查询下级
                $recommend_user_id = object(parent::TABLE_USER)->find_recommend_user_id($recommend_user_id);
                if($recommend_user_id === false){
                    break;
                }
            }
        }
        return $recommend_data;
    }


    /**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$user_id
	 * @return	array
	 */
	public function remove($user_id = ''){
		if( empty($user_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('user_recommend')
		->where(array('user_id=[+]', (string)$user_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
}