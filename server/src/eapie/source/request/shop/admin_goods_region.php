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
class admin_goods_region extends \eapie\source\request\shop {
	
	
	
	/**
	 * 添加售卖地区
	 * 
	 * SHOPADMINGOODSREGIONADD
	 * {"class":"shop/admin_goods_region","method":"api_add"}
	 * 
	 * [{"shop_goods_id":"商品ID","shop_goods_region_info":"商品信息","shop_goods_region_state":"商品状态0|1","shop_goods_region_sort":"商品排序","shop_goods_region_scope":"商品范围","shop_goods_region_province":"商品售卖省","shop_goods_region_city":"商品售卖市","shop_goods_region_district":"商品售卖区"}]
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_REGION_ADD);
		//数据检测
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_region_info', parent::TABLE_SHOP_GOODS_REGION, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_region_state', parent::TABLE_SHOP_GOODS_REGION, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_region_sort', parent::TABLE_SHOP_GOODS_REGION, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_region_scope', parent::TABLE_SHOP_GOODS_REGION, array('args'));
		
		//获取商品数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($data['shop_goods_id']);
		if( empty($shop_goods_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $shop_goods_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
		
		//判断 省市区
		if( $data['shop_goods_region_scope'] == 3 ){
			object(parent::ERROR)->check($data, 'shop_goods_region_province', parent::TABLE_SHOP_GOODS_REGION, array('args'));
			object(parent::ERROR)->check($data, 'shop_goods_region_city', parent::TABLE_SHOP_GOODS_REGION, array('args'));
			object(parent::ERROR)->check($data, 'shop_goods_region_district', parent::TABLE_SHOP_GOODS_REGION, array('args'));
		}else
		if( $data['shop_goods_region_scope'] == 2 ){
			object(parent::ERROR)->check($data, 'shop_goods_region_province', parent::TABLE_SHOP_GOODS_REGION, array('args'));
			object(parent::ERROR)->check($data, 'shop_goods_region_city', parent::TABLE_SHOP_GOODS_REGION, array('args'));
			$data['shop_goods_region_district'] = '';
		}else
		if( $data['shop_goods_region_scope'] == 1 ){
			object(parent::ERROR)->check($data, 'shop_goods_region_province', parent::TABLE_SHOP_GOODS_REGION, array('args'));
			$data['shop_goods_region_city'] = '';
			$data['shop_goods_region_district'] = '';
		}
		
		$is_exist = object(parent::TABLE_SHOP_GOODS_REGION)->find_goods_scope_pcd($data['shop_goods_id'], $data['shop_goods_region_province'], $data['shop_goods_region_city'], $data['shop_goods_region_district']);
		if( !empty($is_exist) ){
			throw new error("该商品所在的销售地区已经存在，请勿重复添加");
		}
		
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'shop_goods_id',
			'shop_goods_region_scope',
			'shop_goods_region_province', 
			'shop_goods_region_city', 
			'shop_goods_region_district',
			'shop_goods_region_info',
			'shop_goods_region_state',
			'shop_goods_region_sort',
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//获取id号
		$insert_data['shop_goods_region_id'] = object(parent::TABLE_SHOP_GOODS_REGION)->get_unique_id();
		$insert_data['user_id'] = $_SESSION['user_id'];
		//时间
		$insert_data['shop_goods_region_insert_time'] = time();
		$insert_data['shop_goods_region_update_time'] = time();
		
		if( object(parent::TABLE_SHOP_GOODS_REGION)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['shop_goods_region_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	/**
     * 编辑商品售卖地区
     * 
     * SHOPADMINGOODSREGIONEDIT
     * {"class":"shop/admin_goods_region","method":"api_edit"}
	 * 
	 * [{"shop_goods_region_id":"必填，主键非修改值，商品售卖地区ID","shop_goods_region_info":"简介","shop_goods_region_state":"状态：0封禁|1正常","shop_goods_region_sort":"排序"}]
	 * 
     * @param  [arr]  $data [请求参数]
     * @return bool
     */
    public function api_edit( $data = array() ){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_REGION_EDIT);
		
        //检测数据
        object(parent::ERROR)->check($data, 'shop_goods_region_id', parent::TABLE_SHOP_GOODS_REGION, array('args'));
        if( isset($data['shop_goods_region_info']) )
        object(parent::ERROR)->check($data, 'shop_goods_region_info', parent::TABLE_SHOP_GOODS_REGION, array('args'));
        if( isset($data['shop_goods_region_state']) )
        object(parent::ERROR)->check($data, 'shop_goods_region_state', parent::TABLE_SHOP_GOODS_REGION, array('args'));
        if( isset($data['shop_goods_region_sort']) )
        object(parent::ERROR)->check($data, 'shop_goods_region_sort', parent::TABLE_SHOP_GOODS_REGION, array('args'));
        
        //查询旧数据
        $original = object(parent::TABLE_SHOP_GOODS_REGION)->find($data['shop_goods_region_id']);
        if (empty($original)) throw new error('数据不存在');

        //白名单
        $whitelist = array(
            'shop_goods_region_info', 
            'shop_goods_region_state', 
            'shop_goods_region_sort',
        );
        $update_data = cmd(array($data, $whitelist), 'arr whitelist');

        //过滤不需要更新的数据
        foreach( $update_data as $k => &$v ){
            if (isset($original[$k]) && $original[$k] == $v)
                unset($update_data[$k]);
        }
        if (empty($update_data))
            throw new error('没有需要更新的数据');

        //格式化数据
        $update_data['shop_goods_region_update_time'] = time();

        //更新数据，记录日志
        if( object(parent::TABLE_SHOP_GOODS_REGION)->update(array(array('shop_goods_region_id=[+]', $data['shop_goods_region_id'])), $update_data) ){
            object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
            return $data['shop_goods_region_id'];
        }else{
            throw new error('操作失败');
        }
		
    }
	
	
	
	
	
	
	
	
	
		
	/**
	 * 获取数据列表
	 * 
	 * SHOPADMINGOODSREGIONLIST
	 * {"class":"shop/admin_goods_region","method":"api_list"}
	 * 
	 * [{"search":{"shop_goods_id":"商品ID","shop_goods_name":"商品名称","shop_goods_state":"商品状态：0|1|2|3","shop_goods_region_province":"省","shop_goods_region_city":"市","shop_goods_region_district":"区","state":"状态","scope":"范围：1|2|3"},"sort":["shop_goods_name_desc","shop_goods_name_asc","shop_goods_state_desc","shop_goods_state_asc","scope_desc","scope_asc","province_desc","province_asc","city_desc","city_asc","district_desc","district_asc","state_desc","state_asc","insert_time_desc","insert_time_asc","update_time_desc","update_time_asc","sort_desc","sort_asc"],"page":"1"}]
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_REGION_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'shop_goods_name_desc' => array('shop_goods_name', true),
			'shop_goods_name_asc' => array('shop_goods_name', false),
			'shop_goods_state_desc' => array('shop_goods_state', true),
			'shop_goods_state_asc' => array('shop_goods_state', false),
			
			'scope_desc' => array('shop_goods_region_scope', true),
			'scope_asc' => array('shop_goods_region_scope', false),
			
			'province_desc' => array('shop_goods_region_province', true),
			'province_asc' => array('shop_goods_region_province', false),
			'city_desc' => array('shop_goods_region_city', true),
			'city_asc' => array('shop_goods_region_city', false),
			'district_desc' => array('shop_goods_region_district', true),
			'district_asc' => array('shop_goods_region_district', false),
			
			'state_desc' => array('shop_goods_region_state', true),
			'state_asc' =>  array('shop_goods_region_state', false),
			
			'insert_time_desc' => array('shop_goods_region_insert_time', true),
			'insert_time_asc' => array('shop_goods_region_insert_time', false),
			'update_time_desc' => array('shop_goods_region_update_time', true),
			'update_time_asc' => array('shop_goods_region_update_time', false),
			
			'sort_desc' => array('shop_goods_region_sort', true),
			'sort_asc' => array('shop_goods_region_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('sgr.shop_goods_id', false);
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			$config["where"][] = array('[and] sg.user_id=[+]', $_SESSION['user_id']);
		}
		
		if(!empty($data['search'])){
			if( isset($data['search']['shop_goods_id']) && is_string($data['search']['shop_goods_id']) ){
				$config["where"][] = array('[and] sg.shop_goods_id=[+]', $data['search']['shop_goods_id']);
			}
			if( isset($data['search']['shop_goods_name']) && is_string($data['search']['shop_goods_name']) ){
                $config['where'][] = array('[and] sg.shop_goods_name LIKE "%[-]%"', $data['search']['shop_goods_name']);
            }
			
			
			if( isset($data['search']['shop_goods_region_province']) && is_string($data['search']['shop_goods_region_province']) ){
                $config['where'][] = array('[and] sgr.shop_goods_region_province LIKE "%[-]%"', $data['search']['shop_goods_region_province']);
            }
			if( isset($data['search']['shop_goods_region_city']) && is_string($data['search']['shop_goods_region_city']) ){
                $config['where'][] = array('[and] sgr.shop_goods_region_city LIKE "%[-]%"', $data['search']['shop_goods_region_city']);
            }
			if( isset($data['search']['shop_goods_region_district']) && is_string($data['search']['shop_goods_region_district']) ){
                $config['where'][] = array('[and] sgr.shop_goods_region_district LIKE "%[-]%"', $data['search']['shop_goods_region_district']);
            }
			
			
			if( isset($data['search']['state']) && 
			(is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
			in_array($data['search']['state'], array("0", "1")) ){
				$config["where"][] = array('[and] sgr.shop_goods_region_state=[+]', $data['search']['state']);
				}
			
			if( isset($data['search']['scope']) && 
			(is_string($data['search']['scope']) || is_numeric($data['search']['scope'])) &&
			in_array($data['search']['scope'], array("1", "2", "3")) ){
				$config["where"][] = array('[and] sgr.shop_goods_region_scope=[+]', $data['search']['scope']);
				}
			
			if( isset($data['search']['shop_goods_state']) && 
			(is_string($data['search']['shop_goods_state']) || is_numeric($data['search']['shop_goods_state'])) &&
			in_array($data['search']['shop_goods_state'], array("0", "1", "2", "3")) ){
				$config["where"][] = array('[and] sg.shop_goods_state=[+]', $data['search']['shop_goods_state']);
				}
			
		}
		
		return object(parent::TABLE_SHOP_GOODS_REGION)->select_page($config);
	}
	
	
	
	
		
    /**
	 * 删除商品销售地区
	 * 
	 * SHOPADMINGOODSREGIONREMOVE
	 * {"class":"shop/admin_goods_region","method":"api_remove"}
	 * 
	 * [{"shop_goods_region_id":"商城商品销售地区ID"}]
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_REGION_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'shop_goods_region_id', parent::TABLE_SHOP_GOODS_REGION, array('args'));
		
		//查询旧数据
        $original = object(parent::TABLE_SHOP_GOODS_REGION)->find($data['shop_goods_region_id']);
        if (empty($original)) throw new error('数据不存在');
		
		if( object(parent::TABLE_SHOP_GOODS_REGION)->remove($data['shop_goods_region_id']) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $original);
			return $data['shop_goods_region_id'];
		}else{
			throw new error("操作失败");
		}
		
    }
    
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>