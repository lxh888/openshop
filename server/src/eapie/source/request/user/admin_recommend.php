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
class admin_recommend extends \eapie\source\request\user {
	
	/**
     * E麦商城更新分销关系链缓存
     * USERADMINRECOMMENDUPDATERECOMMEND
	 * {"class":"user/admin_recommend","method":"api_update_recommend"}
     */
    public function api_update_recommend(){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_UPDATE_RECOMMEND);
        $return = array();
		$config = array();
		$config['select'] = array('*');
		$res = array(
			'update_re' => array(),
			'insert_re' => array(),
		);

		$result = object(parent::TABLE_USER)->select($config);

		foreach ($result as $key => $value) {
			$user_invite_data = object(parent::TABLE_USER_RECOMMEND)->find($value['user_id']);
			// 判断是否已经在recommend表
			if ( !empty($user_invite_data) ){
				// 是否已经是会员
				if($user_invite_data['user_recommend_state'] == 0){
					continue;
				} else {
					$has = object(parent::TABLE_USER_RECOMMEND)->_has_admin_id($value['user_id']);
					if($has['has_admin_id']) {
						if( object(parent::TABLE_USER_RECOMMEND)->_is_distribution_admin_id($has['admin_id']) ) {
							$config = array();
							$config['where'] = array(
								array('user_id=[+]',$value['user_id'])
							);
							$data = object(parent::TABLE_USER_RECOMMEND)->select($config);
							foreach ($data as $key => $value) {
								// 更新关系链中的身份
								$user_recommend_updata_where = array( array('user_id=[+]',$value['user_id']) );
								$user_recommend_updata_data = array('user_recommend_state' => 0);
								$res['update_re'][] = array(
									'user_id' => $value['user_id'],
									'result' => object(parent::TABLE_USER_RECOMMEND)->update($user_recommend_updata_where,$user_recommend_updata_data),
								);
							}
							continue;
						} else {
							continue;
						}
					} else {
						continue;
					}
				}
			}
			$is_member = 1;
			// 是否有身份ID
            $has_admin_id = object(parent::TABLE_USER_RECOMMEND)->_has_admin_id($value['user_id']);
            if($has_admin_id['has_admin_id']) {
                // 判断身份ID是否为五级分销身份ID
                $is_distribution = object(parent::TABLE_USER_RECOMMEND)->_is_distribution_admin_id($has_admin_id['admin_id']);
                if($is_distribution){
					$is_member = 0;
                }
            }

			// 需要查询并插入关系链
			$recommend_user_id = object(parent::TABLE_USER)->find_recommend_user_id($value['user_id']);
			// 初始化数据
			$recommend_data = array();
			if($recommend_user_id !== false){
				$time = 0;
				// 循环赋值数组
				while (true) {
					$recommend_data[$time] = array(
						'user_id' => $value['user_id'],
						'user_recommend_id' => object(parent::TABLE_USER_RECOMMEND)->get_unique_id(),
						'user_recommend_user_id' => $recommend_user_id,
						'user_recommend_state' => $is_member,
						'user_recommend_level' => $time + 1,
						'user_recommend_update_time' => time(),
						'user_recommend_insert_time' => time(),
					);
					// 插入数据
					$res['insert_re'][] = array(
						'user_id' => $value['user_id'],
						'result' => object(parent::TABLE_USER_RECOMMEND)->insert($recommend_data[$time]),
					);
					$time++;
					//继续查询下级
					$recommend_user_id = object(parent::TABLE_USER)->find_recommend_user_id($recommend_user_id);
					if($recommend_user_id === false){
						break;
					}
				}
			}
		}
		return $res;
    }
}