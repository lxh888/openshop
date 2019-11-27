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



namespace eapie\source\request\house;

use eapie\main;
use eapie\error;

//楼盘订单
class order extends \eapie\source\request\house
{

    /**
     * 初始化
     *
     * api: HOUSEORDERSELFINIT
     * req: null
     * 
     * @return string 楼盘订单ID
     */
    public function api_self_init($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        //查询数据
        $where = array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] house_order_state=3'),
        );
        $data = object(parent::TABLE_HOUSE_ORDER)->find_where($where);

        //是否已初始化
        if ($data)
            return $data['house_order_id'];

        //插入数据
        $insert_data['house_order_id'] = object(parent::TABLE_HOUSE_ORDER)->get_unique_id();
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['house_order_state'] = 3;
        $insert_data['house_order_insert_time'] = time();
        $insert_data['house_order_update_time'] = time();

        if (object(parent::TABLE_HOUSE_ORDER)->insert($insert_data)) {
            return $insert_data['house_order_id'];
        } else {
            throw new error('初始化失败');
        }
    }


    //===========================================
    // 操作数据
    //===========================================


    /**
     * 编辑订单信息
     * 
     * api: HOUSEORDERSELFEDIT
     * req: {
     *  id              [str] [必填] [订单ID]
     *  client_name     [str] [必填] [客户姓名]
     *  client_phone    [int] [必填] [客户手机号]
     *  agent_name      [str] [必填] [经纪人姓名]
     *  agent_phone     [int] [必填] [经纪人手机号]
     *  state           [int] [可选] [订单状态。0拒绝，2保存。默认2]
     * }
     * 
     * @return string 订单ID
     */
    public function api_self_edit($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        //检测数据
        object(parent::ERROR)->check($input, 'id', parent::TABLE_HOUSE_ORDER, array('args', 'exist'), 'house_order_id');
        object(parent::ERROR)->check($input, 'client_name', parent::TABLE_HOUSE_ORDER, array('args'), 'house_order_client_name');
        object(parent::ERROR)->check($input, 'client_phone', parent::TABLE_HOUSE_ORDER, array('args'), 'house_order_client_phone');
        object(parent::ERROR)->check($input, 'agent_name', parent::TABLE_HOUSE_ORDER, array('args'), 'house_order_agent_name');
        object(parent::ERROR)->check($input, 'agent_phone', parent::TABLE_HOUSE_ORDER, array('args'), 'house_order_agent_phone');

        //是否取消订单
        if (isset($input['state']) && $input['state'] == 0) {
            $state = 0;
        } else {
            $state = 2;
        }

        //白名单过滤
        $whitelist = array(
            'client_name',
            'client_phone',
            'agent_name',
            'agent_phone',
        );
        $filter = cmd(array($input, $whitelist), 'arr whitelist');

        //添加前缀
        $update_data = array();
        foreach ($filter as $key => $value) {
            $update_data['house_order_'.$key] = $value;
        }

        //更新数据
        $update_data['house_order_state'] = $state;
        $update_data['house_order_update_time'] = time();
        $update_where = array(
            array('house_order_id=[+]', $input['id']),
            array('[and] user_id=[+]', $_SESSION['user_id']),
        );

        if (object(parent::TABLE_HOUSE_ORDER)->update($update_where, $update_data)) {
            return $input['id'];
        } else {
            throw new error('操作失败');
        }
    }


    /**
     * 成交
     *
     * api: HOUSEORDERSELFDONE
     * req: {
     *  trader_name [str] [必填] [客户姓名]
     *  trader_phone [int] [必填] [客户手机号]
     * }
     * 
     * @return string 订单ID
     */
    public function api_self_done($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        //检测数据
        object(parent::ERROR)->check($input, 'id', parent::TABLE_HOUSE_ORDER, array('args', 'exist'), 'house_order_id');
        object(parent::ERROR)->check($input, 'trader_name', parent::TABLE_HOUSE_ORDER, array('args'), 'house_order_client_name');
        object(parent::ERROR)->check($input, 'trader_phone', parent::TABLE_HOUSE_ORDER, array('args'), 'house_order_client_phone');

        //白名单过滤
        $whitelist = array(
            'trader_name',
            'trader_phone',
        );
        $filter = cmd(array($input, $whitelist), 'arr whitelist');

        //添加前缀
        $update_data = array();
        foreach ($filter as $key => $value) {
            $update_data['house_order_'.$key] = $value;
        }

        //更新数据
        $update_data['house_order_state'] = 1;
        $update_data['house_order_update_time'] = time();
        $update_where = array(
            array('house_order_id=[+]', $input['id']),
            array('[and] user_id=[+]', $_SESSION['user_id'])
        );

        if (object(parent::TABLE_HOUSE_ORDER)->update($update_where, $update_data)) {
            return $input['id'];
        } else {
            throw new error('操作失败');
        }
    }


    /**
     * 编辑自己楼盘项目的订单的状态
     * api: HOUSEORDERSELFPRODUCTEDIT
     * req: {
     *  id      [str] [必填] [订单ID]
     *  state   [int] [必填] [楼盘项目核实状态。0假，1真]
     * }
     * 
     * @return string [订单ID]
     */
    public function api_self_product_edit($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        object(parent::ERROR)->check($input, 'id', parent::TABLE_HOUSE_ORDER, array('args'), 'house_order_id');
        object(parent::ERROR)->check($input, 'state', parent::TABLE_HOUSE_ORDER, array('args'), 'house_product_verify_state');

        // 查询楼盘订单信息
        $house_order = object(parent::TABLE_HOUSE_ORDER)->find($input['id']);
        if (empty($house_order))
            throw new error('数据不存在');

        if ($house_order['house_product_user_id'] != $_SESSION['user_id'])
            throw new error('无权操作');

        $update_where = array(
            array('house_order_id = [+]', $input['id']),
            array('[and] house_product_user_id = [+]', $_SESSION['user_id']),
        );
        $update_data = array(
            'house_product_verify_state' => $input['state'],
            'house_product_verify_time' => time(),
        );

        if (object(parent::TABLE_HOUSE_ORDER)->update($update_where, $update_data)) {
            return $input['id'];
        } else {
            throw new error('操作失败');
        }
    }


    //===========================================
    // 查询数据
    //===========================================


    /**
     * 查询列表
     *
     * api: HOUSEORDERSELFLIST
     * req: {
     * 
     * }
     * req.search {
     *  state [int|arr] [可选] [订单状态，0拒绝，1成交，2编辑中，3初始化]
     * }
     * 
     * @return array
     */
    public function api_self_list($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );

        //字段
        $config['select'] = array(
            'house_order_id AS id',
            'house_order_client_name AS client_name',
            'house_order_client_phone AS client_phone',
            'house_order_agent_name AS agent_name',
            'house_order_agent_phone AS agent_phone',
            'house_order_trader_name AS trader_name',
            'house_order_trader_phone AS trader_phone',
            'house_order_state AS state',
            'house_order_insert_time AS insert_time',
            'house_order_update_time AS update_time'
        );

        //排序
        $config['orderby'][] = array('house_order_insert_time', true);
        $config['orderby'][] = array('house_order_id', true);

        //条件
        $config['where'][] = array('ho.user_id=[+]', $_SESSION['user_id']);

        //筛选——状态
        if (!empty($input['search']['state'])) {
            $state = $input['search']['state'];
            if (is_numeric($state)) {
                $config['where'][] = array('[and] house_order_state=[-]', $state);
            } elseif (is_array($state) && is_numeric(implode($state))) {
                $config['where'][] = array('[and] house_order_state in ([-])', implode(',', $state), true);
            }
        } else {
            $config['where'][] = array('[and] house_order_state<>3');
        }

        //查询数据
        $data = object(parent::TABLE_HOUSE_ORDER)->select_page($config);

        //格式化数据
        foreach ($data['data'] as &$val) {
            $val['insert_time'] = date('Y-m-d H:i:s', $val['insert_time']);
            $val['update_time'] = date('Y-m-d H:i:s', $val['update_time']);
        }

        return $data;
    }

    /**
     * 查询详情
     *
     * api: HOUSEORDERSELFGET
     * req: {
     *  id [str] [必填] [订单ID]
     * }
     * 
     * @return array
     */
    public function api_self_get($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        //检测数据
        object(parent::ERROR)->check($input, 'id', parent::TABLE_HOUSE_ORDER, array('args'), 'house_order_id');

        //查询订单数据
        $data = object(parent::TABLE_HOUSE_ORDER)->find($input['id']);
        if (empty($data) || $data['user_id'] != $_SESSION['user_id'])
            throw new error('数据不存在');

        if ($data['house_order_trash'] == 1)
            throw new error('该数据已被回收');

        //查询订单图片数据
        $data_img = object(parent::TABLE_HOUSE_ORDER_IMAGE)->select(array(
            array('house_order_id=[+]', $input['id']),
        ));

        //格式化数据
        $data['img'] = $data_img;

        return $data;
    }

    /**
     * 自己楼盘项目的订单列表
     * api: HOUSEORDERSELFPRODUCTLIST
     * 
     * @return array
     */
    public function api_self_product_list($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );

        //字段
        $config['select'] = array(
            'house_order_id AS id',
            'hp.house_product_id',
            'hp.house_product_name',
            'hp.house_product_state',
        );

        //排序
        $config['orderby'][] = array('house_order_insert_time', true);

        //条件
        $config['where'][] = array('ho.house_product_user_id = [+]', $_SESSION['user_id']);

        //查询数据
        $data = object(parent::TABLE_HOUSE_ORDER)->select_page($config);

        //格式化数据

        return $data;
    }
}