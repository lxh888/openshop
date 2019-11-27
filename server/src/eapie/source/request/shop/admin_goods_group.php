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




namespace eapie\source\request\shop;

use eapie\main;
use eapie\error;

/**
 * 拼团商品
 * @author green
 */
class admin_goods_group extends \eapie\source\request\shop
{


    //===========================================
    // 操作
    //===========================================


    /**
     * 添加
     * @author green
     * 
     * api: SHOPADMINGOODSGROUPADD
     * 
     * @param   array $input [请求参数]
     * @return  bool
     */
    public function api_add($input = array())
    {
        // 检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_GROUP_ADD);

        // 检测请求参数
        object(parent::ERROR)->check($input, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
        object(parent::ERROR)->check($input, 'shop_goods_sku_id', parent::TABLE_SHOP_GOODS_SKU, array('args'));
        object(parent::ERROR)->check($input, 'shop_goods_group_name', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
        object(parent::ERROR)->check($input, 'shop_goods_group_info', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
        object(parent::ERROR)->check($input, 'shop_goods_group_sort', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
        object(parent::ERROR)->check($input, 'shop_goods_group_price', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
        object(parent::ERROR)->check($input, 'shop_goods_group_people', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
        object(parent::ERROR)->check($input, 'shop_goods_group_start_time', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
        object(parent::ERROR)->check($input, 'shop_goods_group_end_time', parent::TABLE_SHOP_GOODS_GROUP, array('args'));

        // 查询商品信息
        $shop_goods = object(parent::TABLE_SHOP_GOODS)->find($input['shop_goods_id']);
        if (!$shop_goods) {
            throw new error('商品不存在');
        }

        // 判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
        if (!object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true)) {
            if ($shop_goods['user_id'] !== $_SESSION['user_id']) {
                throw new error('权限不足，不能操作非自己添加的数据');
            }
        }

        // 查询商品规格信息
        $shop_goods_sku = object(parent::TABLE_SHOP_GOODS_SKU)->find($input['shop_goods_sku_id']);
        if (!$shop_goods_sku) {
            throw new error('商品规格不存在');
        }
        if ($shop_goods_sku['shop_goods_id'] !== $input['shop_goods_id']) {
            throw new error('商品规格和商品不匹配');
        }

        // 是否已存在
        $shop_goods_group = object(parent::TABLE_SHOP_GOODS_GROUP)->find_where(array(
            array('shop_goods_id = [+]', $input['shop_goods_id']),
            array('shop_goods_sku_id = [+]', $input['shop_goods_sku_id']),
        ));
        if ($shop_goods_group) {
            throw new error('该拼团商品已存在');
        }

        // 过滤参数
        $insert_data = cmd(array($input, array(
            'shop_goods_id', 
            'shop_goods_sku_id',
            'shop_goods_group_name', 
            'shop_goods_group_info', 
            'shop_goods_group_sort',
            'shop_goods_group_price',
            'shop_goods_group_people',
            'shop_goods_group_start_time',
            'shop_goods_group_end_time',
        )), 'arr whitelist');

        // 其它参数
        $insert_data['shop_goods_group_id'] = object(parent::TABLE_SHOP_GOODS_GROUP)->get_unique_id();
        $insert_data['shop_goods_group_start_time'] = cmd(array($insert_data['shop_goods_group_start_time']), 'time mktime');
        $insert_data['shop_goods_group_end_time'] = cmd(array($insert_data['shop_goods_group_end_time']), 'time mktime');
        $insert_data['shop_goods_group_insert_time'] = time();
        $insert_data['shop_goods_group_update_time'] = time();
        $insert_data['user_id'] = $_SESSION['user_id'];

        // 插入数据
        if (object(parent::TABLE_SHOP_GOODS_GROUP)->insert($insert_data)) {
            // 记录日志
            object(parent::TABLE_ADMIN_LOG)->insert($input, $insert_data);
            return $insert_data['shop_goods_id'];
        } else {
            throw new error('操作失败');
        }
    }

    /**
     * 编辑
     * @author green
     * 
     * api: SHOPADMINGOODSGROUPEDIT
     * {"class":"shop/admin_goods_when","method":"api_edit"}
     * 
     * @param   array $input [请求参数]
     * @return  bool
     */
    public function api_edit($input = array())
    {
        // 检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_GROUP_EDIT);

        // 检查请求参数
        object(parent::ERROR)->check($input, 'shop_goods_group_id', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
        if (isset($input['shop_goods_group_name'])) { 
            object(parent::ERROR)->check($input, 'shop_goods_group_name', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
        }
        if (isset($input['shop_goods_group_info'])) { 
            object(parent::ERROR)->check($input, 'shop_goods_group_info', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
        }
        if (isset($input['shop_goods_group_sort'])) { 
            object(parent::ERROR)->check($input, 'shop_goods_group_sort', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
        }
        if (isset($input['shop_goods_group_price'])) {
            object(parent::ERROR)->check($input, 'shop_goods_group_price', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
        }
        if (isset($input['shop_goods_group_people'])) {
            object(parent::ERROR)->check($input, 'shop_goods_group_people', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
        }
        if (isset($input['shop_goods_group_start_time'])) { 
            object(parent::ERROR)->check($input, 'shop_goods_group_start_time', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
            $input['shop_goods_group_start_time'] = cmd(array($input['shop_goods_group_start_time']), 'time mktime');
        }
        if (isset($input['shop_goods_group_end_time'])) { 
            object(parent::ERROR)->check($input, 'shop_goods_group_end_time', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
            $input['shop_goods_group_end_time'] = cmd(array($input['shop_goods_group_end_time']), 'time mktime');
        }

        // 查询原始数据
        $original = object(parent::TABLE_SHOP_GOODS_GROUP)->find($input['shop_goods_group_id']);
        if (empty($original)) {
            throw new error('数据不存在');
        }

        // 判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
        if (!object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true)) {
            if ($original['user_id'] !== $_SESSION['user_id']) {
                throw new error('权限不足，不能操作非自己添加的数据');
            }
        }

        // 过滤请求参数
        $update_data = cmd(array($input, array(
            'shop_goods_group_name', 
            'shop_goods_group_info', 
            'shop_goods_group_sort',
            'shop_goods_group_price',
            'shop_goods_group_people',
            'shop_goods_group_start_time',
            'shop_goods_group_end_time',
        )), 'arr whitelist');

        // 删除没修改的参数
        if (!empty($update_data)) {
            foreach ($update_data as $key => $value) {
                if (isset($original[$key])) {
                    if ($original[$key] == $value) {
                        unset($update_data[$key]);
                    }
                }
            }
        }

        if (empty($update_data)) {
            throw new error('没有需要更新的数据');
        }

        // 其它参数
        $update_data['shop_goods_group_update_time'] = time();
        $update_where = array(array('shop_goods_group_id = [+]', $input['shop_goods_group_id']));

        // 更新数据
        $res = object(parent::TABLE_SHOP_GOODS_GROUP)->update($update_where, $update_data);
        if ($res) {
            // 记录日志
            object(parent::TABLE_ADMIN_LOG)->insert($input, $update_data);
            return $input['shop_goods_group_id'];
        } else {
            throw new error('操作失败');
        }
    }

    /**
     * 删除
     * @author green
     * 
     * api: SHOPADMINGOODSGROUPREMOVE
     * 
     * @param   array   $input
     * @return  string
     */
    public function api_remove($input = array())
    {
        // 检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_GROUP_REMOVE);

        // 校验数据
        object(parent::ERROR)->check($input, 'shop_goods_group_id', parent::TABLE_SHOP_GOODS_GROUP, array('args'));

        // 查询原始数据
        $original = object(parent::TABLE_SHOP_GOODS_GROUP)->find($input['shop_goods_group_id']);
        if (empty($original)) {
            throw new error('数据不存在');
        }

        // 判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
        if (!object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true)) {
            if ($original['user_id'] !== $_SESSION['user_id']) {
                throw new error('权限不足，不能操作非自己添加的数据');
            }
        }

        // 删除数据
        if (object(parent::TABLE_SHOP_GOODS_GROUP)->remove($original['shop_goods_group_id'])) {
            // 记录日志
            object(parent::TABLE_ADMIN_LOG)->insert($input, $original);
            return $input['shop_goods_group_id'];
        } else {
            throw new error('删除失败');
        }
    }


    //===========================================
    // 查询
    //===========================================


    /**
     * 列表
     * @author green
     * 
     * api: SHOPADMINGOODSGROUPLIST
     * 
     * @param   array   $input
     * @return  array
     */
    public function api_list($input = array())
    {
        // 检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_GROUP_READ);

        // 更新状态
        object(parent::TABLE_SHOP_GOODS_GROUP)->update_state();

        // 筛选
        $where = array();
        $where[] = array('sg.shop_goods_trash=0');

        // 判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
        if (!object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true)) {
            $where[] = array('[and] sgw.user_id = [+]', $_SESSION['user_id']);
        }

        if (!empty($input['search'])) {
            $search = $input['search'];
			if (isset($search['shop_goods_id']) && is_string($search['shop_goods_id'])) {
                $where[] = array('[and] sg.shop_goods_id = [+]', $search['shop_goods_id']);
            }
			
            if (isset($search['shop_goods_group_id']) && is_string($search['shop_goods_group_id'])) {
                $where[] = array('[and] sgw.shop_goods_group_id = [+]', $search['shop_goods_group_id']);
            }
			
            if (isset($search['shop_goods_name']) && is_string($search['shop_goods_name'])) {
                $where[] = array('[and] sg.shop_goods_name LIKE "%[-]%"', $search['shop_goods_name']);
            }
            if (isset($search['state']) && is_numeric($search['state'])) {
                $where[] = array('[and] sgw.shop_goods_group_state = [+]', $search['state']);
            }
            if (isset($search['shop_goods_state']) && is_numeric($search['shop_goods_state'])) {
                $where[] = array('[and] sg.shop_goods_state = [+]', $search['shop_goods_state']);
            }
        }

        // 排序
        $orderby = object(parent::REQUEST)->orderby($input, array(
            'shop_goods_name_desc' => array('shop_goods_name', true),
            'shop_goods_name_asc' => array('shop_goods_name', false),
            'shop_goods_state_desc' => array('shop_goods_state', true),
            'shop_goods_state_asc' => array('shop_goods_state', false),
            'name_desc' => array('shop_goods_group_name', true),
            'name_asc' => array('shop_goods_group_name', false),
            'sort_desc' => array('shop_goods_group_sort', true),
            'sort_asc' => array('shop_goods_group_sort', false),
            'state_desc' => array('shop_goods_group_state', true),
            'state_asc' => array('shop_goods_group_state', false),
            'start_time_desc' => array('shop_goods_group_start_time', true),
            'start_time_asc' => array('shop_goods_group_start_time', false),
            'end_time_desc' => array('shop_goods_group_end_time', true),
            'end_time_asc' => array('shop_goods_group_end_time', false),
            'insert_time_desc' => array('shop_goods_group_insert_time', true),
            'insert_time_asc' => array('shop_goods_group_insert_time', false),
            'update_time_desc' => array('shop_goods_group_update_time', true),
            'update_time_asc' => array('shop_goods_group_update_time', false),
        ));

		//避免排序重复
		$orderby[] = array('shop_goods_group_id', false);
		
        // 查询数据
        $config = array(
            'where' => $where,
            'orderby' => $orderby,
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );
        return object(parent::TABLE_SHOP_GOODS_GROUP)->select_page($config);
    }

    /**
     * 单个
     * @author green
     * 
     * api: SHOPADMINGOODSGROUPGET
     * req: {
     *  shop_goods_id [str] [必填] [商品ID]
     * }
     * 
     * @param   array   $input
     * @return  array
     */
    public function api_get($input = array())
    {
        // 检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_GROUP_READ);

        // 检查请求参数
        object(parent::ERROR)->check($input, 'shop_goods_group_id', parent::TABLE_SHOP_GOODS_GROUP, array('args'));

        // 更新状态
        object(parent::TABLE_SHOP_GOODS_GROUP)->update_state();

        return object(parent::TABLE_SHOP_GOODS_GROUP)->find($input['shop_goods_group_id']);
    }

    /**
     * 检查编辑的权限
     * @author green
     * 
     * api: SHOPADMINGOODSGROUPEDITCHECK
     * 
     * @return  bool
     */
    public function api_edit_check()
    {
        // 检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_GROUP_EDIT);
        return true;
    }


}