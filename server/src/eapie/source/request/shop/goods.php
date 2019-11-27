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

//商品
class goods extends \eapie\source\request\shop
{


    /**
     * 查询列表
     *
     * api: SHOPGOODSLIST
     * req: {
     *  property   [int] [积分商品]
     *  search  [arr] [可选] [搜索、筛选]
     *  sort    [arr] [可选] [排序]
     *  size    [int] [可选] [每页的条数]
     *  page    [int] [可选] [当前页数，如果是等于 all 那么则查询所有]
     *  start   [int] [可选] [开始的位置，如果存在，则page无效]
     * }
     *
     * @param search {
     *  type_id    [str] [类别ID]
     *  type_name  [str] [类别名称]
     *  keywords   [str] [关键词]
     *  money_min  [int] [最小价格]
     *  money_max  [int] [最大价格]
     * }
     * 
     * 返回的数据：
     * res: {
     *  row_count'      [int] [数据总条数]
     *  limit_count'    [int] [已取出条数]
     *  page_size'      [int] [每页的条数]
     *  page_count'     [int] [总页数]
     *  data'           [arr] [数据]
     * }
     * 
     * @return  array
     */
    public function api_list($input = array())
    {
        //查询配置
        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
        );

        //查询字段
        $sql_join_stock_max_price = object(parent::TABLE_SHOP_GOODS_SKU)->sql_join_stock_max_price("sg");
        $sql_join_stock_min_price = object(parent::TABLE_SHOP_GOODS_SKU)->sql_join_stock_min_price("sg");
        $config['select'] = array(
            'sg.shop_goods_id',
            'sg.shop_goods_parent_id',
            'sg.shop_goods_sn AS sn',
            'sg.shop_goods_property AS property',
            'sg.shop_goods_name AS name',
            'sg.shop_goods_info AS info',
            'sg.shop_goods_click AS click',
            'sg.shop_goods_sales AS sales',
            '(' . $sql_join_stock_max_price . ') as price_max',
            '(' . $sql_join_stock_min_price . ') as price_min',
        );

        
        //排序
        $config['orderby'] = object(parent::REQUEST)->orderby($input, array(
            //添加时间
            'insert_time_desc' => array('sg.shop_goods_insert_time', true),
            'insert_time_asc' => array('sg.shop_goods_insert_time', false),
            //更新时间
            'update_time_desc' => array('sg.shop_goods_update_time', true),
            'update_time_asc' => array('sg.shop_goods_update_time', false),
            
            //点击量
            'click_desc' => array('sg.shop_goods_click', true),
            'click_asc' => array('sg.shop_goods_click', false),
            //销量
            'sales_desc' => array('sg.shop_goods_sales', true),
            'sales_asc' => array('sg.shop_goods_sales', false),
            //自定义排序
            'sort_desc' => array('sg.shop_goods_sort', true),
            'sort_asc' => array('sg.shop_goods_sort', false),
            //最小价格
            'price_min_desc' => array('price_min', true),
            'price_min_asc' => array('price_min', false),
            //最小价格
            'price_max_desc' => array('price_max', true),
            'price_max_asc' => array('price_max', false),

        ));
		$config['orderby'][] = array('sg.shop_goods_sort', false);
        $config['orderby'][] = array('sg.shop_goods_id', false);

        //条件
        $config['where'][] = array('sg.shop_goods_state=1');
        $config['where'][] = array('[and] sg.shop_goods_trash=0');

        //筛选
        if (isset($input['search']))
            $config = $this->_filter($config, $input['search']);


        //精确类别 type   name|module|label|parent_id
        if (!empty($input['search']['type']) && is_array($input['search']['type'])) {
            $type_where = array();
            if (isset($input['search']['type']['name']) && is_string($input['search']['type']['name'])) {
                $type_where[] = array('type_name=[+]', $input['search']['type']['name']);
            }
            if (isset($input['search']['type']['module']) && is_string($input['search']['type']['module'])) {
                $type_where[] = array('type_module=[+]', $input['search']['type']['module']);
            }
            if (isset($input['search']['type']['label']) && is_string($input['search']['type']['label'])) {
                $type_where[] = array('type_label=[+]', $input['search']['type']['label']);
            }
            if (isset($input['search']['type']['parent_id']) && is_string($input['search']['type']['parent_id'])) {
                $type_where[] = array('type_parent_id=[+]', $input['search']['type']['parent_id']);
            }

            if (!empty($type_where)) {
                $data = object(parent::TABLE_TYPE)->find_where($type_where);
                if ($data) {
                    //是否一级类别
                    if (empty($data['type_parent_id'])) {
                        $sql_join_goods_id = object(parent::TABLE_SHOP_GOODS_TYPE)->sql_join_type_parent_goods_id($data['type_id']);
                    } else {
                        $sql_join_goods_id = object(parent::TABLE_SHOP_GOODS_TYPE)->sql_join_type_son_goods_id($data['type_id']);
                    }
                    $config['where'][] = array('[and] sg.shop_goods_id in ([-])', $sql_join_goods_id, true);
                } else {

                    return array(
                        'row_count' => 0,
                        'limit_count' => 0,
                        'page_size' => 0,
                        'page_count' => 0,
                        'page_now' => 0,
                        'data' => array()
                    );
                }
            }
        }


        if(isset($input['property']) && is_numeric($input['property'])){
            $config['where'][] = array('[and] sg.shop_goods_property=[-]',$input['property']);
        }

        //查询数据
        $data = object(parent::TABLE_SHOP_GOODS)->select_page($config);
        if (empty($data['data']))
            return $data;

        //查询商品附属数据
        $goods = object(parent::TABLE_SHOP_GOODS)->get_additional_data($data['data'], array(
            'shop_goods_type' => array(
                'where' => array(array('[and] t.type_state=1')),
            ),
            /*'shop_goods_image_main' => array(
				'select' => array(
					'shop_goods_id',
					'image_id',
					'image_width',
					'image_height',
					'image_state'
				)
			),
			'shop_goods_sku' => array(
				'select' => array(
					'shop_goods_id',
					'shop_goods_sku_id as id',
					'shop_goods_spu_id as spu_id',
					'image_id',
					'shop_goods_sku_name as name',
					'shop_goods_sku_info as info',
					'shop_goods_sku_stock as stock',
					'shop_goods_sku_price as price',
				)
			),*/
        ));



        //格式化数据
        foreach ($goods as &$v) {
            $v['id'] = $v['shop_goods_id'];
            $v['parent_id'] = $v['shop_goods_parent_id'];
            $v['image_id']  = empty($v['shop_goods_image_main'][0]['image_id']) ? '' : $v['shop_goods_image_main'][0]['image_id'];
            $v['price_min'] = empty($v['shop_goods_sku_min']['shop_goods_sku_price']) ? 0 : $v['shop_goods_sku_min']['shop_goods_sku_price'];
            $v['price_max'] = empty($v['shop_goods_sku_max']['shop_goods_sku_price']) ? 0 : $v['shop_goods_sku_max']['shop_goods_sku_price'];
            $v['market_price_min'] = empty($v['shop_goods_sku_min']['shop_goods_sku_market_price']) ? 0 : $v['shop_goods_sku_min']['shop_goods_sku_market_price'];
            $v['market_price_max'] = empty($v['shop_goods_sku_max']['shop_goods_sku_market_price']) ? 0 : $v['shop_goods_sku_max']['shop_goods_sku_market_price'];

            //商品类别
            $v['type'] = array();
            $v['type_list'] = array();
            foreach ($v['shop_goods_type'] as $v_type) {
                $v['type_list'][] = array(
                    'id' => $v_type['type_id'],
                    'name' => $v_type['type_name'],
                    'module' => $v_type['type_module'],
                    'label' => $v_type['type_label'],
                );
            }

            unset($v['shop_goods_id'],
            $v['shop_goods_parent_id'],
            $v['shop_goods_image_main'],
            $v['shop_goods_type'],
            //$v['shop_goods_sku'],
            $v['shop_goods_sku_min'],
            $v['shop_goods_sku_max'],
            $v['shop_goods_spu']);
        }
        unset($v);

        //如果是 限时商品
        if (!empty($input['search']['when'])) {
            $shop_goods_ids = array();
            foreach ($goods as $get_id_v) {
                $shop_goods_ids[] = $get_id_v['id'];
            }


            //获取分类数据
            $in_string = "\"" . implode("\",\"", $shop_goods_ids) . "\"";
            $when_config = array(
                'where' => array(),
                'select' => array(
                    'shop_goods_id',
                    'shop_goods_when_name as name',
                    'shop_goods_when_info as info',
                    "FROM_UNIXTIME(shop_goods_when_start_time,'%Y-%m-%d %H:%i') as start_time",
                    'shop_goods_when_start_time as start_timestamp',
                    "FROM_UNIXTIME(shop_goods_when_end_time,'%Y-%m-%d %H:%i') as end_time",
                    'shop_goods_when_end_time as end_timestamp',
                )
            );
            $when_config['where'][] = array("shop_goods_id IN([-])", $in_string, true);
            $goods_when_list = object(parent::TABLE_SHOP_GOODS_WHEN)->select($when_config);
            // 初始化库存
            $stock = 0;
            // 将列表循环给商品
            foreach ($goods as $goods_k => $goods_v) {
                if (empty($goods_when_list)) {
                    break;
                }
                // 计算库存
                for ($i = 0; $i < count($goods_v['shop_goods_sku']); $i++) { 
                    $stock = $stock + (int)$goods_v['shop_goods_sku'][$i]['shop_goods_sku_stock'];
                }
                $goods[$goods_k]['sku_stock'] = $stock;
                // 重新初始化库存
                $stock = 0;
                foreach ($goods_when_list as $when_k => $when_v) {
                    if ($when_v['shop_goods_id'] == $goods_v['id']) {
                        // 删除id
                        unset($when_v['shop_goods_id']);
                        $goods[$goods_k]['goods_when'] = $when_v;
                        unset($goods_when_list[$when_k]);
                    }
                }
            }
        }

        // 重新回收sku属性
        foreach ($goods as &$v) {
            unset($v['shop_goods_sku']);
        }

        $data['data'] = $goods;

        return $data;
    }


    /**
     * 前台获取一条商城商品数据
     * 
     * api: SHOPGOODSGET
     * req: {
     *  id [str] [必填] [商品ID]
     * }
     * 
     * @param   array   $input []
     * @return  array
     */
    public function api_get($input = array())
    {
        //检测请求参数
        object(parent::ERROR)->check($input, 'id', parent::TABLE_SHOP_GOODS, array('args'), 'shop_goods_id');

        //查询数据
        $goods = object(parent::TABLE_SHOP_GOODS)->find($input['id']);
        if (empty($goods))
            throw new error('商品不存在');
        if ($goods['shop_goods_state'] != 1)
            throw new error('商品未发布');
        if ($goods['shop_goods_trash'] == 1)
            throw new error('商品已回收');

        //是否限时购商品
        $data_when = object(parent::TABLE_SHOP_GOODS_WHEN)->find($input['id']);
        if ($data_when) {
            //检测状态
            $state = object(parent::TABLE_SHOP_GOODS_WHEN)->check_state($data_when);
            if ($state === false)
                throw new error('限时购已结束');

            $goods['goods_when'] = array(
                'name' => $data_when['shop_goods_when_name'],
                'info' => $data_when['shop_goods_when_info'],
                'start_time' => date('Y-m-d H:i', $data_when['shop_goods_when_start_time']),
                'start_timestamp' => $data_when['shop_goods_when_start_time'],
                'end_time' => date('Y-m-d H:i', $data_when['shop_goods_when_end_time']),
                'end_timestamp' => $data_when['shop_goods_when_end_time'],
            );
        }

        //查询附属数据
        $data = array($goods);
        $data = object(parent::TABLE_SHOP_GOODS)->get_additional_data($data, array(
            'shop_goods_type' => array(
                'where' => array(
                    array('[and] t.type_state=1')
                ),
            ),
        ));
        $data = $data[0];

        //格式化数据-消除前缀
        $data = cmd(array('shop_goods_', $data, true),  'arr key_prefix');

        //黑名单
        $blacklist = array(
            'state',
            'warning',
            'stock_warning',
            'stock_mode',
            'sort',
            'seller_note',
            'trash',
            'trash_time',
            'user_id',
        );
        $data = cmd(array($data, $blacklist), 'arr blacklist');

        //格式化数据-商品分类
        $data['goods_type'] = array();
        foreach ($data['type'] as $val) {
            $data['goods_type'][] = $val['type_name'];
        }
        unset($data['type']);

        //格式化数据-商品图片
        $data['goods_image'] = array();
        foreach ($data['image_main'] as $val) {
            $data['goods_image'][] = $val['image_id'];
        }
        unset($data['image_main']);


        //格式化数据-商品属性
        $data['goods_spu'] = array();
        if (empty($data['spu'])) {
            $data['spu'] = array();
        }
        foreach ($data['spu'] as $val) {
            $item = array(
                'id' => $val['shop_goods_spu_id'],
                'parent_id' => $val['shop_goods_spu_parent_id'],
                'name' => $val['shop_goods_spu_name'],
                'info' => $val['shop_goods_spu_info'],
                'sort' => $val['shop_goods_spu_sort'],
                'image_id' => $val['image_id'],
                'required' => $val['shop_goods_spu_required'],
                'son' => array()
            );
            foreach ($val['son'] as $val2) {
                $item['son'][] = array(
                    'id' => $val2['shop_goods_spu_id'],
                    'parent_id' => $val2['shop_goods_spu_parent_id'],
                    'name' => $val2['shop_goods_spu_name'],
                    'info' => $val2['shop_goods_spu_info'],
                    'sort' => $val2['shop_goods_spu_sort'],
                    'image_id' => $val2['image_id'],
                    'required' => $val2['shop_goods_spu_required'],
                );
            }
            $data['goods_spu'][] = $item;
        }
        unset($data['spu']);

        //格式化数据-商品规格
        $data['goods_sku'] = array();
        if (empty($data['sku'])) {
            $data['sku'] = array();
        }
        foreach ($data['sku'] as $val) {
            $data['goods_sku'][] = array(
                'id' => $val['shop_goods_sku_id'],
                'spu_id' => $val['shop_goods_spu_id'],
                'image_id' => $val['image_id'],
                'name' => $val['shop_goods_sku_name'],
                'info' => $val['shop_goods_sku_info'],
                'stock' => $val['shop_goods_sku_stock'],
                'price' => $val['shop_goods_sku_price'],
            );
        }
        unset($data['sku']);

        //商品最低价和最高价
        $data['price_min'] = isset($data['sku_min']['shop_goods_sku_price']) ? $data['sku_min']['shop_goods_sku_price'] : 0;
        $data['price_max'] = isset($data['sku_max']['shop_goods_sku_price']) ? $data['sku_max']['shop_goods_sku_price'] : 0;
        $data['market_price_min'] = isset($data['sku_min']['shop_goods_sku_market_price']) ? $data['sku_min']['shop_goods_sku_market_price'] : 0;
        $data['market_price_max'] = isset($data['sku_max']['shop_goods_sku_market_price']) ? $data['sku_max']['shop_goods_sku_market_price'] : 0;
        unset($data['sku_min'], $data['sku_max']);

        // 单品分销的奖励金额
        $recommend_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("shop_recommend_reward"), true);
        if( isset($recommend_config) && $recommend_config['is_open'] ) {
            switch ($recommend_config['method']) {
                case 0:
                    # 按身份发放
                    $data['reward_money'] = intval($data['price_max'] * $recommend_config['area_agent']['royalty'] * 0.01);
                    break;
                case 1:
                    # 随机发放
                    $max_money = (int)$recommend_config['max_royalty_random'];
                    $min_money = (int)$recommend_config['min_royalty_random'];
                    $award_money = mt_rand($min_money,$max_money);
                    $data['reward_money'] = intval($data['price_max'] * $award_money * 0.01);
                    break;
                case 2:
                    # 定额发放
                    $data['reward_money'] = (int)$recommend_config['quota_recommend_money'] * 0.01;
                    break;
                case 3:
                    $data['reward_money'] = $goods['shop_goods_recommend_money'];
                    break;
                default:
                    $data['reward_money'] = 0;
                    break;
            }
        }
        return $data;
    }


    /**
     *  ------Mr.Zhao-----2019.07.25------
     * 二维码——————单品分享
     *
     * api: SHOPGOODSSELFSHARE
     * 
	 * {"class":"shop/goods","method":"api_self_share"}
     * 
     * 
     * 请求参数：
	 *[{"type":"二维码类型:app|applet|web","weixin_applet_config 微信小程序配置(小程序码必填)":{"scene":"最大32个可见字符，只支持数字，大小写英文以及部分特殊字符","page":"默认主页，必须是已经发布的小程序存在的页面","width":"默认430，二维码的宽度，单位 px，最小 280px，最大 1280px","auto_color":"默认false，自动配置线条颜色","line_color":"auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {\"r\":\"xxx\",\"g\":\"xxx\",\"b\":\"xxx\"} 十进制表示","is_hyaline":"默认false，是否需要透明底色"}}]
     * 
     * @param  [arr]  $input [请求参数]
     * @return image
     */
    public function api_self_share($input = array())
    {


        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测数据
        if( empty($input['type']) || 
        !is_string($input['type']) || 
        !in_array($input['type'], array('app', 'applet', 'web') )){
        	throw new error('二维码类型错误');
        }

        //检测请求参数
        object(parent::ERROR)->check($input, 'id', parent::TABLE_SHOP_GOODS, array('args'), 'shop_goods_id');

        //查询手机号
        $user_phone = object(parent::TABLE_USER_PHONE)->find_user_login_data($_SESSION['user_id']);
        if (empty($user_phone)) {
            throw new error('手机号异常');
        }
        $user_phone = $user_phone['user_phone_id'];
        // 用户昵称
        $user_nickname = '匿名用户';
        if(!empty($user_phone['user_nickname'])){
            $user_nickname = $user_phone['user_nickname'];
        }


        //查询数据
        // $good = object(parent::TABLE_SHOP_GOODS)->find($input['id']);
        $good = object(parent::TABLE_SHOP_GOODS)->find_join_goods_image($input['id']);
        if (empty($good))
            throw new error('商品不存在');
        if ($good['shop_goods_state'] != 1)
            throw new error('商品未发布');
        if ($good['shop_goods_trash'] == 1)
            throw new error('商品已回收');
        $goods_property = $good['shop_goods_property'];
        $goods = object(parent::TABLE_SHOP_GOODS)->get_additional_data(array(array('shop_goods_id' => $input['id'])));

        // 商品名称
        if (isset($goods[0]['shop_goods_image_main'][0]['shop_goods_name'])) {
            $goods_name = $goods[0]['shop_goods_image_main'][0]['shop_goods_name'];
        } else {
            $goods_name = $good['shop_goods_name'] ? $good['shop_goods_name'] : '';
        }

        // 商品图片ID
        if(isset($goods[0]['shop_goods_image_main'][0]['image_id'])){
            $good_img_id = $goods[0]['shop_goods_image_main'][0]['image_id'];
        }else{
            $good_img_id = $good['image_id'];
        }
        if (empty($good_img_id)) {
            throw new error('商品图片不存在');
        }

        // 图片标题
        $title = $goods_name;
        $little_title = 'E麦易选';
        $text = '宝贝推荐自好友 '.$user_nickname;
        if(object(parent::TABLE_USER_RECOMMEND)->verification_yitao_distribution()){
            $little_title = '爱尚购';
            $text = '商品推荐自好友 '.$user_nickname;
        }
        

        // 标题文字过长处理
        if(strlen($title)>90){
            $title = substr($title,0,87);
            $title .= '...';
        }

        // 普通商品
        if($goods_property ==0){
            if(isset($goods[0]['shop_goods_sku_min']['shop_goods_sku_price'])){
                $good_min_money = $goods[0]['shop_goods_sku_min']['shop_goods_sku_price']/100;
            }
            if(isset($goods[0]['shop_goods_sku_min']['shop_goods_sku_additional_credit'])){
                $good_min_additional_credit = $goods[0]['shop_goods_sku_min']['shop_goods_sku_additional_credit']/100;
            }
            if(isset($goods[0]['shop_goods_sku_max']['shop_goods_sku_price'])){
                $good_max_money = $goods[0]['shop_goods_sku_max']['shop_goods_sku_price']/100;
            }
            if(isset($goods[0]['shop_goods_sku_max']['shop_goods_sku_additional_credit'])){
                $good_max_additional_credit = $goods[0]['shop_goods_sku_max']['shop_goods_sku_additional_credit']/100;
            }

            // 拼凑标题字符
            if(isset($good_min_money)){
                $title.='【';
                $title.=$good_min_money;
                if(isset($good_max_money)&&$good_min_money<>$good_max_money){
                $title.=' ~ ';
                $title.=$good_min_money;
                }
                $title.='元】';
            }
            if(isset($good_min_additional_credit)){
                $title.='【';
                $title.=$good_min_additional_credit;
                if(isset( $good_max_additional_credit)&&$good_min_additional_credit<>$good_max_additional_credit){
                $title.=' ~ ';
                $title.=$good_max_additional_credit;
                }
                $title.='积分】';
            }
        }

        // 积分商品
        if($goods_property ==1){
            if (isset($goods[0]['shop_goods_sku_min']['shop_goods_sku_price'])) {
                $good_min_credit = $goods[0]['shop_goods_sku_min']['shop_goods_sku_price'] / 100;
            }
            if (isset($goods[0]['shop_goods_sku_max']['shop_goods_sku_price'])) {
                $good_max_credit = $goods[0]['shop_goods_sku_max']['shop_goods_sku_price'] / 100;
            }
            if (isset($goods[0]['shop_goods_sku_min']['shop_goods_sku_additional_money'])) {
                $good_min_additional_money = $goods[0]['shop_goods_sku_min']['shop_goods_sku_additional_money'] / 100;
            }
            if (isset($goods[0]['shop_goods_sku_max']['shop_goods_sku_additional_money'])) {
                $good_max_additional_money = $goods[0]['shop_goods_sku_max']['shop_goods_sku_additional_money'] / 100;
            }

            // 拼凑标题字符
            if (isset($good_min_credit)) {
                $title .= '【';
                $title .= $good_min_credit;
                if (isset($good_max_credit) && $good_min_credit <> $good_max_credit) {
                    $title .= ' ~ ';
                    $title .= $good_max_credit;
                }
                $title .= '积分】';
            }
            if (isset($good_min_additional_money)) {
                $title .= '【';
                $title .= $good_min_additional_money;
                if (isset($good_max_additional_money) && $good_min_additional_money <> $good_max_additional_money) {
                    $title .= ' ~ ';
                    $title .= $good_max_additional_money;
                }
                $title .= '元】';
            }
        }
        

        // 七牛云地址
        $qiniu_nrl = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_domain"), true);

        // 分享图片配置文件
        $config_share = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("user_goods_share"), true);
        if(empty($config_share)){
            throw new error('获取配置失败');
        }

        //获取字体资源
        $resource_font = object(parent::PLUGIN_RESOURCE)->get_ttf_path('msyhbd.ttf');
        if (!empty($resource_font['errno']) ||  empty($resource_font['data'])) {
            throw new error('字体资源文件缺失');
        }
        
        //二维码路径
        //获取一个临时文件路径，当程序执行完之后，该缓存文件会自动删除
        $qrcode_tempfile = object(parent::CACHE)->tempfile('user_goods_share_poster', 'png');
        if (empty($qrcode_tempfile)) {
            throw new error('临时文件创建失败');
        }

        //配置信息
        $config = array(
            "poster_url" => "", //海报图片的URL
            "width" => 300,
            "height" => 500,
            "little_title_size" => 5, //小标题
            "little_title_color" => ["51", "0", "0"],
            "title_size" => 14, //大标题，包含商品名称和价格
            "title_color" => ["40", "40", "40"],
            "text_size" => 7, //推荐文字，包含用户昵称
            "text_color" => ["100", "100", "100"],
            "qrcode_size" => 7, //验证码大小
        );

        if ($input['type'] == 'applet') {
            if (!isset($config_share['applet'])) {
                throw new error('配置异常');
            }
            $config_temp = $config_share['applet'];
			
			//获取微信小程序二维码
            $config_qrcode = array();
            if( isset($input['weixin_applet_config']['scene']) ){
                $config_qrcode['scene'] = $input['weixin_applet_config']['scene'];
            } else {
                $config_qrcode['scene'] = $user_phone;
            }
            if (isset($input['weixin_applet_config']['page']))
                $config_qrcode['page'] = $input['weixin_applet_config']['page'];
            if (isset($input['weixin_applet_config']['width']))
                $config_qrcode['width'] = $input['weixin_applet_config']['width'];
            if (isset($input['weixin_applet_config']['auto_color']))
                $config_qrcode['width'] = $input['weixin_applet_config']['auto_color'];
            if (isset($input['weixin_applet_config']['line_color']))
                $config_qrcode['line_color'] = $input['weixin_applet_config']['line_color'];
            if (isset($input['weixin_applet_config']['is_hyaline']))
                $config_qrcode['is_hyaline'] = $input['weixin_applet_config']['is_hyaline'];
			$image_id = object(parent::REQUEST_USER)->weixin_applet_qrcode( $config_qrcode );
			
			//获取二维码路径
			$request_data = object(parent::PLUGIN_HTTP_CURL)->request_get(array(
				'url' => $qiniu_nrl.$image_id,
			));
			if( !empty($request_data['errno']) ){
				throw new error($request_data['error']);
			}
            
            // 二维码放到临时文件中
			file_put_contents($qrcode_tempfile, $request_data['data']);
        } else
        if ($input['type'] == 'web') {

            $config_temp = $config_share['web'];
            if(isset($config_temp['qrcode_size'])){
                $config['qrcode_size'] = $config_temp['qrcode_size'];
            }
            $phpqrcode_config = array(
                'data' => $config_temp['website_url'] . '/?type=user_goods_share&user_id=' . $_SESSION['user_id'] . '&user_phone=' . $user_phone . '&goods_id=' . $input['id'],
                'size' => $config['qrcode_size'],
                'path' => $qrcode_tempfile
            );

            //输出二维码到临时文件
            object(parent::PLUGIN_PHPQRCODE)->output($phpqrcode_config);
        } else
        if ($input['type'] == 'app') {
            if (!isset($config_share['app'])) {
                throw new error('配置异常');
            }

            $config_temp = $config_share['app'];
            if(isset($config_temp['qrcode_size'])){
                $config['qrcode_size'] = $config_temp['qrcode_size'];
            }
            $res_data = array(
                'website_url' => $config_temp['website_url'],
                'type' => 'user_goods_share',
                'user_id' => $_SESSION['user_id'],
                'user_phone' => $user_phone,
                'goods_id' => $input['id']
            );

            //二维码配置参数
            $phpqrcode_config = array(
                'data' =>  cmd(array($res_data), 'json encode'),
                'size' => $config['qrcode_size'],
                'path' => $qrcode_tempfile
            );

            //输出二维码到临时文件
            object(parent::PLUGIN_PHPQRCODE)->output($phpqrcode_config);
        }


        //合并配置
        if( isset($config_temp['poster_url']) )
        $config['poster_url'] = $config_temp['poster_url'];

        if( isset($config_temp['width']) )
        $config['width'] = $config_temp['width'];
        if( isset($config_temp['height']) )
        $config['height'] = $config_temp['height'];

        if( isset($config_temp['little_title']['size']) )
        $config['little_title_size'] = $config_temp['little_title']['size'];
        if( isset($config_temp['little_title']['color'][0]) )
        $config['little_title']['color'][0] = $config_temp['little_title']['color'][0];
        if( isset($config_temp['little_title']['color'][1]) )
        $config['little_title']['color'][1] = $config_temp['little_title']['color'][1];
        if( isset($config_temp['little_title']['color'][2]) )
        $config['little_title']['color'][2] = $config_temp['little_title']['color'][2];

        if( isset($config_temp['title']['size']) )
        $config['title_size'] = $config_temp['title']['size'];
        if( isset($config_temp['title']['color'][0]) )
        $config['title']['color'][0] = $config_temp['title']['color'][0];
        if( isset($config_temp['title']['color'][1]) )
        $config['title']['color'][1] = $config_temp['title']['color'][1];
        if( isset($config_temp['title']['color'][2]) )
        $config['title']['color'][2] = $config_temp['title']['color'][2];

        if( isset($config_temp['text']['size']) )
        $config['text_size'] = $config_temp['text']['size'];
        if( isset($config_temp['text']['color'][0]) )
        $config['text']['color'][0] = $config_temp['text']['color'][0];
        if( isset($config_temp['text']['color'][1]) )
        $config['text']['color'][1] = $config_temp['text']['color'][1];
        if( isset($config_temp['text']['color'][2]) )
        $config['text']['color'][2] = $config_temp['text']['color'][2];

        // 获取资源的三种方式
        $arr = array(
            1 => function ($poster_url) {
                return imagecreatefromgif($poster_url);
            },
            2 => function ($poster_url) {
                return imagecreatefromjpeg($poster_url);
            },
            3 => function ($poster_url) {
                return imagecreatefrompng($poster_url);
            }
        );

        //获取海报/画布图片
        if (empty(trim($config['poster_url']))) {
            // 创建画布（背景图）
            $dest = imagecreatetruecolor($config['width'], $config['height']);
            // 给画布分配颜色
            $color = imagecolorallocate($dest, 255, 255, 255);
            // 绘制带填充的矩形
            imagefilledrectangle($dest, 0, 0, $config['width'], $config['height'], $color);
        } else {
            $dest_getimagesize = getimagesize($config['poster_url']);
            if (!empty($dest_getimagesize[2]) && in_array($dest_getimagesize[2], array(1, 2, 3))) {
                // 海报图（背景图）
                $dest = $arr[$dest_getimagesize[2]]($config['poster_url']);
            } else {
                throw new error('海报背景图片类型不支持');
            }
        }

        // 获取二维码图片
        $src_getimagesize = getimagesize($qrcode_tempfile);
        if (!empty($src_getimagesize[2]) && in_array($src_getimagesize[2], array(1, 2, 3))) {
            // 临时文件中的二维码原图
            $src = $arr[$src_getimagesize[2]]($qrcode_tempfile);
        } else {
            throw new error('二维码图片类型不支持');
        }

        if (empty($src)) {
            throw new error('二维码资源为空');
        }


        // 生成二维码缩略图

        $src_w = imagesx($src); //原始二维码宽
        $src_h = imagesy($src); //原始二维码高 

        $dst_w = $config['width'] / 12 * 4; //二维码缩略图画布宽
        $dst_h = $dst_w; //二维码缩略图画布高
        // $dst_h = $config['height'] / 20 * 6; //二维码缩略图画布高


        //创建缩略画布（宽高都是背景图宽度的1/2）
        $img_dst = imagecreatetruecolor($dst_w, $dst_h); //画布
        //复制原图像素并插入到缩略图中
        imagecopyresampled($img_dst, $src, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);


        $dest_w = imagesx($dest); //背景图宽
        $dest_h = imagesy($dest); //背景图高 

        $g_w = (string) $dest_w / 12 * 8; //获取商品图片宽
        $g_h = (string) $dest_h / 20 * 8; //获取商品图片高


        //获取商品图片类型
        if (empty($good_img_id)) {
            throw new error('商品图片不存在');
        }
        $src1_getimagesize = getimagesize($qiniu_nrl . $good_img_id);
        if (!empty($src1_getimagesize[2]) && in_array($src1_getimagesize[2], array(1, 2, 3))) {
            // 七牛云按照设定宽高比值取图片
            $src1 = $arr[$src1_getimagesize[2]]($qiniu_nrl . $good_img_id . '?imageView2/2/h/' . $g_h);
            // $src1 = $arr[$src1_getimagesize[2]]($qiniu_nrl . $good_img_id . '?imageView2/1/w/' . $g_w . '/h/' . $g_h);
        } else {
            throw new error('商品图片类型不支持');
        }


        $src1_w = imagesx($src1); //实际商品图片宽
        $src1_h = imagesy($src1); //实际商品图片高 


        // 合并商品图片
        $x =$dest_w / 12 * 2; //海报X坐标
        // 如果商品原图比按比例取回的图小，坐标加差值
        if ($g_w > $src1_w) {
            $x += ($g_w - $src1_w) / 2;
        }
        $y = $dest_h / 20 * 6; //海报Y坐标
        if ($g_h > $src1_h) {
            $y += ($g_h - $src1_h) / 2;
        }
        imagecopymerge($dest, $src1, $x, $y, 0, 0, $src1_w, $src1_h, 100);
        // imagecopymerge( $dest, $img_dst, 海报x坐标, 海报y坐标, 二维码X坐标, 二维码Y坐标, 二维码宽度, 二维码高度, 合并程度值范围从0到100);

        // 合并二维码缩略图到背景图
        imagecopymerge($dest, $img_dst, $dest_w / 12 * 2.5, $dest_h / 20 * 15, 0, 0, $dst_w, $dst_h, 100);

        // 写入小标题
        imagettftext(
            $dest,
            $config['little_title_size'],
            0,
            ceil($dest_w / 12 * 1),
            $dest_h / 20 * 1,
            imagecolorallocate($dest, $config['little_title_color'][0], $config['little_title_color'][1], $config['little_title_color'][2]),
            $resource_font['data'],
            $little_title
        );

        // 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度
        $title_box = "";
        // 将字符串拆分成一个个单字 保存到数组 letter 中
        for ($i = 0; $i < mb_strlen($title); $i++) {
            $letter[] = mb_substr($title, $i, 1);
        }
        foreach ($letter as $l) {
            $teststr = $title_box . " " . $l;
            // $fontBox = imagettfbbox(14, 0, $resource_font['data'], $teststr);
            $fontBox = imagettfbbox( $config['title_size'], 0, $resource_font['data'], $teststr);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($fontBox[2] > $dest_w / 12 * 8) && ($title_box !== "")) {
                $title_box .= "\n";
            }
            $title_box .= $l;
        }

        // 写入标题文字（含商品价格）
        imagettftext(
            $dest,
            $config['title_size'],
            0,
            ceil(($dest_w - $fontBox[2]) / 2),  // ceil($dest_w / 12 * 1),
            $dest_h / 20 * 2,
            imagecolorallocate($dest, $config['title_color'][0], $config['title_color'][1], $config['title_color'][2]),
            $resource_font['data'],
            $title_box
        );
        

        // 推荐人文字换行处理
        $content = "";
        for ($i = 0; $i < mb_strlen($text); $i++) {
            $letter1[] = mb_substr($text, $i, 1);
        }
        foreach ($letter1 as $l) {
            $teststr = $content . " " . $l;
            // $fontBox1 = imagettfbbox(14, 0, $resource_font['data'], $teststr);
            $fontBox1 = imagettfbbox($config['text_size'], 0, $resource_font['data'], $teststr);
            if (($fontBox1[2] > $dest_w / 12 * 3) && ($content !== "")) {
                $content .= "\n";
            }
            $content .= $l;
        }

        // 写入推荐人文字
        imagettftext(
            $dest,
            $config['text_size'],
            0,
            $dest_w / 12 * 7,
            $dest_h / 20 * 16,
            imagecolorallocate($dest, $config['text_color'][0], $config['text_color'][1], $config['text_color'][2]),
            $resource_font['data'],
            $content
        );




        header('Content-Type: image/png');
        imagepng($dest);
        if ($src1) {
            imagedestroy($src1);
        }
        if ($dest) {
            imagedestroy($dest);
        }
        if ($src) {
            imagedestroy($src);
        }
        if ($img_dst) {
            imagedestroy($img_dst);
        }
        exit();




    }






    //===========================================
    // 私有方法
    //===========================================


    /**
     * 筛选
     * @param  array $config [查询配置]
     * @param  array $filter [筛选内容]
     * @return array
     */
    private function _filter($config, $filter)
    {
        //搜索
        if (!empty($filter['keywords']) && is_string($filter['keywords'])) {
            $keywords = cmd(array($filter['keywords']), 'str addslashes');
            $keywords = "%{$keywords}%";
            $config['where'][] = array('[and] sg.shop_goods_name like [+]', $keywords);
        }

        //类别
        if (!empty($filter['type_id']) && is_string($filter['type_id'])) {
            //查询类别信息
            $data = object(parent::TABLE_TYPE)->find($filter['type_id']);
            if ($data) {
                //是否一级类别
                if (empty($data['type_parent_id'])) {
                    $sql_join_goods_id = object(parent::TABLE_SHOP_GOODS_TYPE)->sql_join_type_parent_goods_id($data['type_id']);
                } else {
                    $sql_join_goods_id = object(parent::TABLE_SHOP_GOODS_TYPE)->sql_join_type_son_goods_id($data['type_id']);
                }

                $config['where'][] = array('[and] sg.shop_goods_id in ([-])', $sql_join_goods_id, true);
            }
        }



        //价格
        if (isset($filter['price_min']) || isset($filter['price_max'])) {
            $min = isset($filter['price_min']) ? $filter['price_min'] : 0;
            $max = isset($filter['price_max']) ? $filter['price_max'] : null;
            if (!is_numeric($min) || $min < 0)
                throw new error('最小价格不合法');

            if ($max) {
                if (!is_numeric($max) || $max < $min)
                    throw new error('最大价格不合法');
            }

            //获取子查询SQL语句
            $subquery_sql = object(parent::TABLE_SHOP_GOODS_SKU)->sql_stock_price_goods_id($min, $max);
            $config['where'][] = array('[and] sg.shop_goods_id in (' . $subquery_sql . ')');
        }

        //限时购
        if (!empty($filter['when'])) {
            object(parent::TABLE_SHOP_GOODS_WHEN)->update_state_clear();
            /*$sql_goods_id = object(parent::TABLE_SHOP_GOODS_WHEN)->sql_goods_id();
            $config['where'][] = array('[and] sg.shop_goods_id IN ([-])', $sql_goods_id, true);*/
            //$sql_join_shop_goods_when = object(parent::TABLE_SHOP_GOODS_WHEN)->sql_join_goods_id("sg");
            $config['where'][] = array('[and] sgw.shop_goods_id <>""');
            $config['where'][] = array('[and] sgw.shop_goods_when_state = 1');
        } else {
            /*$sql_goods_id = object(parent::TABLE_SHOP_GOODS_WHEN)->sql_goods_id();
            $config['where'][] = array('[and] sg.shop_goods_id NOT IN ([-])', $sql_goods_id, true);*/
            //$sql_join_shop_goods_when = object(parent::TABLE_SHOP_GOODS_WHEN)->sql_join_goods_id("sg");
            $config['where'][] = array('[and] sgw.shop_goods_id IS NULL');
        }

        return $config;
    }

}