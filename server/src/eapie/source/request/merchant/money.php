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

class money extends \eapie\source\request\merchant
{

    /**
     * 获取当前登录用户的指定商家余额 
     * 
     * api: MERCHANTMONEYSELF
     * req: {
     *  merchant_id  [str] [可选] [商家ID，默认该用户所属的商家第一个ID]
     * }
     * 
     * @return  [int] [商家余额]
     */
    public function api_self($input = array())
    {
        //检测用户角色
        $merchant_id = $this->check_role($input);
        $data = object(parent::TABLE_MERCHANT_MONEY)->find_now_data($merchant_id);
        return empty($data['merchant_money_value']) ? 0 : $data['merchant_money_value'];
    }

    /**
     * 列表数据
     *
     * api: MERCHANTMONEYSELFLIST
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


        //临时修改
        $config['select'] = array(
            'mm.merchant_money_id AS id',
            'mm.merchant_money_type AS type',
            'mm.merchant_money_plus AS plus',
            'mm.merchant_money_minus AS minus',
            'mm.merchant_money_value AS value',
            'mm.merchant_money_time AS `time`',
        );

        //条件
        $config['where'][] = array('mm.merchant_id=[+]', $input['merchant_id']);

        //排序
        $config['orderby'][] = array('mm.merchant_money_time', true);
        $config['orderby'][] = array('mm.merchant_money_id', true);

        //查询数据
        $data = object(parent::TABLE_MERCHANT_MONEY)->select_page($config);
        //获取商家钱包的订单备注
        if (empty($data['data'])) {
            return $data;
        }

        $ids = array();
        foreach ($data['data'] as &$val) {
            $ids[] = $val['id'];
        }

        //获取订单备注
        $in_string = "\"" . implode("\",\"", $ids) . "\"";
        $order_plus_where = array();
        $order_plus_where[] = array("order_pay_state=1");
        $order_plus_where[] = array("[and] order_plus_method=[+]", "merchant_money");
        $order_plus_where[] = array("[and] order_plus_transaction_id IN([-])", $in_string, true); //是不加单引号并且强制不过滤
        $order_plus_data = object(parent::TABLE_ORDER)->select(array("where" => $order_plus_where));

        $order_minus_where = array();
        $order_minus_where[] = array("order_pay_state=1");
        $order_minus_where[] = array("[and] order_minus_method=[+]", "merchant_money");
        $order_minus_where[] = array("[and] order_minus_transaction_id IN([-])", $in_string, true); //是不加单引号并且强制不过滤
        $order_minus_data = object(parent::TABLE_ORDER)->select(array("where" => $order_minus_where));

        //格式化数据
        $trade_type = object(parent::TABLE_ORDER)->get_type();
        foreach ($data['data'] as &$val) {
            $val['type'] = isset($trade_type[$val['type']]) ? $trade_type[$val['type']] : '未知';
            $val['time'] = date('Y-m-d H:i:s', $val['time']);

            //判断符号
            if ($val['plus'] > 0) {
                $val['sign']  = '+';
                $val['trade'] = $val['plus'];

                if (!empty($order_plus_data)) {
                    foreach ($order_plus_data as $order_v) {
                        if ($order_v['order_plus_transaction_id'] == $val['id']) {
                            $val['comment'] = $order_v['order_comment'];
                            break;
                        }
                    }
                }
            } else {
                $val['sign']  = '-';
                $val['trade'] = $val['minus'];

                if (!empty($order_minus_data)) {
                    foreach ($order_minus_data as $order_v) {
                        if ($order_v['order_minus_transaction_id'] == $val['id']) {
                            $val['comment'] = $order_v['order_comment'];
                            break;
                        }
                    }
                }
            }

            unset($val['plus'], $val['minus']);
        }

        return $data;



        //字段
        /*$config['select'] = array(
            'mm.merchant_money_id AS id',
            'mm.merchant_money_type AS type',
            'mm.merchant_money_plus AS plus',
            'mm.merchant_money_minus AS minus',
            'mm.merchant_money_value AS value',
            'mm.merchant_money_time AS `time`',
            'o.order_comment AS comment',
        );

        //条件
        $config['where'][] = array('mm.merchant_id=[+]', $input['merchant_id']);

        //排序
        $config['orderby'][] = array('mm.merchant_money_time', true);
        $config['orderby'][] = array('mm.merchant_money_id', true);

        //查询数据
        $data = object(parent::TABLE_MERCHANT_MONEY)->select_order_page($config);

        //格式化数据
        $trade_type = object(parent::TABLE_ORDER)->get_type();
        foreach ($data['data'] as &$val) {
            $val['type'] = isset($trade_type[$val['type']]) ? $trade_type[$val['type']] : '未知';
            $val['time'] = date('Y-m-d H:i:s', $val['time']);

            //判断符号
            if ($val['plus'] > 0) {
                $val['sign']  = '+';
                $val['trade'] = $val['plus'];
            } else {
                $val['sign']  = '-';
                $val['trade'] = $val['minus'];
            }

            unset($val['plus'], $val['minus']);
        }

        return $data;*/
    }




    /** 
     * ----- Mr.Zhao ----- 2019.06.11 -----
     * 
     * 商家收款（微信、支付宝、用户钱包）
     * api:MERCHANTMONEYSELFPAYMENT
     * 
     * 
     * [{"merchant_id":"商家ID","action_user_id":"商家用户或者收银员的user_id","money_fen":"必须|要支付余额(人民币，分)","pay_method":"支付方式 weixinpay 微信支付、alipay 支付宝支付、user_money 用户钱包支付","weixin_login_code":"微信支付是需要，用于换取用户OpenID","weixin_trade_type":"微信的交易类型，必须是大写，如JSAPI|APP|MWEB|NATIVE","comment":"备注信息"}]
     * {"class":"merchant/money","method":"api_self_payment"}
     * 
     * @param	array		$data
     * @return string	返回订单ID
     * 
     */
    public function api_self_payment($data = array())
    {
        //parent::PAY_METHOD_WEIXINPAY => "微信支付",
        //parent::PAY_METHOD_ALIPAY => "支付宝支付",
        //parent::PAY_METHOD_USER_MONEY => "用户钱包",

        // 检测是否登录
        object(parent::REQUEST_USER)->check();
        object(parent::ERROR)->check($data, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
        object(parent::ERROR)->check($data, 'pay_method', parent::TABLE_ORDER, array('args'));
        object(parent::ERROR)->check($data, 'money_fen', parent::TABLE_ORDER, array('args'));
        object(parent::ERROR)->check($data, 'comment', parent::TABLE_ORDER, array('args'));
        // 判断是否传入操作人ID，以后换码时恢复
        if (isset($data['action_user_id']) && $data['action_user_id'] != '') {
            object(parent::ERROR)->check($data, 'action_user_id', parent::TABLE_ORDER, array('args', 'exists'), 'order_action_user_id');
        } else {
            $data['action_user_id'] = $_SESSION['user_id'];
        }


        //获取配置
        if ($data['money_fen'] < 1) {
            throw new error("支付余额不能小于0");
        }

        //获取商家数据
        $merchant_data = object(parent::TABLE_MERCHANT)->find($data['merchant_id']);
        if (empty($merchant_data["merchant_state"]) ||  $merchant_data["merchant_state"] != 1) {
            throw new error('商家未认证');
        }

        //获取商户的库存积分  判断要赠送的积分
        $merchant_credit_data = object(parent::TABLE_MERCHANT_CREDIT)->find_now_data($data['merchant_id']);

        //获取配置
        if (!empty($merchant_data["merchant_json"])) {
            $merchant_data["merchant_json"] = cmd(array($merchant_data["merchant_json"]), "json decode");
        }
        $config_rmb_consume_user_credit = array();
        if (!empty($merchant_data["merchant_json"]["config_rmb_consume_user_credit"])) {
            $config_rmb_consume_user_credit = $merchant_data["merchant_json"]["config_rmb_consume_user_credit"];
        }
        $rmb_consume_user_credit = object(parent::REQUEST_APPLICATION)->get_rmb_consume_user_credit($data['money_fen'], $config_rmb_consume_user_credit, TRUE);
        //用户消费赠送积分开启中
        if (!empty($rmb_consume_user_credit['state'])) {
            if (empty($merchant_credit_data) || ($merchant_credit_data["merchant_credit_value"] - $rmb_consume_user_credit['user_credit_plus']) < 0) {
                throw new error('商家积分不足');
            }
        }


        if ($data['pay_method'] == parent::PAY_METHOD_WEIXINPAY || $data['pay_method'] == parent::PAY_METHOD_ALIPAY) {

            //支付宝、微信支付
            if (!empty($_SESSION['user']['user_nickname'])) {
                $user_comment = $_SESSION['user']['user_nickname'];
            } else {
                //获取当前用户登录手机号
                $user_phone_data = object(parent::TABLE_USER_PHONE)->find_user_login_data($_SESSION['user_id']);
                if (empty($user_phone_data["user_phone_id"])) {
                    throw new error('登录手机号异常');
                }
                $user_comment = $user_phone_data["user_phone_id"];
            }

            //备注信息
            $data["comment"] = !empty($data["comment"]) ? " 《" . $merchant_data["merchant_name"] . "》 [" . $user_comment . "] - " . $data["comment"] : " 《" . $merchant_data["merchant_name"] . "》  [" . $user_comment . "]";

            $insert = array(
                "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
                "order_type" => parent::TRANSACTION_TYPE_RECHARGE, //充值
                "order_comment" => "商家钱包余额收款" . $data["comment"],
                "order_plus_method" => "merchant_money", //收款方式
                "order_plus_account_id" => $data['merchant_id'], //收款账户ID
                "order_plus_value" => $data['money_fen'], //收款值
                "order_plus_update_time" => time(),

                "order_action_user_id" => $data['action_user_id'],
                "order_minus_method" => $data["pay_method"],
                "order_minus_account_id" => $_SESSION['user_id'],
                "order_minus_value" => $data['money_fen'], //支付款人民币
                "order_minus_update_time" => time(),
                "order_state" => 1, //确定订单
                "order_pay_state" => 0, //未支付
                "order_insert_time" => time(),
                "order_json" => array(
                    "rmb_consume_user_credit" => $rmb_consume_user_credit,
                )
            );


            //获取支付配置
            $return_api = object(parent::REQUEST_APPLICATION)->get_pay_config($data, $insert["order_json"], array(
                "money_fen" => $insert["order_minus_value"],
                "subject" => "商家收款-钱包余额收款",
                "body" => "商家钱包余额收款",
                "order_id" => $insert['order_id']
            ));


            $return_api['order_id'] = $insert['order_id'];
            if (!empty($insert["order_json"]) && is_array($insert["order_json"])) {
                $insert["order_json"] = cmd(array($insert["order_json"]), "json encode");
            }

            if (object(parent::TABLE_ORDER)->insert($insert)) {
                return $return_api;
            } else {
                throw new error("操作失败");
            }
        } else
        if ($data['pay_method'] == parent::PAY_METHOD_USER_MONEY) {
            //用户钱包支付
            object(parent::ERROR)->check($data, 'pay_password', parent::TABLE_USER, array('args'));
            //检测支付密码
            $res = object(parent::TABLE_USER)->check_pay_password($data['pay_password']);
            if ($res !== true)
                throw new error($res);

            //获取用户余额数据
            $user_money_data = object(parent::TABLE_USER_MONEY)->find_now_data($_SESSION['user_id']);
            if (
                empty($user_money_data) || ($user_money_data["user_money_value"] - $data["money_fen"]) < 0
            ) {
                throw new error('用户余额不足');
            }

            if (!empty($_SESSION['user']['user_nickname'])) {
                $user_comment = $_SESSION['user']['user_nickname'];
            } else {
                //获取当前用户登录手机号
                $user_phone_data = object(parent::TABLE_USER_PHONE)->find_user_login_data($_SESSION['user_id']);
                if (empty($user_phone_data["user_phone_id"])) {
                    throw new error('登录手机号异常');
                }
                $user_comment = $user_phone_data["user_phone_id"];
            }

            //备注信息
            $data["comment"] = !empty($data["comment"]) ? " 《" . $merchant_data["merchant_name"] . "》 [" . $user_comment . "] - " . $data["comment"] : " 《" . $merchant_data["merchant_name"] . "》  [" . $user_comment . "]";

            $order_id = object(parent::TABLE_USER_MONEY)->insert_transfer_merchant_money(array(
                "merchant_credit" => $merchant_credit_data,
                "user_id" => $_SESSION['user_id'],
                "user_money" => $user_money_data,
                "transfer_money_value" => $data["money_fen"],
                "order_comment" => "商家收款" . $data["comment"],
                "order_plus_account_id" => $data['merchant_id'],
                "order_action_user_id" => $data['action_user_id'],
                "order_json" => array(
                    "rmb_consume_user_credit" => $rmb_consume_user_credit,
                )
            ));

            if (empty($order_id)) {
                throw new error('操作失败');
            } else {
                return $order_id;
            }
        } else {
            throw new error('支付方式异常');
        }
    }
}