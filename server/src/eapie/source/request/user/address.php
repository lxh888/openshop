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

class address extends \eapie\source\request\user
{


    /**
     * 添加收货地址
     *
     * api: USERADDRESSSELFADD
     * req: {
     *  consignee   [str] [必填] [收货人]
     *  phone       [int] [必填] [手机号]
     *  province    [str] [必填] [省]
     *  city        [str] [必填] [市]
     *  district    [str] [必填] [区]
     *  details     [str] [必填] [详址]
     *  default     [int] [可选] [默认地址。1是0否]
     * }
     * 
     * @return string 收货地址ID
     */
    public function api_self_add($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测输入
        object(parent::ERROR)->check($input, 'consignee', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_consignee');
        object(parent::ERROR)->check($input, 'phone', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_phone');
        object(parent::ERROR)->check($input, 'province', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_province');
        object(parent::ERROR)->check($input, 'city', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_city');
        object(parent::ERROR)->check($input, 'district', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_district');
        object(parent::ERROR)->check($input, 'details', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_details');

        //是否默认收货地址
        $default = 0;
        if (isset($input['default']) && $input['default'] == 1)
            $default = 1;

        //白名单
        $whitelist = array(
            'consignee',
            'phone',
            'province',
            'city',
            'district',
            'details',
        );
        $input = cmd(array($input, $whitelist), 'arr whitelist');

        //添加前缀
        $insert_data = cmd(array('user_address_', $input),  'arr key_prefix');

        $insert_data['user_address_id'] = object(parent::TABLE_USER_ADDRESS)->get_unique_id();
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['user_address_default'] = $default;
        $insert_data['user_address_insert_time'] = time();
        $insert_data['user_address_update_time'] = time();

        //插入数据
        if ($res = object(parent::TABLE_USER_ADDRESS)->insert($insert_data)) {
            //是否为默认地址
            if ($default === 1)
                $this->_update_default_address($insert_data['user_address_id']);

            return $insert_data['user_address_id'];
        } else {
            throw new error('添加失败');
        }
    }


    /**
     * 编辑
     *
     * api: USERADDRESSSELFEDIT
     * req: {
     *  id [str] [必填] [地址ID]
     *  consignee   [str] [必填] [收货人]
     *  phone       [int] [必填] [手机号]
     *  province    [str] [必填] [省]
     *  city        [str] [必填] [市]
     *  district    [str] [必填] [区]
     *  details     [str] [必填] [详址]
     *  default     [int] [可选] [默认地址。1是0否]
     * }
     * 
     * @return string 地址ID
     */
    public function api_self_edit($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测输入
        object(parent::ERROR)->check($input, 'id', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_id');
        object(parent::ERROR)->check($input, 'consignee', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_consignee');
        object(parent::ERROR)->check($input, 'phone', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_phone');
        object(parent::ERROR)->check($input, 'province', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_province');
        object(parent::ERROR)->check($input, 'city', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_city');
        object(parent::ERROR)->check($input, 'district', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_district');
        object(parent::ERROR)->check($input, 'details', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_details');

        //是否默认收货地址
        $default = 0;
        if (isset($input['default']) && $input['default'] == 1)
            $default = 1;

        //白名单过滤
        $whitelist = array(
            'consignee',
            'phone',
            'province',
            'city',
            'district',
            'details'
        );
        $filter = cmd(array($input, $whitelist), 'arr whitelist');

        //查询原始数据
        $original = object(parent::TABLE_USER_ADDRESS)->find($input['id']);
        if (empty($original))
            throw new error('地址ID有误，数据不存在');

        //添加前缀
        $update_data = cmd(array('user_address_', $input),  'arr key_prefix');

        //过滤不需要更新的数据
        foreach ($update_data as $key => &$val) {
            if (isset($original[$key]) && $original[$key] == $val)
                unset($update_data[$key]);
        }
        if (empty($update_data))
            throw new error('没有需要更新的数据');

        //更新数据
        $update_data['user_address_update_time'] = time();
        $update_where = array(
            array('user_address_id=[+]', $input['id']),
            array('[and] user_id=[+]', $_SESSION['user_id']),
        );

        if (object(parent::TABLE_USER_ADDRESS)->update($update_where, $update_data)) {
            //是否为默认地址
            if ($default === 1)
                $this->_update_default_address($input['id']);

            return $input['id'];
        } else {
            throw new error('操作失败');
        }
    }

    /**
     * 删除
     *
     * api: USERADDRESSSELFREMOVE
     * req: {
     *  id [str] [必填] [地址ID]
     * }
     * 
     * @return string 地址ID
     */
    public function api_self_remove($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测输入
        object(parent::ERROR)->check($input, 'id', parent::TABLE_USER_ADDRESS, array('args', 'exist'), 'user_address_id');

        //条件
        $delete_where = array(
            array('user_address_id=[+]', $input['id']),
            array('[and] user_id=[+]', $_SESSION['user_id']),
        );

        if (object(parent::TABLE_USER_ADDRESS)->delete($delete_where)) {
            return $input['id'];
        } else {
            throw new error('删除失败');
        }
    }


    //===========================================
    // 查询数据
    //===========================================


    /**
     * 查询收货地址列表
     *
     * api: USERADDRESSSELFLIST
     * req: null
     * 
     * @return array
     */
    public function api_self_list($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );

        //字段
        $config['select'] = array(
            'user_address_id AS id',
            'user_address_consignee AS consignee',
            'user_address_phone AS phone',
            'user_address_province AS province',
            'user_address_city AS city',
            'user_address_district AS district',
            'user_address_details AS details',
            'user_address_default AS `default`',
        );

        //条件
        $config['where'][] = array('user_id=[+]', $_SESSION['user_id']);

        //排序
        $config['orderby'][] = array('user_address_default', true);
        $config['orderby'][] = array('user_address_update_time', true);

        //查询数据
        $data = object(parent::TABLE_USER_ADDRESS)->select($config);

        return $data;
    }

    /**
     * 查询收货地址详情
     *
     * api: USERADDRESSSELFGET
     * req: {
     *  id  [str] [可选] [地址ID。不填则默认地址]
     * }
     * 
     * @return array
     */
    public function api_self_get($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //是否指定收货地址ID
        if (isset($input['id']) && is_string($input['id'])) {
            $where = array(
                array('user_address_id=[+]', $input['id']),
                array('[and] user_id=[+]', $_SESSION['user_id']),
            );
        } else {
            $where = array(
                array('user_address_default=1'),
                array('[and] user_id=[+]', $_SESSION['user_id']),
            );
        }

        //查询数据
        $data = object(parent::TABLE_USER_ADDRESS)->find_where($where);
        if (empty($data))
            throw new error('没有数据');

        //格式化数据
        $output = array();
        $output['id'] = $data['user_address_id'];
        $output['consignee'] = $data['user_address_consignee'];
        $output['phone'] = $data['user_address_phone'];
        $output['province'] = $data['user_address_province'];
        $output['city'] = $data['user_address_city'];
        $output['district'] = $data['user_address_district'];
        $output['details'] = $data['user_address_details'];
		$output['default'] = $data['user_address_default'];
		
        return $output;
    }


    //===========================================
    // 私有方法
    //===========================================


    /**
     * 更新默认地址
     * 
     * @param  string $id 地址ID
     * @return void
     */
    private function _update_default_address($id = '')
    {
        object(parent::TABLE_USER_ADDRESS)->update(
            array(
                array('user_id=[+]', $_SESSION['user_id']),
                array('[and] user_address_id <> [+]', $id),
            ),
            array('user_address_default' => 0)
        );
    }

}