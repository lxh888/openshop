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

class admin_user extends \eapie\source\request\merchant
{

    // 操作数据 =====================================

    /**
     * 商家用户——添加
     * 
     * api: MERCHANTADMINUSERADD
     * req: {
     *  user                [str] [必填] [用户ID或手机号]
     *  merchant_id         [str] [必填] [商家ID]
     *  merchant_user_name  [str] [可选] [商家用户名称]
     *  merchant_user_info  [str] [可选] [商家用户简介]
     *  merchant_user_state [int] [可选] [状态，0 封禁，1 正常，2审核中]
     *  merchant_user_sort  [int] [可选] [排序]
     *  merchant_user_json  [str] [可选] [配置信息]
     * }
     * 
     * @param  [arr]  $input [请求参数]
     * @return bool
     */
    public function api_add($input = array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_USER_ADD);

        //校验数据
        object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args', 'exists_id'));
        object(parent::ERROR)->check($input, 'merchant_user_name', parent::TABLE_MERCHANT_USER, array('args'));
        object(parent::ERROR)->check($input, 'merchant_user_info', parent::TABLE_MERCHANT_USER, array('args'));
        object(parent::ERROR)->check($input, 'merchant_user_state', parent::TABLE_MERCHANT_USER, array('args'));
        object(parent::ERROR)->check($input, 'merchant_user_sort', parent::TABLE_MERCHANT_USER, array('args'));

        //查询用户ID
        if (empty($input['user'])) throw new error('请填写用户ID或用户手机号');
		
        $user_data = object(parent::TABLE_USER)->find_id_or_phone($input['user']);
        if (empty($user_data['user_id'])) throw new error('用户不存在');
        $user_id = $user_data['user_id'];

        //判断是否已存在
        $exist = object(parent::TABLE_MERCHANT_USER)->check_exist($user_id, $input['merchant_id']);
        if ($exist) throw new error('商家用户已存在');

        //白名单
        $whitelist = array(
            'merchant_id',
            'merchant_user_name',
            'merchant_user_info',
            'merchant_user_state',
            'merchant_user_sort',
        );
        $insert_data = cmd(array($input, $whitelist), 'arr whitelist');

        //格式化数据
        $insert_data['merchant_user_id'] = object(parent::TABLE_MERCHANT_USER)->get_unique_id();
        $insert_data['user_id'] = $user_id;
        $insert_data['merchant_user_insert_time'] = time();
        $insert_data['merchant_user_update_time'] = time();

        //插入数据，记录日志
        if (object(parent::TABLE_MERCHANT_USER)->insert($insert_data)) {
            object(parent::TABLE_ADMIN_LOG)->insert($input, $insert_data);
            return $insert_data['merchant_user_id'];
        } else {
            throw new error('添加失败');
        }
    }

    /**
     * 商家用户——编辑商家用户信息
     * 
     * api: MERCHANTADMINUSEREDIT
     * req: {
     *  merchant_user_id    [str] [必填] [商家用户表ID]
     *  merchant_user_name  [str] [可选] [商家用户名称]
     *  merchant_user_info  [str] [可选] [商家用户简介]
     *  merchant_user_state [int] [可选] [状态，0 封禁，1 正常，2审核中]
     *  merchant_user_sort  [int] [可选] [排序]
     *  merchant_user_json  [str] [可选] [配置信息]
     * }
     * 
     * @param  [arr]  $input [请求参数]
     * @return bool
     */
    public function api_edit($input = array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_USER_EDIT);

        //检测数据
        object(parent::ERROR)->check($input, 'merchant_user_id', parent::TABLE_MERCHANT_USER, array('args'));
        if (isset($input['merchant_user_name']))
            object(parent::ERROR)->check($input, 'merchant_user_name', parent::TABLE_MERCHANT_USER, array('args'));
        if (isset($input['merchant_user_info']))
            object(parent::ERROR)->check($input, 'merchant_user_info', parent::TABLE_MERCHANT_USER, array('args'));
        if (isset($input['merchant_user_state']))
            object(parent::ERROR)->check($input, 'merchant_user_state', parent::TABLE_MERCHANT_USER, array('args'));
        if (isset($input['merchant_user_sort']))
            object(parent::ERROR)->check($input, 'merchant_user_sort', parent::TABLE_MERCHANT_USER, array('args'));
        if (isset($input['merchant_user_json']))
            object(parent::ERROR)->check($input, 'merchant_user_json', parent::TABLE_MERCHANT_USER, array('args'));

        //查询原始数据
        $original = object(parent::TABLE_MERCHANT_USER)->find($input['merchant_user_id']);
        if (empty($original))
            throw new error('ID有误，该数据不存在');

        //白名单
        $whitelist = array(
            'merchant_user_name', 
            'merchant_user_info', 
            'merchant_user_state',
            'merchant_user_sort',
            'merchant_user_json',
        );
        $update_data = cmd(array($input, $whitelist), 'arr whitelist');

        //过滤不需要更新的数据
        foreach ($update_data as $k => &$v) {
            if (isset($original[$k]) && $original[$k] == $v)
                unset($update_data[$k]);
        }
        if (empty($update_data))
            throw new error('没有需要更新的数据');

        //格式化数据
        $update_data['merchant_user_update_time'] = time();

        //更新数据，记录日志
        if (object(parent::TABLE_MERCHANT_USER)->update($input['merchant_user_id'], $update_data)) {
            object(parent::TABLE_ADMIN_LOG)->insert($input, $update_data);
            return $input['merchant_user_id'];
        } else {
            throw new error('操作失败');
        }
    }

    /**
     * 商家用户——删除商家用户
     * 
     * api: MERCHANTADMINUSERREMOVE
     * req: {
     *  merchant_user_id    [str] [必填] [商家用户表ID]
     * }
     * 
     * @param  [arr]  $input [请求参数]
     * @return [str] [商家用户表ID]
     */
    public function api_remove($input = array())
    {
        //检测权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_USER_REMOVE);

        //检测数据
        object(parent::ERROR)->check($input, 'merchant_user_id', parent::TABLE_MERCHANT_USER, array('args'));

        //判断改数据是否存在
        $original = object(parent::TABLE_MERCHANT_USER)->find($input['merchant_user_id']);
        if (empty($original))
            throw new error('商家用户表ID有误，该数据不存在');

        //删除数据，记录日志
        if (object(parent::TABLE_MERCHANT_USER)->delete($input['merchant_user_id'])) {
            object(parent::TABLE_ADMIN_LOG)->insert($input, $original);
            return $input['merchant_user_id'];
        } else {
            throw new error('删除失败');
        }
    }

    // 查询数据 =====================================

    /**
     * 商家用户——查询商家的用户列表
     * MERCHANTADMINUSERLIST
	 * 
     * @param  array  $data [请求参数]
     * @return array
     */
    public function api_list( $data = array() ){
        //检测权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_USER_READ);
		
		$config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
        );
		
		//排序
        $config["orderby"] = object(parent::REQUEST)->orderby($data, array(
            'user_id_desc' => array('user_id', true),
            'user_id_asc' => array('user_id', false),
            'user_parent_id_desc' => array('user_parent_id', true),
            'user_parent_id_asc' => array('user_parent_id', false),
            'user_nickname_desc' => array('user_nickname', true),
            'user_nickname_asc' => array('user_nickname', false),
            'user_sex_desc' => array('user_sex', true),
            'user_sex_asc' => array('user_sex', false),
            'user_phone_verify_list_desc' => array('user_phone_verify_list', true),
            'user_phone_verify_list_asc' => array('user_phone_verify_list', false),
			
			'merchant_id_desc' => array('merchant_id', true),
            'merchant_id_asc' => array('merchant_id', false),
            
			'merchant_user_name_desc' => array('merchant_user_name', true),
            'merchant_user_name_asc' => array('merchant_user_name', false),
			
			'merchant_user_state_desc' => array('merchant_user_state', true),
            'merchant_user_state_asc' => array('merchant_user_state', false),
            
			'id_desc' => array('merchant_user_id', true),
            'id_asc' => array('merchant_user_id', false),
            
            'insert_time_desc' => array('merchant_user_insert_time', true),
            'insert_time_asc' => array('merchant_user_insert_time', false),
            'update_time_desc' => array('merchant_user_update_time', true),
            'update_time_asc' => array('merchant_user_update_time', false),
        ));

        //避免排序重复
        $config["orderby"][] = array('merchant_user_id', false);
		
		//搜索
        if (!empty($data['search'])) {
        	
			if( isset($data['search']['merchant_user_state']) && 
			(is_string($data['search']['merchant_user_state']) || is_numeric($data['search']['merchant_user_state'])) &&
			in_array($data['search']['merchant_user_state'], array("0", "1", "2")) ){
				$config["where"][] = array('[and] mu.merchant_user_state=[+]', $data['search']['merchant_user_state']);
				}
			
			if (isset($data['search']['merchant_user_name']) && is_string($data['search']['merchant_user_name'])) {
                $config['where'][] = array('[and] mu.merchant_user_name LIKE "%[-]%"', $data['search']['merchant_user_name']);
            }

            if (isset($data['search']['merchant_user_id']) && is_string($data['search']['merchant_user_id'])) {
                $config['where'][] = array('[and] mu.merchant_user_id=[+]', $data['search']['merchant_user_id']);
            }
			
			if (isset($data['search']['user_id']) && is_string($data['search']['user_id'])) {
                $config['where'][] = array('[and] mu.user_id=[+]', $data['search']['user_id']);
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
                $config['where'][] = array('[and] mu.user_id=[+]', $user_id);
            }
			
            if (isset($data['search']['user_parent_id']) && is_string($data['search']['user_parent_id'])) {
                $config['where'][] = array('[and] u.user_parent_id=[+]', $data['search']['user_parent_id']);
            }
			
			if (isset($data['search']['merchant_id']) && is_string($data['search']['merchant_id'])) {
                $config['where'][] = array('[and] mu.merchant_id=[+]', $data['search']['merchant_id']);
            }

			if (isset($data['search']['merchant_name']) && is_string($data['search']['merchant_name'])) {
                $config['where'][] = array('[and] m.merchant_name LIKE "%[-]%"', $data['search']['merchant_name']);
            }
			
        }
		
        //查询数据
        $data = object(parent::TABLE_MERCHANT_USER)->select_page($config);
        return $data;
    }

    // 检测 =======================================

    /**
     * 检查商家编辑的权限
     * 
     * MERCHANTADMINUSEREDITCHECK
     * {"class":"merchant/admin","method":"api_edit_check"}
     * 
     * @param   void
     * @return  bool
     */
    public function api_edit_check()
    {
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_USER_EDIT);
        return true;
    }

    // 其它 =======================================

    /**
     * 临时接口——注册用户添加商家权限
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public function mch_add_ceshi($arr)
    {
        object(parent::TABLE_MERCHANT_USER)->insert($arr);
    }








}