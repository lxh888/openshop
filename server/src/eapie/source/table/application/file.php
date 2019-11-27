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
class file extends main {
	
	
	
	/*文件表*/
	
	
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
		'file_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少文件ID参数"),
					'echo'=>array("文件ID数据类型不合法"),
					'!null'=>array("文件ID不能为空"),
					),
			//参数检测
			'format'=>array(
					'echo'=>array("文件ID的数据类型不合法"),
					),		
			//检查编号是否存在		
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_FILE, 'find_exists_id'), "文件ID有误，数据不存在",) 
			),
			//检查编号是否合法	
			'legal_id'=>array(
					'method'=>array(array(parent::TABLE_FILE, 'find_legal_id'), "文件ID有误，未上传或者信息不全") 
			),
		),
	
		
		"file_name" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少文件名称参数"),
					'echo'=>array("文件名称数据类型不合法"),
					'!null'=>array("文件名称不能为空"),
					),
			//参数检测
			'format'=>array(
					'echo'=>array("文件名称的数据类型不合法"),
					),			
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "文件名称的字符长度太多")
					),		
		),
	
		"file_type" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少文件类型参数"),
					'echo'=>array("文件类型数据不合法"),
					'!null'=>array("文件类型不能为空"),
					),
			'format'=>array(
					'echo'=>array("文件类型的数据类型不合法"),
					),				
		),
		
		"file_size" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("文件字节的数据类型不合法"),
					'match'=>array('/^[0-9]{1,}$/iu', "文件字节必须是整数"),
					),
			'empty'=>array(
					'!match'=>array('/^[0]{1,}$/iu', "该文件是一个空文件"),
					),		
		),
	
		"file_format" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("文件后缀的数据类型不合法"),
					),
		),
		
	
		"file_path" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("文件的储存地址不合法"),
					),
		),
		
		
		"file_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("文件排序的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/iu', "文件排序必须是整数"),
					),
		),
	
		"file_hash" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("文件的HASH值数据类型不合法"),
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
	 * 根据ID，判断是否存在
	 * 
	 * @param	string 		$file_id
	 */
	public function find_exists_id($file_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($file_id), function($file_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('file')
			->where(array('file_id=[+]', (string)$file_id))
			->find('file_id');
		});
	}
	
	
	
	/**
	 * 根据ID，判断是否合法（1上传成功）
	 * file_state 状态。0上传中，未成功；1上传成功
	 * 
	 * @param	string 		$file_id
	 */
	public function find_legal_id($file_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($file_id), function($file_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('file')
			->where(array('file_id=[+]', (string)$file_id), array("file_state=1"))
			->find('file_id');
		});
	}
		
	
	
			
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$file_id
	 * @return	array
	 */
	public function find($file_id = ''){
		if( empty($file_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($file_id), function($file_id){
			return db(parent::DB_APPLICATION_ID)
			->table('file')
			->where(array('file_id=[+]', (string)$file_id))
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
		->table('file')
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
		->table('file')
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
	 * @param	array	$file_id
	 * @return	array
	 */
	public function remove($file_id = ''){
		if( empty($file_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('file')
		->where(array('file_id=[+]', (string)$file_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}		
		
	
	
	
	
	
	
	
	
	
}
?>