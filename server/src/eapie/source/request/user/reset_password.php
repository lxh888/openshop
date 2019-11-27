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

class reset_password extends \eapie\source\request\user
{

    /**
     * 设置错误次数，达到这个错误次数则需要输出验证码
     * 
     * @var int
     */
    private $_error_count = 3;

    /**
     * 重置用户密码。
     *
     * api: USERRESETPASSWORD
     * req: [
     *   phone              [int] [必须|手机号]
     *   password           [str] [必须|新的登录密码]
     *   confirm_password   [str] [必须|确认新的登录密码]
     *   phone_verify_key   [str] [必须|手机验证码键名称，重置密码reset_password]
     *   phone_verify_code  [str] [必须|手机验证码]
     *   log_out            [bol] [再登录状态下不自动退出。如果为true，那么重置密码成功则重新登录，默认false]
     * ]
     * 
     * @return  bool
     */
    public function api($data = array())
    {
        //判断是否登录
        object(parent::REQUEST_SESSION)->check();

        //数据检测 $data["phone"] 是手机号在检测验证码的时候已经检测了
        object(parent::ERROR)->check($data, 'password', parent::TABLE_USER, array('args'), 'user_password');
        object(parent::ERROR)->check($data, 'confirm_password', parent::TABLE_USER, array('args'), 'user_confirm_password');
        if ($data['password'] !== $data['confirm_password']) {
            throw new error ('两次密码输入不一致');
        }
        //检测验证码，这里面会检测 $data["phone"] 的合法性
        object(parent::REQUEST_SESSION)->phone_verify_code_check($data);

        //获取手机数据
        $user_phone_data = object(parent::TABLE_USER_PHONE)->find($data['phone']);
        if (empty($user_phone_data)) {
            throw new error ('手机号登记数据获取异常');
        }
        //判断手机是否已经存在用户并且已经认证
        if (empty($user_phone_data['user_id']) || empty($user_phone_data['user_phone_state'])) {
            throw new error('手机号没有注册');
        }
        //检测用户ID是否有效
        object(parent::ERROR)->check($user_phone_data, 'user_id', parent::TABLE_USER, array('exists_id'));

        //获得用户密码
        $user_update_data = array();
        $user_update_data['user_left_password'] = md5($data['password'].$user_phone_data['user_id']);
        $user_update_data['user_right_password'] = md5($user_phone_data['user_id'].$data['password']);
        $user_update_data['user_update_time'] = time();
        $user_update_where = array();
        $user_update_where[] = array('user_id=[+]', $user_phone_data['user_id']);
        if (!object(parent::TABLE_USER)->update($user_update_where, $user_update_data)) {
            throw new error ('密码更新失败，用户信息更新异常');
        }

        //注意，只要是认证了的，都可以重置密码。并且用认证得到手机重置密码后，该手机会自动设为 登录手机号
        //手机类型。0 联系手机号，1 登录手机号
        if (empty($user_phone_data['user_phone_type'])) {
            $phone_update_where = array();
            $phone_update_where[] = array('user_phone_id=[+]', $data['phone']);
            $phone_update_where[] = array('[and] user_phone_state=1');
            $phone_update_where[] = array('[and] user_phone_type=0');
            $phone_update_data = array(
                'user_phone_type' => 1,
                'user_phone_update_time' => time()
                );  
            if (!object(parent::TABLE_USER_PHONE)->update($phone_update_where, $phone_update_data)) {
                throw new error ('手机类型更新失败，无法');
            }
        }

        //判断是否退出登陆，"log_out" 默认false不退出登陆。true退出登录
        if (!empty($data['log_out'])) {
            object(parent::REQUEST_USER)->log_out();
        }

        return true;
    }

    /**
     * 登录状态下以旧密码重置新密码
     * 
     * api: USERRESETPASSWORDSELF
     * req: {
     *  former_password   [str] [必须|旧密码],
     *  password          [str] [必须|新密码],
     *  confirm_password  [str] [必须|确认新密码],
     *  log_out           [bol] [是否重新登录，默认false]
     *  image_verify_key  [str] [当需要验证码时|验证码键名称],
     *  image_verify_code [str] [当需要验证码时|验证码]
     * }
     * 
     * 输错3次旧密码就要输入图片验证码
     * ['user_json']["reset_password_error_count"] 
     */
    public function api_self($data)
    {
        //判断是否登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        //检测数据
        object(parent::ERROR)->check($data, 'former_password', parent::TABLE_USER, array('args'), 'user_former_password');
        object(parent::ERROR)->check($data, 'password', parent::TABLE_USER, array('args'), 'user_password');
        object(parent::ERROR)->check($data, 'confirm_password', parent::TABLE_USER, array('args'), 'user_confirm_password');
        if ($data['password'] !== $data['confirm_password']) {
            throw new error ('两次新密码输入不一致');
        }

        //查询用户数据
        $user_data = object(parent::TABLE_USER)->find($user_id);
        $user_data['user_json'] = cmd(array($user_data['user_json']), 'json decode');

        //检查验证码
        if (
            isset($user_data['user_json']['reset_password_error_count'])
            && is_int($user_data['user_json']['reset_password_error_count'])
            && $user_data['user_json']['reset_password_error_count'] >= $this->$_error_count
        ) {
            object(parent::REQUEST_SESSION)->image_verify_code_check($data);
        }

        //判断旧密码
        if (
            $user_data['user_left_password'] !== md5($data['former_password'].$user_id)
            || $user_data['user_right_password'] !== md5($user_id.$data['former_password'])
        ) {
            $this->_count_error($user_data);
            throw new error ("旧密码错误");
        }

        //清空错误次数
        $this->_count_error($user_data, true);

        //更新用户密码
        $user_update_data = array();
        $user_update_data['user_left_password'] = md5($data['password'].$user_id);
        $user_update_data['user_right_password'] = md5($user_id.$data['password']);
        $user_update_data['user_update_time'] = time();
        $user_update_where = array();
        $user_update_where[] = array('user_id=[+]', $user_id);
        if (!object(parent::TABLE_USER)->update($user_update_where, $user_update_data)) {
            throw new error ('密码重置失败');
        }

        //判断是否退出登陆，"log_out" 默认false不退出登陆。true退出登录
        if (!empty($data['log_out'])) {
            object(parent::REQUEST_USER)->log_out();
        }

        return true;
    }

    /**
     * 记录/清理 登录错误次数
     * @param  [arr] $user_data [用户数据]
     * @param  [bol] $clear     [清空错误次数，默认false]
     * @return [bol]
     */
    private function _count_error($user_data, $clear = false)
    {
        if (empty($user_data['user_json']) || !is_array($user_data['user_json'])) {
            $user_json = array();
        } else {
            $user_json = $user_data['user_json'];
        }

        //是否清空错误次数
        if ($clear) {
            unset($user_json['reset_password_error_count']);
        } else {
            if ( !isset($user_json['reset_password_error_count']) || !is_int($user_json['reset_password_error_count'])) {
                $user_json['reset_password_error_count'] = 1;
            } else {
                $user_json['reset_password_error_count'] ++;
            }
        }

        $where = array();
        $where[] = array('user_id=[+]', $user_data['user_id']);
        $data = array();
        $data['user_json'] = cmd(array($user_json), 'json encode');

        object(parent::TABLE_USER)->update($where, $data);
    }

}