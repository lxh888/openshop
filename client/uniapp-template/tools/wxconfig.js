// /*
//  * @Descripttion: 微信 操作相关js
//  * @Author: TM丶
//  * @LastEditors: TM丶
//  * @Date: 2019-04-04 13:53:17
//  * @LastEditTime: 2019-04-15 16:20:14
//  */
// 
// 
// 
// // const HREF = Window.location.href;
// // const REDIRECT_URL = encodeURIComponent(HREF);
// 
// const wxconfig = {
//     init(){
//         // if(HREF.indexOf('state')===-1 && process.env.NODE_ENV !=='development'){
//         //     // get(`https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx228a06aa92de05a2&redirect_uri=${REDIRECT_URL}&response_type=code&scope=snsapi_base&state=123#wechat_redirect`)
//         //     location.href = `https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx228a06aa92de05a2&redirect_uri=${REDIRECT_URL}&response_type=code&scope=snsapi_base&state=123#wechat_redirect`; 
//         // }else{
//             // 主要是因为：
//             // 【IOS】：ios微信端，路由变化时，微信认为SPA的url是不变的。
//             // 【Android】：android微信端，路由变化时，SPA的url是会变的
//             // 所以，发起签名的url必须是微信锁定的url。剔除location.hash就ok了
//             SystApi.getWXToken('/getsine',{url:HREF.replace(location.hash, '')}).then(function (res) {
//                 if (res.errorCode === 0) {
//                     wx.config({
//                         appId: res.body.appId,
//                         timestamp: res.body.timestamp, // 必填，生成签名的时间戳
//                         nonceStr: res.body.nonceStr, // 必填，生成签名的随机串
//                         signature: res.body.signature, // 必填，签名，见附录1
//                         jsApiList: [
//                             'checkJsApi',
//                             'scanQRCode',
//                             'onMenuShareAppMessage',
//                             'onMenuShareTimeline',
//                             'getLocation',
//                             'openLocation'
//                         ]
//                     });
//                     wx.ready(function(){
//                         function getQueryArgs(){
//                             let qs = location.search.length > 0 ? location.search.substring(1) : '',
//                                 items = qs.length ? qs.split('&') : [],
//                                 args = {},
//                                 item = null,
//                                 name = null,
//                                 value = null,
//                                 i = 0,
//                                 len = items.length;
//                             for (let i = 0; i < len; i++) {
//                                 item = items[i].split('=');
//                                 name = decodeURIComponent(item[0]);
//                                 value = decodeURIComponent(item[1]);
//                                 if (name.length) {
//                                     args[name] = value;
//                                 }
//                             }
//                             return args;
//                         }
//                         let args = getQueryArgs();
//                         SystApi.getWXToken('/userInfo',{pCode:args.code}).then(res=>{
//                             sessionStorage.setItem('wxOpenId', res.body);
//                         })
//                         //授权状态
//                         sessionStorage.wxAccredit = true;
// 
//                         // let shareUrl = window.location.origin + window.location.pathname+"#/share/0";
//                         // //分享到朋友圈
//                         // wx.onMenuShareTimeline({
//                         //     title: "漫链通", // 分享标题
//                         //     link: shareUrl, // 分享链接
//                         //     desc: "共享品味漫生活", // 分享描述
//                         //     imgUrl: window.location.origin + window.location.pathname+"static/img/sharelogo.png", // 分享图标
//                         //     success: function() {
//                         //         // 用户确认分享后执行的回调函数
//                         //         Vue.prototype.$toast("分享成功");
//                         //     },
//                         //     cancel: function() {
//                         //         console.log("用户取消分享后朋友圈执行的回调函数");
//                         //     }
//                         // });
//                         // // 分享好友
//                         // wx.onMenuShareAppMessage({
//                         //     title: "漫链通", // 分享标题
//                         //     link: shareUrl, // 分享链接
//                         //     desc: "共享品味漫生活", // 分享描述
//                         //     imgUrl: window.location.origin + window.location.pathname+"static/img/sharelogo.png", // 分享图标
//                         //     success: function() {
//                         //         Vue.prototype.$toast("分享成功");
//                         //     },
//                         //     cancel: function() {
//                         //         // 用户取消分享后执行的回调函数
//                         //         console.log("用户取消分享朋友后执行的回调函数2");
//                         //     }
//                         // });
//                          
//                     })
//                     //微信预加载失败回调
//                     wx.error(function (res) {
//                         console.error("微信预加载失败回调",res);
//                     });
//                 }
//             });
//         // }
// 
//     }
// }
// 
// 
// export default wxconfig;
// 