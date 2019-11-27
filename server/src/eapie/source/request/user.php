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




namespace eapie\source\request;

use eapie\main;
use eapie\error;

class user extends main
{

    //权限码
    const AUTHORITY_USER_ADD = "user_add"; //添加权限
    const AUTHORITY_USER_READ = "user_read"; //读取权限
    const AUTHORITY_USER_EDIT = "user_edit"; //编辑权限
    //用户手机号
    const AUTHORITY_USER_PHONE_ADD = "user_phone_add"; //添加用户认证手机
    const AUTHORITY_USER_PHONE_EDIT = "user_phone_edit"; //编辑用户认证手机
    const AUTHORITY_USER_PHONE_REMOVE = "user_phone_remove"; //删除用户认证手机  
    const AUTHORITY_USER_PHONE_VERIFY_CODE = "user_phone_verify_code"; //用户手机检索


    //用户认证
    const AUTHORITY_USER_IDENTITY_EDIT = 'user_identity_edit';
    const AUTHORITY_USER_IDENTITY_READ = 'user_identity_read';

    const AUTHORITY_CREDIT_READ   = 'user_credit_read';
    const AUTHORITY_CREDIT_EDIT   = 'user_credit_edit';

    const AUTHORITY_MONEY_READ = 'user_money_read';
    const AUTHORITY_MONEY_EDIT = 'user_money_edit';

    //赠送收益
    const AUTHORITY_MONEY_EARNING_READ = 'user_money_earning_read';
    const AUTHORITY_MONEY_EARNING_EDIT = 'user_money_earning_edit';

    //养老金	
    const AUTHORITY_MONEY_ANNUITY_READ = 'user_money_annuity_read';
    const AUTHORITY_MONEY_ANNUITY_EDIT = 'user_money_annuity_edit';

    //扶贫资金操作	
    const AUTHORITY_MONEY_HELP_READ = 'user_money_help_read';
    const AUTHORITY_MONEY_HELP_EDIT = 'user_money_help_edit';

    //服务费操作	
    const AUTHORITY_MONEY_SERVICE_READ = 'user_money_service_read';

    //消费共享金操作	
    const AUTHORITY_MONEY_SHARE_READ = 'user_money_share_read';



    const AUTHORITY_CONFIG_READ = 'user_config_read';
    const AUTHORITY_CONFIG_EDIT = 'user_config_edit';



    const AUTHORITY_COMMENT_READ = "user_comment_read";
    const AUTHORITY_COMMENT_REMOVE = "user_comment_remove";
    const AUTHORITY_COMMENT_EDIT = 'user_comment_edit';


    const AUTHORITY_WITHDRAW_READ = 'user_withdraw_read'; //用户钱包提现读取
    const AUTHORITY_WITHDRAW_STATE = 'user_withdraw_state'; //用户钱包提现审核	


    const AUTHORITY_ACCOUNT_EXCEL    = 'user_account_excel'; //用户账户导出菜单
    const AUTHORITY_MONEY_ANNUITY_EXCEL    = 'user_money_annuity_excel'; //用户养老基金Excel导出
    const AUTHORITY_MONEY_HELP_EXCEL    = 'user_money_help_excel'; //用户扶贫基金Excel导出
    const AUTHORITY_MONEY_SERVICE_EXCEL    = 'user_money_service_excel'; //用户服务费Excel导出
    const AUTHORITY_MONEY_EXCEL    = 'user_money_excel'; //用户预付费Excel导出
    const AUTHORITY_EXCEL    = 'user_excel'; //用户Excel导出

    const AUTHORITY_ADD_PARENT_USER = 'add_parent_user'; // 添加该用户所在区域的区域代理为邀请人
    const AUTHORITY_UPDATE_RECOMMEND = 'update_recommend'; // E麦商城更新分销关系链缓存

    // E麦
    const AUTHORITY_REPLACEMENT_COUPON = 'replacement_coupon'; //补发393会员优惠券
    const AUTHORITY_RESET_USER_RECOMMEND = 'reset_user_recommend'; //重置会员邀请关系及身份

    // 伊起购
    const AUTHORITY_ADMIN_OFFICER_READ = "user_officer_read";   //查看团长列表
    const AUTHORITY_ADMIN_OFFICER_EDIT = "user_officer_edit";   //编辑、审核团长
    const AUTHORITY_ADMIN_OFFICER_DELETE = "user_officer_delete";   //团长申请信息删除

    /**
     * 登录方式
     * 
     * @var array
     */
    private $_log_in_method = array(
        'unknown' => '未知',
        'phone_sign_up' => '手机注册并登录',
        'phone_log_in' => '手机密码登录',
        'weixin_log_in' => '微信登录',
        'weixin_applet_log_in' => '微信小程序登录',
    );




    /**
     * 用户登陆操作
     * 
     * email_log_in             邮箱登录
     * email_sign_up            邮箱注册自动登录
     * 
     * @param   string      $user_id        用户编号
     * @param   string      $method         登录方式
     * @param   array       $table_data     用到的数据   
     * @return  void
     */
    protected function _log_in_($user_id, $method, $table_data = array())
    {
        //不存在登陆用户，则返回
        $this->log_out(); //前面如果已经登陆了，需要退出

        /**
         * --开始判断用户是否状态正常，检查用户是否设置为 单处登录-----------------------
         * 1) 如果用户已经停用，则报错，禁止再登录。唯一办法是需要管理员开启正常
         * 2) 如果是单点登录的设置，那么将该用户的其他登录信息都退出 
         * $_SESSION['用户表']['数据']['config']['single_sign_on'] 为真代表单点登录。不存在或为false 则表示可以多处登录
         * 可能还要考虑app端相关
         */

        //重新登陆
        $_SESSION['user_id'] = $user_id;
        //获取登录方式
        $method = isset($this->_log_in_method[$method]) ? $this->_log_in_method[$method] : $this->_log_in_method['unknown'];
        //插入登陆日志
        $user_log_id = object(parent::TABLE_USER_LOG)->insert(array(
            'method'    => $method,
            'table_data'    => $table_data,
        ));
        //日志插入失败
        if (empty($user_log_id)) {
            $_SESSION['user_id'] = '';
            throw new error("登录记录异常");
        }


        //需要先插入登录日记
        $user_data = object(parent::TABLE_USER)->find_user(); //更新用户的数据
        //1) 如果用户已经停用，则报错，禁止再登录。唯一办法是需要管理员开启正常
        if (isset($user_data['user_state']) && empty($user_data['user_state'])) {
            //这里肯定存在登录日志，摧毁日志，防止状态为0的用户恶意增加日志数据
            object(parent::TABLE_USER_LOG)->remove($user_log_id);
            throw new error("用户已经被封禁");
        }
        if (!empty($user_data['user_trash'])) {
            //这里肯定存在登录日志，摧毁日志，防止状态为0的用户恶意增加日志数据
            object(parent::TABLE_USER_LOG)->remove($user_log_id);
            throw new error("用户已经被回收");
        }


        //用户数据不合法并被清理，报错
        if (empty($_SESSION['user_id'])) {
            throw new error("登录数据异常");
        }

        return true;
    }




    /**
     * 检查权限
     * 是否已登陆
     * 
     * @param   bool    $return_bool    是否返回布尔值
     * @return  mixed
     */
    public function check($return_bool = false)
    {
        if (empty($return_bool)) {
            //检查是否已初始化
            object(parent::REQUEST_SESSION)->check();
        } else {
            $bool = object(parent::REQUEST_SESSION)->check(true);
            if (empty($bool)) {
                return false;
            }
        }

        object(parent::TABLE_USER)->find_user(); //更新用户的数据
        //判断用户是否已登陆
        if (empty($_SESSION['user_id']) || empty($_SESSION['user'])) {
            if (isset($_SESSION['user'])) unset($_SESSION['user']); //存在就登录数据则删除
            if (empty($return_bool)) {
                throw new error("没有登录，无权操作");
            } else {
                return false;
            }
        }

        return true;
    }



    /**
     * 当前用户退出
     * 
     * @param   void
     * @return  bool
     */
    public function log_out()
    {
        //更新退出日志
        //登陆的用户 更新 user_log_out_time
        if (!empty($_SESSION['user_id'])) {
            if (empty($_SESSION['session_private']['user_log_id'])) {
                $bool = object(parent::TABLE_USER_LOG)->update_log_out();
            } else {
                $bool = object(parent::TABLE_USER_LOG)->update_log_id_out($_SESSION['session_private']['user_log_id']);
            }

            if (empty($bool)) {
                throw new error("退出失败");
            }
        }

        //更新会话
        $_SESSION['user_id'] = '';
        if (isset($_SESSION['user'])) unset($_SESSION['user']); //存在就登录数据则删除  
        if (isset($_SESSION['session_private'])) $_SESSION['session_private'] = array(); //删除一些关键数据

        return true;
    }




    /**
     * 获取微信小程序二维码
     * $qrcode_config : {
     *  token       [str] [必填] [微信小程序接口调用凭证]
     *  scene       [str] [必填] [最大32个可见字符，只支持数字，大小写英文以及部分特殊字符]
     *  page        [str] [可选] [默认主页，必须是已经发布的小程序存在的页面]
     *  width       [int] [可选] [默认430，二维码的宽度，单位 px，最小 280px，最大 1280px]
     *  auto_color  [bol] [可选] [默认false，自动配置线条颜色]
     *  line_color  [arr] [可选] [auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示]
     *  is_hyaline  [bol] [可选] [默认false，是否需要透明底色]
     * }
     * 
     * @param	array	$qrcode_config		小程序码得到配置信息
     * @param	array	$user_data			用户数据
     */
    public function weixin_applet_qrcode($qrcode_config = array(), $user_data = array())
    {
        if (empty($user_data)) {
            if (!empty($_SESSION['user'])) {
                $user_data = $_SESSION['user'];
            } else {
                throw new error('用户数据异常，获取微信小程序二维码失败');
            }
        }

        //查询微信小程序配置
        $config_applet = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('weixin_applet_access'), true);
        if (empty($config_applet)) throw new error('微信小程序配置异常');

        //获取二维码配置
        $qrcode_config_str = cmd(array($qrcode_config), 'json encode');
        $new_qrcode_md5 = md5($config_applet['id'] . $qrcode_config_str) . md5($qrcode_config_str . $config_applet['id']);

        //是否已缓存
        $user_json = cmd(array($user_data['user_json']), 'json decode');
        $user_json = $user_json ?: array();
        if (!empty($user_json['user_recommend']['weixin_applet']['image_id'])) {
            $image_id = $user_json['user_recommend']['weixin_applet']['image_id'];

            //要比对 app id
            $config_applet_app_id = '';
            if (!empty($user_json['user_recommend']['weixin_applet']['appid'])) {
                $config_applet_app_id = $user_json['user_recommend']['weixin_applet']['appid'];
            }

            //还要比对二维码配置参数
            $qrcode_config_md5 = '';
            if (!empty($user_json['user_recommend']['weixin_applet']['qrcode_config_md5'])) {
                $qrcode_config_md5 = $user_json['user_recommend']['weixin_applet']['qrcode_config_md5'];
            }

            //是否同一小程序应用
            if ($config_applet_app_id == $config_applet['id'] && $new_qrcode_md5 == $qrcode_config_md5) {
                return $image_id;
            } else {
                //删除图片                    
                object(parent::REQUEST_APPLICATION)->qiniu_image_remove(array('image_id' => $image_id));
            }
        }

        //获取微信小程序接口调用凭证
        $token = object(parent::PLUGIN_WEIXIN_SESSION_APPLET)->get_access_token($config_applet);
        if (empty($token['errno'])) {
            $token = $token['data'];
        } else {
            throw new error($token['error']);
        }

        $qrcode = object(parent::PLUGIN_WEIXIN_SESSION_APPLET)->get_qrcode($token, $qrcode_config);
        if (empty($qrcode['errno'])) {
            $qrcode = $qrcode['data'];
        } else {
            throw new error($qrcode['error']);
        }

        //保存二维码到七牛云
        $response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload(array('binary' => $qrcode));
        $image_id = $response['image_id'];

        //保存二维码图片ID到数据库
        $weixin_applet = array(
            'image_id' => $image_id,
            'appid' => $config_applet['id'],
            'qrcode_config_md5' => $new_qrcode_md5, //不同的参数，就重新生成
        );
        if (empty($user_json['user_recommend'])) {
            $user_json['user_recommend'] = array();
        }
        $user_json['user_recommend']['weixin_applet'] = $weixin_applet;
        $update_data = array();
        $update_data['user_json'] = cmd(array($user_json), 'json encode');
        $update_data['user_update_time'] = time();
        object(parent::TABLE_USER)->update(array(array('user_id=[+]', $user_data['user_id'])), $update_data);

        return $image_id;
    }



    /**
     * 获取微信小程序二维码
     * $qrcode_config : {
     *  token       [str] [必填] [微信小程序接口调用凭证]
     *  scene       [str] [必填] [最大32个可见字符，只支持数字，大小写英文以及部分特殊字符]
     *  page        [str] [可选] [默认主页，必须是已经发布的小程序存在的页面]
     *  width       [int] [可选] [默认430，二维码的宽度，单位 px，最小 280px，最大 1280px]
     *  auto_color  [bol] [可选] [默认false，自动配置线条颜色]
     *  line_color  [arr] [可选] [auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示]
     *  is_hyaline  [bol] [可选] [默认false，是否需要透明底色]
     * }
     * 
     * @param	array	$qrcode_config		小程序码得到配置信息
     * @param	array	$user_data			用户数据
     */
    public function weixin_applet_qrcode_length($qrcode_config = array(), $user_data = array())
    {
        if (empty($user_data)) {
            if (!empty($_SESSION['user'])) {
                $user_data = $_SESSION['user'];
            } else {
                throw new error('用户数据异常，获取微信小程序二维码失败');
            }
        }

        //查询微信小程序配置
        $config_applet = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('weixin_applet_access'), true);
        if (empty($config_applet)) throw new error('微信小程序配置异常');

        //获取二维码配置
        $qrcode_config_str = cmd(array($qrcode_config), 'json encode');
        $new_qrcode_md5 = md5($config_applet['id'] . $qrcode_config_str) . md5($qrcode_config_str . $config_applet['id']);

        //是否已缓存
        $user_json = cmd(array($user_data['user_json']), 'json decode');
        $user_json = $user_json ?: array();
        if (!empty($user_json['user_recommend']['weixin_applet']['image_id'])) {
            $image_id = $user_json['user_recommend']['weixin_applet']['image_id'];

            //要比对 app id
            $config_applet_app_id = '';
            if (!empty($user_json['user_recommend']['weixin_applet']['appid'])) {
                $config_applet_app_id = $user_json['user_recommend']['weixin_applet']['appid'];
            }

            //还要比对二维码配置参数
            $qrcode_config_md5 = '';
            if (!empty($user_json['user_recommend']['weixin_applet']['qrcode_config_md5'])) {
                $qrcode_config_md5 = $user_json['user_recommend']['weixin_applet']['qrcode_config_md5'];
            }

            //是否同一小程序应用
            if ($config_applet_app_id == $config_applet['id'] && $new_qrcode_md5 == $qrcode_config_md5) {
                return $image_id;
            } else {
                //删除图片                    
                object(parent::REQUEST_APPLICATION)->qiniu_image_remove(array('image_id' => $image_id));
            }
        }

        //获取微信小程序接口调用凭证
        $token = object(parent::PLUGIN_WEIXIN_SESSION_APPLET)->get_access_token($config_applet);
        if (empty($token['errno'])) {
            $token = $token['data'];
        } else {
            throw new error($token['error']);
        }

        // return $qrcode_config;
        $qrcode = object(parent::PLUGIN_WEIXIN_SESSION_APPLET)->get_qrcode_length($token, $qrcode_config);
        if (empty($qrcode['errno'])) {
            $qrcode = $qrcode['data'];
        } else {
            throw new error($qrcode['error']);
        }

        //保存二维码到七牛云
        $response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload(array('binary' => $qrcode,'user_id'=>$user_data['user_id']));
        $image_id = $response['image_id'];

        //保存二维码图片ID到数据库
        $weixin_applet = array(
            'image_id' => $image_id,
            'appid' => $config_applet['id'],
            'qrcode_config_md5' => $new_qrcode_md5, //不同的参数，就重新生成
        );
        if (empty($user_json['user_recommend'])) {
            $user_json['user_recommend'] = array();
        }
        $user_json['user_recommend']['weixin_applet'] = $weixin_applet;
        $update_data = array();
        $update_data['user_json'] = cmd(array($user_json), 'json encode');
        $update_data['user_update_time'] = time();
        object(parent::TABLE_USER)->update(array(array('user_id=[+]', $user_data['user_id'])), $update_data);

        return $image_id;
    }
}