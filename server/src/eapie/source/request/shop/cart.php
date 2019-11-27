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
class cart extends \eapie\source\request\shop {
	
	
	//购物车
	
	
	
	/**
	 * 加入购物车
	 * 
	 * [{"goods_sku_id":"商品规格ID","number":"商品数量","recommend_user_id":"邀请或推荐该商品者的用户ID","buy_now":"是否立即购买"}]
	 * 
	 * SHOPCARTSELFADD
	 * {"class":"shop/cart","method":"api_self_add"}
	 * 
	 * @param	array	$arguments
	 * @return string 购物车ID
	 */
	public function api_self_add( $arguments = array() ){
		//检测登录
        object(parent::REQUEST_USER)->check();
		//检测输入
        object(parent::ERROR)->check($arguments, 'goods_sku_id', parent::TABLE_SHOP_GOODS_SKU, array('args', 'exists_id'), 'shop_goods_sku_id');
        object(parent::ERROR)->check($arguments, 'number', parent::TABLE_SHOP_CART, array('args'), 'shop_cart_number');
		//根据 SKU id 获取 商品数据
		//商品规格数据
        $goods_sku_data = object(parent::TABLE_SHOP_GOODS_SKU)->find($arguments['goods_sku_id']);
        if( empty($goods_sku_data) ){
        	throw new error('商品规格ID有误，数据不存在');
        }
        //商品数据
        $goods_data = object(parent::TABLE_SHOP_GOODS)->find_join($goods_sku_data['shop_goods_id']);
		if( empty($goods_data) ){
        	throw new error('商品规格ID异常，商品不存在');
        }
		
		//状态。0审核失败；1售卖中；2待审核；3停售编辑中; 
		if( $goods_data['shop_goods_state'] != 1 ){
			throw new error('商品已下架');
		}
		
		//限时商品
		if( !empty($goods_data['when_shop_goods_id']) ){
			if( empty($goods_data['shop_goods_when_state']) || $goods_data['shop_goods_when_state'] == 0 ){
				throw new error('商品限时购已结束');
			}
			if( !empty($goods_data['shop_goods_when_state']) && $goods_data['shop_goods_when_state'] == 2 ){
				throw new error('商品限时购还没有开始');
			}
		}
		
		$config = array(
			'orderby' => array(
				array('shop_goods_spu_sort'),
				array('shop_goods_spu_parent_id'),
				array('shop_goods_spu_update_time'),
				array('shop_goods_spu_id'),
			),
            'select' => array(
                'shop_goods_spu_id',
                'shop_goods_spu_parent_id',
                'shop_goods_spu_name',
                'image_id',
            )
        );
        //商品属性(shop_goods_spu)数据
        $temp_additional_goods_sku = object(parent::TABLE_SHOP_GOODS)->get_spu_data(array($goods_sku_data), $config, $config);
		$additional_goods_sku = $temp_additional_goods_sku[0];
	    //获取商品主图
	    $additional_goods_image_main = object(parent::TABLE_SHOP_GOODS_IMAGE)->get_main_img_id($goods_data['shop_goods_id']);
		//推荐人
		if( isset($arguments['recommend_user_id']) && $arguments['recommend_user_id'] != '' ){
			object(parent::ERROR)->check($arguments, 'recommend_user_id', parent::TABLE_SHOP_CART, array('format', 'exists'));
		}else{
			$arguments['recommend_user_id'] = '';
		}
		
		$shop_cart_json = cmd(array(array(
			'recommend_user_id' => $arguments['recommend_user_id'],
			'goods' => $goods_data,
			'goods_sku' => $additional_goods_sku,
			'image_id' => $additional_goods_image_main
		)),'json encode');
		
		
		//查询购物是否已经存在  商品id与规格id  存在则拿出来
		$cart_data = object(parent::TABLE_SHOP_CART)->find_user_sku_goods(
			$_SESSION['user_id'], 
			$goods_sku_data['shop_goods_sku_id'], 
			$goods_sku_data['shop_goods_id']
			);
		
		if( empty($cart_data) ){
			//是否超出库存
	        if( $arguments['number'] > $goods_sku_data['shop_goods_sku_stock'] ){
	        	throw new error('库存不足');
	        }
			
			$cart_insert = array(
				'shop_cart_id' => object(parent::TABLE_SHOP_CART)->get_unique_id(),
				'user_id' => $_SESSION['user_id'],
				'shop_id' => $goods_data['shop_id'],
				'shop_goods_id' => $goods_data['shop_goods_id'],
				'shop_goods_sku_id' => $goods_sku_data['shop_goods_sku_id'],
				'shop_cart_number' => $arguments['number'],
				'shop_cart_json' => $shop_cart_json,
				'shop_cart_insert_time' => time(),
				'shop_cart_update_time' => time()
			);
			
			$bool = object(parent::TABLE_SHOP_CART)->insert($cart_insert);
			$shop_cart_id = $cart_insert['shop_cart_id'];
		}else{
			// 是否立即支付
			if (empty($arguments['buy_now'])) {
				$shop_cart_number = $arguments['number'] + $cart_data['shop_cart_number'];
			} else {
				$shop_cart_number = $arguments['number'];
			}
			
			//是否超出库存
	        if( $shop_cart_number > $goods_sku_data['shop_goods_sku_stock'] ){
	        	throw new error('库存不足');
	        }
			
			$cart_update = array(
				'shop_id' => $goods_data['shop_id'],
				'shop_cart_number' => $shop_cart_number,
				'shop_cart_json' => $shop_cart_json,
				'shop_cart_update_time' => time()
			);
			$cart_where = array();
			$cart_where[] = array('shop_cart_id=[+]', $cart_data['shop_cart_id']);
			$cart_where[] = array('[and] user_id=[+]', $_SESSION['user_id']);
			$cart_where[] = array('[and] shop_goods_id=[+]', $goods_data['shop_goods_id']);
			$cart_where[] = array('[and] shop_goods_sku_id=[+]', $goods_sku_data['shop_goods_sku_id']);
			//必须等于原数据，防止数据被覆盖
			$cart_where[] = array('[and] shop_cart_number=[-]', $cart_data['shop_cart_number']);
			$cart_where[] = array('[and] shop_cart_update_time=[-]', $cart_data['shop_cart_update_time']);
			$bool = object(parent::TABLE_SHOP_CART)->update($cart_where, $cart_update);
			$shop_cart_id = $cart_data['shop_cart_id'];
		}
		
		
		//插入数据
        if( empty($bool) ){
            throw new error('添加失败');
        }else{
        	return $shop_cart_id;
        }
		
	}
	
	
	
	
	/**
     * 获取购物车商品信息
     *
	 * SHOPCARTSELFDATA
	 * {"class":"shop/cart","method":"api_self_data"}
	 * 
	 * [{"goods_sku_id":["选中的商品规格ID，是一个数组","商品规格ID2","商品规格ID3","商品规格ID4..."]}]
	 * 
	 * @param	array	$arguments
	 * @return  array
     */
    public function api_self_data( $arguments = array() ){
    	//检测登录
        object(parent::REQUEST_USER)->check();
		
        //查询配置
        $config = array(
            'orderby' => array(
				array('shop_cart_insert_time', true),
				array('shop_cart_id', true)
			),
            'where'   => array(
            	array('user_id=[+]', $_SESSION['user_id'])
			),
        );

        //查询数据
        $cart_data = object(parent::TABLE_SHOP_CART)->select($config);
		if( empty($cart_data) ){
            $cart_data['status'] = 0;
			return $cart_data;
		}
		//获取商品、规格信息
		$carts = object(parent::TABLE_SHOP_CART)->get_data($cart_data);
		//已选中的商品
		$checked_sku_ids = !empty($arguments['goods_sku_id']) && is_array($arguments['goods_sku_id'])? $arguments['goods_sku_id'] : array();
		$all_shop_total_credit = 0;//所有商铺的商品所需支付积分
		$all_shop_total_money = 0;//所有商铺的商品所需支付人民币
		$all_shop_goods_count = 0;//所有商铺的商品数量
		
		$all_shop_total_checked_credit = 0;//所有商铺的商品所需支付积分
		$all_shop_total_checked_money = 0;//所有商铺的商品所需支付人民币
		$all_shop_goods_checked_count = 0;//所有商铺的商品数量
		//开始整合数据
		foreach($carts['shop_list'] as $shop_key => $shop_value){
			$carts['shop_list'][$shop_key]['goods'] = array();
			if( empty($cart_data) ){
				unset($carts['shop_list'][$shop_key]);
				continue;
			}
			//循环购物车数据
			foreach($cart_data as $cart_key => $cart_value){
				if($shop_value['shop_id'] != $cart_value['shop_id']){
					continue;
				}
				
				$goods = object(parent::TABLE_SHOP_CART)->get_goods($cart_value, $carts['shop_goods_list'], $carts['shop_goods_sku_list']);
				unset($goods['shop_goods'], $goods['shop_goods_sku'], $cart_data[$cart_key]);
				
				if( $goods['state'] === 'OK' && in_array($goods['sku_id'], $checked_sku_ids) ){
					if( $goods['property'] == 1 ){
						$all_shop_total_checked_credit += $goods['price']*$goods['number'];
					}else{
						$all_shop_total_checked_money += $goods['price']*$goods['number'];
					}
					$all_shop_goods_checked_count ++;
				}
				
				if( $goods['property'] == 1 ){
					$all_shop_total_credit += $goods['price']*$goods['number'];
				}else{
					$all_shop_total_money += $goods['price']*$goods['number'];
				}
				$all_shop_goods_count ++;
				$carts['shop_list'][$shop_key]['goods'][] = $goods;
			}
			
			//没有商品，那么删除
			if( empty($carts['shop_list'][$shop_key]['goods']) ){
				unset($carts['shop_list'][$shop_key]);
			}

		}
		
		return array(
            'status' => 1,
			'shop' => $carts['shop_list'],
			'statistic' => array(
				'total_credit' => $all_shop_total_credit, //所有商铺的商品所需支付积分
				'total_money' => $all_shop_total_money, //所有商铺的商品所需支付人民币
				'goods_count' => $all_shop_goods_count, //所有商铺的商品数量
				'checked_total_credit' => $all_shop_total_checked_credit, //选中商铺的商品所需支付积分
				'checked_total_money' => $all_shop_total_checked_money, //选中商铺的商品所需支付人民币
				'checked_goods_count' => $all_shop_goods_checked_count //选中商铺的商品数量
			),
		);
    }
	
	
	
	


 
    /**
     * 编辑购物车商品信息
     *
     * api: SHOPCARTSELFEDIT
     * req: {
     *  id             [str] [必填] [购物车ID]
     *  number         [int] [必填] [商品数量]
     * }
     * 
     * @return string 购物车ID
     */
    public function api_self_edit($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测输入
        object(parent::ERROR)->check($input, 'id', parent::TABLE_SHOP_CART, array('args'), 'shop_cart_id');
        object(parent::ERROR)->check($input, 'number', parent::TABLE_SHOP_CART, array('args'), 'shop_cart_number');

        //查询原始数据
        $original = object(parent::TABLE_SHOP_CART)->find($input['id']);
        if (empty($original))
            throw new error('购物车ID有误，数据不存在');

        //是否数量没变
        if ($original['shop_cart_number'] == $input['number'])
            return $input['id'];

        //查询商品信息
        $goods = object(parent::TABLE_SHOP_GOODS_SKU)->find_join_goods_spu($original['shop_goods_sku_id']);
        if (empty($goods))
            throw new error('商品规格ID有误，数据不存在');

        //是否超出库存
        if ($input['number'] > $goods['sku_stock'])
            throw new error('库存不足');

        //更新购物车商品数量
        $update_data['shop_cart_number'] = $input['number'];
        $update_data['shop_cart_update_time'] = time();

        //更新条件
        $update_where = array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] shop_cart_id=[+]', $input['id'])
        );

        //更新数据
        if (object(parent::TABLE_SHOP_CART)->update($update_where, $update_data)) {
            return $input['id'];
        } else {
            throw new error('操作失败');
        }
    }


    /**
     * 删除购物车商品
     *
     * api: SHOPCARTSELFREMOVE
     * req: {
     *    cart_ids [arr] [必填] [购物车ID，索引数组]
     * }
     * 
     * @return bool
     */
    public function api_self_remove($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测输入
        if (!(isset($input['cart_ids']) && is_array($input['cart_ids']) && is_string(implode($input['cart_ids']))))
            throw new error('参数不合法');

        $cart_ids_str = "'" . implode("','", $input['cart_ids']) . "'";
        $delete_where = array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] shop_cart_id in ([-])', $cart_ids_str, true)
        );

        //删除数据
        if (object(parent::TABLE_SHOP_CART)->delete($delete_where)) {
            return true;
        } else {
            throw new error('删除失败');
        }
    }


    //===========================================
    // 查询数据
    //===========================================


    /**
     * 查询购物车列表数据
     *
     * api: SHOPCARTSELFLIST
     * req: list接口通用参数
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

        //条件
        $config['where'][] = array('sc.user_id=[+]', $_SESSION['user_id']);

        //排序
        $config['orderby'][] = array('sc.shop_cart_insert_time', true);
        $config['orderby'][] = array('sc.shop_cart_id', true);

        //查询数据
        $data = object(parent::TABLE_SHOP_CART)->select_page($config);

        //格式化数据
        $shop = array();
        foreach ($data['data'] as $val) {
            //店铺分组
            if (!array_key_exists($val['shop_id'], $shop)) {
                $shop[$val['shop_id']] = array(
                    'shop_id' => $val['shop_id'],
                    'shop_name' => $val['shop_name'] ?: '自营',
                    'shop_goods' => array(),
                );
            }

            //商品信息
            $goods = array(
                'cart_id' => $val['cart_id'],
                'goods_id' => $val['goods_id'],
                'goods_sku_id' => $val['goods_sku_id'],
                'number' => $val['number'],
                'json' => array(),
            );

            //是否商品已失效
            if (is_null($val['goods_name']) || is_null($val['sku_name']) || $val['shop_goods_state'] != 1 || $val['shop_goods_trash'] == 1) {
                $goods['json'] = cmd(array($val['json']), 'json decode');
                $goods['json']['display'] = false;
            } else {
                $goods['json'] = array(
                    'goods_name' => $val['goods_name'],
                    'goods_image_id' => $val['goods_image_id'],
                    'sku_name' => $val['sku_name'],
                    'sku_image_id' => $val['sku_image_id'],
                    'sku_stock' => $val['sku_stock'],
                    'sku_price' => $val['sku_price'],
                    'spu_name' => $val['spu_name'],
                    'display' => true
                );
            }

            $shop[$val['shop_id']]['shop_goods'][] = $goods;
        }

        $data['data'] = array_values($shop);
        return $data;
    }







}