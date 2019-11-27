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

//品牌
class brand extends main 
{


    //权限码——品牌
    const AUTHORITY_BRAND_ADD       = 'brand_add';
    const AUTHORITY_BRAND_REMOVE    = 'brand_remove';
    const AUTHORITY_BRAND_EDIT      = 'brand_edit';
    const AUTHORITY_BRAND_READ      = 'brand_read';


}