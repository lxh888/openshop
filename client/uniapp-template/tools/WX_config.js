	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	
	// #ifdef H5
	let jweixin = require('jweixin-module');
	let url_href = location.href;
	// #endif
	let ef = new eonfox();
	
	
	export default {
		init(){				
				console.log(url_href)
				ef.submit({
					request:{
						sha:['USERSHAREWEBSHARE',[{'url':url_href.replace(location.hash, '')}]]
					},
					callback:function(res){
						var shareData=fns.checkError(res,'sha',function(errno,error){
							// #ifdef H5
							uni.showToast({
								title:'分享功能需要登录',
								icon:'none'
							})
							// #endif
						})
						console.log(shareData,'`1`1`1`1`1`')
						if(shareData){
							console.log(shareData,'打印分享签名')
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
							        'getLocation',
							        'openLocation'
							    ]
							});
							
							
							jweixin.ready(()=>{
								console.log(123456)
								let tel = '';
								uni.getStorage({
									key:"userinfo",
									success:function(res) {
										console.log(res)
										tel = res.data.data.p.data[0].user_phone_id
									},
									fail() {
										console.log('失败回调')
										uni.showToast({
											title:'请先登录才能分享',
											success() {
												url:'../pages/register/register'
											}
											
										})
										return
									}
								})
								
								
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
								
								
								
								
								
								let shareUrl = window.location.origin + window.location.pathname+"#/pages/index/index?userphone="+ tel;
								
								let png = 'https://mp.weixin.qq.com/wxopen/basicprofile?action=get_headimg&token=922427462&t=20190816110939';
								console.log('分享数据',shareUrl,'`````````',png)						
								//分享到朋友圈
								jweixin.onMenuShareTimeline({									
								    title: "", // 分享标题
								    link: shareUrl, // 分享链接
								    desc: '欢迎光临母婴商城', // 分享描述
								    imgUrl:window.location.origin + window.location.pathname + 'static/basicprofile.png', // 分享图标
								    success: function() {
										uni.showToast({
											title:'分享成功'
										})
								        // 用户确认分享后执行的回调函数
								        console.log("用户分享后朋友圈执行的回调函数");
								    },
								    cancel: function() {
								        console.log("用户取消分享后朋友圈执行的回调函数");
								    }
								});
								// 分享好友
								jweixin.onMenuShareAppMessage({									
									title: '母婴商场', // 分享标题
									link: shareUrl, // 分享链接
									desc: '欢迎光临母婴商城', // 分享描述
									imgUrl: window.location.origin + window.location.pathname + 'static/basicprofile.png', // 分享图标
									success: function(res) {
										console.log('测试分享')
										uni.showToast({
											title:'分享成功'
										})
										Vue.prototype.$toast("分享成功");
									},
									cancel: function() {
										// 用户取消分享后执行的回调函数
										console.log("用户取消分享朋友后执行的回调函数2");
									}
								});
							})
						}
						
						
						
						
					}
				})
								
			
		}
	}