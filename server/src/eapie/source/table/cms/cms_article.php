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



namespace eapie\source\table\cms;
use eapie\main;
class cms_article extends main {


    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(__CLASS__);


    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
        'cms_article_id' => array(
            'args' => array(
                'exist' => array('缺少文章ID参数'),
                'echo'  => array('文章ID数据类型不合法'),
                '!null' => array('文章ID不能为空')
            ),
            'exists' => array(
                'method' => array(array(parent::TABLE_CMS_ARTICLE, 'find_exists_id'), '文章ID有误，数据不存在')
            )
        ),
        'cms_article_name' => array(
            'args' => array(
                'exist' => array('缺少文章标题参数'),
                'echo'  => array('文章标题数据类型不合法'),
                '!null' => array('文章标题不能为空'),
                '<width'=> array(200, '文章标题的字数太多')
            ),
        ),
        'cms_article_info' => array(
            'args' => array(
                'echo'  => array('文章简介数据类型不合法'),
            ),
        ),
         'cms_article_source' => array(
            'args' => array(
                'echo'  => array('文章来源的数据类型不合法'),
            ),
        ),
        'cms_article_content' => array(
            'args' => array(
                'echo'  => array('文章内容数据类型不合法'),
            ),
        ),
        
		"cms_article_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("文章排序的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/', "文章排序必须是整数"),
					),
		),
		
        'cms_article_state' => array(
            'args' => array(
                'exist' => array('缺少文章状态参数'),
                'match' => array('/^[0123]{1}$/', '文章状态必须是0、1、2、3')
            )
        ),
		"cms_article_keywords" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("文章关键字的数据类型不合法"),
					),
		),
		"cms_article_description" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("文章描述的数据类型不合法"),
					),
		),
		
		
		
    );
    
	
	
    /**
     * 获取一个id号
     * 
     * @param   void
     * @return  string
     */
    public function get_unique_id(){
        return cmd(array(22), 'random autoincrement');
    }

    
    				
	/**
	 * 根据ID，判断是否存在
	 * 
	 * @param	string 		$cms_article_id
	 */
	public function find_exists_id($cms_article_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($cms_article_id), function($cms_article_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('cms_article')
			->where(array('cms_article_id=[+]', (string)$cms_article_id))
			->find('cms_article_id');
		});
	}
	
    
    
    /**
     * 增
     * 
     * @param  array  $data      插入的数据
     * @param  array  $call_data 插入的绑定数据
     * @return boolean           响应
     */
    public function insert($data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('cms_article')
            ->call('data', $call_data)
            ->insert($data);

        //清理当前项目缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 改
     * 
     * @param  array  $where     where条件
     * @param  array  $data      更新的数据
     * @param  array  $call_data 更新的绑定数据
     * @return boolean           响应
     */
    public function update($where = array(), $data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('cms_article')
            ->call('where', $where)
            ->call('data', $call_data)
            ->update($data);

        //清理当前项目缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 根据唯一标识，删除数据
     * 
     * @param  string $id 唯一标识ID
     * @return boolean
     */
    public function remove($id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('cms_article')
            ->where(array('cms_article_id=[+]', $id))
            ->delete();

        //清理当前项目缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 根据唯一标识，查询数据
     * 
     * @param  string $id 唯一标识ID
     * @return array
     */
    public function find($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function($id){
            return db(parent::DB_APPLICATION_ID)
                ->table('cms_article')
                ->where(array('cms_article_id=[+]', $id))
                ->find();
        });
    }



    /**
     * 获取所有的分页数据
     * 
     * @param  array  $config 查询配置
     * @return array
     */
    public function select_page($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            $limit = array(
                (isset($call_limit[0])? $call_limit[0] : 0),
                (isset($call_limit[1])? $call_limit[1] : 0)
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

			
            //获取总条数
            $total_count = db(parent::DB_APPLICATION_ID)
            ->table('cms_article ca')
            ->call('where', $call_where)
            ->find('count(distinct ca.cms_article_id) as count');

            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count']/$data['page_size']);
                    $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;//当前页数
                }
            }
			
			
			if( empty($select) ){
				$select = array(
					'ca.*'
				);
			}
			
			
            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('cms_article ca')
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
			
            return $data;
        });
    }




	/**
	 * 获取附加数据
	 * 
	 * @param	array		$data
	 * @param	array		$config
	 * @return	array	
	 */
	public function get_additional_data($data = array(), $config = array()){
		if( empty($data) ){
			return $data;
		}
		
		$goods_ids = array();
		foreach($data as $key => $value){
			if( !isset($value['cms_article_id']) ){
				//分类id不存在，则直接返回
				break;
			}
			$data[$key]["cms_article_type"] = array();//初始化键值
			$data[$key]["cms_article_image_main"] = array();
			$ids[] = $value['cms_article_id'];
		}	
		
		//没有可查询的数据
		if( empty($ids) ){
			return $data;
		}
		
		//标识符的目的是，有些数据不一致
		$identifier = md5(cmd(array($ids), "json encode").cmd(array($data), "json encode")).
		md5(cmd(array($data), "json encode").cmd(array($ids), "json encode"));
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($ids, $config, $identifier), function($ids, $config, $identifier) use ($data){
			
			//获取分类数据
			$in_string = "\"".implode("\",\"", $ids)."\"";
			
			
			//获取分类数据
			if(empty($config['cms_article_type']['where']) || !is_array($config['cms_article_type']['where']))
			$config['cms_article_type']['where'] = array();
			if(empty($config['cms_article_type']['orderby']) || !is_array($config['cms_article_type']['orderby']))
			$config['cms_article_type']['orderby'] = array();
			if(empty($config['cms_article_type']['limit']) || !is_array($config['cms_article_type']['limit']))
			$config['cms_article_type']['limit'] = array();
			if(empty($config['cms_article_type']['select']) || !is_array($config['cms_article_type']['select']))
			$config['cms_article_type']['select'] = array();
			$config['cms_article_type']['where'][] = array("[and] cat.cms_article_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
			$article_type_data = object(parent::TABLE_CMS_ARTICLE_TYPE)->select_join($config['cms_article_type']);
			
			//获取商品主图的数据
			if(empty($config['cms_article_image']['where']) || !is_array($config['cms_article_image']['where']))
			$config['cms_article_image']['where'] = array();
			if(empty($config['cms_article_image']['orderby']) || !is_array($config['cms_article_image']['orderby']))
			$config['cms_article_image']['orderby'] = array();
			if(empty($config['cms_article_image']['limit']) || !is_array($config['cms_article_image']['limit']))
			$config['cms_article_image']['limit'] = array();
			if(empty($config['cms_article_image']['select']) || !is_array($config['cms_article_image']['select']))
			$config['cms_article_image']['select'] = array();
			
			$config['cms_article_image']['where'][] = array("[and] cai.cms_article_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
			$config['cms_article_image']['where'][] = array("[and] i.image_state=1");
			$config['cms_article_image']['where'][] = array("[and] cai.cms_article_image_main=1");
			if( empty($config['cms_article_image']['orderby']) ){
				$config['cms_article_image']['orderby'][] = array("image_sort");
				$config['cms_article_image']['orderby'][] = array("cms_article_image_id");
			}
			$article_image_data = object(parent::TABLE_CMS_ARTICLE_IMAGE)->select_join($config['cms_article_image']);
			
			
			foreach($data as $parent_key => $parent_value){
				//已经删完了则终止
				if( empty($article_type_data) && 
				empty($article_image_data) ){
					break;
				}
				
				//获得主图
				if( !empty($article_image_data) ){
					foreach($article_image_data as $image_key => $image_value){
						if($image_value['cms_article_id'] == $parent_value['cms_article_id']){
							$data[$parent_key]['cms_article_image_main'][] = $image_value;
							unset($article_image_data[$image_key]);
						}
					}
				}
				
				//获得分类
				if( !empty($article_type_data) ){
					foreach($article_type_data as $type_key => $type_value){
						if($type_value['cms_article_id'] == $parent_value['cms_article_id']){
							$data[$parent_key]['cms_article_type'][] = $type_value;
							unset($article_type_data[$type_key]);
						}
					}
				}
				
				
			}


			return $data;
			
			
		});
		
		
	}









}