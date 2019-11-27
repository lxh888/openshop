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



namespace eapie\source\table\user;

use eapie\main;

//用户身份认证
class user_identity extends main
{


    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(__CLASS__, "user");


    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
        'real_name' => array(
            'args'=> array(
                'exist' => array('缺少真实姓名参数'),
                'echo'  => array('真实姓名的数据类型不合法'),
                '!null' => array('真实姓名不能为空'),
            )
        ),
        'user_identity_gender' => array(
            'args'=> array(
                'exist' => array('缺少性别参数'),
                'match' => array('/^(1|2)$/', '性别不合法'),
            )
        ),
        'card_number' => array(
            'args'=> array(
                'exist' => array('缺少身份证号码参数'),
                'echo'  => array('身份证号码的数据类型不合法'),
                '!null' => array('身份证号码不能为空'),
                'match' => array('/(^\d{18}$)|(^\d{17}(\d|X|x)$)/', '身份证号码格式错误'),
            )
        ),
        'card_address' => array(
            'args'=> array(
                'exist' => array('缺少身份证住址参数'),
                'echo'  => array('身份证住址的数据类型不合法'),
                '!null' => array('身份证住址不能为空'),
            )
        ),
        'user_identity_state' => array(
            'args' => array(
                'echo' => array('状态的数据类型不合法'),
                'match' => array('/^[0123]{1}$/', '状态值必须是0、1、2、3'),//状态。0审核失败；1售卖中；2待审核；3编辑中
            ),
        ),
        'user_identity_card_province' => array(
            'args'=> array(
                'exist' => array('缺少省份参数'),
                'echo'  => array('省份的数据类型不合法'),
                '!null' => array('省份不能为空'),
            )
        ),
        'user_identity_card_city' => array(
            'args'=> array(
                'exist' => array('缺少城市参数'),
                'echo'  => array('城市的数据类型不合法'),
                '!null' => array('城市不能为空'),
            )
        ),
        'user_identity_card_district' => array(
            'args'=> array(
                'exist' => array('缺少地区参数'),
                'echo'  => array('地区的数据类型不合法'),
                '!null' => array('地区不能为空'),
            )
        ),
    );


    //===========================================
    // 操作
    //===========================================


    public function insert($input = array())
    {
        $res = db(parent::DB_APPLICATION_ID)
            ->table('user_identity')
            ->insert($input);

        //清理当前项目缓存
        if ($res)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $res;
    }


    public function update($where = array(), $data = array(), $call_data = array())
    {
        if (empty($data))
            return false;

        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_identity')
            ->call('where', $where)
            ->call('data', $call_data)
            ->update($data);

        //清理当前项目缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    public function remove($user_id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_identity')
            ->where(array('user_id=[+]', $user_id))
            ->delete();

        //清理当前项目缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    //===========================================
    // 查询
    //===========================================


    /**
     * 查——某个用户的认证数据
     * @param  [str] $user_id [用户ID]
     * @return array
     */
    public function find($user_id = '')
    {
        if (empty($user_id))
            return false;

        return object(parent::CACHE)->data(__CLASS__, __METHOD__,
            array($user_id),
            function ($user_id) {
                return db(parent::DB_APPLICATION_ID)
                    ->table('user_identity')
                    ->where(array('user_id=[+]', $user_id))
                    ->find();
            }
        );
    }


    /**
     * 获取所有的分页数据
     * 
     * $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认获取10条
     * );
     * 
     * limit的分页算法是：当前页数-1 * page_size
     * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
     * 
     * 返回的数据：
     * $data = array(
     *  'row_count' => //数据总条数
     *  'limit_count' => //已取出条数
     *  'page_size' => //每页的条数
     *  'page_count' => //总页数
     *  'page_now' => //当前页数
     *  'data' => //数据
     * );
     * 
     * @param   array   $config     配置信息
     * @return  array
     */
    public function select_page($config = array())
    {
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
            
            //用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'LEFT',
                'on' => 'u.user_id = ui.user_id'
            );
            
            
            //获取总条数
            $total_count = db(parent::DB_APPLICATION_ID)
            ->table('user_identity ui')
            ->joinon($user)
            ->call('where', $call_where)
            ->find('count(distinct ui.user_id) as count');
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count']/$data['page_size']);
                    $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;//当前页数
                }
            }

            //图片数据
            $front_image = array(
                'table' => 'image front_i',
                'type' => 'LEFT',
                'on' => 'front_i.image_id = ui.user_identity_front_image_id'
            );
            $back_image = array(
                'table' => 'image back_i',
                'type' => 'LEFT',
                'on' => 'back_i.image_id = ui.user_identity_back_image_id'
            );
            $waist_image = array(
                'table' => 'image waist_i',
                'type' => 'LEFT',
                'on' => 'waist_i.image_id = ui.user_identity_waist_image_id'
            );
            $other_image = array(
                'table' => 'image other_i',
                'type' => 'LEFT',
                'on' => 'other_i.image_id = ui.user_identity_other_image_id'
            );
            
            if( empty($select) ){
                $user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
                $select = array(
                    'u.user_parent_id',
                    'u.user_nickname',
                    'u.user_logo_image_id',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
                    'ui.*',
                    "front_i.image_width as user_identity_front_image_width",
                    "front_i.image_height as user_identity_front_image_height",
                    "back_i.image_width as user_identity_back_image_width",
                    "back_i.image_height as user_identity_back_image_height",
                    "waist_i.image_width as user_identity_waist_image_width",
                    "waist_i.image_height as user_identity_waist_image_height",
                    "other_i.image_width as user_identity_other_image_width",
                    "other_i.image_height as user_identity_other_image_height",
                );
            }
            
            //查询数据
            $data['data'] = db(parent::DB_APPLICATION_ID)
            ->table('user_identity ui')
            ->joinon($user, $front_image, $back_image, $waist_image, $other_image)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);

            return $data;
        });
    }


    //===========================================
    // 检测
    //===========================================


    /**
     * 检测某用户是否已认证
     * 
     * @param  string   $user_id                        用户ID
     * @param  int      $user_identity_update_time      修改时间
     * @param   array   $config                         配置
     * @return boolean
     */
    public function check_state($user_id = '', $user_identity_update_time = 0, $config = array())
    {
        if (empty($user_id))
            return false;

        //时间
        if( empty($user_identity_update_time) ){
            $find = db(parent::DB_APPLICATION_ID)
            ->table('user_identity')
            ->where(array('user_id=[+]', $user_id))
            ->where(array('[and] user_identity_state=1'))
            ->find("user_identity_update_time");
            
            if( !isset($find['user_identity_update_time']) ){
                return false;//说明没有数据
            }
            $user_identity_update_time = $find['user_identity_update_time'];
        }
        
        if( empty($config) || empty($config['expire_time']) ){
            $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("user_identity"), true);
        }
        
        //已经关闭了 有效期的判断
        if( empty($config['expire_state']) )    return true;
        //有效期时间秒不存在或者不合法
        if( !isset($config['expire_time']) || !is_numeric($config['expire_time']) ) return false;
         //判断是否过期
        $interval = time() - $user_identity_update_time;
        if ( $interval > $config['expire_time'] ) {
            //更改认证状态
            $this->update(array(array('user_id=[+]', $user_id)), array('user_identity_state' => 2));
            return false;
        }else{
            return true;
        }
        
        
    }


    /**
     * 清理所有用户的认证时间，不合格的设为2
     * 
     * @param   array   $config                         配置
     * @return boolean
     */
    public function update_state_clear( $config = array() ){
        if( empty($config) || empty($config['expire_time']) ){
            $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("user_identity"), true);
        }
        
        //已经关闭了 有效期的判断
        if( empty($config['expire_state']) ){
            return false;
        }   
        
        $bool = db(parent::DB_APPLICATION_ID)
        ->table('user_identity')
        ->where(array( '('.time().'- user_identity_update_time) > [-]', $config['expire_time'] ), array('[and] user_identity_state=1'))
        ->update( array('user_identity_state' => 2) /*, function($p){
            printexit($p);
        }*/);

        //清理当前项目缓存
        if( $bool ){
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $bool;
    }


    /**
     * 检测配置——是否自动认证
     * @param  array  $config [用户认证配置]
     * @return bool
     */
    public function check_config_auto_state(array $config)
    {
        if (empty($config['auto_state']))
            return false;

        return $config['auto_state'] == 1;
    }


}