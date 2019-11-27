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

class withdraw extends \eapie\source\request\merchant
{


    /**
     * 添加提现申请
     *
     * api: MERCHANTWITHDRAWSELFADD
     * req: {
     *  merchant_id     [str] [必填] [商家ID]
     *  type            [str] [必填] [提现类型。merchant_money]
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
        //检测商家和商家用户状态
        object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
		
		if( !object(parent::TABLE_MERCHANT)->check_authentication($input['merchant_id']) ){
			 throw new error('商家未认证');
		}
		
        if (!object(parent::TABLE_MERCHANT_USER)->check_state($user_id, $input['merchant_id']))
            throw new error('非法商家用户');

        //检测认证
        $data = object(parent::TABLE_USER_IDENTITY)->find($user_id);
        if (empty($data) || $data['user_identity_state'] != 1 || !object(parent::TABLE_USER_IDENTITY)->check_state($user_id, $data['user_identity_update_time']))
            throw new error('未实名认证');

        //是否已存在申请
        $apply = object(parent::TABLE_MERCHANT_WITHDRAW)->find_where(array(
            array('merchant_id=[+]', $input['merchant_id']),
            array('[and] merchant_withdraw_state=2'),
        ));
        if ($apply)
            throw new error('已有申请正在审核');

        //检测数据
        object(parent::ERROR)->check($input, 'type', parent::TABLE_MERCHANT_WITHDRAW, array('args'), 'merchant_withdraw_type');
        object(parent::ERROR)->check($input, 'method', parent::TABLE_MERCHANT_WITHDRAW, array('args'), 'merchant_withdraw_method');
        object(parent::ERROR)->check($input, 'value', parent::TABLE_MERCHANT_WITHDRAW, array('args'), 'money_fen');
        if (isset($input['comment']))
            object(parent::ERROR)->check($input, 'comment', parent::TABLE_MERCHANT_WITHDRAW, array('args'), 'merchant_withdraw_comment');


		//这里要判断提现的方式  --------------------预留需求

        //是否余额不足
        $merchant_money = object(parent::TABLE_MERCHANT_MONEY)->find_now_data($input['merchant_id']);
        if (empty($merchant_money['merchant_money_value']) || $input['value'] > $merchant_money['merchant_money_value'])
            throw new error('余额不足');
		
		
		$merchant_withdraw_rmb = $input['value'];

        //获取提现配置
        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('rmb_withdraw_merchant_money'), true);
        if (empty($config['state']) || !isset($config['min_merchant_money']) || !is_numeric($config['min_merchant_money']))
            throw new error ('提现功能已经关闭');
		
        //最小提现的金额
        if ($merchant_withdraw_rmb < $config['min_merchant_money'])
            throw new error('最小提现金额' . $config['min_merchant_money'] / 100 . '元');

		//-------------------------------


        //白名单过滤
        $whitelist = array(
            'type',
            'method',
            'value',
            'comment',
        );
        $filter = cmd(array($input, $whitelist), 'arr whitelist');

        //添加前缀
        $insert_data = cmd(array('merchant_withdraw_', $filter),  'arr key_prefix');

        $insert_data['merchant_withdraw_id'] = object(parent::TABLE_MERCHANT_WITHDRAW)->get_unique_id();
        $insert_data['merchant_id'] = $input['merchant_id'];
        $insert_data['user_id'] = $user_id;
		$insert_data['merchant_withdraw_rmb'] = $merchant_withdraw_rmb;
        $insert_data['merchant_withdraw_insert_time'] = time();

        //插入数据
        if (object(parent::TABLE_MERCHANT_WITHDRAW)->insert($insert_data)) {
            return $insert_data['merchant_withdraw_id'];
        } else {
            throw new error('操作失败');
        }
    }


    /**
     * 查询提现信息
     *
     * api: MERCHANTWITHDRAWSELFLIST
     * req: {
     *  merchant_id [str] [必填] [商家ID]
     * }
     * 
     * @return array
     */
    public function api_self_list($input = array())
    {
        //判断是否登录
        object(parent::REQUEST_USER)->check();

        //检测是否合法用户
        object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
        if (!object(parent::TABLE_MERCHANT_USER)->check_exist($_SESSION['user_id'], $input['merchant_id'], true))
            throw new error('权限不足');

        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );

        //字段
        $config['select'] = array(
            'mw.merchant_withdraw_id AS id',
            'mw.merchant_withdraw_type AS type',
            'mw.merchant_withdraw_method AS method',
            'mw.merchant_withdraw_value AS value',
            'mw.merchant_withdraw_comment AS comment',
            'mw.merchant_withdraw_state AS state',
            'mw.merchant_withdraw_fail_info AS fail_info',
            'mw.merchant_withdraw_fail_time AS fail_time',
            'mw.merchant_withdraw_pass_time AS pass_time',
            'mw.merchant_withdraw_insert_time AS `time`',
        );

        //条件
        $config['where'][] = array('mw.merchant_id=[+]', $input['merchant_id']);

        //排序
        $config['orderby'][] = array('mw.merchant_withdraw_insert_time', true);
        $config['orderby'][] = array('mw.merchant_withdraw_id', true);

        //查询数据
        $data = object(parent::TABLE_MERCHANT_WITHDRAW)->select_page($config);

        //格式化数据
        $withdraw_type = object(parent::TABLE_MERCHANT_WITHDRAW)->get_type();
        $withdraw_method = object(parent::TABLE_MERCHANT_WITHDRAW)->get_method();
        foreach ($data['data'] as &$val) {
            $val['type'] = isset($withdraw_type[$val['type']]) ? $withdraw_type[$val['type']] : '未知';
            $val['method'] = isset($withdraw_method[$val['method']]) ? $withdraw_method[$val['method']] : '未知';
            $val['time'] = date('Y-m-d H:i:s', $val['time']);

            if ($val['fail_time'])
                $val['fail_time'] = date('Y-m-d H:i:s', $val['fail_time']);

            if ($val['pass_time'])
                $val['pass_time'] = date('Y-m-d H:i:s', $val['pass_time']);
        }

        return $data;
    }

}