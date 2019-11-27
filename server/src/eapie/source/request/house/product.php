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

//楼盘项目
class product extends \eapie\source\request\house
{

    /**
     * 初始化
     *
     * api: HOUSEPRODUCTSELFINIT
     * req: null
     * 
     * @return string 楼盘项目ID
     */
    public function api_self_init($input = array())
    {
        // 检测经纪人状态
        $this->_check_agent_state_($input);

        // 查询楼盘项目数据
        $house_product = object(parent::TABLE_HOUSE_PRODUCT)->find_where(array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] house_product_state=4'),
        ));

        // 是否已初始化
        if ($house_product) {
            // 查询图片数据
            $house_product_images = object(parent::TABLE_HOUSE_PRODUCT_IMAGE)->select(array(
                'where' => array(array('house_product_id=[+]', $house_product['house_product_id']))
            ));

            //格式化数据
            $img = array();
            foreach ($house_product_images as $val) {
                $img_type = $val['house_product_image_type'];
                if (!array_key_exists($img_type, $img))
                    $img[$img_type] = array();

                $img[$img_type][] = $val['image_id'];
            }

            return array(
                'house_product_id' => $house_product['house_product_id'],
                'img' => $img,
            );
        }

        //插入数据
        $insert_data['house_product_id'] = object(parent::TABLE_HOUSE_PRODUCT)->get_unique_id();
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['house_product_state'] = 4;
        $insert_data['house_product_insert_time'] = time();
        $insert_data['house_product_update_time'] = time();

        if (object(parent::TABLE_HOUSE_PRODUCT)->insert($insert_data)) {
            return array(
                'house_product_id' =>$insert_data['house_product_id']
            );
        } else {
            throw new error('初始化失败');
        }
    }


    //===========================================
    // 操作数据
    //===========================================


    /**
     * 编辑项目信息
     *
     * api: HOUSEPRODUCTSELFEDIT
     * req: {
     *  id              [str] [必填] [楼盘项目ID]
     *  developer       [str] [必填] [开发商]
     *  manage_company  [str] [必填] [物业公司名称]
     *  manage_money    [int] [必填] [物业费。分/平方]
     *  json            [arr] [必填] [项目分析，项目动态]
     * 
     *  type            [str] [必填] [楼盘——类型]
     *  name            [str] [必填] [楼盘——名称]
     *  total_floor     [int] [必填] [楼盘——总层高，单位层]
     *  total_room      [int] [必填] [楼盘——总体量，套]
     *  plot_ratio      [dec] [必填] [楼盘——容积率]
     *  greening_rate   [dec] [必填] [楼盘——绿化率]
     *  
     *  room_rate       [dec] [必填] [房子——得房率]
     *  room_state      [int] [必填] [房子——销售状态。]
     *  ladder_ratio    [str] [必填] [房子——梯户比。几梯几户]
     *  
     *  agent_name      [str] [必填] [经纪人——对接人姓名]
     *  agent_phone     [int] [必填] [经纪人——渠道公司电话]
     *  agent_commision [int] [必填] [经纪人——参考佣金]
     *  agent_company   [str] [必填] [经纪人——销售公司]
     *  
     *  province        [str] [必填] [位置——省]
     *  city            [str] [必填] [位置——市]
     *  district        [str] [必填] [位置——区]
     *  address         [str] [必填] [位置——地址]
     *  longitude       [dec] [必填] [位置——经度]
     *  latitude        [dec] [必填] [位置——纬度]
     *
     *  time_land       [date] [必填] [拿地时间]
     *  time_sale       [date] [必填] [开盘时间]
     *
     *  remark          [str] [可选] [备注]
     * }
     * 
     * @param  array  $input 请求参数
     * @return string 楼盘客户表ID
     */
    public function api_self_edit($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        //检测数据
        object(parent::ERROR)->check($input, 'id', parent::TABLE_HOUSE_PRODUCT, array('args', 'exist'), 'house_product_id');
        object(parent::ERROR)->check($input, 'developer', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_developer');
        //楼盘
        object(parent::ERROR)->check($input, 'type', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_type');
        object(parent::ERROR)->check($input, 'name', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_name');
        //房子
        object(parent::ERROR)->check($input, 'room_price', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_room_price');
        object(parent::ERROR)->check($input, 'room_state', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_room_state');
        //地址
        object(parent::ERROR)->check($input, 'province', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_province');
        object(parent::ERROR)->check($input, 'city', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_city');
        object(parent::ERROR)->check($input, 'district', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_district');
        object(parent::ERROR)->check($input, 'address', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_address');
        object(parent::ERROR)->check($input, 'longitude', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_longitude');
        object(parent::ERROR)->check($input, 'latitude', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_latitude');
        //经纪人
        object(parent::ERROR)->check($input, 'agent_name', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_agent_name');
        object(parent::ERROR)->check($input, 'agent_phone', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_agent_phone');
        object(parent::ERROR)->check($input, 'agent_commision', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_agent_commision');
        object(parent::ERROR)->check($input, 'agent_company', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_agent_company');
        //时间
        object(parent::ERROR)->check($input, 'time_delivery', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_time_delivery');

        if (isset($input['remark']))
            object(parent::ERROR)->check($input, 'remark', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_remark');
        if (isset($input['json']))
            object(parent::ERROR)->check($input, 'json', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_json');

        //是否初始化
        $original = object(parent::TABLE_HOUSE_PRODUCT)->find($input['id']);
        if (empty($original))
            throw new error('请先初始化楼盘');

        if ($original['house_product_trash'] == 1)
            throw new error('该楼盘已被回收');

        if ($original['house_product_delete_state'] == 1)
            throw new error('该楼盘已被删除');

        //检测是否上传图片
        $this->_check_product_image($input['id']);

        //白名单过滤
        $whitelist = array(
            'developer',
            'manage_company',
            'manage_money',
            'json',
            'remark',
            'type',
            'name',
            'total_room',
            'plot_ratio',
            'greening_rate',
            'room_height',
            'room_rate',
            'room_price',
            'room_state',
            'ladder_ratio',
            'agent_name',
            'agent_phone',
            'agent_commision',
            'agent_company',
            'province',
            'city',
            'district',
            'address',
            'longitude',
            'latitude',
            'time_land',
            'time_delivery',
        );
        $filter = cmd(array($input, $whitelist), 'arr whitelist');

        //添加前缀
        $update_data = array();
        foreach ($filter as $key => $val) {
            $update_data['house_product_'.$key] = $val;
        }

		// 同楼盘，在发布时项目名字可以一样，有人发布过但产品也是一样，那就不能让他发布这个相同产品类型“产品类型是指；公寓，写字楼，住房”
		// $repetition_data = object(parent::TABLE_HOUSE_PRODUCT)->find_where(array(
		// 	array('house_product_id<>[+]', $input['id']),
		// 	array('[and] house_product_name=[+]', $update_data['house_product_name']),
		// 	array('[and] house_product_type=[+]', $update_data['house_product_type'])
		// ));
		// if( !empty($repetition_data) ){
		// 	throw new error('该项目名称下的产品，已经存在相同类型，请勿重复上传！');
		// }
		

        //更新数据
        $update_data['house_product_update_time'] = time();

        if (isset($input['json']))
            $update_data['house_product_json'] = cmd(array($input['json']), 'json encode');
        if ($original['house_product_state'] == 4)
            $update_data['house_product_state'] = 2;

        $update_where = array(
            array('house_product_id=[+]', $input['id']),
            array('[and] user_id=[+]', $_SESSION['user_id']),
        );

        if (object(parent::TABLE_HOUSE_PRODUCT)->update($update_where, $update_data)) {
            return $input['id'];
        } else {
            throw new error('操作失败');
        }
    }

    /**
     * 删除
     * api: HOUSEPRODUCTSELFREMOVE
     * req: {
     *  id  [str] [必填] [楼盘项目ID]
     * }
     *
     * @return bool
     */
    public function api_self_remove($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        //检测数据
        object(parent::ERROR)->check($input, 'id', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_id');

        // 查询楼盘项目
        $house_product = object(parent::TABLE_HOUSE_PRODUCT)->find($input['id']);

        if (empty($house_product) || $house_product['house_product_trash'] == 1 || $house_product['house_product_delete_state'] == 1)
            return true;

        // 更新删除状态
        $update_where = array(
            array('house_product_id=[+]', $input['id']),
            array('[and] user_id=[+]', $_SESSION['user_id']),
        );
        $update_data = array(
            'house_product_delete_state' => 1,
            'house_product_delete_time' => time(),
        );

        if (object(parent::TABLE_HOUSE_PRODUCT)->update($update_where, $update_data)) {
            return true;
        } else {
            throw new error('操作失败');
        }
    }


    //===========================================
    // 查询数据
    //===========================================


    /**
     * 查询楼盘项目列表
     *
     * api: HOUSEPRODUCTLIST
     * req: list通用参数
     * 
     * @param search {
     *  keywords    [str] [关键词]
     *  province    [str] [省]
     *  city        [str] [市]
     *  district    [str] [区]
     *  type        [str] [类型]
     *  recommendation [bol] [推荐]
     *  follow      [bol] [关注]
     * }
     * 
     * @param  array  $input [请求参数]
     * @return array
     */
    public function api_list($input = array())
    {
        // 是否登录，获取用户关注的楼盘项目ID
        $follow_ids = array();
        if (object(parent::REQUEST_USER)->check(true)) {
            $follow_data = object(parent::TABLE_USER_FOLLOW)->select(array(
                'select' => array('user_follow_key'),
                'where' => array(
                    array('user_id=[+]', $_SESSION['user_id']),
                    array('[and] user_follow_module=[+]', parent::MODULE_HOUSE_PRODUCT),
                ),
            ));
            foreach ($follow_data as $val) {
                $follow_ids[] = $val['user_follow_key'];
            }
        }

        // 查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );

        // 字段
        $sql_product_image = object(parent::TABLE_HOUSE_PRODUCT_IMAGE)->sql_join_product_main_id('hp');
        $config['select'] = array(
            'hp.house_product_id AS id',
            'hp.house_product_name AS name',
            'hp.house_product_type AS type',
            'hp.house_product_room_price AS room_price',
            'hp.house_product_room_state AS room_state',
            'hp.house_product_agent_name AS agent_name',
            'hp.house_product_agent_phone AS agent_phone',
            'hp.house_product_address AS address',
            'hp.house_product_district AS district',
            'hp.house_product_agent_commision AS agent_commision',
            'hp.house_product_insert_time AS insert_time',
            'hp.house_product_update_time AS update_time',
            'hp.house_product_time_delivery AS time_delivery',
            '('.$sql_product_image.') as image_id',
        );

        // 条件
        $config['where'][] = array('house_product_state = 1');
        $config['where'][] = array('[and] house_product_trash = 0');
        $config['where'][] = array('[and] house_product_delete_state = 0');

        // 筛选——省
        if (!empty($input['search']['province']) && is_string($input['search']['province'])) {
            $val = cmd(array($input['search']['province']), 'str addslashes');
            $val = '%'.$val.'%';
            $config['where'][] = array('[and] house_product_province like [+]', $val);
        }

        // 筛选——市
        if (!empty($input['search']['city']) && is_string($input['search']['city'])) {
            $val = cmd(array($input['search']['city']), 'str addslashes');
            $val = '%'.$val.'%';
            $config['where'][] = array('[and] house_product_city like [+]', $val);
        }

        // 筛选——区
        if (!empty($input['search']['district']) && is_string($input['search']['district'])) {
            $val = cmd(array($input['search']['district']), 'str addslashes');
            $val = '%'.$val.'%';
            $config['where'][] = array('[and] house_product_district like [+]', $val);
        }

        // 筛选——类型
        if (!empty($input['search']['type']) && is_string($input['search']['type'])) {
            $config['where'][] = array('[and] house_product_type = [+]', $input['search']['type']);
        }

        // 筛选——搜索
        if (!empty($input['search']['keywords'])) {
            $keywords = $input['search']['keywords'];
            //是否搜索价格
            if (is_numeric($keywords)) {
                $keywords *= 100;
                $config['where'][] = array('[and] house_product_room_price between []', $keywords - 200000);
                $config['where'][] = array('[and] []', $keywords + 200000);
                $config['orderby'][] = array('house_product_room_price', false);
            } elseif (is_string($keywords)) {
                $keywords = cmd(array($input['search']['keywords']), 'str addslashes');
                $keywords = '%'.$input['search']['keywords'].'%';
                $config['where'][] = array('[and] CONCAT(house_product_name,house_product_type,house_product_address) like [+]', $keywords);
                $config['orderby'][] = array("(CONCAT(house_product_name,house_product_type,house_product_address) like '{$keywords}')", true);
            }
        }

        // 筛选——推荐楼盘
        if (!empty($input['search']['recommendation'])) {
            $config['where'][] = array('[and] house_product_top_end_time > [-]', time());
            $config['orderby'][] = array('house_product_top_sort', true);
        }

        // 筛选——关注
        if (!empty($input['search']['follow'])) {
            $ids_str = "'".implode("','", $follow_ids)."'";
            $config['where'][] = array('[and] hp.house_product_id in ([-])', $ids_str, true);
        }

        // 排序
        $config['orderby'][] = array('hp.house_product_update_time', true);
        $config['orderby'][] = array('hp.house_product_id', true);

        // 查询数据
        $data = object(parent::TABLE_HOUSE_PRODUCT)->select_page($config);

        // 楼盘主键ID
        $house_product_ids = array();

        // 格式化数据
        foreach ($data['data'] as &$val) {
            $house_product_ids[] = $val['id'];
            // 是否关注
            $val['is_follow'] = in_array($val['id'], $follow_ids);
            $val['score'] = 5;
        }

        // 查询楼盘评价评分
        $evaluate = object('eapie\source\table\user\user_evaluate')->select_score('house_product', $house_product_ids);
        foreach ($data['data'] as &$val) {
            foreach ($evaluate as $val2) {
                if ($val['id'] === $val2['key']) {
                    $val['score'] = intval($val2['score']);
                }
            }
        }

        return $data;
    }

    /**
     * 楼盘图鉴
     * api: HOUSEPRODUCTLISTMAP
     * @param  array  $input [请求参数]
     * @return array
     */
    public function api_list_map($input = array())
    {
        // 查询配置
        $config = array(
            'select'  => array(
                'hp.house_product_id AS id',
                'hp.house_product_name AS name',
                'hp.house_product_type AS type',
                'hp.house_product_province AS province',
                'hp.house_product_city AS city',
                'hp.house_product_district AS district',
            ),
            'where'   => array(
                array('hp.house_product_state = 1'),
                array('[and] hp.house_product_trash = 0'),
                array('[and] hp.house_product_delete_state = 0'),
            ),
            'orderby' => array(),
            'limit'   => array(),
        );

        // 查询数据
        $data = object(parent::TABLE_HOUSE_PRODUCT)->select_page($config);

        // 格式化数据
        $list_product = array();
        foreach ($data['data'] as $i) {
            $province = $i['province'];
            $district = $i['city'] . $i['district'];

            if (!isset($list_product[$province])) {
                $list_product[$province] = array();
            }
            if (!isset($list_product[$province][$district])) {
                $list_product[$province][$district] = array();
            }

            unset($i['province'], $i['city'], $i['district']);
            $list_product[$province][$district][] = $i;
        }

        $data['data'] = $list_product;
        return $data;
    }

    /**
     * 查询列表数据
     *
     * api: HOUSEPRODUCTSELFLIST
     * req: {
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
        $sql_filing_count = object(parent::TABLE_HOUSE_FILING)->sql_get_count_by_house_product_id('hp');
        $sql_filing_unread_count = object(parent::TABLE_HOUSE_FILING)->sql_get_count_unread_by_house_product_id('hp');
        $config['select'] = array(
            'hp.house_product_id AS id',
            'hp.house_product_name AS name',
            'hp.house_product_room_price AS room_price',
            'hp.house_product_room_state AS room_state',
            'hp.house_product_agent_name AS agent_name',
            'hp.house_product_agent_phone AS agent_phone',
            'hp.house_product_state AS state',
            'hp.house_product_sort AS sort',
            'hpt.house_product_top_sort AS top_sort',
            'hpt.house_product_top_start_time AS top_start_time',
            'hpt.house_product_top_end_time AS top_end_time',
            '('.$sql_filing_count.') as filing_count',
            '('.$sql_filing_unread_count.') as filing_unread_count',
        );

        //条件
        $config['where'][] = array('hp.user_id=[+]', $_SESSION['user_id']);
        $config['where'][] = array('[and] house_product_state in (0,1,2)');
        $config['where'][] = array('[and] house_product_trash = 0');
        $config['where'][] = array('[and] house_product_delete_state = 0');

        //排序
        $config['orderby'][] = array('hp.house_product_sort', true);
        $config['orderby'][] = array('hp.house_product_insert_time', true);
        $config['orderby'][] = array('hp.house_product_id', true);

        //查询数据
        $data = object(parent::TABLE_HOUSE_PRODUCT)->select_page($config);

        //格式化数据
        $time = time();
        foreach ($data['data'] as &$val) {
            $val['room_state'] = ($val['room_state'] == 1) ? '在售' : '待售';
            // $val['is_top'] = ($val['top_end_time'] > $time) ? true : false;
            // 测试
            $val['is_top'] = false;
        }

        return $data;
    }

    /**
     * 查询详情
     *
     * api: HOUSEPRODUCTGET
     * req: {
     *  id  [str] [必填] [楼盘项目ID]
     * }
     * 
     * @param  array  $input 请求参数
     * @return array
     */
    public function api_get($input = array())
    {
        //检测数据
        object(parent::ERROR)->check($input, 'id', parent::TABLE_HOUSE_PRODUCT, array('args'), 'house_product_id');

        //——查询项目数据——
        $data = object(parent::TABLE_HOUSE_PRODUCT)->find($input['id']);
        if (empty($data) || $data['house_product_trash'] == 1 || $data['house_product_delete_state'] == 1)
            throw new error('数据不存在');

        //删除前缀
        $data = cmd(array('house_product_', $data, true),  'arr key_prefix');

        //格式化数据
        $data['json'] = cmd(array($data['json']), 'json decode');

        //——查询图片数据——
        $data_img = object(parent::TABLE_HOUSE_PRODUCT_IMAGE)->select(array(
            'where' => array(array('house_product_id=[+]', $input['id']))
        ));

        //格式化数据
        $img = array();
        foreach ($data_img as $val) {
            $img_type = $val['house_product_image_type'];
            if (!array_key_exists($img_type, $img))
                $img[$img_type] = array();

            $img[$img_type][] = $val['image_id'];
        }

        $data['img'] = $img;

        return $data;
    }

    /**
     * 查询楼盘信息
     *
     * api: HOUSEGET
     * req: {
     *  name [str] [必填] [楼盘名称]
     * }
     * 
     * @return object
     */
    public function api_house_get($input = array())
    {
        if (empty($input['name']) || !is_string($input['name']))
            return array();

        return object(parent::TABLE_HOUSE_PRODUCT)->get_house_data($input['name']);
    }

    /**
     * 查询楼盘项目类型
     *
     * api: HOUSEPRODUCTCONFIGTYPE
     * req: null
     * 
     * @return array
     */
    public function api_config_type()
    {
        return object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('house_product_type'), true);
    }


    //===========================================
    // 私有方法
    //===========================================


    /**
     * 检测楼盘图片
     * @param  string $house_product_id 楼盘项目ID
     * @return void
     */
    private function _check_product_image($house_product_id = '')
    {
        //查询该楼盘项目的所有图片
        $data_img = object(parent::TABLE_HOUSE_PRODUCT_IMAGE)->select(array(
            'where' => array(
                array('house_product_id=[+]', $house_product_id),
            )
        ));
        if (empty($data_img))
            throw new error('请上传图片');

        //已上传的图片类型
        $data_img_type = array();
        foreach ($data_img as $val) {
            $data_img_type[] = $val['house_product_image_type'];
        }

        //查询楼盘项目的图片类型
        $config_type = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('house_product_image_type'), true);
        foreach ($config_type as $key => $val) {
            if (in_array($key, $data_img_type))
                continue;
            else 
                throw new error('请上传图片：'.$val);
        }
    }

}