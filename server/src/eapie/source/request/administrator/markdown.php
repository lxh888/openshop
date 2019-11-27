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



namespace eapie\source\request\administrator;
use eapie\main;
use eapie\error;
class markdown extends \eapie\source\request\administrator {
	
	
	/**
	 * 获取路径
	 * 
	 * @param	void | string	$book_name		书籍文件夹名称
	 * @return	string
	 */
	private function _path($book_name = ''){
		if( $book_name == ''){
			return	ROOT_PATH.DIRECTORY_SEPARATOR.'eapie'.DIRECTORY_SEPARATOR.'source'.DIRECTORY_SEPARATOR.'markdown';
		}else{
			return	ROOT_PATH.DIRECTORY_SEPARATOR.'eapie'.DIRECTORY_SEPARATOR.'source'.DIRECTORY_SEPARATOR.'markdown'.DIRECTORY_SEPARATOR.$book_name;
		}
	}
	
	
	
	/**
	 * 获取目录及文件
	 * 
	 * @param	array	$bound			绑定数据：文件路径与id的绑定
	 * @param	string	$book_name		书籍文件夹名称
	 * @param	string	$path			目录路径
	 * @param	array	$blacklist		黑名单，再递归的时候不传递该参数
	 * @return	array
	 */
	private function _catalog(&$bound, $book_name, $path, $blacklist = array()){
		$catalog = array();
		
		//先获取文件
		$files = cmd(array($path), 'disk file_path');
		asort($files);
		if( !empty($files) ){
			foreach($files as $file_path => $file_name){
				if( !preg_match('/(.*)\.md$/i', $file_name) ){
					continue;//不是 .md 文件则不记录
				}
				
				if( !empty($blacklist) && in_array($file_name, $blacklist) ){
					continue;//存在黑名单中，不记录
				}
				
				
				//绑定
				$catalog_path = str_replace($this->_path($book_name), '', $file_path);
				$bound[$catalog_path] = array(
					'title' => $file_name,
					'name' => preg_replace('/\.md$/iu', '', $file_name),
					'path' => $catalog_path,
					'book' => $book_name,
					'id' => cmd(array(22), 'random autoincrement'),
				);
				$catalog[] = $bound[$catalog_path];
			}
		}
		
		
		//获取目录
		$dirs = cmd(array($path), 'disk dir_path');
		asort($dirs);
		if( !empty($dirs) ){
			foreach($dirs as $dir_path => $dir_name){
				if( !empty($blacklist) && in_array($dir_name, $blacklist) ){
					continue;//存在黑名单中，不记录
				}
				
				$catalog[] = array(
					'title' => $dir_name,
					'book' => $book_name,
					'id' => cmd(array(22), 'random autoincrement'),
					'children' => $this->_catalog($bound, $book_name, $dir_path),//递归
				);
			}
		}
		
		
		return $catalog;
	}
	
	
	
	
	
	
	/**
	 * 获取markdown书籍
	 * 
	 * ADMINISTRATORMARKDOWNBOOK
	 * {"class":"administrator/markdown","method":"api_book"}
	 * 
	 * book.json   是书籍的信息，还记录屏蔽的目录、文件信息(被屏蔽的意思是不展示出来)
	 * 
	 * @param	void
	 * @return	array
	 */
	public function api_book(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MARKDOWN_READ);
		
		$path_list = cmd(array($this->_path()), 'disk dir_path');
		if( empty($path_list) ){
			return null;
		}
		
		
		$books = array();
		foreach($path_list as $book_dir => $book_name){
			$books[$book_name] = array(
				'title' => $book_name,
				'keywords' => '',
				'description' => ''
			);
			
			//获取 book.json
			$book_json = $book_dir.'/book.json';
			if( is_file($book_json) ){
				$book_json = cmd(array(file_get_contents($book_json)), 'json decode');
				if( is_array($book_json) ){
					//合并
					$books[$book_name] = array_merge($books[$book_name], $book_json);
				}
			}
			
			//循环获取子目录  第一级目录要注意 blacklist 黑名单
			$blacklist = !empty($books[$book_name]['blacklist']) && is_array($books[$book_name]['blacklist'])? 
			$books[$book_name]['blacklist'] : array();
			
			//收集id与路由的绑定，路由为键
			$books[$book_name]['bound'] = array();
			//获取目录
			$books[$book_name]['catalog'] = $this->_catalog($books[$book_name]['bound'], $book_name, $book_dir, $blacklist);
		}
		
		return $books;
	}
	
	
	
	
	/**
	 * 获取markdown文件内容
	 * 
	 * ADMINISTRATORMARKDOWNCONTENT
	 * {"class":"administrator/markdown","method":"api_content"}
	 * 
	 * $data = array(
	 * 		'book' => 书籍文件夹名称
	 * 		'path' => 路径
	 * )
	 * 
	 * @param	array	$data
	 * @return	string
	 */
	public function api_content($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MARKDOWN_READ);
		
		if( empty($data['book']) || !is_string($data['book']) || empty($data['path']) || !is_string($data['path']) ){
			throw new error("书籍名称或地址不合法");
		}
		
		$file_path = $this->_path($data['book']).$data['path'];
		if( !is_file($file_path) ){
			throw new error("该书籍页面不存在");
		}
		
		$application = object(parent::MAIN)->api_application();
		$url = http(function($http) use ($application){
			return $http['protocol'].'://'.$http['host'].'/index.php/temp/application/'.$application["application_id"];
		});
		
		
		$file_contents = file_get_contents($file_path);
		$book_name = $data['book'];
		//处理图片地址
		$file_contents = preg_replace_callback('/\!\[([^\[\]]+)?\]\(([^\(\)]+)\)/iu', function($matches) use ($url, $book_name) {
			//如果是http开头
			$file_url = trim($matches[2]);
			if( stripos($file_url, 'http') === 0 ){
				$url = $file_url;
			}else{
				
				$title = '';
				//如果存在描述，如：./images/test.png "小程序微信登陆流程参考图.png" 
				preg_match('/^([^\"]+)\"([^\"]+)?\"$|^([^\']+)\'([^\']+)?\'$/iu', $file_url, $url_matches);
				if( !empty($url_matches[1]) ){
					$arguments = array(
						'book' => $book_name,
						'path' => $url_matches[1]
					);
					
					if( isset($url_matches[2]) ){
						$title = ' "'.$url_matches[2].'" ';
					}
					
				}else{
					$arguments = array(
						'book' => $book_name,
						'path' => $file_url
					);
				}
				//printexit($url_matches, $arguments);
				//$base64_arguments = base64_encode(cmd(array($arguments), 'json encode'));
				$url .= '/data/ADMINISTRATORMARKDOWNIMAGE/'.cmd(array('['.cmd(array($arguments), 'json encode').']'), 'url encode').$title;
				//printexit( $matches , $arguments, $url, $base64_arguments);
			}
			
			return '!['.$matches[1].']('.$url.')';
		}, $file_contents);
		
		return $file_contents;
	}
	
	
	
	
	
	/**
	 * 显示markdown图片文件
	 * 
	 * ADMINISTRATORMARKDOWNIMAGE
	 * {"class":"administrator/markdown","method":"api_image"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_image($data = array()){
		if( empty($data['book']) || !is_string($data['book']) || empty($data['path']) || !is_string($data['path']) ){
			throw new error("书籍名称或地址不合法");
		}
		
		$file_path = $this->_path($data['book']).trim(trim($data['path']),'.');
		//printexit( $file_path, is_file($file_path) );
		$file_path = realpath($file_path);
		if( empty($file_path) ){
			throw new error("该书籍的图片不存在");
		}
		if( !is_file($file_path) ){
			throw new error("该书籍的图片不是一个文件");
		}
		
		$getimagesize = getimagesize($file_path);
		if( empty($getimagesize['mime']) ){
			throw new error("该书籍的图片不合法，无法获取 Content-type");
		}
		
		header( "Content-type: ".$getimagesize['mime']);
		echo file_get_contents($file_path);exit;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>