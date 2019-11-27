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



namespace eapie\source\plugin\office;
class excel {
	
	
	
	
	public function __construct (){
		require_once __DIR__.'/PHPExcel-1.8.2/PHPExcel.php';
   	}
	
	
	
	
	
	/**
	 * 默认输出
	 * 
	 * @param	string		$filename			文件名称
	 * @param	string		$title				标题		
	 * @param	function	$callback_function	回调函数
	 * @param	array		$properties			资源信息
	 * @param	array		$file_type			输出类型
	 * @return	save php://output
	 */
	public function output($filename, $filetitle, $callback_function = NULL, $properties = array(), $file_type = "xlsx"){
		$default_properties = array(
			"creator" => "PHPExcel",//创建人
			"last_modified_by" => "PHPExcel",//最后修改人
			"title" => "Office PHPExcel Document",//标题
			"subject" => "Office PHPExcel Document",//题目
			"description" => "Office PHPExcel Document",//描述
			"keywords" => "Office PHPExcel Document", //关键字
			"category" => "Test result file",//种类
		); 
		
		if( !empty($properties) && is_array($properties) ){
			$properties = array_merge($default_properties, $properties);
		}else{
			$properties = $default_properties;
		}
		
		//创建新的PHPExcel对象
		$obj = new \PHPExcel();
		// Set document properties
		$obj->getProperties()->setCreator( $properties['creator'] )
							 ->setLastModifiedBy( $properties['last_modified_by'] )
							 ->setTitle( $properties['title'] )
							 ->setSubject( $properties['subject'] )
							 ->setDescription( $properties['description'] )
							 ->setKeywords( $properties['keywords'] )
							 ->setCategory( $properties['category'] );
		
		if(gettype($callback_function) == 'object' && get_class($callback_function) == 'Closure'){
			call_user_func_array ( $callback_function, array( &$obj ) );
		}
		
		// 重命名工作表
		$obj->getActiveSheet()->setTitle($filetitle);
		// 将活动工作表索引设置为第一个工作表，因此Excel将其作为第一个工作表打开
		$obj->setActiveSheetIndex(0);
		
		$file_type = strtolower($file_type);
		if( $file_type == "xlsx" ){
			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');
			
			// If you're serving to IE over SSL, then the following may be needed
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0
			
			$obj_writer = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
			
		}else
		if( $file_type == "xls" ){
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');
			
			// If you're serving to IE over SSL, then the following may be needed
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0
			
			$obj_writer = \PHPExcel_IOFactory::createWriter($obj, 'Excel5');
		}
		
		
		$obj_writer->save('php://output');		
		exit;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>