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
class withdraw extends \eapie\source\request\user {




    /**
     * 添加提现申请
     *
     * api: USERWITHDRAWSELFADD
     * {"class":"user/withdraw","method":"api_self_add"}
     * req: {
     *  user_id     [str] [必填] [商家ID]
     *  type            [str] [必填] [提现类型。user_money]
     *  method          [str] [必填] [提现方式。]
     *  value           [int] [必填] [提现金额。单位分]
     *  comment         [str] [可选] [备注]
     * }
     * 
     * @return string 提现ID
     */
    public function api_self_add($input = array())
    {
        //判断是否登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        //代理查询条件,验证是否有提现权限（江油快递）
        $where = array(
            array('user_id =[+]',$user_id)
        );
        $agent_user = object(parent::TABLE_AGENT_USER)->find_where($where);
        if(empty($agent_user) || $agent_user['agent_user_state'] != 1){
            throw new error('不能发起提现申请');
        }

        //验证是否存在提现账户

        //实名认证
        $user_identity = object(parent::TABLE_USER_IDENTITY)->find($_SESSION['user_id']);
        	if( empty($user_identity) || 
        	$user_identity['user_identity_state'] != 1 || 
        	!object(parent::TABLE_USER_IDENTITY)->check_state($_SESSION['user_id'], $user_identity['user_identity_update_time']) )
            	throw new error('未实名认证，请在个人中心完成实名认证');

        //是否已存在申请
        $apply = object(parent::TABLE_USER_WITHDRAW)->find_where(array(
            array('user_id =[+]',$user_id),
            array('[and] user_withdraw_state=2'),
        ));
        if ($apply)
            throw new error('已有申请正在审核');
 
        //本周开始结束时间
        $time_check = $this->action_time(1);
        //验证本周是否有申请
        $apply_week = object(parent::TABLE_USER_WITHDRAW)->find_where(array(
            array('user_id =[+]',$user_id),
            array('[and] user_withdraw_state=1'),
            array("[and] user_withdraw_insert_time between []", $time_check['start_time']),
            array("[and] []", $time_check['end_time'])
        ));
        if ($apply_week)
            throw new error('本周提现次数已用完');

        //检测数据
        object(parent::ERROR)->check($input, 'type', parent::TABLE_USER_WITHDRAW, array('args'), 'user_withdraw_type');
        object(parent::ERROR)->check($input, 'method', parent::TABLE_USER_WITHDRAW, array('args'), 'user_withdraw_method');
        object(parent::ERROR)->check($input, 'value', parent::TABLE_USER_WITHDRAW, array('args'), 'money_fen');
        if (isset($input['comment']))
            object(parent::ERROR)->check($input, 'comment', parent::TABLE_USER_WITHDRAW, array('args'), 'user_withdraw_comment');


		//这里要判断提现的方式  --------------------预留需求

        //是否余额不足
        $user_money = object(parent::TABLE_USER_MONEY)->find_now_data($user_id);
        
        if (empty($user_money['user_money_value']) || $input['value'] > $user_money['user_money_value'])
            throw new error('余额不足');
		
		
		$user_withdraw_rmb = $input['value'];

        //获取提现配置
        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('rmb_withdraw_user_money'), true);
        // return $config;
        if (empty($config['state']) || !isset($config['min_user_money']) || !is_numeric($config['min_user_money']))
            throw new error ('提现功能已经关闭');
		
        //最小提现的金额
        if ($user_withdraw_rmb < $config['min_user_money'])
            throw new error('最小提现金额' . $config['min_user_money'] / 100 . '元');

		//最大提现金额
        if ($user_withdraw_rmb > $config['max_user_money'])
            throw new error('最大提现金额' . $config['max_user_money'] / 100 . '元');

        //白名单过滤
        $whitelist = array(
            'type',
            'method',
            'value',
            'comment',
        );
        $filter = cmd(array($input, $whitelist), 'arr whitelist');

        //添加前缀
        $insert_data = cmd(array('user_withdraw_', $filter),  'arr key_prefix');

        $insert_data['user_withdraw_id'] = object(parent::TABLE_USER_WITHDRAW)->get_unique_id();
        $insert_data['user_id'] = $user_id;
		$insert_data['user_withdraw_rmb'] = $user_withdraw_rmb;
        $insert_data['user_withdraw_insert_time'] = time();

        // return $insert_data;
        //插入数据
        if (object(parent::TABLE_USER_WITHDRAW)->insert($insert_data)) {
            return $insert_data['user_withdraw_id'];
        } else {
            throw new error('操作失败');
        }
    }




    //获取本周开始结束时间
    private function action_time ($time_type=1)
    {
        //  默认返回周
        $time = time();
        switch($time_type){
            case 0: //天开始结束时间
                $start_time = date('Y-m-d 00:00:00',$time);
                $end_time = date('Y-m-d 59:59:59',$time);
                break;
            case 1: //周开始结束时间
                $start_time = strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m", $time),date("d", $time)-date("w", $time)+1-7,date("Y", $time))));
                $end_time = strtotime(date("Y-m-d H:i:s",mktime(23,59,59,date("m", $time),date("d", $time)-date("w", $time)+7-7,date("Y", $time))));
                break;    
            default :   //月开始结束时间     
                $start_time = mktime(0,0,0,date("m",$time)-1,1,date("Y",$time));
                $end_time = mktime(23,59,59,date("m",$time)-1,date("t",$start_time),date("Y",$time));
                break;
        }
        return ['start_time'=>$start_time,'end_time'=>$end_time];
    }



    /**
     * 查询提现信息
     *
     * api: USERWITHDRAWSELFLIST
     * {"class":"user/withdraw","method":"api_self_list"}
     * req: {
     *  user_id [str] [必填] [商家ID]
     * }
     * 
     * @return array
     */
    public function api_self_list($input = array())
    {
        //判断是否登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        //检测是否是代理
        $where = array(
            array('user_id =[+]',$user_id),
            array('agent_user_state = 1')
        );
        $agent_user = object(parent::TABLE_AGENT_USER)->find_where($where);
        if(empty($agent_user) || $agent_user['agent_user_state'] != 1){
            throw new error('你没有查看权限');
        }

        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );

        //字段
        $config['select'] = array(
            'uw.user_withdraw_id AS id',
            'uw.user_withdraw_type AS type',
            'uw.user_withdraw_method AS method',
            'uw.user_withdraw_value AS value',
            'uw.user_withdraw_comment AS comment',
            'uw.user_withdraw_state AS state',
            'uw.user_withdraw_fail_info AS fail_info',
            'uw.user_withdraw_fail_time AS fail_time',
            'uw.user_withdraw_pass_time AS pass_time',
            'uw.user_withdraw_insert_time AS `time`',
            // 'au.*',
            // 'u.*',
        );

        //条件
        $config['where'][] = array('uw.user_id=[+]', $user_id);

        //排序
        $config['orderby'][] = array('uw.user_withdraw_insert_time', true);
        $config['orderby'][] = array('uw.user_withdraw_id', true);

        //查询数据
        $data = object(parent::TABLE_USER_WITHDRAW)->select_page_join_agent_user($config);
        //格式化数据
        $withdraw_type = object(parent::TABLE_USER_WITHDRAW)->get_type();
        $withdraw_method = object(parent::TABLE_USER_WITHDRAW)->get_method();
        
        $arr = array();
        foreach ($data['data'] as &$val) {
            
            $val['type'] = isset($withdraw_type[$val['type']]) ? $withdraw_type[$val['type']] : '未知';
            $val['method'] = isset($withdraw_method[$val['method']]) ? $withdraw_method[$val['method']] : '未知';
            $val['time'] = date('Y-m-d H:i', $val['time']);
            if ($val['fail_time'])
                $val['fail_time'] = date('Y-m-d H:i', $val['fail_time']);

            if ($val['pass_time'])
                $val['pass_time'] = date('Y-m-d H:i', $val['pass_time']);
            $arr[] = $val;
        }

        return $arr;
    }

}