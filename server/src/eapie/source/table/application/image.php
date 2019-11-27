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



namespace eapie\source\table\application;
use eapie\main;
class image extends main {
	
	
	/*图片表*/
	
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__);
	

	
	
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'image_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少图片ID参数"),
					'echo'=>array("图片ID数据类型不合法"),
					'!null'=>array("图片ID不能为空"),
					),
			//参数检测
			'format'=>array(
					'echo'=>array("图片ID的数据类型不合法"),
					),		
			//检查编号是否存在		
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_IMAGE, 'find_exists_id'), "图片ID有误，数据不存在",) 
			),
			//检查编号是否合法	
			'legal_id'=>array(
					'method'=>array(array(parent::TABLE_IMAGE, 'find_legal_id'), "图片ID有误，未上传或者信息不全") 
			),
		),
	
	
		"image_name" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少图片名称参数"),
					'echo'=>array("图片名称数据类型不合法"),
					'!null'=>array("图片名称不能为空"),
					),
			//参数检测
			'format'=>array(
					'echo'=>array("图片名称的数据类型不合法"),
					),			
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "图片名称的字符长度太多")
					),		
		),
	
		"image_type" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少图片类型参数"),
					'echo'=>array("图片类型数据不合法"),
					'!null'=>array("图片类型不能为空"),
					),
			'mime_limit'=>array(
					'match'=>array('/^(image\/jpg|image\/jpeg|image\/pjpeg|image\/png|image\/x\-png|image\/gif|image\/bmp)$/i', "上传图片类型不合法"),
					),			
					
		),
		
		"upload" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("没有图片上传"),
					'method'=>array(array(parent::TABLE_IMAGE, 'check_exists_upload'), "上传图片不存在") 
					),
		),
		
		
		"image_size" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("图片字节的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/iu', "图片字节必须是整数"),
					),
			'empty'=>array(
					'method'=>array(array(parent::TABLE_IMAGE, 'check_size_empty'), "不能上传空文件") 
				),		
		),
	
		"image_format" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("图片后缀的数据类型不合法"),
					),
		),
		
	
		"image_path" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("图片的储存地址不合法"),
					),
		),
		
		"image_width" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("图片的宽度数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/iu', "图片的宽度必须是整数"),
					),
		),
		
		"image_height" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("图片的高度数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/iu', "图片的高度必须是整数"),
					),
		),
		
		"image_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("图片排序的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/iu', "图片排序必须是整数"),
					),
		),
	
		"image_hash" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("图片的HASH值数据类型不合法"),
					),
		),
	
	
	
	);
	
	
	
				
	/**
	 * 获取一个id号
	 * 
	 * @param	void
	 * @return	string
	 */
	public function get_unique_id(){
		return cmd(array(22), 'random autoincrement');
	}	
	
	
	/**
	 * 检测上传图片是否存在
	 * 
	 * @param	int 		$size
	 * @return	bool
	 */
	public function check_exists_upload($upload){
		return empty($upload)? false : true;
	}
	
	
	/**
	 * 检测图片的大小
	 * 
	 * @param	int 		$size
	 * @return	bool
	 */
	public function check_size_empty($size){
		return empty($size)? false : true;
	}
		
				
	/**
	 * 根据ID，判断是否存在
	 * 
	 * @param	string 		$image_id
	 */
	public function find_exists_id($image_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($image_id), function($image_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('image')
			->where(array('image_id=[+]', (string)$image_id))
			->find('image_id');
		});
	}
	
	
	
	/**
	 * 根据ID，判断是否合法（1上传成功）
	 * image_state 状态。0上传中，未成功；1上传成功
	 * 
	 * @param	string 		$image_id
	 */
	public function find_legal_id($image_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($image_id), function($image_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('image')
			->where(array('image_id=[+]', (string)$image_id), array("image_state=1"))
			->find('image_id');
		});
	}
		
	
	
			
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$image_id
	 * @return	array
	 */
	public function find($image_id = ''){
		if( empty($image_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($image_id), function($image_id){
			return db(parent::DB_APPLICATION_ID)
			->table('image')
			->where(array('image_id=[+]', (string)$image_id))
			->find();
		});
		
	}	
	
	
		
	
				
	/**
	 * 插入新数据
	 * 
	 * @param	array		$data			数据
	 * @param	array		$call_data		数据
	 * @return	bool
	 */
	public function insert($data = array(), $call_data = array()){
		if( empty($data) && empty($call_data) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('image')
		->call('data', $call_data)
		->insert($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
	
	
	
	
				
	/**
	 * 更新数据
	 * 
	 * @param	array		$where
	 * @param	array		$data
	 * @param	array		$call_data
	 * @return	bool
	 */
	public function update($where = array(), $data = array(), $call_data = array()){
		if( empty($where) || (empty($data) && empty($call_data)) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('image')
		->call('where', $where)
		->call('data', $call_data)
		->update($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
			
	
	
			
		
	/**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$image_id
	 * @return	array
	 */
	public function remove($image_id = ''){
		if( empty($image_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('image')
		->where(array('image_id=[+]', (string)$image_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}		
		
	
	
	
	
	
	
	
	
	
	
}
?>