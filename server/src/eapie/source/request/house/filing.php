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

// 楼盘报备表
class filing extends \eapie\source\request\house
{

    /**
     * 添加报备
     *
     * api: HOUSEFILINGSELFADD
     * req: {
     *  house_product_id    [str] [必填] [楼盘项目ID]
     *  client_name         [str] [必填] [客户姓名]
     *  client_phone        [str] [必填] [客户手机号]
     *  client_sex          [int] [必填] [客户性别]
     *  client_age          [int] [必填] [客户年龄]
     *  visit_time          [str] [必填] [到访时间]
     * }
     * 
     * @param  array  $input 请求参数
     * @return string 报备表ID
     */
    public function api_self_add($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        //检测数据
        object(parent::ERROR)->check($input, 'house_product_id', parent::TABLE_HOUSE_PRODUCT, array('args', 'exist'));
        object(parent::ERROR)->check($input, 'client_name', parent::TABLE_HOUSE_CLIENT, array('args'), 'name');
        object(parent::ERROR)->check($input, 'client_phone', parent::TABLE_HOUSE_FILING, array('args'), 'house_filing_client_phone');
        object(parent::ERROR)->check($input, 'client_sex', parent::TABLE_HOUSE_CLIENT, array('args'), 'sex');
        object(parent::ERROR)->check($input, 'client_age', parent::TABLE_HOUSE_CLIENT, array('args'), 'age');
        object(parent::ERROR)->check($input, 'visit_time', parent::TABLE_HOUSE_FILING, array('args'), 'house_filing_visit_time');

        // 查询楼盘信息
        $house_product = object(parent::TABLE_HOUSE_PRODUCT)->find($input['house_product_id']);
        if (empty($house_product) || $house_product['house_product_trash'] === '1')
            throw new error('该楼盘不存在');
            
        if ($house_product['house_product_state'] !== '1')
            throw new error('该楼盘未发布');

        // 查询用户信息
        $user = object(parent::TABLE_USER)->find($_SESSION['user_id']);

        // 查询经纪人电话
        $user_phone = object(parent::TABLE_USER_PHONE)->find_user_login_data($_SESSION['user_id']);

        //插入数据
        $insert_data['house_filing_id'] = object(parent::TABLE_HOUSE_FILING)->get_unique_id();
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['house_product_id'] = $input['house_product_id'];
        $insert_data['house_filing_agent_name'] = $user['user_name'];
        $insert_data['house_filing_agent_phone'] = $user_phone['user_phone_id'];
        $insert_data['house_filing_agent_company'] = $user['user_company'];
        $insert_data['house_filing_client_name'] = $input['client_name'];
        $insert_data['house_filing_client_phone'] = $input['client_phone'];
        $insert_data['house_filing_client_sex'] = $input['client_sex'];
        $insert_data['house_filing_client_age'] = $input['client_age'];
        $insert_data['house_filing_visit_time'] = $input['visit_time'];
        $insert_data['house_filing_insert_time'] = time();
        $insert_data['house_filing_update_time'] = time();

        if (object(parent::TABLE_HOUSE_FILING)->insert($insert_data)) {
            $output = array(
                'owner_id' => $house_product['user_id'],
                'house_filing_id' => $insert_data['house_filing_id'],
                'house_product_name' => $house_product['house_product_name'],
            );

            // 推送
            $this->_push_to_single($output);

            return $output;
        } else {
            throw new error('添加失败');
        }
    }


    /**
     * 
     *
     * api: HOUSEFILINGREAD
     * 
     * @param  string $house_filing_id [报备ID]
     * @return bool
     */
    public function api_read($house_filing_id = '')
    {
        if (empty($house_filing_id) || !is_string($house_filing_id)) {
            return;
        }

        // 查询报备信息
        $house_filing = object(parent::TABLE_HOUSE_FILING)->find($house_filing_id);
        if (empty($house_filing) || $house_filing['house_filing_read'] === '1') {
            return false;
        }

        $house_product_id = $house_filing['house_product_id'];
        $house_filing_ids = array($house_filing_id);
        $house_filing_agent_phones = array($house_filing['house_filing_agent_phone']);

        return $this->_update_read($house_product_id, $house_filing_ids, $house_filing_agent_phones);
    }


    //===========================================
    // 查询
    //===========================================


    /**
     * 查询列表
     *
     * api: HOUSEFILINGLIST
     * req: list通用参数
     * search {
     *  house_product_id    [str] [可选] [楼盘ID]
     *  date                [str] [可选] [报备日期]
     * }
     * 
     * @return array
     */
    public function api_list($input = array())
    {
        // 检测经纪人状态
        $this->_check_agent_state_($input);

        // 查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );

        // 字段
        $config['select'] = array(
            'hf.house_product_id',
            'hf.house_filing_id AS id',
            'hf.house_filing_agent_name AS agent_name',
            'hf.house_filing_agent_phone AS agent_phone',
            'hf.house_filing_agent_company AS agent_company',
            'hf.house_filing_client_name AS client_name',
            'hf.house_filing_client_phone AS client_phone',
            'hf.house_filing_visit_time AS visit_time',
            'hf.house_filing_read AS `read`',
            'hf.house_filing_insert_time AS time',
            'hp.house_product_name AS product_name',
        );

        // 条件
        $config['where'][] = array('hp.user_id = [+]', $_SESSION['user_id']);

        // 筛选——楼盘项目ID
        if (!empty($input['search']['house_product_id']) && is_string($input['search']['house_product_id'])) {
            $config['where'][] = array('[and] hf.house_product_id = [+]', $input['search']['house_product_id']);
        }

        // 筛选——楼盘项目名称
        if (!empty($input['search']['house_product_name']) && is_string($input['search']['house_product_name'])) {
            $val = cmd(array($input['search']['house_product_name']), 'str addslashes');
            $val = '%'.$val.'%';
            $config['where'][] = array('[and] hp.house_product_name like [+]', $val);
        }

        // 筛选——报备日期
        if (!empty($input['search']['date'])) {
            $timestamp_start = strtotime($input['search']['date']);
            if ($timestamp_start) {
                $config['where'][] = array('[and] hf.house_filing_insert_time > [-]', $timestamp_start);
                $config['where'][] = array('[and] hf.house_filing_insert_time < [-]', $timestamp_start + 86400);
            }
        }

        // 排序
        $config['orderby'][] = array('hf.house_filing_read', false);
        $config['orderby'][] = array('hf.house_filing_insert_time', true);
        $config['orderby'][] = array('hf.house_filing_id', true);

        //查询数据
        $data = object(parent::TABLE_HOUSE_FILING)->select_page($config);

        // 未读报备
        $unread = array();

        //格式化数据
        foreach ($data['data'] as &$val) {
            if ($val['read'] === '0') {
                $key = $val['house_product_id'];
                if (!isset($unread[$key])) {
                    $unread[$key] = array(
                        'house_filing_ids' => array(),
                        'house_filing_agent_phones' => array(),
                    );
                }
                $unread[$key]['house_filing_ids'][] = $val['id'];
                $unread[$key]['house_filing_agent_phones'][] = $val['agent_phone'];
            }
            $val['agent_json'] = array('CompanyId' => $val['agent_company']);
            $val['time'] = date('Y-m-d H:i', $val['time']);
        }

        // 更新状态，发送短信通知
        if ($unread) {
            foreach ($unread as $key => $val) {
                $this->_update_read($key, $val['house_filing_ids'], $val['house_filing_agent_phones']);
            }
        }

        return $data;
    }

    /**
     * 查询列表
     *
     * api: HOUSEFILINGSELFLIST
     * req: list通用参数
     * 
     * @return array
     */
    public function api_self_list($input = array())
    {
        // 检测经纪人状态
        $this->_check_agent_state_($input);

        // 查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );

        // 字段
        $config['select'] = array(
            'hf.house_filing_id AS id',
            'hf.house_filing_client_name AS client_name',
            'hf.house_filing_client_phone AS client_phone',
            'hf.house_filing_visit_time AS visit_time',
            'hf.house_filing_insert_time AS time',
            'hp.house_product_name AS product_name',
        );

        // 条件
        $config['where'][] = array('hf.user_id = [+]', $_SESSION['user_id']);

        // 排序
        $config['orderby'][] = array('hf.house_filing_insert_time', true);
        $config['orderby'][] = array('hf.house_filing_id', true);

        //查询数据
        $data = object(parent::TABLE_HOUSE_FILING)->select_page($config);

        //格式化数据

        return $data;
    }

    /**
     * 未读报备信息数量
     * api: HOUSEFILINGSELFUNREADCOUNT
     * @return integer
     */
    public function api_self_unread_count()
    {
        if (empty($_SESSION['user_id'])) {
            return 0;
        }

        $sql_select_product_id = object(parent::TABLE_HOUSE_PRODUCT)->sql_select_product_id($_SESSION['user_id']);
        $select = array(
            'COUNT(*) AS count',
        );
        $call_where = array(
            array('house_product_id in ([-])', $sql_select_product_id, true),
            array('[and] house_filing_read = 0'),
        );
        $data = object(parent::TABLE_HOUSE_FILING)->select_where($select, $call_where);

        return empty($data[0]['count']) ? 0 : $data[0]['count'];
    }


    //===========================================
    // 私有方法
    //===========================================


    /**
     * 更新状态为已读，给报备经纪人发通知短信
     * @param  string $house_product_id     [楼盘项目ID]
     * @param  array  $house_filing_ids     [报备ID，索引数组]
     * @param  array  $unread_filing_ids    [报备人手机号，索引数组]
     * @return void
     */
    private function _update_read($house_product_id, $house_filing_ids, $house_filing_agent_phones)
    {
        // 更新已读
        $house_filing_ids_str = '"'.implode('","', $house_filing_ids).'"';
        $where = array(
            array('house_filing_id in ([-])', $house_filing_ids_str, true),
            array('[and] house_filing_read = 0'),
        );
        $data = array(
            'house_filing_read' => 1,
            'house_filing_update_time' => time(),
        );
        object(parent::TABLE_HOUSE_FILING)->update($where, $data);

        // 短信配置
        $dysms_access = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('dysms_access'), true);
        $client_object = object(parent::PLUGIN_ALIYUN_DYSMS)->client($dysms_access);

        // 查询楼盘信息
        $house_product = object(parent::TABLE_HOUSE_PRODUCT)->find($house_product_id);
        if (empty($house_product)) {
            return false;
        }

        // 发送短信
        $client_object->send(array(
            'phone_numbers' => implode(',', $house_filing_agent_phones),
            'sign_name' => '汉唐楼盘分销',
            'template_code' => 'SMS_164185152',
            'template_param' => array(
                'name' => $house_product['house_product_name'],
                'people' => $house_product['house_product_agent_phone'].'('.$house_product['house_product_agent_name'].')',
            ),
        ));
    }

    /**
     * 对单个用户推送消息
     * @return void
     */
    private function _push_to_single($data)
    {
        // 查询接收者信息
        $user = object(parent::TABLE_USER)->find($data['owner_id']);
        $user_json = cmd(array($user['user_json']), 'json decode');
        if (empty($user_json['ClientID'])) {
            return;
        }

        // 查询推送配置
        $getui_access = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('getui_access'), true);
        if (empty($getui_access['state'])) {
            return;
        }

        $getui = object('eapie\source\plugin\getui\getui')->init($getui_access);
        if (!empty($getui['errno'])) {
            throw new error($getui['error']);
        }

        // 模板参数
        $template = array(
            'logo' => 'logo.png',
            'title' => '汉唐楼盘分销',
            'text' => "（{$data['house_product_name']}）有报备 请注意查收",
            'transmission_content' => '!@#$%^&*()_+',
            'ring' => true,
            'vibrate' => true,
            'clearable' => true,
            'ClientID' => $user_json['ClientID'],
        );

        $getui_res = object('eapie\source\plugin\getui\getui')->push_message_to_single($template);
        if (!empty($getui_res['errno'])) {
            throw new error($getui_res['error']);
        }
    }

}