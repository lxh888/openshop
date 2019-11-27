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

//商家用户
class user extends \eapie\source\request\merchant
{


    /**
     * 添加申请
     *
     * api: MERCHANTUSERSELFADD
     * req: {
     *  merchant_id [str] [必填] [商家ID]
     *  name        [str] [必填] [商家用户名称]
     * }
     * 
     * @return string 商家用户ID
     */
    public function api_self_add($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测输入
        object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args', 'exists_id'));
        object(parent::ERROR)->check($input, 'name', parent::TABLE_MERCHANT_USER, array('args'), 'merchant_user_name');

        //是否已存在
        if (object(parent::TABLE_MERCHANT_USER)->check_exist($_SESSION['user_id'], $input['merchant_id']))
            return true;

        $insert_data['merchant_user_id'] = object(parent::TABLE_MERCHANT_USER)->get_unique_id();
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['merchant_id'] = $input['merchant_id'];
        $insert_data['merchant_user_name'] = $input['name'];
        $insert_data['merchant_user_insert_time'] = time();
        $insert_data['merchant_user_update_time'] = time();

        //插入数据
        if (object(parent::TABLE_MERCHANT_USER)->insert($insert_data)) {
            return $insert_data['merchant_user_id'];
        } else {
            throw new error('操作失败');
        }
    }


    /**
     * 查询当前用户的认证状态
     *
     * api: MERCHANTUSERSELFSTATE
     * req: {
     *  merchant_id [str] [可选] [商家ID]
     * }
     * 
     * @return integer [0未通过审核，1通过审核，2待审核，3编辑中, 4不存在]
     */
    public function api_self_state($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $where = array(
            array('user_id=[+]', $_SESSION['user_id']),
        );

        //是否指定商家ID
        if (isset($input['merchant_id'])) {
            object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
            $where[] = array('[and] merchant_id=[+]', $input['merchant_id']);
        }

        //查询数据
        $data = object(parent::TABLE_MERCHANT_USER)->find_where($where);
        if (empty($data))
            return 4;

        return $data['merchant_user_state'];
    }


    /**
     * 检测当前商家用户是否认证
     * api: MERCHANTUSERSELFIDENTITYCHECK
     * 
     * req: {
     *  merchant_id [str] [必填] [商家ID]
     * }
     * 
     * @param  array  $input 请求参数
     * @return bool
     */
    public function api_self_identity_check($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //校验数据
        object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
        if (!object(parent::TABLE_MERCHANT_USER)->check_state($_SESSION['user_id'], $input['merchant_id'])) {
            return false;
        }
        //查询数据
        $data = object(parent::TABLE_USER_IDENTITY)->find($_SESSION['user_id']);
        if (empty($data) || $data['user_identity_state'] != 1)
            return false;
        return object(parent::TABLE_USER_IDENTITY)->check_state($_SESSION['user_id'], $data['user_identity_update_time']);
    }




    /**
     *  ----- Mr.Zhao ----- 2019.06.11 -----
     * 
     * 二维码——当前用户的商家的收钱二维码
     *
     * api: MERCHANTUSERSELFQRCODEMONEYPLUS
     * req: {
     *  merchant_id [str] [可选] [商家ID，默认该用户的第一个商家ID]
     *  level   [str] [可选] [级别,容错率,(L,M,Q,H)]
     *  size    [int] [可选] [二维码大小，默认3]
     *  padding [int] [可选] [二维码内边距,默认0]
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
            //是否指定商家ID
            $mch_id = null;
            if (!empty($input['merchant_id']) && is_string($input['merchant_id'])) {
                $mch_id = $input['merchant_id'];
            } else {
                $mch_ids = object(parent::TABLE_MERCHANT_USER)->get_mch_ids($user_id);
                if (empty($mch_ids)) {
                    $data['errno'] = 1;
                    $data['error'] = '非法商家';
                } else {
                    $mch_id = $mch_ids[0];
                }
            }

            $where = array();
            $where[] = array('user_id=[+]', $user_id);
            $where[] = array('[and] merchant_id=[+]', $mch_id);
            $merchant_user = object(parent::TABLE_MERCHANT_USER)->find_where($where);

            if (!empty($merchant_user)) {
                // 商家ID
                $data['data']['merchant_id'] = $mch_id;
                // 商家用户ID
                $data['data']['merchant_user_id'] = $merchant_user['merchant_user_id'];
                // 操作人的用户ID
                $data['data']['user_id'] = $user_id;
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
}