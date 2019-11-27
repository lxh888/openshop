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



namespace eapie\source\request\user;
use eapie\main;
use eapie\error;
class admin_money extends \eapie\source\request\user {
	
	
	/*赠送收益操作*/
	
		
	/**
	 * 根据检索条件获取用户钱包总余额
	 * 
	 * USERADMINMONEYTOTAL
	 * {"class":"user/admin_money","method":"api_total"}
	 * 
	 * @param	array		$search
	 * @return	int
	 */
	public function api_total($search = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MONEY_READ);
		
		$where = array();
		if(!empty($search)){
			if( isset($search['user_id']) && is_string($search['user_id']) ){
				$where[] = array('[and] um.user_id=[+]', $search['user_id']);
			}
			
			if (isset($search['user_nickname']) && is_string($search['user_nickname'])) {
				$user_data = object(parent::TABLE_USER)->find_like_nickname($search['user_nickname']);
				if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
				$where[] = array('[and] um.user_id=[+]', $user_id);
            }
			
			if (isset($search['user_phone']) && is_string($search['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($search['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $where[] = array('[and] um.user_id=[+]', $user_id);
            }
			
			if (isset($search['min_value']) && 
			is_numeric($search['min_value']) && 
			(int)$search['min_value'] >= 0 ) {
				$where[] = array('um.user_credit_value > [-]', ( (int)$search['min_value'] - 1) );
            }
			
			if (isset($search['max_value']) && 
			is_numeric($search['max_value']) && 
			(int)$search['max_value'] >= 0 ) {
				$where[] = array('um.user_credit_value < [-]', ( (int)$search['max_value'] + 1) );
            }
			
		}
		
		return object(parent::TABLE_USER_MONEY)->find_now_where_sum($where);
	}
	
	
	
		
	/**
	 * 获取数据列表
	 * 需要判断浏览权限
	 * 
	 * $request = array(
	 * 	'search' => array(),//搜索、筛选
	 * 	'sort' => array(),//排序
	 *  'size' => 0,//每页的条数
	 * 	'page' => 0, //当前页数，如果是等于 all 那么则查询所有
	 *  'start' => 0, //开始的位置，如果存在，则page无效
	 * );
	 * limit的分页算法是：当前页数-1 * page_size
	 * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
	 * 
	 * 返回的数据：
	 * $data = array(
	 * 	'row_count' => //数据总条数
	 * 	'limit_count' => //已取出条数
	 * 	'page_size' => //每页的条数
	 *  'page_count' => //总页数
	 * 	'data' => //数据
	 * );
	 * 
	 * USERADMINMONEYLIST
	 * {"class":"user/admin_money","method":"api_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MONEY_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'user_id_desc' => array('user_id', true),
			'user_id_asc' => array('user_id', false),
			
			'user_nickname_desc' => array('user_nickname', true),
			'user_nickname_asc' => array('user_nickname', false),
			
			'value_desc' => array('user_money_value', true),
			'value_asc' =>  array('user_money_value', false),
			
			'time_desc' => array('user_money_time', true),
			'time_asc' => array('user_money_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('user_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['user_id']) && is_string($data['search']['user_id']) ){
				$config["where"][] = array('[and] u.user_id=[+]', $data['search']['user_id']);
			}
			
			if (isset($data['search']['user_nickname']) && is_string($data['search']['user_nickname'])) {
                $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $data['search']['user_nickname']);
            }
			if (isset($data['search']['user_phone']) && is_string($data['search']['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] u.user_id=[+]', $user_id);
            }
			
			if (isset($data['search']['min_value']) && 
			is_numeric($data['search']['min_value']) && 
			(int)$data['search']['min_value'] >= 0 ) {
				$sql_join_now_value = object(parent::TABLE_USER_MONEY)->sql_join_user_now_value("u");
                $config['where'][] = array('[and] ('.$sql_join_now_value.') > []', ( (int)$data['search']['min_value'] - 1) );
            }
			
			if (isset($data['search']['max_value']) && 
			is_numeric($data['search']['max_value']) && 
			(int)$data['search']['max_value'] >= 0 ) {
				$sql_join_now_value = object(parent::TABLE_USER_MONEY)->sql_join_user_now_value("u");
                $config['where'][] = array('[and] ('.$sql_join_now_value.') < []', ( (int)$data['search']['max_value'] + 1) );
            }
			
		}
		
		// return $config;
		return object(parent::TABLE_USER_MONEY)->select_user_page($config);
	}
	
	
	
	
		
	/**
	 * 获取流水号列表
	 * 
	 * USERADMINMONEYSERIALLIST
	 * {"class":"user/admin_money","method":"api_serial_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_serial_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MONEY_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'user_id_desc' => array('user_id', true),
			'user_id_asc' => array('user_id', false),
			
			'user_nickname_desc' => array('user_nickname', true),
			'user_nickname_asc' => array('user_nickname', false),
			
			'type_desc' => array('user_money_type', true),
			'type_asc' => array('user_money_type', false),
			
			'time_desc' => array('user_money_time', true),
			'time_asc' =>  array('user_money_time', false),
			
			'order_action_user_nickname_desc' => array('order_action_user_nickname', true),
			'order_action_user_nickname_asc' => array('order_action_user_nickname', false),
			
			'order_action_user_phone_verify_list_desc' => array('order_action_user_phone_verify_list', true),
			'order_action_user_phone_verify_list_asc' => array('order_action_user_phone_verify_list', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('user_money_id', false);
		$type_list = object(parent::TABLE_ORDER)->get_type();
		
		if(!empty($data['search'])){
			if( isset($data['search']['user_id']) && is_string($data['search']['user_id']) ){
				$config["where"][] = array('[and] u.user_id=[+]', $data['search']['user_id']);
			}
			
			if (isset($data['search']['user_nickname']) && is_string($data['search']['user_nickname'])) {
                $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $data['search']['user_nickname']);
            }
			if (isset($data['search']['user_phone']) && is_string($data['search']['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] u.user_id=[+]', $user_id);
            }
			
			
			
			if (isset($data['search']['order_action_user_id']) && is_string($data['search']['order_action_user_id'])) {
				//数据过滤
				$data['search']['order_action_user_id'] = cmd(array($data['search']['order_action_user_id']), 'str addslashes');
                $config['where'][] = array('[and] (plus_oau.user_id="'.$data['search']['order_action_user_id'].'" OR minus_oau.user_id="'.$data['search']['order_action_user_id'].'")', NULL, true);
            }
			if (isset($data['search']['order_action_user_nickname']) && is_string($data['search']['order_action_user_nickname'])) {
				$data['search']['order_action_user_nickname'] = cmd(array($data['search']['order_action_user_nickname']), 'str addslashes');
				$config['where'][] = array('[and] (plus_oau.user_nickname LIKE "%'.$data['search']['order_action_user_nickname'].'%" OR minus_oau.user_nickname LIKE "%'.$data['search']['order_action_user_nickname'].'%")', NULL, true);
            }
			if (isset($data['search']['order_action_user_phone']) && is_string($data['search']['order_action_user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['order_action_user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
        		$config['where'][] = array('[and] (plus_oau.user_id="'.$user_id.'" OR minus_oau.user_id="'.$user_id.'")', NULL, true);
            }
			
			
			
			if( isset($data['search']['user_money_id']) && is_string($data['search']['user_money_id']) ){
				$config["where"][] = array('[and] um.user_money_id=[+]', $data['search']['user_money_id']);
			}
			if( isset($data['search']['type']) && is_string($data['search']['type']) ){
				$config["where"][] = array('[and] um.user_money_type=[+]', $data['search']['type']);
			}
			if( isset($data['search']['type_name']) && is_string($data['search']['type_name']) && !empty($type_list) ){
				foreach($type_list as $type_k => $type_v){
					if(mb_strstr($type_v, $data['search']['type_name']) !== false){
						$config["where"][] = array('[and] um.user_money_type=[+]', $type_k);
						break;
					}
				}
			}
		
		}
		$data_list = object(parent::TABLE_USER_MONEY)->select_serial_page($config);
		
		if( !empty($data_list["data"]) && !empty($type_list) ){
			foreach($data_list["data"] as $key => $value){
				if( isset($type_list[$value["user_money_type"]]) ){
					$data_list["data"][$key]["user_money_type_name"] = $type_list[$value["user_money_type"]];
				}
			}
		}
		
		return $data_list;
	}
	
	
	
	
	
	
	
	
	/**
	 * 添加/减少用户钱包余额
	 * 
	 * USERADMINMONEYEDIT
	 * {"class":"user/admin_money","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MONEY_EDIT);
		object(parent::ERROR)->check( $data, 'user_id', parent::TABLE_USER, array('args', 'exists_id'));
		object(parent::ERROR)->check( $data, 'value', parent::TABLE_ORDER, array('args') ,'money_fen');
		object(parent::ERROR)->check( $data, 'type', parent::TABLE_ORDER, array('args'));
		object(parent::ERROR)->check( $data, 'comment', parent::TABLE_ORDER, array('args') );
		
		//备注信息
		$data["comment"] = !empty($data["comment"])? "管理员操作 - ".$data["comment"] : "管理员操作";
		
		if($data['type'] == parent::TRANSACTION_TYPE_ADMIN_MINUS){
			//如果是人工减少
			//获取商户的库存积分  判断要赠送的积分
			$user_money_data = object(parent::TABLE_USER_MONEY)->find_now_data($data['user_id']);
			
			if( empty($user_money_data["user_money_value"]) 
			|| ($user_money_data["user_money_value"] - $data['value']) < 0 ){
				throw new error('该用户钱包的余额不足');
			}
			
			
		}else
		if($data['type'] == parent::TRANSACTION_TYPE_ADMIN_PLUS){
			$user_money_data = array();
		}else{
			throw new error('交易类型异常');
		}
		
		
		$bool = object(parent::TABLE_USER_MONEY)->insert_admin(array(
			"admin_user_id" => $_SESSION["user_id"],
			"user_id" => $data['user_id'],
			"comment" => $data['comment'],
			"value" => $data['value'],
			"type" => $data['type'],
			"user_money" => $user_money_data,
		));
		
		if( empty($bool) ){
			throw new error('操作失败');
		}else{
			object(parent::TABLE_ADMIN_LOG)->insert($data, $data);
			return $bool;
		}
		
	}
	
	

	
	
	
	
	
		
	
	/**
	 *  ----- Mr.Zhao ----- 2019.06.13 -----
	 * 
	 * 用户预付费Excel导出
	 * 
	 * api:USERADMINMONEYEXCEL
	 * 
	 * req:{
	 * 	phone  				[str]   [可选]  [用户的电话号码，不填的时候查询所有用户]
	 * 	start_time			[str]	[可选]	[要统计的开始时间，不填的时候按当月第一天计算，示例值'2018-06-04 04:04:03']
	 * 	end_time			[str]	[可选]	[结束时间，不填的时候按当前时间计算，示例值'2019-06-04 04:04:03']
	 * 	balance_is_zero		[bool]	[可选]	[期末余额为零不显示，true不显示，false显示，默认值为false]
	 * 	occurrence_is_zero	[bool]	[可选]	[当期发生额为零不显示，true不显示，false显示，默认值为false]
	 * }
	 * 
	 * {"class":"user/admin_money","method":"api_excel"}
	 * 
	 * @param	array
	 * @return	file
	 */
	public function api_excel($data = array())
	{

		// 检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MONEY_EXCEL);

		// $data['start_time'] = "2019-06-12 04:04:03";

		$start_time = mktime(0, 0, 0, date('m'), 1, date('Y'));;
		$end_time = time();
		if (isset($data['start_time'])) {
			$start_time = cmd(array($data['start_time']), "time mktime");
		}
		if (isset($data['end_time'])) {
			$end_time = cmd(array($data['end_time']), "time mktime");
		}

		// 根据user_id排序，取1000条数据
		$money_config = array(
			'orderby' => array(
				array('user_money_time', true),
				array('user_id'),
			),
			'where' => array(),
			// 'limit' => array(0, 1000),
		);

		// 根据用户手机号进行筛选
		if (isset($data['phone']) && is_string($data['phone'])) {
			$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['phone']);
			if (empty($user_data['user_id'])) {
				throw new error('没有该用户数据');
			}
			$money_config['where'][] = array('user_id=[+]', $user_data['user_id']);
		}

		// 根据时间段进行筛选

		// if (isset($data['start_time']) && isset($data['end_time'])) {
		// 	$money_config['where'][] = array("[and] user_money_time between []", $start_time);
		// 	$money_config['where'][] = array("[and] []", $end_time);
		// }
		$money_config['where'][] = array("[and] user_money_time between []", $start_time);
		$money_config['where'][] = array("[and] []", $end_time);

		// 查询数据
		$user_money = object(parent::TABLE_USER_MONEY)->select($money_config);
		if (empty($user_money)) {
			throw new error('当前条件下数据为空');
		}


		// 所有用户ID
		$user_ids = array();
		foreach ($user_money as $v) {
			if (!in_array($v['user_id'], $user_ids)) {
				$user_ids[] = $v['user_id'];
			}
		}


		//获取分类数据
		$in_string = "\"" . implode("\",\"", $user_ids) . "\"";
		$phone_config = array(
			'where' => array(),
			'select' => array(
				'distinct( user_id ) as user_id',
				'user_phone_id as phone',
			),
			'orderby' => array(
				array('user_phone_type', true),
				array('user_phone_id')
			)
		);
		$phone_config['where'][] = array("[and] user_id IN([-])", $in_string, true); //是不加单引号并且强制不过滤
		$phone_config['where'][] = array("[and] user_phone_state=1");

		// 用户手机号码
		$user_phone_data = object(parent::TABLE_USER_PHONE)->select($phone_config);

		$user_config = array(
			'where' => array(),
			'select' => array(
				'distinct( user_id ) as user_id',
				'user_nickname as nickname',
			)
		);
		$user_config['where'][] = array("[and] user_id IN([-])", $in_string, true); //是不加单引号并且强制不过滤

		// 用户数据
		$user_datas = object(parent::TABLE_USER)->select($user_config);


		// 根据user_id进行分组
		$user_datas_group = array();
		foreach ($user_money as $v) {
			$user_datas_group[$v['user_id']][] = $v;
		}

		// 计算当期发生额和期末余额
		foreach ($user_datas as &$u) {
			// 当期发生额
			$u['occurrence'] = 0;
			$u['time_between'] = date("Y年m月d日", $start_time) . '——' . date("Y年m月d日", $end_time);

			foreach ($user_phone_data as $p) {
				if ($p['user_id'] == $u['user_id']) {
					$u['phone'] = $p['phone'];
					break;
				}
			}

			foreach ($user_datas_group as $k => $val) {
				if ($k == $u['user_id']) {
					foreach ($val as $k1 => $v1) {
						if ($k1 == 0) {
							// 期末余额
							$u['balance'] = $v1['user_money_value'];
						}
						if ($v1['user_money_plus'] != 0) {
							$u['occurrence'] += $v1['user_money_plus'];
						} else {
							$u['occurrence'] -= $v1['user_money_minus'];
						}
					}
				}
			}
		}

		// 不显示期末余额为零的数据
		if (isset($data['balance_is_zero']) && $data['balance_is_zero'] == true) {
			foreach ($user_datas as $key => $value) {
				if ($value['balance'] == 0) {
					unset($user_datas[$key]);
				}
			}
		}

		// 不显示当期发生额为零的数据
		if (isset($data['occurrence_is_zero']) && $data['occurrence_is_zero'] == true) {
			foreach ($user_datas as $key => $value) {
				if ($value['occurrence'] == 0) {
					unset($user_datas[$key]);
				}
			}
		}
		if (empty($user_datas)) {
			throw new error('当前条件下数据为空');
		}


		// 把用户数据整理成索引数组，便于Excel数据处理
		$excel_datas = array();
		foreach ($user_datas as $key => $value) {
			$excel_datas[$key] = array();
			$excel_datas[$key][] = $key;
			$excel_datas[$key][] = $value['nickname'];
			$excel_datas[$key][] = !empty($value['phone'])?' ' . $value['phone']:'';
			$excel_datas[$key][] = $value['time_between'];
			$excel_datas[$key][] = $value['occurrence'] / 100;
			$excel_datas[$key][] = $value['balance'] / 100;
			// $excel_datas[$key][] = number_format($value['occurrence']/100,2);
			// $excel_datas[$key][] = number_format($value['balance']/100,2);
		}

		// 表格标题
		$title = '预付款';
		// 文件名称
		$fileName = $title . date('_YmdHis');
		// 表头
		$cellName = array('序号', '姓名', '手机', '统计时段', '当期发生额(元)', '期末余额(元)');
		// 表头有几行占用
		$topNumber = 2;

		$cellKey = array(
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
			'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
			'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM',
			'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'
		);



		object(parent::PLUGIN_EXCEL)->output($fileName, $title, function ($obj) use ($excel_datas, $title, $cellName, $topNumber, $cellKey) {


			//处理表头标题
			$obj->getActiveSheet()->mergeCells('A1:' . $cellKey[count($cellName) - 1] . '1'); //合并单元格（如果要拆分单元格是需要先合并再拆分的，否则程序会报错）
			$obj->setActiveSheetIndex(0)->setCellValue('A1', $title);
			$obj->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$obj->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
			$obj->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$obj->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

			// $obj->getActiveSheet()->getColumnDimension('A')->setWidth(50);

			//处理表头
			foreach ($cellName as $k => $v) {
				$obj->setActiveSheetIndex(0)->setCellValue($cellKey[$k] . $topNumber, $v); //设置表头数据
				$obj->getActiveSheet()->freezePane($cellKey[$k + 1] . ($topNumber + 1)); //冻结窗口
				$obj->getActiveSheet()->getStyle($cellKey[$k] . $topNumber)->getFont()->setBold(true); //设置是否加粗
				$obj->getActiveSheet()->getStyle($cellKey[$k] . $topNumber)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直居中
				if ($k > 0) {
					$obj->getActiveSheet()->getColumnDimension($cellKey[$k])->setWidth(16);		//设置表格宽度
				}
				// $obj->getActiveSheet()->getColumnDimension($cellKey[$k])->setAutoSize(true);   //内容自适应
			}

			foreach ($excel_datas as $k => $excel_data) {
				foreach ($excel_data as $k1 => $val) {
					$obj->getActiveSheet()->setCellValue($cellKey[$k1] . ($k + 1 + $topNumber), $val);
					if (strlen($val) > 16) {
						$obj->getActiveSheet()->getColumnDimension($cellKey[$k1])->setWidth(strlen($val));		//设置表格宽度
					}
				}
			}
		});



		exit;
	}
	

	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>