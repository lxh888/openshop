# uniapp开发微信公众号

标签（空格分隔）： uiapp 微信公众号

---

## **在uniapp中微信公众号配置js-sdk**
#### **1.绑定域名**
*先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
备注：登录后可在“开发者中心”查看对应的接口权限。*
#### **2.引入JS文件**
1) html引入
在需要调用JS接口的页面引入如下JS文件，（支持https）：http://res.wx.qq.com/open/js/jweixin-1.4.0.js
如需进一步提升服务稳定性，当上述资源不可访问时，可改访问：http://res2.wx.qq.com/open/js/jweixin-1.4.0.js （支持https）。*

2) uniapp引入
第一步：`npm install jweixin-module --save`
第二步：创建一个`WX_config.js`文件,放在`tools`文件夹里，内容如下
```javascript
    import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
    let jweixin = require('jweixin-module');
	let ef = new eonfox();
	export default {
		init(){				
			ef.submit({
				request:{
				    sha:['USERSHAREWEBSHARE',[{'url':url_href.replace(location.hash, '')}]]
					},
					callback:function(res){
						var shareData=fns.checkError(res,'sha',function(errno,error){
						})
						if(shareData){
							jweixin.config({
							    appId: shareData.sha.appId,
							    timestamp: shareData.sha.timestamp, // 必填，生成签名的时间戳
							    nonceStr: shareData.sha.nonceStr, // 必填，生成签名的随机串
							    signature: shareData.sha.signature, // 必填，签名，见附录1
							    jsApiList: [
							        'checkJsApi',
							        'scanQRCode',
							        'onMenuShareAppMessage',
							        'onMenuShareTimeline',
							        'getLocation',//获取位置
							        'openLocation'
							    ]
							});
							
							
							jweixin.ready(()=>{
								//获取位置
								jweixin.getLocation({
									type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
									success: function (res) {
										console.log(res,'位置信息')
										var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
										var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
										var speed = res.speed; // 速度，以米/每秒计
										var accuracy = res.accuracy; // 位置精度
									}
								});
					
				})
		}
	}
```
第三步：在`main.js`里这样写
```javascript
    import WXconfig from './tools/WX_config.js';
    
    //初始化微信配置
	WXconfig.init();
```
第四步：在页面使用
```javascript
    let jweixin = require('jweixin-module');
    
    jweixin.getLocation({
        type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
        success: function (res) {
            console.log(res,'location')
        }
    })
```
*备注：支持使用 AMD/CMD 标准模块加载方法加载*





