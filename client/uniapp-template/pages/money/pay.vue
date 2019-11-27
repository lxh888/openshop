<template>
	<view class="app">
		<best-payment-password :show="pay_password" digits="6" @submit="checkPwd" @cancel="togglePayment" ></best-payment-password>
		<view class="price-box">
			<text>支付金额</text>
			<text class="price">{{money/100}} <text v-if="credit>0">+{{credit}}积分</text></text>
			<text></text>
		</view>

		<view class="pay-type-list">
			<view class="type-item b-b" @click="changePayType(1)">
				<text class="icon yticon icon-weixinzhifu"></text>
				<view class="con">
					<text class="tit">微信支付</text>
					<text>推荐使用微信支付</text>
				</view>
				<label class="radio">
					<radio value="" color="#fa436a" :checked='payType == 1' />
					</radio>
				</label>
			</view>
			<!-- #ifdef APP-PLUS -->
				<view class="type-item b-b" @click="changePayType(2)">
					<text class="icon yticon icon-alipay"></text>
					<view class="con">
						<text class="tit">支付宝支付</text>
					</view>
					<label class="radio">
						<radio value="" color="#fa436a" :checked='payType == 2' />
						</radio>
					</label>
				</view>
			<!-- #endif -->
			<view class="type-item" @click="changePayType(3)">
				<text class="icon yticon icon-erjiye-yucunkuan"></text>
				<view class="con">
					<text class="tit">钱包支付</text>
					<text>可用余额{{balance/100}}</text>
				</view>
				<label class="radio">
					<radio value="" color="#fa436a" :checked='payType == 3' />
					</radio>
				</label>
			</view>
			<view class="type-item" @click="changePayType(4)" v-if="pay_state==2">
				<image  class="iconImg" src="http://mp.emshop.eonfox.com/zrhzstatic/muying/jifen1.png" mode=""></image>
				<view class="con">
					<text class="tit">积分支付</text>
					<text></text>
				</view>
				<label class="radio">
					<radio value="" color="#fa436a" :checked='payType == 4' />
					</radio>
				</label>
			</view>
		</view>
		
		<text class="mix-btn" @click="confirm">确认支付</text>
	</view>
</template>

<script>
import bestPaymentPassword from '@/components/best-payment-password/best-payment-password.vue'
import eonfox from '@/components/eonfox/eonfox.js';
import fns from '@/components/eonfox/fns.js';
	var ef = new eonfox();
	export default {
		data() {
			return {
				payType: 1,
				orderInfo: {},
				pay_password:false,
				password:'',
				order_id:'',
				money:'',
				credit:'',
				moenytype:'',
				jifentype:'',
				balance:'', //钱包余额
				status:'' ,//支付状态 1钱包支付 2积分支付
			};
		},
		components: {
			bestPaymentPassword
		},
		onLoad(options) {
			//order_id 订单id  money价格   credit积分  type支付状态  moenytype人民币支付状态   jifentype积分支付状态
			console.log(options)
			this.order_id=options.order_id
			this.money=options.money
			this.credit=options.credit
			this.moenytype=options.moenytype
			this.jifentype=options.jifentype
			this.pay_state=options.type  
			
			var _this=this
			ef.submit({
				request: {
					balance: ['USERMONEYSELFTOTAL'] ,//余额
				},
				callback: function(data) {
					var dataList = fns.checkError(data, ['balance'], function(errno, error) {
						uni.showToast({
							title:error,
							icon:'none'
						})
					})
					console.log(dataList)
					if(dataList.balance){
						_this.balance = dataList.balance
					}
					},
				})
		},

		methods: {
			//选择支付方式
			changePayType(type) {
				var _this = this
				if(_this.jifentype ==0 && _this.pay_state==2){
					_this.pay_password=true
					_this.status=2
					uni.showToast({
						title:'请支付积分',
						icon:'none'
					})
					return
				}
				else{
					//1微信 2支付宝 3钱包 4积分
					_this.payType = type;
					console.log(_this.payType)
				}
				
			},
			//确认支付
			confirm: async function() {
				// uni.redirectTo({
				// 	url: '/pages/money/paySuccess'
				// })
				var _this=this
				if(_this.payType==1){
					//#ifdef H5
					   _this.payWeChatPMJSAPI()
					//#endif
					// #ifdef MP-WEIXIN
					_this.pay_weixin();
					// #endif
					// #ifdef APP-PLUS
					_this.pay_App_weixin();
					// #endif
				}
				if(_this.payType==2){
					_this.pay_alipay();
				}
				if(_this.payType==3){
					var b=parseFloat(_this.balance)
					var m=parseFloat(_this.money)
					console.log('111',typeof b)
					console.log('222',typeof  _this.money)
					if(b > m){
						
						_this.pay_method='user_money'
						_this.pay_password=true
						_this.status=1 //支付状态 1钱包支付 2积分支付
					}else{
						uni.showToast({
							title:'余额不足',
							icon:'none'
						})
					}
					
				}
			},
			//微信公众号支付
				payWeChatPMJSAPI(){
					var _this = this;
					ef.submit({
						request:{
							s:['SESSIONWEIXINACCESSTOKEN']
						},
						callback(data){
							var dataList=fns.checkError(data,'s',function(errno,error){
								uni.showToast({
									title: '请先确认微信授权',
									icon:'none'
								})
							})
							console.log(dataList);
							if(dataList.s){
								ef.submit({
									request: {
										s:[ 'SHOPORDERSELFPAYMENT',
										[{order_id:_this.order_id,
											pay_method: 'weixinpay',
											// pay_password:_this.password,
											weixin_login_openid: dataList.s.openid,
											weixin_trade_type: 'MPJSAPI'
										}] 
										]
									},
									
									callback(data) {
										console.log('调起微信支付', data);
										var dataList = fns.checkError(data, 's', function(errno, error) {
											uni.showToast({
												title: error,
												icon: 'none'
											});
										});
										var ress = dataList.s;
										if (ress) {
											console.log('ress', ress);
											
											var getBrandWCPayRequest = {
													appId: ress.appid,
													timeStamp: String(ress.time_stamp), // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
													nonceStr: ress.nonce_str, // 支付签名随机串，不长于 32 位
													package: 'prepay_id='+ress.prepay_id, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
													signType: ress.sign_type, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
													paySign: ress.pay_sign, // 支付签名
												  };
											
											console.log('getBrandWCPayRequest', getBrandWCPayRequest);
											function onBridgeReady(){
											   WeixinJSBridge.invoke(
												  'getBrandWCPayRequest', getBrandWCPayRequest,
												function(res){
													// uni.showToast({
													// 	title:JSON.stringify(res),
													// 	icon:'none',
													// 	duration:10000
													// })
												  if( res.err_msg == "get_brand_wcpay_request:ok" ){
												 
														
														uni.showToast({
															title:'支付成功',
															success() {
																setTimeout(function(){
																	uni.redirectTo({
																		url: '/pages/money/paySuccess'
																	})
																},2000)
															}
														})
												  } 
												  if(res.err_msg == "get_brand_wcpay_request:fail" ){
														uni.showToast({
															title:'支付失败',
															icon:'none'
														})
												  }
												  if(res.err_msg == "get_brand_wcpay_request:cancel"){
													  uni.showToast({
														title:'已取消支付',
														icon:'none'
													  })
												  }
											   }); 
											}
											
											if (typeof WeixinJSBridge == "undefined"){
											   if( document.addEventListener ){
												   document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
											   }else if (document.attachEvent){
												   document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
												   document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
											   }
											}else{
											   onBridgeReady();
											}
											
										}else{
											console.log('提交订单失败', ress);
											_this.payWeChatPMJSAPI();
											
										}
										console.log('提交订单', ress);
										
									},
									error(err) {
										console.log('提交订单失败', err, 1);
									}
								});
								
								
							}else{
								
								console.log('location', location.href);
								
								//当 ACCESSTOKEN 不存在
								ef.left_token(function(left_token){
									var notify_url = encodeURIComponent(location.href);
									var url = ef.api_server_url+"?"+encodeURI('data=[["SESSIONWEIXINAUTHORIZE",[{"notify_url":"'+notify_url+'"}]]]')+"&token="+left_token;
									console.log(url);
									location.href = url;
								});
								
							}
						},
						error(err){
							fns.err('err',err,1)
						}
					})
				},
				//微信支付  (第一步)
				pay_weixin(){
					var _this = this;
					var shopOrderSelfAddConfig = {
						pay_method:'weixinpay',
						order_id:_this.order_id
					};
					wx.login({
						//微信小程序登录获取code
						success(res) {
							console.log("wx.login"+JSON.stringify(res));
							
							// #ifdef MP-WEIXIN
							_this.code = res.code;
							shopOrderSelfAddConfig.weixin_login_code = res.code;
							shopOrderSelfAddConfig.weixin_trade_type = "JSAPI";
							// #endif
							// #ifdef APP-PLUS
							if( res.authResult && res.authResult.openid ){
								_this.openid = res.authResult.openid;
								shopOrderSelfAddConfig.weixin_login_openid = res.authResult.openid;
							}
							shopOrderSelfAddConfig.weixin_trade_type = "APP";
							// #endif
							
							if( res.code || (res.authResult && res.authResult.openid) ){
								// 发起网络请求
								console.log('发起网络请求');
								ef.submit({
									request: {
										s: ['SHOPORDERSELFPAYMENT', [shopOrderSelfAddConfig]]
									},
									callback(data){
										console.log('调起微信支付',data)
										/* uni.showToast({
											title:data,
											icon:'none',
											duration: 30000
										}); */
										var shopOrderSelfAddData = fns.checkError(data,'s',function(errno,error){
											uni.showToast({
												title:error,
												icon:'none',
												duration: 3000
											});
										});
										
										if( !shopOrderSelfAddData ){
											return false;
										}
										
										console.log('提交订单', shopOrderSelfAddData);
										_this.requestPayment(shopOrderSelfAddData.s);
										
									},
									error(err){
										fns.err('提交订单失败',err,1)
									}
								});
								
							} else {
								_this.isDisable = false;
								console.log('登录失败！' + res.errMsg );
								console.log('登录失败2444！' + JSON.stringify(res) );
								
								uni.showToast({
									title: '登录失败！' + res.errMsg,
									icon: 'none',
									//duration: 30000
								});
							}
							
						}
					});
				},
				//调起支付（微信）
				requestPayment(wxArr) {
					var _this = this;
					_this.test='开始了'
					var _this = this;
						console.log('支付参数'+JSON.stringify(wxArr));
						console.log('调起支付');
						_this.test='调起支付'
					
					// #ifdef APP-PLUS
					var orderInfo = {
						appid:wxArr.appid,
						partnerid:wxArr.mch_id,//商户号
						prepayid:wxArr.prepay_id,//预支付交易会话ID
						package:'Sign=WXPay',//扩展字段,暂填写固定值Sign=WXPay
						noncestr:wxArr.nonce_str,//随机字符串
						timestamp:wxArr.time_stamp,//时间戳	
						sign:wxArr.pay_sign,//签名
					};
					// #endif
					
					uni.requestPayment({
						// #ifdef MP-WEIXIN
						provider: 'wxpay',
						timeStamp: String(wxArr.time_stamp),
						nonceStr: wxArr.nonce_str,
						package: 'prepay_id=' + wxArr.prepay_id,
						signType: wxArr.sign_type,
						paySign: wxArr.pay_sign,
						orderInfo:wxArr,
						// #endif
						// #ifdef APP-PLUS
						provider:'wxpay',
						/* appid:wxArr.appid,
						partnerid:wxArr.mch_id,//商户号
						prepayid:wxArr.prepay_id,//预支付交易会话ID
						package:'Sign=WXPay',//扩展字段,暂填写固定值Sign=WXPay
						noncestr:wxArr.nonce_str,//随机字符串
						timestamp:wxArr.time_stamp,//时间戳	
						sign:wxArr.sign,//签名 */
						orderInfo: orderInfo,
						// #endif
						success: function(res) {
							_this.test = '支付成功?'
							console.log('支付成功success:' , JSON.stringify(res));
							// return
							if (res.errMsg == 'requestPayment:ok') {
								_this.test='支付成功'
								//支付成功是进行订单查询
								
								uni.showToast({
									title: '支付成功',
									icon: 'none',
									success() {
										setTimeout(function(){
											uni.redirectTo({
												url: '/pages/money/paySuccess'
											})
										},1500)
									}
								})	
							} 
						},
						fail: function(err) {
							_this.test = '调用支付失败'+JSON.stringify(err);
							console.log('调用支付失败',JSON.stringify(err));
							/* uni.showToast({
								title: "调用支付失败："+JSON.stringify(err),
								icon:'none',
								duration: 100000
							}); */
							return false;
						},
					});
				},
				
				pay_App_weixin(){
					var _this = this;
					var shopOrderSelfAddConfig = {
						pay_method:'weixinpay',
						order_id:_this.order_id
					};
				
				shopOrderSelfAddConfig.weixin_trade_type = "APP";
			
					// 发起网络请求
					console.log('发起网络请求');
					ef.submit({
						request: {
							s: ['SHOPORDERSELFPAYMENT',[shopOrderSelfAddConfig]]
						},
						callback(data){
							console.log('调起微信支付',data)
							/* uni.showToast({
								title:data,
								icon:'none',
								duration: 30000
							}); */
							var shopOrderSelfAddData = fns.checkError(data,'s',function(errno,error){
								uni.showToast({
									title:error,
									icon:'none',
									duration: 3000
								});
							});
							
							if( !shopOrderSelfAddData ){
								return false;
							}
							
							console.log('提交订单', shopOrderSelfAddData);
							_this.requestPayment(shopOrderSelfAddData.s);
							
						},
						error(err){
							fns.err('提交订单失败',err,1)
						}
					});
				
				},
			checkPwd(pwd){
				var _this=this
				_this.password=pwd
					if (_this.status == 1) {
						_this.paymoeny(_this.password)
					}
					if (_this.status == 2) {
						_this.payjifen(_this.password)
					}
				
			},
			//余额支付
			paymoeny(password){
				var _this=this
				ef.submit({
					request:{
						s:['SHOPORDERSELFPAYMENT',[{order_id:_this.order_id,pay_method:'user_money',pay_password:password}]]
					},
					callback(data) {
					    const dataList=fns.checkError(data, 's', function(errno, error) {
						uni.showToast({
							title: error,
							icon: 'none'
						});
						})
						console.log('da...',dataList)
						
						if(dataList.s.order_id){
							_this.password=''
							uni.showToast({
								title:'支付成功',
								success() {
									setTimeout(function(){
										uni.redirectTo({
											url: '/pages/money/paySuccess'
										})
									},1500)
								}
							})
						
						}
						
						console.log('支付结果');
					}
				})
					
			},
			
			payjifen(password){
				var _this=this
				ef.submit({
					request:{
						s:['SHOPORDERSELFPAYMENT',[{order_id:_this.order_id,pay_method:'user_credit',pay_password:password}]]
					},
					callback(data) {
					    const dataList=fns.checkError(data, 's', function(errno, error) {
						uni.showToast({
							title: error,
							icon: 'none'
						});
						})
						console.log('da...',dataList)
						
							if(dataList.s.order_id){
								_this.pay_display = true;
								_this.password=''
								uni.showToast({
									title:'支付成功',
									success() {
										setTimeout(function(){
											uni.redirectTo({
												url: '/pages/money/paySuccess'
											})
										},1500)
									}
								})
								
							}
						console.log('支付结果');
					}
				})
					
			},
			//支付宝支付
			pay_alipay(){
				var _this=this;
				_this.pay_password=false;
				ef.submit({
						request: {
							s: ['SHOPORDERSELFPAYMENT', [{
								order_id:_this.order_id,
								pay_method:'alipay', //支付方式 weixinpay 微信支付、alipay 支付宝支付
								alipay_trade_type:'APP'
							} ]]
						},
						callback(data){
							console.log('调起支付宝支付',data)
							fns.checkError(data,'s',function(errno,error){
								uni.showToast({
									title:error,
									icon:'none'
								})
							})
							var ali=data.data.s.data.alipay
							if(ali){
								uni.requestPayment({
										provider: 'alipay',
										orderInfo:ali,
										success: function (res) {
											console.log('success:' + JSON.stringify(res));
											uni.showToast({
												title:'支付成功',
												success() {
													setTimeout(function(){
														uni.redirectTo({
															url: '/pages/money/paySuccess'
														})
													},1500)
													
												}
											})
										},
										fail: function (err) {
											console.log('fail:' + JSON.stringify(err));
										}
									});
							}
						},
						error(err){
							fns.err('提交订单失败',err,1)
						}
			
				})
			},
			togglePayment(){
				this.pay_password=false
			},
		}
	}
</script>

<style lang='scss'>
	.app {
		width: 100%;
	}

	.price-box {
		background-color: #fff;
		height: 265upx;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		font-size: 28upx;
		color: #909399;

		.price{
			font-size: 50upx;
			color: #303133;
			margin-top: 12upx;
			&:before{
				content: '￥';
				font-size: 40upx;
			}
		}
	}

	.pay-type-list {
		margin-top: 20upx;
		background-color: #fff;
		padding-left: 60upx;
		
		.type-item{
			height: 120upx;
			padding: 20upx 0;
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding-right: 60upx;
			font-size: 30upx;
			position:relative;
		}
		
		.icon{
			width: 100upx;
			font-size: 52upx;
		}
		.icon-erjiye-yucunkuan {
			color: #fe8e2e;
		}
		.icon-weixinzhifu {
			color: #36cb59;
		}
		.icon-alipay {
			color: #01aaef;
		}
		.tit{
			font-size: $font-lg;
			color: $font-color-dark;
			margin-bottom: 4upx;
		}
		.con{
			flex: 1;
			display: flex;
			flex-direction: column;
			font-size: $font-sm;
			color: $font-color-light;
		}
	}
	.mix-btn {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 630upx;
		height: 80upx;
		margin: 80upx auto 30upx;
		font-size: $font-lg;
		color: #fff;
		background-color: $base-color;
		border-radius: 10upx;
		box-shadow: 1px 2px 5px rgba(219, 63, 96, 0.4);
	}
	.iconImg{
		width: 60upx;
		height: 60upx;
		margin-right: 40upx;
	}
</style>
