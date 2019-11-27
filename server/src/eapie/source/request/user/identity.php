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

// 用户身份认证
class identity extends \eapie\source\request\user
{

    /**
     * 查询当前用户的身份认证信息
     *
     * api: [旧] USERSELFIDENTITY
     * api: USERIDENTITYSELF
     * req: null
     * 
     * @return {
     *  real_name       [str] [真实姓名]
     *  card_number     [str] [身份证号码]
     *  card_address    [str] [身份证住址]
     * }
     */
    public function api_self()
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //查询数据
        $data = object(parent::TABLE_USER_IDENTITY)->find($_SESSION['user_id']);
		
        if (empty($data))
            return array();

		if( !object(parent::TABLE_USER_IDENTITY)->check_state($_SESSION['user_id'], $data['user_identity_update_time']) ){
			return array();
		}
		
        //白名单
        $whitelist = array(
            'user_identity_real_name',
            'user_identity_card_number',
            'user_identity_card_address',
            'user_identity_json',
            'user_identity_insert_time',
            'user_identity_update_time',
        );
        $output = cmd(array($data, $whitelist), 'arr whitelist');
        $output = cmd(array('user_identity_', $output, true),  'arr key_prefix');

        //格式化数据
        if ($output) {
            // $output['real_name'] = '*'.mb_substr($output['real_name'], 1);
            // $output['card_number'] = substr($output['card_number'], 0 , 1).'****************'.substr($output['card_number'], -1 , 1);
            // $output['card_address'] = mb_substr($output['card_address'], 0 , 1).'****'.mb_substr($output['card_address'], -1 , 1);
            $output['json'] = cmd(array($output['json']), 'json decode');
            $output['insert_time'] = date('Y-m-d H:i:s', $output['insert_time']);
            $output['update_time'] = date('Y-m-d H:i:s', $output['update_time']);
        }

        return $output;
    }

    /**
     * 添加
     * 
	 * 旧：USERSELFIDENTITYADD
     * api: USERIDENTITYSELFADD
     * req: {
     *  real_name       [str] [必填] [真实姓名]
     *  card_number     [str] [必填] [身份证号码]
     *  card_address    [str] [必填] [身份证地址信息]
     *  province        [str] [必填] [省]
     *  city            [str] [必填] [市]
     *  district        [str] [必填] [区]
     * }
     * 
     * @return string [用户ID]
     */
    public function api_self_add($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //校验数据
        object(parent::ERROR)->check($input, 'real_name', parent::TABLE_USER_IDENTITY, array('args'));
        object(parent::ERROR)->check($input, 'card_number', parent::TABLE_USER_IDENTITY, array('args'));
        object(parent::ERROR)->check($input, 'card_address', parent::TABLE_USER_IDENTITY, array('args'));
        object(parent::ERROR)->check($input, 'province', parent::TABLE_USER_IDENTITY, array('args'), 'user_identity_card_province');
        object(parent::ERROR)->check($input, 'city', parent::TABLE_USER_IDENTITY, array('args'), 'user_identity_card_city');
        object(parent::ERROR)->check($input, 'district', parent::TABLE_USER_IDENTITY, array('args'), 'user_identity_card_district');
        if (isset($input['json'])) {
            $json = $input['json'];
            if (!is_array($json))
                throw new error('json参数不合法');
        } else {
            $json = array();
        }

        //查询数据
        $data = object(parent::TABLE_USER_IDENTITY)->find($_SESSION['user_id']);
        if (empty($data))
            throw new error('请上传身份证');
        if (empty($data['user_identity_front_image_id']))
            throw new error('请上传身份证正面');
        if (empty($data['user_identity_back_image_id']))
            throw new error('请上传身份证反面');
        if ($data['user_identity_state'] == 1 || $data['user_identity_state'] == 2)
            throw new error('已认证或认证中');

        //格式化数据
        $update_data = array();
        $update_data['user_identity_real_name'] = $input['real_name'];
        $update_data['user_identity_card_number'] = $input['card_number'];
        $update_data['user_identity_card_address'] = $input['card_address'];
        $update_data['user_identity_card_province'] = $input['province'];
        $update_data['user_identity_card_city'] = $input['city'];
        $update_data['user_identity_card_district'] = $input['district'];
        $update_data['user_identity_json'] = cmd(array($json), 'json encode');
        $update_data['user_identity_state'] = 2;
		$update_data['user_identity_update_time'] = time();

        //检测用户认证配置
        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('user_identity'), true);
        if (object(parent::TABLE_USER_IDENTITY)->check_config_auto_state($config))
            $update_data['user_identity_state'] = 1;

        //更新数据
        if (object(parent::TABLE_USER_IDENTITY)->update(array(array('user_id=[+]', $_SESSION['user_id'])), $update_data)) {
            return $_SESSION['user_id'];
        } else {
            throw new error('认证失败');
        }
    }

    /**
     * 编辑
     * api: USERIDENTITYSELFEDIT
     * @return string [用户ID]
     */
    public function api_self_edit($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //校验数据
        object(parent::ERROR)->check($input, 'real_name', parent::TABLE_USER_IDENTITY, array('args'));
        object(parent::ERROR)->check($input, 'card_number', parent::TABLE_USER_IDENTITY, array('args'));
        object(parent::ERROR)->check($input, 'card_address', parent::TABLE_USER_IDENTITY, array('args'));
        object(parent::ERROR)->check($input, 'province', parent::TABLE_USER_IDENTITY, array('args'), 'user_identity_card_province');
        object(parent::ERROR)->check($input, 'city', parent::TABLE_USER_IDENTITY, array('args'), 'user_identity_card_city');
        object(parent::ERROR)->check($input, 'district', parent::TABLE_USER_IDENTITY, array('args'), 'user_identity_card_district');

        if (empty($input['gender']) || $input['gender'] != 2) {
            $input['gender'] = 1;
        }

        if (isset($input['json'])) {
            $json = $input['json'];
            if (!is_array($json))
                throw new error('json参数不合法');
        } else {
            $json = array();
        }

        //格式化数据
        $update_data = array();
        $update_data['user_identity_real_name'] = $input['real_name'];
        $update_data['user_identity_gender'] = $input['gender'];
        $update_data['user_identity_card_number'] = $input['card_number'];
        $update_data['user_identity_card_address'] = $input['card_address'];
        $update_data['user_identity_card_province'] = $input['province'];
        $update_data['user_identity_card_city'] = $input['city'];
        $update_data['user_identity_card_district'] = $input['district'];
        $update_data['user_identity_json'] = cmd(array($json), 'json encode');
        $update_data['user_identity_state'] = 2;
        $update_data['user_identity_update_time'] = time();

        // 检测用户认证配置
        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('user_identity'), true);
        if (object(parent::TABLE_USER_IDENTITY)->check_config_auto_state($config))
            $update_data['user_identity_state'] = 1;

        // 更新条件
        $call_where = array(
            array('user_id = [+]', $_SESSION['user_id']),
        );

        //更新数据
        if (object(parent::TABLE_USER_IDENTITY)->update($call_where, $update_data)) {
            return $_SESSION['user_id'];
        } else {
            throw new error('操作失败');
        }
    }


	/**
     * 获取当前用户的身份认证编辑信息
	 * 
     * USERIDENTITYSELFEDITGET
     * {"class":"user/identity","method":"api_self_edit_get"}
     * 
	 * 响应参数：
	 * {"real_name":"真实姓名","card_number":"身份证号码","card_address":"身份证地址信息","json":"其他信息","insert_time":"插入时间","update_time":"更新时间","front_image_id":"身份证正面。图片ID","back_image_id":"身份证背面。图片ID","waist_image_id":"身份证半面。图片ID","other_image_id":"其他图片。图片ID"}
	 * 
     * @param  void
     * @return array
     */
    public function api_self_edit_get(){
    	//检测登录
        object(parent::REQUEST_USER)->check();
		$data = object(parent::TABLE_USER_IDENTITY)->find( $_SESSION['user_id'] );
		if( empty($data) ){
			throw new error('没有认证信息的数据');
		}
		if( $data['user_identity_state'] != 3 ){
			throw new error('当前认证信息不是编辑状态');
		}
        
		return array(
			'real_name' => $data['user_identity_real_name'],
			'card_number' => $data['user_identity_card_number'],
			'card_address' => $data['user_identity_card_address'],
			'json' => cmd(array($data['user_identity_json']), 'json decode'),
			'insert_time' => date('Y-m-d H:i:s', $data['user_identity_insert_time']),
			'update_time' => date('Y-m-d H:i:s', $data['user_identity_update_time']),
			'front_image_id' => $data['user_identity_front_image_id'],
			'back_image_id' => $data['user_identity_back_image_id'],
			'waist_image_id' => $data['user_identity_waist_image_id'],
			'other_image_id' => $data['user_identity_other_image_id'],
		);
    }
	
	



    /**
     * 上传身份证图片
     *
     * api: [旧] USERSELFIDENTITYUPLOAD
     * api: USERIDENTITYSELFUPLOAD
     * req: {
     *  type [str] [必填] [图片类型：front（正面），back（反面），waist（身份证半面），other（其它）]
     * }
     * 
     * @param  array  $input [description]
     * @return str [图片ID]
     */
    public function api_self_upload($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        if (empty($input['type']))
            throw new error('缺少图片类型');

        //检测图片类型
        switch ($input['type']) {
            case 'front':
                $type = 'user_identity_front_image_id';
                break;
            case 'back':
                $type = 'user_identity_back_image_id';
                break;
            case 'waist':
                $type = 'user_identity_waist_image_id';
                break;
            case 'other':
                $type = 'user_identity_other_image_id';
                break;
            default:
                throw new error('图片类型不合法');
                break;
        }

        //查询原始数据
        $original = object(parent::TABLE_USER_IDENTITY)->find($_SESSION['user_id']);

        //是否已认证或认证中
        if ($original && ($original['user_identity_state'] == 1 || $original['user_identity_state'] == 2))
            throw new error('已认证或认证中');

        //保存图片到七牛云
        $response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();
        $image_id = $response['image_id'];

        //没数据
        if (empty($original)) {
            $insert_data = array();
            $insert_data[$type] = $image_id;
            $insert_data['user_id'] = $_SESSION['user_id'];
            $insert_data['user_identity_state'] = 3;
            $insert_data['user_identity_insert_time'] = time();
            $insert_data['user_identity_update_time'] = time();
            object(parent::TABLE_USER_IDENTITY)->insert($insert_data);
            return $image_id;
        }

        //更新数据
        $update_data = array($type => $image_id);
        $update_where = array(array('user_id=[+]', $_SESSION['user_id']));

        if (object(parent::TABLE_USER_IDENTITY)->update($update_where, $update_data)) {
            //删除旧图片
            if (!empty($original[$type])) {
                $response['image_id'] = $original[$type];
                object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
            }

            return $image_id;
        } else {
            object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
            throw new error ('更新失败');
        }
    }


    /**
     * 查询当前用户是否已认证
     *
     * api: [旧] USERSELFIDENTITYVERIFY
     * api: USERIDENTITYSELFVERIFYCHECK
     * req: null
     * 
     * @return boolean
     */
    public function api_self_verify_check()
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //查询数据
        $data = object(parent::TABLE_USER_IDENTITY)->find($_SESSION['user_id']);
        if (empty($data) || $data['user_identity_state'] != 1)
            return false;

		return object(parent::TABLE_USER_IDENTITY)->check_state($_SESSION['user_id'], $data['user_identity_update_time']);
    }


    /**
     * 查询当前用户的认证状态
     *
     * api: USERIDENTITYSELFSTATE
     * req: null
     * 
     * @return int [0未通过审核，1通过审核，2待审核，3编辑中, 4不存在]
     */
    public function api_self_state()
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //查询数据
        $data = object(parent::TABLE_USER_IDENTITY)->find($_SESSION['user_id']);
        return isset($data['user_identity_state'])? intval($data['user_identity_state']) : 4;
    }


    // ==========================================


    /**
     * 更新认证
     * @param  [arr] $original [原始数据]
     * @param  [arr] $present  [当前数据]
     * @return [str] [用户ID]
     */
    private function _update($original = array(), $present = array())
    {
        //认证失败
        if ($original['user_identity_state'] == 0) {
            $update_data['user_identity_real_name'] = $present['real_name'];
            $update_data['user_identity_card_number'] = $present['card_number'];
            $update_data['user_identity_card_address'] = $present['card_address'];
        } else {
            //认证过期
            if ($present['real_name'] !== $original['user_identity_real_name']
                || $present['card_number'] !== $original['user_identity_card_number']
                || $present['card_address'] !== $original['user_identity_card_address']
            ){
                throw new error('与上次认证信息不一致');
            }
        }
        $update_data['user_identity_state'] = 2;

        if (object(parent::TABLE_USER_IDENTITY)->update(array(array('user_id=[+]', $_SESSION['user_id'])), $update_data))
            return $_SESSION['user_id'];
        else
            throw new error ('认证失败');
    }


}