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

class group_goods extends \eapie\source\request\shop
{

    /* 拼团功能组件 */


      /**
       * 前台输出拼团商品列表
       * 
       * api: SHOPGROUPGOODSLISTGOODS
       * {"class":"shop/group_goods","method":"api_list_goods"}
       * 
       * @param array $data 
       * @return array $result 
       *   group_id [str] [拼团ID]
       *   goods_id [str] [商品ID]
       *   goods_name [str] [商品名称]
       *   num [int] [成功拼团所需人数]
       *   now_num [int] [现在拼团人数]
       *   original_money [bigint] [原价（分）]
       *   group_price [bigint] [拼团价格]
       *   start_time [bigint] [开始时间]
       *   end_time [bigint] [结束时间]
       *   image_id [str] [图片ID]
       */
      public function api_list_goods($data = array()){

         //查询配置
         $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_USER),
         );


         //查询字段
        $sql_join_stock_max_price = object(parent::TABLE_SHOP_GOODS_SKU)->sql_join_stock_max_price("sgg");
       	$sql_join_stock_min_price = object(parent::TABLE_SHOP_GOODS_SKU)->sql_join_stock_min_price("sgg");
        $config['select'] = array(
            'sgg.shop_group_goods_id AS group_id',
            'sgg.shop_goods_id AS goods_id',
            'sg.shop_goods_name AS goods_name',
            'sgg.shop_group_goods_num AS num',
            'sgg.shop_group_goods_now_num AS now_num',
            'sku.shop_goods_sku_price as original_price',
            '(' . $sql_join_stock_max_price . ') as original_price_max',
            '(' . $sql_join_stock_min_price . ') as original_price_min',
            'sgg.shop_group_goods_price AS group_price',
            'sgg.shop_group_goods_start_time AS start_time',
            'sgg.shop_group_goods_end_time AS end_time',
            'sg.shop_goods_property AS property',
            'sgi.image_id as image_id',
        );
		
         //排序
         $config["orderby"] = object(parent::REQUEST)->orderby($data, array(
            'group_id_desc' => array('shop_group_goods_id', true),
            'group_id_asc' => array('shop_group_goods_id', false),
            'price_desc' => array('shop_group_goods_price', true),
            'price_asc' => array('shop_group_goods_price', false),
            'num_desc' => array('shop_group_goods_num', true),
            'num_asc' => array('shop_group_goods_num', false),
            'start_time_desc' => array('shop_group_goods_start_time', true),
            'start_time_asc' => array('shop_group_goods_start_time', false),
            'end_time_desc' => array('shop_group_goods_end_time', true),
            'end_time_asc' => array('shop_group_goods_end_time', false),
            'now_num_desc' => array('shop_group_goods_now_num', true),
            'now_num_asc' => array('shop_group_goods_now_num', false),
         ));
         
         //避免排序重复
         $config["orderby"][] = array('shop_group_goods_id', false);
         

         //条件
         $config['where'][] = array('sgg.shop_group_goods_is_end=0');

         //查询数据
         $data = object(parent::TABLE_SHOP_GROUP_GOODS)->select_page($config);
         return $data;
      }

      /**
       * 拼团支付
       *
       * api: SHOPGROUPGOODSSELFPAY
       * {"class":"shop/group_goods","method":"api_self_pay"}
       * req: {
       *  id              [int] [必填] [拼团订单ID]
       *  pay_method      [str] [必填] [支付方式]
       *  pay_password    [int] [可选] [支付密码。支付方式为用户钱包时，必填]
       * }
       * 
       * @return string 订单ID
       */
      public function api_self_pay($input = array())
      {
         // 检测登录
         object(parent::REQUEST_USER)->check();
         $user_id = $_SESSION['user_id'];

         // 检测输入
         object(parent::ERROR)->check($input, 'id', parent::TABLE_SHOP_ORDER, array('args'), 'shop_order_id');
         object(parent::ERROR)->check($input, 'pay_method', parent::TABLE_SHOP_ORDER, array('args'));

         // 查询商城订单信息
         $shop_group_order = object(parent::TABLE_SHOP_GROUP_ORDER)->find($input['id']);
         if (empty($shop_group_order) || $shop_group_order['user_id'] != $user_id)
               throw new error('订单数据不存在');

         //0 => 时间未到，人数未满,不结束，不成功
         //1 => 时间到，人数未满，拼团结束，未拼团成功
         //2 => 时间到，人数已满，拼团结束，拼团成功
         //3 => 时间未到，拼团人数已满，拼团结束，拼团成功
         if($shop_group_order['shop_group_order_state'] == 1 || $shop_group_order['shop_group_order_state'] == 2 || $shop_group_order['shop_group_order_state'] == 3)
               throw new error('拼团时间到');

         if ($shop_group_order['shop_group_order_pay_state'] == 1)
               throw new error('订单已支付');

         // 查询配置信息
         $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('shop_order'), true);

         // ——判断订单超时时间——
         if (!empty($config['order_timeout'])) {
               if (time() - $shop_group_order['shop_group_order_insert_time'] > $config['order_timeout']) {
                  $update_where = array(array('shop_group_order_id=[+]', $input['id']));
                  $update_data = array(
                     'shop_group_order_status' => 1, // 订单已关闭
                     'shop_group_order_update_time' => time(),
                  );
                  object(parent::TABLE_SHOP_GROUP_ORDER)->update($update_where, $update_data);
                  throw new error('订单已失效，请重新下单');
               }
         }

         // 获取商品信息
         $cache = object(parent::TABLE_SHOP_GROUP_GOODS)->find_join($shop_group_order['shop_group_goods_id']);
         $shop_group_goods_name = $cache['shop_goods_name'];
         unset($cache);

         //资金订单信息
         $order = array(
            'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
            'order_type' => parent::TRANSACTION_TYPE_SHOP_GROUP_ORDER,
            'order_comment' => '商城拼团',
            'order_action_user_id' => $user_id,
            'order_plus_method' => '',
            'order_plus_account_id' => '',
            'order_plus_value' => $shop_group_order['shop_group_order_price'],
            'order_minus_method' => $input['pay_method'],
            'order_minus_account_id' => $user_id,
            'order_minus_value' => $shop_group_order['shop_group_order_price'],
            'order_state' => 1,
            'order_pay_state' => 1,
            'order_sign' => $shop_group_order['shop_order_id'],
            'order_json' => array(),
            'order_insert_time' => time(),
         );
         
         // 拼团现在人数+1
         $output['group_now_num'] = object(parent::TABLE_SHOP_GROUP_GOODS)->group_now_num_add($shop_group_order['shop_group_goods_id']);


         // 判断支付方式
         switch ($input['pay_method']) {
            case parent::PAY_METHOD_WEIXINPAY://微信
            case parent::PAY_METHOD_ALIPAY://支付宝
               // 获取支付参数
               $subject = $shop_group_goods_name;
               $output = object(parent::REQUEST_APPLICATION)->get_pay_config($input, $order['order_json'], array(
                  'money_fen' => $shop_group_order['shop_group_order_price'],
                  'subject' => $subject,
                  'body' => $subject,
                  'order_id' => $order['order_id']
               ));

               // 插入订单数据
               if (object(parent::TABLE_ORDER)->insert($order)) {
                  $output['order_id'] = $order['order_id'];
                  return $output;
               } else {
                  throw new error ('操作失败');
               }

               break;
            case parent::PAY_METHOD_USER_MONEY://用户钱包
               // 检测输入
               object(parent::ERROR)->check($input, 'pay_password', parent::TABLE_USER, array('args'));

               // 检测支付密码
               $res = object(parent::TABLE_USER)->check_pay_password($input['pay_password']);
               if ($res !== true)
                  throw new error($res);

               // 检测余额
               $user_money = object(parent::TABLE_USER_MONEY)->find_now_data($user_id);
               if (empty($user_money['user_money_value']) || $user_money['user_money_value'] < $shop_group_order['shop_group_order_price'])
                  throw new error('余额不足');

               // 插入订单数据
               return object(parent::TABLE_SHOP_GROUP_ORDER)->pay_by_user_money($shop_group_order['order_id'], $user_money);

               break;
         }
      }


    /**
     * 单个拼团商品浏览（详情页），不需要登录
     * 
     * api: SHOPGROUPGOODSGETGOODS
     * {"class":"shop/group_goods","method":"api_get_goods"}
     * 
     * @param $data array
     * data['group_id'] [str] [拼团ID]
     * @return $res array
     * 
     *   [false 拼团已经结束]
     * 
     *   [$data array [拼团商品详情数据包]
     *      $data['goods_type'] [商品分类]
     *      $data['goods_type'] [商品图片]
     *      $data['goods_spu'] [商品属性]
     *      $data['goods_sku'] [商品规格]
     *      $data['money_min'] [商品低价]
     *      $data['money_max'] [商品高价]
     *      $data['goods_group'] [商品拼团相关]
     *         $data['goods_group']['id'] [拼团ID]
     *         $data['goods_group']['group_price'] [拼团价格]
     *         $data['goods_group']['start_time'] [拼团开始时间]
     *         $data['goods_group']['end_time'] [拼团结束时间]
     *         $data['goods_group']['now_num'] [拼团参与人数]
     *         $data['goods_group']['num'] [成团人数]]
     */
     public function api_get_goods($data = array()){
         //校验数据
         object(parent::ERROR)->check($data, 'group_id', parent::TABLE_SHOP_GROUP_GOODS, array('args', 'exists_id'));

         $is_end = object(parent::TABLE_SHOP_GROUP_GOODS)->group_is_end($data['group_id']);

         if ($is_end === 0) {

            //拼团未结束，查询商品数据
            $group = object(parent::TABLE_SHOP_GROUP_GOODS)->find($data['group_id']);

            $goods = object(parent::TABLE_SHOP_GOODS)->find($group['shop_goods_id']);
            if (empty($goods))
                  throw new error('商品不存在');
            if ($goods['shop_goods_state'] != 1)
                  throw new error('商品未发布');
            if ($goods['shop_goods_trash'] == 1)
                  throw new error('商品已回收');

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

            //格式化数据
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
            unset($data['sku_min'], $data['sku_max']);

            //拼团数据
            $data['goods_group'] = array(
               'id' => $group['shop_group_goods_id'],
               'group_price' => $group['shop_group_goods_price'],
               'start_time' => $group['shop_group_goods_start_time'],
               'end_time' => $group['shop_group_goods_end_time'],
               'now_num' => $group['shop_group_goods_now_num'],
               'num' => $group['shop_group_goods_num'],
               'is_end' => $group['shop_group_goods_is_end'],
               'is_success' => $group['shop_group_goods_is_success'],
               //'property' => $group['shop_group_goods_property'],
            );
            unset($group);
            return $data;
         } elseif($is_end == 1) {
            //拼团已经结束
            $data = array('code' => 1, 'msg' => '时间到，人数未满，拼团结束，未拼团成功');
            return $data;
         } elseif($is_end == 2) {
            //拼团已经结束
            $data = array('code' => 2, 'msg' => '时间到，人数已满，拼团结束，拼团成功');
            return $data;
         } elseif($is_end == 3) {
            //拼团已经结束
            $data = array('code' => 3, 'msg' => '时间未到，拼团人数已满，拼团结束，拼团成功');
            return $data;
         } else {
            //拼团已经结束
            $data = array('msg' => '拼团已结束');
            return $data;
         }
      }

      /**
       * 加入拼团下单购买，需要登录
       *
       * api: SHOPGROUPGOODSSELFADDGROUP
       * {"class":"shop/group_goods","method":"api_self_add_group"}
       * 
       * @param array $input
       *     [string]    $input['group_id']        拼团ID
       *     [string]    $input['address_id']      地址ID
       *     [string]    $input['pay_method']      支付方式
       * @return array $result
       */
      public function api_self_add_group($input = array()){
         //检测登录
         object(parent::REQUEST_USER)->check();
         $user_id = $_SESSION['user_id'];

         //检测请求参数
         object(parent::ERROR)->check($input, 'group_id', parent::TABLE_SHOP_GROUP_GOODS, array('args', 'exists_id'));
         object(parent::ERROR)->check($input, 'address_id', parent::TABLE_SHOP_GROUP_ORDER, array('args'), 'user_address_id');
         object(parent::ERROR)->check($input, 'pay_method', parent::TABLE_SHOP_GROUP_ORDER, array('args'));


         //查询拼团是否结束、是否成功
         $is_end = object(parent::TABLE_SHOP_GROUP_GOODS)->group_is_end($input['group_id']);

         if($is_end === 1 || $is_end === 2 || $is_end === 3)
            throw new error('拼团结束');
         
         //收货地址信息
         $user_address = $this->_get_user_address($input['address_id']);

         //查询拼团信息
         $group = object(parent::TABLE_SHOP_GROUP_GOODS)->find($input['group_id']);
         
         //查询sku_id
         $goods_sku_id = $group['shop_goods_sku_id'];
         
         //计算金额（拼团订单金额 + 运费）

         // TODO 运费计算
         $shipping_money = 0;
         $money = (int)$group['shop_group_goods_price'] + $shipping_money;


         $cache = object(parent::TABLE_SHOP_GOODS)->find($group['shop_goods_id']);
         
         $group['shop_group_goods_property'] = (int)$cache['shop_goods_property'];
         
         unset($cache);
         //初始化拼团订单信息
         $group_order = array(
            'shop_group_order_id' => object(parent::TABLE_SHOP_GROUP_ORDER)->get_unique_id(),   //拼团订单ID
            'user_id' => $user_id,                                                              //用户ID
            'shop_order_id' => object(parent::TABLE_SHOP_GROUP_ORDER)->get_unique_id(),         //商城订单ID
            'shop_group_goods_id' => $group['shop_group_goods_id'],                             //拼团ID
            'shop_group_order_property' => $group['shop_group_goods_property'],                 //积分商品或普通商品
            'shop_group_order_price' => $money,                                                 //入团价格
            'shop_group_order_state' => $is_end,                                                //拼团状态
            'shop_order_pay_method' => $input['pay_method'],                                    //支付方式
            'shop_group_order_pay_state' => 0,                                                  //支付状态
            'shop_group_order_insert_time' => time(),
            'shop_group_order_update_time' => time(),
            'shop_group_order_json' => array(),
         );


         $group_order = array_merge($group_order, $user_address);
         $group_order['shop_group_order_json'] = cmd(array($group_order['shop_group_order_json']), 'json encode');


         //查询商品信息
         $data_goods = object(parent::TABLE_SHOP_GOODS_SKU)->find_join_goods_spu($goods_sku_id);
		
		
         //是否超出库存
         if ($data_goods['sku_stock'] <= 0)
            throw new error('商品：'.$data_goods['goods_name'].'库存不足');

         //回收
         unset($group);
         unset($goods_sku_id);


         //资金订单信息
         $order = array(
            'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
            'order_type' => parent::TRANSACTION_TYPE_SHOP_GROUP_ORDER,
            'order_comment' => '商城拼团',
            'order_action_user_id' => $user_id,
            'order_plus_method' => '',
            'order_plus_account_id' => '',
            'order_plus_value' => $money,
            'order_minus_method' => $input['pay_method'],
            'order_minus_account_id' => $user_id,
            'order_minus_value' => $money,
            'order_state' => 1,
            'order_pay_state' => 0,
            'order_sign' => $group_order['shop_order_id'],
            'order_json' => array(),
            'order_insert_time' => time(),
         );

         $group_order['order_id'] = $order['order_id'];

         //输出数据
         $output = array();
         //判断支付方式
         switch ($input['pay_method']) {
            case parent::PAY_METHOD_WEIXINPAY://微信
            case parent::PAY_METHOD_ALIPAY://支付宝
               $subject = $data_goods['goods_name'];
               $output = object(parent::REQUEST_APPLICATION)->get_pay_config($input, $order['order_json'], array(
                  'money_fen' => $money,
                  'subject' => $subject,
                  'body' => $subject,
                  'order_id' => $order['order_id']
               ));
               break;
            case parent::PAY_METHOD_USER_MONEY://用户钱包
               $output['order_id'] = $order['order_id'];
               $output['shop_order_id'] = $group_order['shop_order_id'];
               $output['order_money'] = $money;
               break;
         }

         
         $order['order_json'] = cmd(array($order['order_json']), 'json encode');


         // 创建订单
         if (object(parent::TABLE_SHOP_GROUP_ORDER)->create_order($order, $group_order)) {
            // 拼团订单ID
            $output['group_order_id'] = $group_order['shop_group_order_id'];
            return $output;
         } else {
               throw new error('操作失败');
         }
      }



      /**
       * 拼团订单列表，需要登录
       * 
       * api: SHOPGROUPGOODSSELFLISTORDER
       * {"class":"shop/group_goods","method":"api_self_list_order"}
       * 
       * @param array $request
       *    $request['limit'] 分页 
       * 
       * @return $result array
         */
      public function api_self_list_order($request = array()){
         //检测登录
         object(parent::REQUEST_USER)->check();
         $user_id = $_SESSION['user_id'];

         //查询配置
         $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($request, parent::REQUEST_USER),
         );

         //查询字段
         $config['select'] = array(
            'gorder.shop_order_id AS order_id',
            'gorder.shop_group_goods_id AS group_id',
            'gorder.shop_group_order_price AS order_price',
            'gorder.shop_group_order_property AS property',
            'gorder.shop_order_pay_price AS pay_price',
            'gorder.shop_order_pay_method AS pay_method',
            'gorder.shop_group_order_pay_state AS pay_state',
            'gorder.shop_group_order_pay_time AS pay_time',
            'gg.shop_goods_id AS goods_id',
            'gg.shop_group_goods_start_time AS start_time',
            'gg.shop_group_goods_end_time as end_time',
            'gg.shop_group_goods_num AS num',
            'gg.shop_group_goods_now_num AS now_num',
            'gorder.shop_group_order_id as group_order_id',
         );

         //避免排序重复
         $config["orderby"][] = array('shop_order_id', false);

         //条件
         $config['where'][] = array('gorder.user_id=[+]', $user_id);

         //查询数据
         $data = object(parent::TABLE_SHOP_GROUP_ORDER)->select_page($config,$user_id);
         $config = array();

         if (empty($data['order'])) {
            return $data;
         } else {
            // for循环查询数组，加入商品图片信息
            for ($i = 0; $i < count($data['order']); $i++) { 
               $config['where'] = array(
                  array('shop_goods_id=[+]',$data['order'][$i]['goods_id']),
                  array('[and] shop_goods_image_main=[+]',1)
               );
               $config['select'] = array(
                  'image_id'
               );
               $img_data = object(parent::TABLE_SHOP_GOODS_IMAGE)->select($config);
               if(isset($img_data[0]['image_id']) && $img_data[0]['image_id'] !== null && $img_data[0]['image_id'] !== ''){
                  $data['order'][$i]['main_img'] = $img_data[0]['image_id'];
               }
            }
            return $data;
         }
      }

      /**
       * 单个拼团订单，需要登录
       * 
       * api: SHOPGROUPGOODSSELFGETORDER
       * {"class":"shop/group_goods","method":"api_self_get_order"}
       * 
       * @param string $data['group_order_id'] 拼团订单ID
       * @return $result array
       */
      public function api_self_get_order($data = array()){
         //检测登录
         object(parent::REQUEST_USER)->check();
         $user_id = $_SESSION['user_id'];

         //检测数据
         object(parent::ERROR)->check($data, 'group_order_id', parent::TABLE_SHOP_GROUP_ORDER, array('args','exist'));

         //查询拼团ID
         $group_id = object(parent::TABLE_SHOP_GROUP_ORDER)->find_group($data['group_order_id']);
        
         $group_id = $group_id['shop_group_goods_id'];
         //查询拼团是否结束、是否成功
         $is_end = object(parent::TABLE_SHOP_GROUP_GOODS)->group_is_end($group_id);

         //查询配置
         $config = array(
            'select' => array(),
            'where' => array(),
         );

         //查询字段
         $config['find'] = array(
            'gorder.shop_order_id AS order_id',
            'gorder.shop_group_goods_id AS group_id',
            'gorder.shop_group_order_price AS order_price',
            'gorder.shop_group_order_property AS property',
            'gorder.shop_group_order_state AS order_state',
            'gorder.shop_group_order_price AS pay_price',
            'gorder.shop_order_pay_method AS pay_method',
            'gorder.shop_group_order_pay_state AS pay_state',
            'gorder.shop_group_order_pay_time AS pay_time',
            'gorder.user_address_consignee AS consignee',
            'gorder.user_address_tel AS tel',
            'gorder.user_address_phone AS phone',
            'gorder.user_address_country AS country',
            'gorder.user_address_province AS province',
            'gorder.user_address_city AS city',
            'gorder.user_address_district AS district',
            'gorder.user_address_details AS details',
            
            'gg.shop_goods_id AS goods_id',
            'gg.shop_group_goods_start_time AS start_time',
            'gg.shop_group_goods_end_time as end_time',

            'gg.shop_group_goods_num AS num',
            'gg.shop_group_goods_now_num AS now_num',
         );

         $config['where'] = array(
            array("gorder.user_id=[+]",$user_id),
            array("[and] gorder.shop_group_order_id=[+]",$data['group_order_id']),
         );

         $result = object(parent::TABLE_SHOP_GROUP_ORDER)->find_join_group_goods($config);

         # 回收config
         unset($config);
         if(empty($result['goods_id'])){
            return $result;
         } else {
            $config['where'] = array(
               array('shop_goods_id=[+]',$result['goods_id']),
               array('[and] shop_goods_image_main=[+]',1)
            );
            $config['select'] = array('image_id');
            $img_data = object(parent::TABLE_SHOP_GOODS_IMAGE)->select($config);
   
            if(isset($img_data[0]['image_id']) && $img_data[0]['image_id'] !== null && $img_data[0]['image_id'] !== ''){
               $result['main_img'] = $img_data[0]['image_id'];
            }
            return $result;
         }
      }

      //===========================================
      // 私有方法
      //===========================================


      /**
       * 查询收货地址
       * 
       * @param  string $user_address_id 收货地址ID
       * @return array
       */
      private function _get_user_address($user_address_id = '')
      {
         //查询收货地址信息
         $address = object(parent::TABLE_USER_ADDRESS)->find($user_address_id);
         if (empty($address))
               throw new error('收货地址ID有误，数据不存在');

         //检测收货地址
         object(parent::ERROR)->check($address, 'user_address_consignee', parent::TABLE_USER_ADDRESS, array('args'));
         object(parent::ERROR)->check($address, 'user_address_phone', parent::TABLE_USER_ADDRESS, array('args'));
         object(parent::ERROR)->check($address, 'user_address_province', parent::TABLE_USER_ADDRESS, array('args'));
         object(parent::ERROR)->check($address, 'user_address_city', parent::TABLE_USER_ADDRESS, array('args'));
         object(parent::ERROR)->check($address, 'user_address_district', parent::TABLE_USER_ADDRESS, array('args'));
         object(parent::ERROR)->check($address, 'user_address_details', parent::TABLE_USER_ADDRESS, array('args'));

         //白名单
         $whitelist = array(
               'user_address_consignee',
               'user_address_phone',
               'user_address_province',
               'user_address_city',
               'user_address_district',
               'user_address_details',
         );

         return cmd(array($address, $whitelist), 'arr whitelist');
      }
}