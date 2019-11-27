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

//收银员
class cashier extends \eapie\source\request\merchant
{

    /**
     * 添加收银员
     * 
     * api: MERCHANTCASHIERSELFADD
     * req:{
     *  phone                   [str]   [必填]  [用户的电话号码]
     *  merchant_id             [str]   [选填]  [商家ID]
     *  nickname   [str]   [选填]  [收银员名称]
     * }
     * 
     * @return  string  收银员ID
     */
    public function api_self_add($data = array())
    {
        // 检测登陆
        object(parent::REQUEST_USER)->check();

        // 检测手机号是否合法
        object(parent::ERROR)->check($data, 'phone', parent::TABLE_USER_PHONE, array('args'), 'user_phone_id');

        // 数据检测
        if (isset($data['merchant_id'])) {
            object(parent::ERROR)->check($data, 'merchant_id', parent::TABLE_MERCHANT_CASHIER, array('args'));
        };


        // 根据手机号获取用户信息
        $cashier_user = object(parent::TABLE_USER_PHONE)->find($data['phone']);

        // 判断用户是否存在
        if (empty($cashier_user))
            throw new error('该用户不存在！');

        $cashier_uid = $cashier_user['user_id'];


        $where = array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] merchant_id=[+]', $data['merchant_id'])
        );

        // 根据商家ID和user_id查询数据 
        $merchant_user = object(parent::TABLE_MERCHANT_USER)->find_where($where);

        // 根据传入的商家ID，判断是否此商家的商家用户
        if (empty($merchant_user))
            throw new error('不是此商家的商家用戶，或商家不存在，无法添加！');


        // 判断是否已经存在此收银员
        $array = array('user_id' => $cashier_uid, 'merchant_id' => $merchant_user['merchant_id']);
        $merchant_cashier = object(parent::TABLE_MERCHANT_CASHIER)->find_by_uid_and_merchant_id($array);
        if (!empty($merchant_cashier))
            throw new error('已经是此商家的收银员了！');

        // 判断是否传入收银员名字字段，没有则返回用户昵称
        $user_info = object(parent::TABLE_USER)->find($cashier_uid);
        $user_nickname = $user_info['user_nickname'];
        $merchant_cashier_name = isset($data['nickname']) ? $data['nickname'] : $user_nickname;


        // 要插入的数据
        $insert_data = array(
            'merchant_cashier_id' => object(parent::TABLE_MERCHANT_CASHIER)->get_unique_id(),
            'merchant_id' => $merchant_user['merchant_id'],
            'merchant_cashier_action_user' => $_SESSION['user_id'],
            'user_id' => $cashier_uid,
            'merchant_cashier_name' => $merchant_cashier_name,
            // 'merchant_cashier_info' => '',
            'merchant_cashier_state' => 1,
            // 'merchant_cashier_json' => '',
            'merchant_cashier_sort' => 0,
            'merchant_cashier_insert_time' => time(),
            'merchant_cashier_update_time' => time()
        );


        //插入数据
        if (object(parent::TABLE_MERCHANT_CASHIER)->insert($insert_data)) {
            return $insert_data['merchant_cashier_id'];
        } else {
            throw new error('操作失败');
        }
    }

    /**
     * 删除收银员
     * 
     * api: MERCHANTCASHIERSELFREMOVE
     * req:{
     *  cashier_id  [str]   [必填]  [用户的电话号码]
     * }
     * 
     * @param   string  收银员ID
     * @return  bool    
     */
    public function api_self_remove($data = array())
    {
        // 检测登陆
        object(parent::REQUEST_USER)->check();


        // 数据检测
        if (isset($data['cashier_id'])) {
            object(parent::ERROR)->check($data, 'cashier_id', parent::TABLE_MERCHANT_CASHIER, array('args'), 'merchant_cashier_id');
        };


        // 根据收银员ID查询商家ID
        // $merchant_id = object(parent::TABLE_MERCHANT_CASHIER)->find($merchant_cashier_id)['merchant_id'];
        $merchant = object(parent::TABLE_MERCHANT_CASHIER)->find($data['cashier_id']);
        if (empty($merchant))
            throw new error('没有找到！');
        $merchant_id = $merchant['merchant_id'];

        // 根据商家ID和user_id查询数据 
        $where = array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] merchant_id=[+]', $merchant_id)
        );
        $merchant_user = object(parent::TABLE_MERCHANT_USER)->find_where($where);

        // 判断商家用户是否收银员所属商家
        if (empty($merchant_user))
            throw new error('不是此商家的商家用戶，无权删除！');

        // 删除数据
        $res = object(parent::TABLE_MERCHANT_CASHIER)->remove($data['cashier_id']);
        if ($res) {
            return $data['cashier_id'];
        } else {
            throw new error('删除失败！');
        }
    }


    /**
     * 根据手机号和商家ID查找收银员
     * 
     * 保留，暂时不用
     * 
     * @param   array   [手机号，商家ID]
     * @return  array
     */
    public function api_self_findbyphone($data = array())
    {
        // 检测登陆
        object(parent::REQUEST_USER)->check();

        // 检测手机号是否合法
        object(parent::ERROR)->check($data, 'phone', parent::TABLE_USER_PHONE, array('args'), 'user_phone_id');

        // 数据检测
        if (isset($data['merchant_id'])) {
            object(parent::ERROR)->check($data, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
        };

        // 根据商家ID和user_id查询数据 
        $where = array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] merchant_id=[+]', $data['merchant_id'])
        );
        $merchant_user = object(parent::TABLE_MERCHANT_USER)->find_where($where);

        // 判断商家用户是否收银员所属商家
        if (empty($merchant_user))
            throw new error('不是此商家的商家用戶，无权操作！');

        // 根据手机号获取用户信息
        $user_id = object(parent::TABLE_USER_PHONE)->find_user_exists($data['phone'])['user_id'];

        // 根据手机ID，判断用户是否存在
        if (empty($user_id))
            throw new error('该用户不存在！');

        // 根据user_id查找数据
        $array = array('user_id' => $user_id, 'merchant_id' => $data['merchant_id']);
        $merchant_cashier = object(parent::TABLE_MERCHANT_CASHIER)->find_by_uid_and_merchant_id($array);

        if (empty($merchant_cashier))
            throw new error('数据为空');
        return $merchant_cashier;
    }

    /**
     * 查询所有收银员
     * api:MERCHANTCASHIERSELFLIST
     * req:{
     *  merchant_id [str]  [必填]  [商家ID]
     *  own         [bool]  [选填]  [是否只查询商家用户自己添加的收银员]
     * }
     * 
     * @param   array   
     * @return  array   收银员列表
     */
    public function api_self_list($data = array())
    {
        // 检测登陆
        object(parent::REQUEST_USER)->check();

        // 数据检测
        if (isset($data['merchant_id'])) {
            object(parent::ERROR)->check($data, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
        };


        // 根据商家ID和user_id查询数据 
        $where = array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] merchant_id=[+]', $data['merchant_id'])
        );
        $merchant_user = object(parent::TABLE_MERCHANT_USER)->find_where($where);

        // 判断商家用户是否收银员所属商家
        if (empty($merchant_user))
            throw new error('不是此商家的商家用戶，无权操作！');

        //查询配置
        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_USER),
        );

        $config['where'] = array(
            array('mc.merchant_id=[+]', $data['merchant_id']),

        );

        if (isset($data['own']) && $data['own'] == true) {
            $config['where'][] = array('[and] mc.action_user=[+]', $_SESSION['user_id']);
        }

        //字段
        $config['select'] = array(
            "mc.merchant_cashier_id as cashier_id",
            "mc.merchant_cashier_action_user as action_user",
            "mc.merchant_id",
            "mc.user_id",
            "mc.merchant_cashier_name as nickname",
            "mc.merchant_cashier_state as state",
            "mc.merchant_cashier_insert_time as insert_time",
        );

        //排序
        $config['orderby'] = array();
        $config['orderby'][] = array('mc.merchant_cashier_insert_time', true);

        //查询条件
        // $config['where'][] = array('ca.cms_article_state=1');
        // $config['where'][] = array('[and] ca.cms_article_trash=0');//没有被删除的


        //查询数据
        // $data = object(parent::TABLE_MERCHANT_CASHIER)->select_page($config);
        $cashier_data = object(parent::TABLE_MERCHANT_CASHIER)->select_page($config);
        if (empty($cashier_data)) {
            return $cashier_data;
        }

        $user_ids = array();
        foreach ($cashier_data['data'] as $v) {
            $user_ids[] = $v['user_id'];
        }

        //获取分类数据
        $in_string = "\"" . implode("\",\"", $user_ids) . "\"";
        $phone_config = array(
            'where' => array(),
            'select' => array(
                'distinct( user_id ) as user_id',
                'user_phone_id as phone',
            ),
            'orderby' => array(
                array('user_phone_type', true),
                array('user_phone_id')
            )
        );
        $phone_config['where'][] = array("[and] user_id IN([-])", $in_string, true); //是不加单引号并且强制不过滤
        $phone_config['where'][] = array("[and] user_phone_state=1");

        $user_phone_data = object(parent::TABLE_USER_PHONE)->select($phone_config);

        foreach ($cashier_data['data'] as &$v) {
            if (empty($user_phone_data)) {
                break;
            }
            foreach ($user_phone_data as $k2 => $v2) {
                if ($v2['user_id'] == $v['user_id']) {
                    $v['phone'] = $v2['phone'];
                    unset($user_phone_data[$k2]);
                    break;
                }
            }
        }
        return $cashier_data;
    }



    /**
     * 启用或禁用收银员
     * 
     * api:MERCHANTCASHIERSELFDISABLE
     * req:{
     *  id      [str]  [必填]  [收银员ID]
     *  state   [int]  [必填]  [状态：0 封禁，1 正常，2审核中]
     * }
     */
    public function api_self_disable($data = array())
    {
        // 检测登陆
        object(parent::REQUEST_USER)->check();

        // 检测数据
        object(parent::ERROR)->check($data, 'id', parent::TABLE_MERCHANT_CASHIER, array('args'), 'merchant_cashier_id');
        object(parent::ERROR)->check($data, 'state', parent::TABLE_MERCHANT_CASHIER, array('args'), 'merchant_cashier_state');

        // 根据收银员ID查找数据
        $merchant_cashier = object(parent::TABLE_MERCHANT_CASHIER)->find($data['id']);

        if (empty($merchant_cashier))
            throw new error('操作不正确，不存在该收银员！');

        // 根据商家ID和user_id查询数据 
        $where = array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] merchant_id=[+]', $merchant_cashier['merchant_id'])
        );
        $merchant_user = object(parent::TABLE_MERCHANT_USER)->find_where($where);

        // 判断商家用户是否收银员所属商家
        if (empty($merchant_user))
            throw new error('不是此商家的商家用戶，无权操作！');

        $update_where = array();
        $update_where[] = array('merchant_cashier_id=[+]', $data['id']);
        $val = array(
            'merchant_cashier_state' => $data['state'],
            'merchant_cashier_update_time' => time()
        );

        $res = object(parent::TABLE_MERCHANT_CASHIER)->update($update_where, $val);

        if (!$res)
            throw new error('修改失败！');

        return $data['id'];
    }









    /**
     * 二维码——收银员的商家收钱二维码
     *
     * api: MERCHANTCASHIERSELFQRCODEMONEYPLUS
     * 
     * req: {
     *  merchant_id [str] [必填] [商家ID]
     *  level       [str] [可选] [级别,容错率,(L,M,Q,H)]
     *  size        [int] [可选] [二维码大小，默认3]
     *  padding     [int] [可选] [二维码内边距,默认0]
     * }
     * 
     * @param  [arr]  $input [请求参数]
     * @return image
     */
    public function api_self_qrcode_money_plus($input = array())
    {
        //二维码参数
        $config = array();
        if (isset($input['level']))
            $config['level'] = $input['level'];
        if (isset($input['size']))
            $config['size'] = $input['size'];
        if (isset($input['padding']))
            $config['padding'] = $input['padding'];

        //二维码内容
        $data = array(
            'errno' => 0,
            'type' => 'merchant_money_plus',
            'data' => array()
        );

        //是否合法用户
        $user_id = null;
        if (object(parent::REQUEST_USER)->check(true)) {
            $user_id = $_SESSION['user_id'];
        } else {
            $data['errno'] = 1;
            $data['error'] = '非法用户';
        }

        //是否合法商家
        if ($user_id) {

            $mch_id = $input['merchant_id'];

            if (!empty($mch_id) && is_string($mch_id)) {
                $cashier = object(parent::TABLE_MERCHANT_CASHIER)->find_by_uid_and_merchant_id(
                    array(
                        'user_id' => $user_id,
                        'merchant_id' => $mch_id
                    )
                );

                if (empty($cashier)) {
                    $data['errno'] = 1;
                    $data['error'] = '非法商家';
                } else {
                    // 商家ID
                    $data['data']['merchant_id'] = $mch_id;
                    // 收银员ID
                    $data['data']['cashier_id'] = $cashier['merchant_cashier_id'];
                    // 操作人的用户ID
                    $data['data']['user_id'] = $user_id;
                }
            } else {
                $data['errno'] = 1;
                $data['error'] = '非法商家';
            }
        }

        //生成二维码
        $config['data'] = $data;
        object(parent::PLUGIN_PHPQRCODE)->output($config);
        exit;
    }



    /**
     * 获取收银员的信息
     * 
     * api:MERCHANTCASHIERSELF
     * req:{
     *  merchant_id [str]   [选填]  [商家ID]
     * }
     * 
     * @param   array   商家ID   
     * @return  array
     */
    public function api_self($input = array())
    {
        // 检测登陆
        object(parent::REQUEST_USER)->check();

        //检测find_user  传用户ID  

        if (!isset($input['merchant_id'])) {

            $data = object(parent::TABLE_MERCHANT_CASHIER)->find_byuid($_SESSION['user_id']);
        } else {
            // 检测数据
            object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT_CASHIER, array('args'));
            $data = object(parent::TABLE_MERCHANT_CASHIER)->find_by_uid_and_merchant_id(
                array(
                    'user_id' => $_SESSION['user_id'],
                    'merchant_id' => $input['merchant_id']
                )
            );
        }

        // 判断是否收银员
        if (empty($data)) {
            throw new error('不是收银员');
        }
        if ($data['merchant_cashier_state'] != 1) {
            throw new error('收银员状态异常');
        }
        $merchant = object(parent::TABLE_MERCHANT)->find($data['merchant_id']);
        if (empty($merchant)) {
            throw new error('商家不合法');
        }

        $data['merchant_logo'] = $merchant['merchant_logo_image_id'];
        $data['merchant_name'] = $merchant['merchant_name'];
        return $data;
    }


    /**
     * 收款记录
     * api:MERCHANTCASHIERSELFORDERLIST
     * req:{
     *  merchant_id [str]   [必填]  [商家ID]
     * }
     * 
     * 商家ID必填
     */
    public function api_self_order_list($data = array())
    {


        // [收款方]
        // order_plus_method  商家钱包   用常理  parent::PAY_METHOD_MERCHANT_MONEY
        // order_plus_account_id 商家ID


        // order_action_user_id 收银员用户ID

        // 【付款方】
        // order_minus_method 用户钱包、用户微信、支付宝
        // order_minus_account_id 用户ID

        // order_pay_state
        // order_state



        // 检测登陆
        object(parent::REQUEST_USER)->check();

        // 检测数据
        if(isset($data['merchant_id'])){
            object(parent::ERROR)->check($data, 'merchant_id', parent::TABLE_MERCHANT_CASHIER, array('args'));
        }else{
            throw new error('缺少商家ID');
        }

        // 判断是否此商家的收银员
        $cashier = object(parent::TABLE_MERCHANT_CASHIER)->find_by_uid_and_merchant_id(array(
            'user_id' => $_SESSION['user_id'],
            'merchant_id' => $data['merchant_id']
        ));

        // printexit($_SESSION['user_id'],$data['merchant_id'],$cashier);
        if (empty($cashier)) {
            throw new error('不是此商家的收银员');
        };

        //查询配置
        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_USER),
        );

        $config['where'] = array(
            array('o.order_action_user_id=[+]', $_SESSION['user_id']),
            array('[and] o.order_plus_method=[+]', parent::PAY_METHOD_MERCHANT_MONEY),
            array('[and] o.order_plus_account_id=[+]', $data['merchant_id']),
            array('[and] o.order_pay_state=1 and o.order_state=1'),
            // array('o.order_state=[-]',1),

        );


        //字段
        $config['select'] = array(
            "o.order_id",
            "o.order_minus_method as method",
            "o.order_minus_account_id as pay_user_id",
            "o.order_plus_value as value",
            // "o.order_pay_time as pay_time"
            // 时间戳转换格式
            "FROM_UNIXTIME(o.order_pay_time,'%Y-%m-%d %H:%i') as pay_time"
        );

        //排序
        $config['orderby'] = array();
        $config['orderby'][] = array('o.order_insert_time', true);
        $config['orderby'][] = array('o.order_id');



        //查询数据
        $order_data = object(parent::TABLE_ORDER)->select_page($config);
        // printexit($order_data);
        if (empty($order_data)) {
            return $order_data;
        }

        $user_ids = array();
        foreach ($order_data['data'] as $v) {
            if (!in_array($v['pay_user_id'], $user_ids)) {
                $user_ids[] = $v['pay_user_id'];
            }
        }

        //获取分类数据
        $in_string = "\"" . implode("\",\"", $user_ids) . "\"";
        $phone_config = array(
            'where' => array(),
            'select' => array(
                'distinct( user_id ) as user_id',
                'user_phone_id as phone',
            ),
            'orderby' => array(
                array('user_phone_type', true),
                array('user_phone_id')
            )
        );
        $phone_config['where'][] = array("[and] user_id IN([-])", $in_string, true); //是不加单引号并且强制不过滤
        $phone_config['where'][] = array("[and] user_phone_state=1");

        $user_phone_data = object(parent::TABLE_USER_PHONE)->select($phone_config);



        $user_config = array(
            'where' => array(),
            'select' => array(
                'distinct( user_id ) as user_id',
                'user_nickname as nickname',
                'user_logo_image_id as user_logo',
            )
        );
        $user_config['where'][] = array("[and] user_id IN([-])", $in_string, true); //是不加单引号并且强制不过滤

        $user_data = object(parent::TABLE_USER)->select($user_config);
        // printexit($user_phone_data);



        foreach ($order_data['data'] as &$v) {
            if (empty($user_phone_data)) {
                break;
            }
            foreach ($user_phone_data as $v1) {
                if ($v1['user_id'] == $v['pay_user_id']) {
                    $v['phone'] = $v1['phone'];
                    //  unset($user_phone_data[$k2]);
                    break;
                }
            }
            foreach ($user_data as $v2) {
                if ($v2['user_id'] == $v['pay_user_id']) {
                    $v['nickname'] = $v2['nickname'];
                    $v['user_logo'] = $v2['user_logo'];
                    break;
                }
            }
        }

        return $order_data;
    }














}