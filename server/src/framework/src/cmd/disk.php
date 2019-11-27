<?php
namespace framework\src\cmd;
use framework\cao as cao;
final class disk extends cao {
	
	
	/*计算器磁盘信息操作，目录、文件操作*/
	
	
	/**
	 * 创建目录
	 * 
	 * @param	mixed		$path		
     * @return	bool
	 */
	static protected function _mkdir_($dir = ''){
		//生成缓存主目录路径
		if( !is_dir($dir) || !is_writable($dir) ){
			//0755 或者最高权限0777
			if( !mkdir($dir, 0777, true) ){
				//处理错误信息
				return false;
				}
			}
		
		return is_dir($dir);
	}
	
	
	
	
	
	
	/**
	 * 获得文件大小
	 * 可以传入一个索引数组(即处理多个文件，返回合计大小)，也可以是字符串，获得一个文件大小
	 * 
	 * @param	mixed		$path		
     * @return	array
	 */
	static protected function _file_size_($path = null){
		$data = 0;
		if( !empty($path)){
			if( is_array($path) ){
				foreach($path as $value){
					if( !is_file($value) ){
						continue;
						} 
					$data += filesize($value);
					}
			}else
			if( is_file($path) ){
				$data += filesize($path);
				}
		}
		
		return $data;
		}
	
	
	
	
	
	/**
     * 返回一个目录的信息
	 * 
     * @param	string		$dir		父目录路径。绝对路径
	 * @param	bool		$is_all		是否是包括子目录的所有文件夹。默认false不是
     * @return	array
     */ 
	static protected function _info_( $dir = '' , $is_all = false ){
		$data = array(
		'file_size' => 0, //传入文件大小  字节
		'file_size_unit' => '', //传入文件大小  带单位
		'file_count' => 0, //传入文件个数
		'dir_count' => 0 //传入文件夹个数
		);
		if( !empty($dir) && is_dir($dir) ){
			self::_info_resource( $dir, $data, $is_all );
			}
		
		//格式化文件大小单位
		$data['file_size_unit'] = parent::cmd(array($data['file_size']), 'unit storage');
		
		return $data;
		}
	
	
	
	
	/**
     * 统计目录里的文件。递归
	 * 打开文件夹。返回一个$ds 资源
	 * readdir($ds)从打开的文件夹资源中读取条目
	 * 文件取完之后会返回一个布尔值(false)
	 * if($file!='.' && $file!='..')过滤点（不过滤否则会是死循环）
	 * closedir($ds)删除目录资源
	 * 
     * @param	string		$dir			父目录路径。绝对路径
	 * @param	array		$data			数据搜集
	 * @param	bool		$is_all		是否是包括子目录的所有文件夹。默认false不是
     * @return	void
     */ 
	static private function _info_resource($dir, &$data, $is_all){
		if( !$ds = @opendir($dir) ) return;
		while( false !== ($file=readdir($ds)) ){
			$path = $dir."/".$file;//取得文件路径。
			if( $file=='.' || $file=='..' ) continue;
			if( is_dir($path) ){
				$data['dir_count'] ++;//统计目录个数
				if( !empty($is_all) ){
					self::_info_resource($path, $data, $is_all);//判断$path 是否是目录。如果是，就递归再次遍历
					}
				}else{
					$data['file_count'] ++;//统计文件个数
					//round()对浮点数进行四舍五入 2表示取2位小数
					$data['file_size'] += filesize($path);//统计文件总大小
					}
			}
		closedir($ds);
		}
	
	
	
	
	
	/**
     * 返回一个目录的所有目录绝对路径
	 * 
     * @param	string		$dir		父目录路径。绝对路径
	 * @param	bool		$is_all		是否是包括子目录的所有文件夹。默认false不是
     * @return	array
     */ 
	static protected function _dir_path_( $dir = '' , $is_all = false ){
		$data = array();
		if( !empty($dir) && is_dir($dir) ){
			self::_dir_path_resource( $dir, $data, $is_all );
			}
		
		return $data;
		}
	
	
	
	/**
     * 这是统计一个目录内的所有文件目录。递归
	 * 
     * @param	string		$dir			父目录路径。绝对路径
	 * @param	array		$data			数据搜集
	 * @param	bool		$is_all		是否是包括子目录的所有文件夹。默认false不是
     * @return	void
     */ 
	static private function _dir_path_resource($dir, &$data, $is_all){
		if( !$ds = @opendir($dir) ) return;
		while( false !== ($file = readdir($ds)) ){
			$path = $dir.DIRECTORY_SEPARATOR.$file;//取得文件路径。
			if( $file == '.' || $file == '..' ) continue;
			if( is_dir($path) ){
				//判断$path 是否是目录。如果是，就递归再次遍历
				$data[$path] = $file;
				if( !empty($is_all) ){
					self::_dir_path_resource($path, $data, $is_all);
					}
				}
			}
		closedir($ds);
		}
	
	
	
	
	/**
     * 删除一个目录的所有目录及文件
	 * 
     * @param	string		$dir		父目录路径。绝对路径
	 * @param	bool		$is_all		是否是包括子目录的所有文件夹。默认false不是
     * @return	bool
     */ 
	static protected function _dir_delete_( $dir = '' , $is_all = false ){
		$data = false;
		if( !empty($dir) && is_dir($dir) ){
			$data = self::_dir_delete_resource( $dir, $data, $is_all );
			}
		
		return $data;
		}	
	
	
	
	/**
     * 删除一个目录的所有目录及文件。递归
	 * 
     * @param	string		$dir			父目录路径。绝对路径
	 * @param	array		$data			数据搜集
	 * @param	bool		$is_all		是否是包括子目录的所有文件夹。默认false不是
     * @return	void
     */ 
	static private function _dir_delete_resource($dir, &$data, $is_all){
		if( !$ds = @opendir($dir) ) return;
		while( false !== ($file = readdir($ds)) ){
			$path = $dir.DIRECTORY_SEPARATOR.$file;//取得文件路径。
			if( $file == '.' || $file == '..' ) continue;
			if( is_dir($path) ){
				//判断是否获得子目录的
				if( !empty($is_all) ){
					//判断$path 是否是目录。如果是，就递归再次遍历
					self::_dir_delete_resource($path, $data, $is_all);
					}
				}else{
					//否则是文件，删除
					unlink($path);
					}
			}
		closedir($ds);
		
		// 删除空目录
		return rmdir($dir);// 返回值是布尔值。删除成功返回 ture
		}	
	
	
	
	
	
	/**
     * 返回一个目录的所有文件绝对路径
	 * $is_all 是否是包括子目录的所有文件夹 。默认false不是
	 * 
     * @param	string		$dir		父目录路径。绝对路径
	 * @param	bool		$is_all		是否是包括子目录的所有文件夹。默认false不是
     * @return	array
     */ 
	static protected function _file_path_( $dir = '', $is_all = false ){
		$data = array();
		if( !empty($dir) && is_dir($dir) ){
			self::_file_path_resource( $dir, $data, $is_all );
			}
		
		return $data;
		}
	
	
	/**
     * 这是统计一个目录内的所有文件。递归
	 * 
     * @param	string		$dir			父目录路径。绝对路径
	 * @param	array		$data			数据搜集
	 * @param	bool		$is_all		是否是包括子目录的所有文件夹。默认false不是
     * @return	void
     */ 
	static private function _file_path_resource( $dir, &$data, $is_all ){
		if( !$ds = @opendir($dir) ) return;
		while( false !== ($file=readdir($ds)) ){
			$path = $dir.DIRECTORY_SEPARATOR.$file;//取得文件路径。
			if( $file=='.' || $file=='..' ) continue;
			if( is_dir($path) ){
				//判断是否获得子目录的
				if( !empty($is_all) ){
					//判断$path 是否是目录。如果是，就递归再次遍历
					self::_file_path_resource($path, $data, $is_all);
					}
				}else{
					$data[$path]= $file;//添加文件路径到数组
					}
			}
		closedir($ds);
		}
	
	
	
	/**
	 * 删除一个目录的文件
	 * 
	 * $is_all 是否是包括子目录的所有文件夹 。默认false不是
	 */
	static protected function _file_delete_( $dir = '', $is_all = false ){
		$data = false;
		if( !empty($dir) && is_dir($dir) ){
			$data = self::_file_delete_resource( $dir, $data, $is_all );
			}
		return $data;
		}
	
	
	
	
	/**
     * 这是统计一个目录内的所有文件。递归
	 * 
     * @param	string		$dir			父目录路径。绝对路径
	 * @param	array		$data			数据搜集
	 * @param	bool		$is_all		是否是包括子目录的所有文件夹。默认false不是
     * @return	void
     */ 
	static private function _file_delete_resource( $dir, &$data, $is_all ){
		if( !$ds = @opendir($dir) ) return;
		while( false !== ($file=readdir($ds)) ){
			$path = $dir.DIRECTORY_SEPARATOR.$file;//取得文件路径。
			if( $file=='.' || $file=='..' ) continue;
			if( is_dir($path) ){
				//判断是否获得子目录的
				if( !empty($is_all) ){
					//判断$path 是否是目录。如果是，就递归再次遍历
					self::_file_delete_resource($path, $data, $is_all);
					}
				}else{
					//否则是文件，删除
					unlink($path);
					}
			}
		closedir($ds);
		
		return true;
		}	
	
	
	
	
}
?>