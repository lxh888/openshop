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

//楼盘客户
class client extends \eapie\source\request\house
{


    //===========================================
    // 操作数据
    //===========================================


    /**
     * 添加客户
     *
     * api: HOUSECLIENTSELFADD
     * req: {
     *  name     [str] [必填] [姓名]
     *  phone    [int] [必填] [手机号]
     *  sex      [int] [必填] [性别]
     *  age      [int] [必填] [年龄]
     * }
     * 
     * @param  array  $input 请求参数
     * @return string 楼盘客户表ID
     */
    public function api_self_add($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        //检测数据
        object(parent::ERROR)->check($input, 'name', parent::TABLE_HOUSE_CLIENT, array('args'));
        object(parent::ERROR)->check($input, 'phone', parent::TABLE_HOUSE_CLIENT, array('args'));
        object(parent::ERROR)->check($input, 'sex', parent::TABLE_HOUSE_CLIENT, array('args'));
        object(parent::ERROR)->check($input, 'age', parent::TABLE_HOUSE_CLIENT, array('args'));

        //检测该客户手机是否已添加
        $row = object(parent::TABLE_HOUSE_CLIENT)->find_where(array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('house_client_phone=[]', $input['phone']),
        ));
        if ($row)
            throw new error('该客户手机号已存在');

        //白名单
        $whitelist = array(
            'name',
            'phone',
            'sex',
            'age',
        );
        $input = cmd(array($input, $whitelist), 'arr whitelist');

        //添加前缀
        $insert_data = array();
        foreach ($input as $key => $val) {
            $insert_data['house_client_'.$key] = $val;
        }

        //插入数据
        $insert_data['house_client_id'] = object(parent::TABLE_HOUSE_CLIENT)->get_unique_id();
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['house_client_insert_time'] = time();
        $insert_data['house_client_update_time'] = time();

        if (object(parent::TABLE_HOUSE_CLIENT)->insert($insert_data)) {
            return $insert_data['house_client_id'];
        } else {
            throw new error('添加失败');
        }
    }


    //===========================================
    // 查询数据
    //===========================================


    /**
     * 查询客户列表
     *
     * api: HOUSECLIENTSELFLIST
     * req: list通用参数
     * 
     * @param search {
     *  phone [int] [手机号]
     * }
     * 
     * @param  array  $input [请求参数]
     * @return array
     */
    public function api_self_list($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_();

        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );

        //字段
        $config['select'] = array(
            'house_client_id AS id',
            'house_client_name AS name',
            'house_client_phone AS phone',
            'house_client_sex AS sex',
            'house_client_age AS age',
            'house_client_insert_time AS time',
        );

        //排序
        $config['orderby'][] = array('house_client_insert_time', true);
        $config['orderby'][] = array('house_client_id', true);

        //条件
        $config['where'][] = array('user_id=[+]', $_SESSION['user_id']);

        //筛选
        if (!empty($input['search']['phone']) && is_numeric($input['search']['phone'])) {
            $phone = '%'.$input['search']['phone'].'%';
            $config['where'][] = array('[and] house_client_phone like [+]', $phone);
        }

        //查询数据
        $data = object(parent::TABLE_HOUSE_CLIENT)->select_page($config);
        return $data;
    }

}