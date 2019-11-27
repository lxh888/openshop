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



function aliyun_dysms_api_sdk_classLoader($class){
	preg_match('/^Aliyun\\\(.*)/i', $class, $match);
	if( empty($match[1]) ){
		return false;
	}
	$path = str_replace('\\', DIRECTORY_SEPARATOR, $match[1]);
	$file = __DIR__ . '/api_sdk/lib/' . $path . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('aliyun_dysms_api_sdk_classLoader');