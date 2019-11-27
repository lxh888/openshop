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



namespace eapie\source\request;
use eapie\main;
use eapie\error;
class shop extends main {
    
    
    const AUTHORITY_GOODS_ADMINISTRATOR = "shop_goods_administrator";//商品管理员资格权限，有该权限则可以管理所有非自己添加的商品，否则没有
    // 管理所有订单
    const AUTHORITY_ORDER_ADMINISTRATOR = 'shop_order_administrator';

    
    const AUTHORITY_GOODS_READ          = "shop_goods_read";
    const AUTHORITY_GOODS_ADD           = "shop_goods_add";
    const AUTHORITY_GOODS_EDIT          = "shop_goods_edit";
    const AUTHORITY_GOODS_TRASH             = "shop_goods_trash";//逻辑删除，丢进回收站
    const AUTHORITY_GOODS_TRASH_READ        = "shop_goods_trash_read";//回收站读取权限
    const AUTHORITY_GOODS_TRASH_RESTORE     = "shop_goods_trash_restore";//回收站还原
    
    
    const AUTHORITY_GOODS_REMOVE            = "shop_goods_remove";//物理删除
    
    
    const AUTHORITY_GOODS_TYPE_EDIT         = "shop_goods_type_edit";//商品分类编辑权限
    
    const AUTHORITY_GOODS_SPU_ADD       = "shop_goods_spu_add";//属性添加权限
    const AUTHORITY_GOODS_SPU_EDIT      = "shop_goods_spu_edit";//属性编辑权限
    const AUTHORITY_GOODS_SPU_REMOVE    = "shop_goods_spu_remove";//属性删除权限
    
    const AUTHORITY_GOODS_SKU_ADD       = "shop_goods_sku_add";//规格添加权限
    const AUTHORITY_GOODS_SKU_EDIT      = "shop_goods_sku_edit";//规格编辑权限
    const AUTHORITY_GOODS_SKU_REMOVE    = "shop_goods_sku_remove";//规格删除权限
    
    
    const AUTHORITY_GOODS_IMAGE_UPLOAD  = "shop_goods_image_upload";//图片上传权限
    const AUTHORITY_GOODS_IMAGE_REMOVE  = "shop_goods_image_remove";//图片删除权限
    const AUTHORITY_GOODS_IMAGE_EDIT    = "shop_goods_image_edit";//图片编辑权限
    
    
    const AUTHORITY_GOODS_FILE_UPLOAD   = "shop_goods_file_upload";//文件上传权限
    const AUTHORITY_GOODS_FILE_REMOVE   = "shop_goods_file_remove";//文件删除权限
    const AUTHORITY_GOODS_FILE_EDIT     = "shop_goods_file_edit";//文件编辑权限
    
    
    const AUTHORITY_GOODS_WHEN_READ     = "shop_goods_when_read";
    const AUTHORITY_GOODS_WHEN_ADD      = "shop_goods_when_add";
    const AUTHORITY_GOODS_WHEN_EDIT     = "shop_goods_when_edit";
    const AUTHORITY_GOODS_WHEN_REMOVE   = "shop_goods_when_remove";//物理删除

    // 拼团商品【green】
    const AUTHORITY_GOODS_GROUP_READ    = 'shop_goods_group_read';
    const AUTHORITY_GOODS_GROUP_ADD     = 'shop_goods_group_add';
    const AUTHORITY_GOODS_GROUP_EDIT    = 'shop_goods_group_edit';
    const AUTHORITY_GOODS_GROUP_REMOVE  = 'shop_goods_group_remove';
    
    const AUTHORITY_GOODS_REGION_READ       = "shop_goods_region_read";
    const AUTHORITY_GOODS_REGION_ADD        = "shop_goods_region_add";
    const AUTHORITY_GOODS_REGION_EDIT       = "shop_goods_region_edit";
    const AUTHORITY_GOODS_REGION_REMOVE     = "shop_goods_region_remove";//物理删除
    
    
    
    // 订单
    const AUTHORITY_ORDER_READ              = "shop_order_read";//读取订单
    const AUTHORITY_ORDER_DETAILS_READ      = "shop_order_details_read";//读取订单详细
    const AUTHORITY_ORDER_TRASH_READ        = "shop_order_trash_read";//读取回收订单
    const AUTHORITY_ORDER_TRASH             = "shop_order_trash";//逻辑删除，丢进回收站
    const AUTHORITY_ORDER_TRASH_RESTORE     = "shop_order_trash_restore";//回收订单还原
    const AUTHORITY_ORDER_AUDIT             = "shop_order_goods_state";  //管理员确认到货
    const AUTHORITY_ORDER_WRITE_OFF         = 'shop_order_write_off';// 核销订单

    // 拼团订单
    const AUTHORITY_ORDER_GROUP_READ        = 'shop_order_group_read';
    const AUTHORITY_ORDER_GROUP_EDIT        = 'shop_order_group_edit';

    const AUTHORITY_ORDER_SHIPPING          = "shop_order_shipping";//订单发货操作
    const AUTHORITY_ORDER_STATE             = "shop_order_state";//订单状态操作
    
    const AUTHORITY_CONFIG_READ     = 'shop_config_read';
    const AUTHORITY_CONFIG_EDIT     = 'shop_config_edit';
    
    
    
    //团购管理
    const AUTHORITY_GROUP_GOODS_READ    = "shop_group_goods_read";
    const AUTHORITY_GROUP_GOODS_ADD     = "shop_group_goods_add";
    const AUTHORITY_GROUP_GOODS_EDIT    = "shop_group_goods_edit";
    const AUTHORITY_GROUP_GOODS_REMOVE  = "shop_group_goods_remove";

    //店铺管理
    const AUTHORITY_SHOP_READ = "shop_read";//查看店铺列表
    const AUTHORITY_SHOP_EDIT = "shop_edit";//店铺编辑
    const AUTHORITY_SHOP_REMOVE = "shop_remove";//店铺删除
    const AUTHORITY_SHOP_ADD = "shop_add";//店铺添加
    

    //库存明细
    const AUTHORITY_SHOP_GOODS_STOCK_LOG_READ = "shop_goods_stock_log_read";//查看库存明细记录
    
    /**
     * 订单创建成功事件
     * 
     * @param   string  $order_id
     * @return  bool
     */
    public function event_order_found($order_id){
        $application = object(parent::MAIN)->api_application();
        $parse_url = http(function($http){
            return $http['parse_url'];
        });
        
        $parse_url['path'] = '/index.php';
        $parse_url['data'] = array(
            'data'=> array(
                array('SHOPEVENTORDERFOUND', array($order_id) )
            ),
            'application' => $application["application_id"],
            'temp' => 1
        );
        $http_async = object(parent::PLUGIN_HTTP_SOCKET)->async_post($parse_url);
        if( !empty($http_async['errno']) ){
            return $http_async;
        }
    } 
    
    
    /**
     * 当订单确认发货的时候触发事件
     * 
     * @param   string  $order_id
     * @return  bool
     */
    public function event_order_shipping_send($order_id){
        $application = object(parent::MAIN)->api_application();
        $parse_url = http(function($http){
            return $http['parse_url'];
        });
        
        $parse_url['path'] = '/index.php';
        $parse_url['data'] = array(
            'data'=> array(
                array('SHOPEVENTORDERSHIPPINGSEND', array($order_id) )
            ),
            'application' => $application["application_id"],
            'temp' => 1
        );
        $http_async = object(parent::PLUGIN_HTTP_SOCKET)->async_post($parse_url);
        if( !empty($http_async['errno']) ){
            return $http_async;
        }
    } 
        
    
    
    
    
    
    /**
     * 当订单确认收货的时候触发事件
     * 
     * @param   string  $order_id
     * @return  bool
     */
    public function event_order_shipping_take($order_id){
        $application = object(parent::MAIN)->api_application();
        $parse_url = http(function($http){
            return $http['parse_url'];
        });
        
        $parse_url['path'] = '/index.php';
        $parse_url['data'] = array(
            'data'=> array(
                array('SHOPEVENTORDERSHIPPINGTAKE', array($order_id) )
            ),
            'application' => $application["application_id"],
            'temp' => 1
        );
        $http_async = object(parent::PLUGIN_HTTP_SOCKET)->async_post($parse_url);
        if( !empty($http_async['errno']) ){
            return $http_async;
        }
    } 
    
    
    
    
    
    /**
     * 订单支付全部完成事件
     * 
     * @param   string  $order_id
     * @return  bool
     */
    public function event_order_payment_complete($order_id){
        $application = object(parent::MAIN)->api_application();
        $parse_url = http(function($http){
            return $http['parse_url'];
        });
        
        $parse_url['path'] = '/index.php';
        $parse_url['data'] = array(
            'data'=> array(
                array('SHOPEVENTORDERPAYMENTCOMPLETE', array($order_id) )
            ),
            'application' => $application["application_id"],
            'temp' => 1
        );
        $http_async = object(parent::PLUGIN_HTTP_SOCKET)->async_post($parse_url);
        if( !empty($http_async['errno']) ){
            return $http_async;
        }
    } 
    
    
    /**
     * 订单支付全部完成事件
     * 
     * @param   string  $order_id
     * @return  bool
     */
    public function event_order_payment_moiety($order_id){
        $application = object(parent::MAIN)->api_application();
        $parse_url = http(function($http){
            return $http['parse_url'];
        });
        
        $parse_url['path'] = '/index.php';
        $parse_url['data'] = array(
            'data'=> array(
                array('SHOPEVENTORDERPAYMENTMOIETY', array($order_id) )
            ),
            'application' => $application["application_id"],
            'temp' => 1
        );
        $http_async = object(parent::PLUGIN_HTTP_SOCKET)->async_post($parse_url);
        if( !empty($http_async['errno']) ){
            return $http_async;
        }
    } 


	/**
     * 团购订单 退款 异步
     * 
     * @param   void
     * @return  bool
     */
    public function event_order_group_refund(){
        $application = object(parent::MAIN)->api_application();
        $parse_url = http(function($http){
            return $http['parse_url'];
        });
        
        $parse_url['path'] = '/index.php';
        $parse_url['data'] = array(
            'data'=> array(
                array('SHOPEVENTORDERGROUPREFUND')
            ),
            'application' => $application["application_id"],
            'temp' => 1
        );
        $http_async = object(parent::PLUGIN_HTTP_SOCKET)->async_post($parse_url);
        if( !empty($http_async['errno']) ){
            return $http_async;
        }
    } 





    /**
     * 检测身份
     * @author green
     * @return string [店铺ID]
     */
    protected function check_identity()
    {
        //判断是否登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        // 查询商家用户信息
        $merchant_user = object(parent::TABLE_MERCHANT_USER)->find_where(array(array('user_id = [+]', $user_id)));
        if (empty($merchant_user)) {
            throw new error('不是商家用户');
        }
        if ($merchant_user['merchant_user_state'] == 0) {
            throw new error('已封禁');
        }

        return $merchant_user['merchant_id'];
    }

}