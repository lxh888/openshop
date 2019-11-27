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




namespace eapie\source\table\shop;
use eapie\main;

class shop_goods_stock_log extends main{

    /**
     * 缓存的键列表
     * @var string
     */
    const CACHE_KEY = array(__CLASS__);

    /**
     * 数据检测
     * @var array
     */
    public $check = array(

    );


    /**
     * 获取一个id号
     * @return  string
     */
    public function get_unique_id($num=0)
    {
        if($num>0){
            return cmd(array($num), 'random autoincrement');
        }
        return cmd(array(22), 'random autoincrement');
    }
    

    /**
     * 新增数据
     */
    public function insert_info($config=array())
    {
    	
		//如果是更新
		if($config['type'] == 3){
			$config['remain_num'] = $config['num'];
		}
		
        $insert_data = array(
            'shop_goods_stock_log_id'=>$this->get_unique_id(16),
            'user_id'=>!empty($config['user_id'])?$config['user_id']:'',
            'buy_user_id'=>!empty($config['buy_user_id'])?$config['buy_user_id']:'',
            'shop_goods_id'=>!empty($config['shop_goods_id'])?$config['shop_goods_id']:'',
            'shop_goods_stock_log_key'=>!empty($config['key'])?$config['key']:'',
            'shop_goods_sku_id'=>!empty($config['sku_id'])?$config['sku_id']:'',
            'type'=>$config['type'],
            'shop_goods_stock_num'=>$config['num'],
            'remain_num'=>$config['remain_num'],
            'insert_time'=>time(),
        );

        $json_info = array();
        if(!empty($config['goods'])){
            $json_info['goods']= $config['goods'];
        }

        if(!empty($config['goods_sku'])){
            $json_info['goods_sku']= $config['goods_sku'];
        }

        if(!empty($json_info)){
            $insert_data['stock_log_json'] = cmd(array($json_info),'json encode');
        }

        $bool = db(parent::DB_APPLICATION_ID)
			->table('shop_goods_stock_log')
			->insert($insert_data);

		//清理缓存
		if ($bool)
			object(parent::CACHE)->clear(self::CACHE_KEY);

		return $bool;
    }


    /**
     * 查询分页数据--关联查询
     * 
     * @param   array   $config     配置信息
     * @return  array
     */
    public function select_join_page($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            $call_joinon = isset($config['joinon']) && is_array($config['joinon']) ? $config['joinon'] : array();

            $limit = array(
                (isset($call_limit[0]) ? $call_limit[0] : 0),
                (isset($call_limit[1]) ? $call_limit[1] : 0)
            );


            //设置返回的数据
            $data = array(
                'row_count' => 0,
                'limit_count' => $limit[0] + $limit[1],
                'page_size' => $limit[1],
                'page_count' => 0,
                'page_now' => 1,
                'data' => array()
            );

            //获取总条数shop_goods_stock_log
            if(!empty($call_joinon)){
            	$total_count = db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_stock_log sgsl')
				->call('joinon',$call_joinon)
                ->call('where', $call_where)
                ->find('count(*) as count');
            }else{
            	$total_count = db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_stock_log sgsl')
                ->call('where', $call_where)
                ->find('count(*) as count');
            }
            

            //是否有数据
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }

            if(!empty($call_joinon)){
                $data['data'] =  db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_stock_log sgsl')
                ->call('joinon',$call_joinon)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select/*, function($p){
                	printexit($p);
                }*/);
            }else{
                $data['data'] =  db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_stock_log sgsl')
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);
            }
            return $data;
        });
    }
} 

?>