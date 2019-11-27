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
class cms extends main {

    //权限码——文章
    const AUTHORITY_ARTICLE_ADD = 'cms_article_add';
    const AUTHORITY_ARTICLE_EDIT = 'cms_article_edit';
    const AUTHORITY_ARTICLE_REMOVE = 'cms_article_remove';
    const AUTHORITY_ARTICLE_READ = 'cms_article_read';
	const AUTHORITY_ARTICLE_TRASH = "cms_article_trash";//逻辑删除，丢进回收站
	const AUTHORITY_ARTICLE_TRASH_READ = "cms_article_trash_read";//回收站读取权限
	const AUTHORITY_ARTICLE_TRASH_EDIT = "cms_article_trash_edit";//回收站编辑
	const AUTHORITY_ARTICLE_TRASH_RESTORE = "cms_article_trash_restore";//回收站还原
	
	




}