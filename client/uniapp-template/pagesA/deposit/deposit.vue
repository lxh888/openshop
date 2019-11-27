<template>
	<view>
		<view class="block">
			<view class="title">
				<text>我的账户</text>
				<text style="font-size: 28rpx;padding-left: 60rpx;color: #007AFF;" @click="gominxi">交易明细</text>
			</view>
			
			<view class="content">
				<view class="my">
					我的账户余额：{{money/100}} 元
				</view>
			</view>
		</view>
		<view class="block">
			<view class="title">
				充值金额
			</view>
			<view class="content">
				<view class="amount">
					<view class="list">
						<view class="box" v-for="(amount,index) in amountList" :key="index" @tap="select(amount)" :class="{'on':amount == inputAmount}">
							{{amount}}元
						</view>
					</view>
					<view class="num">
						<view class="text">
							自定义充值金额
						</view>
						<view class="input">
							<input type="number" v-model="inputAmount" />
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="block">
			<view class="title">
				选择支付方式
			</view>
			<view class="content">
				<view class="pay-list">
					<!-- #ifdef APP-PLUS -->
					<view class="row" @tap="paytype='alipay'">
							<view class="left">
								<image src="../../static/img/alipay.png"></image>
							</view>
							<view class="center">
								支付宝支付
							</view>
							<view class="right">
								<radio :checked="paytype=='alipay'" color="#70b162" />
							</view>
					</view>
					<!-- #endif -->
					<view class="row" @tap="paytype='wxpay'">
							<view class="left">
								<image src="../../static/img/wxpay.png"></image>
							</view>
							<view class="center">
								微信支付
							</view>
							<view class="right">
								<radio :checked="paytype=='wxpay'" color="#70b162" />
							</view>
					</view>
				</view>
			</view>
		</view>
		<view class="pay">
			<view class="btn" @tap="pay">立即充值</view>
			<!-- <view class="tis">
				点击立即充值，即代表您同意<view class="terms">
					《条款协议》
				</view>
			</view> -->
		</view>
		<view class="pay">
			<view class="btn btn-tixi" @click="gopage">去提现</view>			
		</view>
	</view>
</template>

<script>
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	var ef=new eonfox()
	export default {
		data() {
			return {
				money:0,
				inputAmount:'',//金额
				amountList:[100,200,500],//预设3个可选快捷金额
				paytype:'wxpay'//支付类型
			};
		},
		onShow(){
			this.load()
		},
		methods:{
			load(){
				var that=this;
				ef.submit({
					request: {
						s: ['USERMONEYSELFTOTAL']						
					},
					callback: function(data){
						console.log("预付款",data);
						if(data.data.s.errno==0){
							that.money=data.data.s.data;
						}						
					},
					 error: function(err){
							 console.log("出错啦", err);
					},
				});
			},
			onload() {
				var that = this;
				ef.submit({
					request: {
						s: ['USERMONEYSELFTOTAL']
					},
					callback: function(data) {
						console.log('预付款', data);
						if (data.data.s.errno == 0) {
							that.money = data.data.s.data;
							setTimeout(function() {
								uni.navigateBack({
									delta: 1
								});
							}, 1000);
						}
					},
					error: function(err) {
						console.log('出错啦', err);
					}
				});
			},
			select(amount){
				this.inputAmount = amount;
			},
			doDeposit(){
				if(this.paytype=='alipay'){
					console.log('支付宝')
					
					return
				}else{
					console.log('微信')
					return
				}
				if (parseFloat(this.inputAmount).toString() == "NaN") {
					uni.showToast({title:'请输入正确金额',icon:'none'});
					return ;
				}
				if(this.inputAmount<=0){
					uni.showToast({title:'请输入大于100的金额',icon:'none'});
					return ;
				}
				if(parseFloat(this.inputAmount).toFixed(2)!=parseFloat(this.inputAmount)){
					uni.showToast({title:'最多只能输入两位小数哦~',icon:'none'});
					return ;
				}
				//模板模拟支付，实际应用请调起微信/支付宝
				uni.showLoading({
					title:'支付中...'
				});
				setTimeout(()=>{
					uni.hideLoading();
					uni.showToast({
						title:'支付成功'
					});
					setTimeout(()=>{
						uni.switchTab({
							url:'../../pages/user/user'
						});
					},300);
				},700)
			},
			pay(){
				var _this=this
				if(!/^\d+(\.\d+)?$/.test(_this.inputAmount)||_this.inputAmount<=0){
					uni.showToast({
						title:'请输入正确金额',
						icon:'none'
					})
				}else{
					if(_this.paytype!='alipay'){
						_this.WeChatPay()
						// // #ifdef MP-WEIXIN
						// _this.pay_mp_weixin()
						// // #endif
						// // #ifdef APP-PLUS || H5
						// _this.pay_APP_weixin()
						// // #endif
					}
					if(_this.paytype=='alipay'){
						// #ifdef H5 || MP-WEIXIN
						uni.showToast({
							title: '开发中。。。',
							icon:'none'
						});
						// #endif
						// #ifdef APP-PLUS
						_this.payAli();
						// #endif
					}
					
				}
				
			},
			payAli(){
				var _this=this;
				ef.submit({
						request: {
							s: ['APPLICATIONORDERSELFBUYUSERMONEY', [{
								money_fen:_this.inputAmount*100,//必须|要购买余额(人民币，分) 
								pay_method: 'alipay', //支付方式 weixinpay 微信支付、alipay 支付宝支付
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
											_this.onload()
											uni.showToast({
												title:'充值成功',
												icon:'none'
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
			// 微信充值
			WeChatPay(){
				var _this=this
				// if (!/^\d+(\.\d+)?$/.test(_this.pay_money) || _this.pay_money <= 0) {
				// 	uni.showToast({
				// 		title: '请输入正确金额',
				// 		icon: 'none'
				// 	});
				// }else{
					var money_fen=_this.inputAmount*100
					// if(_this.tabIndex!=0){
					// 	money_fen=_this.tabIndex*100
					// }else{
					// 	money_fen=_this.pay_money*100
					// }
					// APP充值
					// #ifdef APP-PLUS
					ef.submit({
						request: {
							s: [
								'APPLICATIONORDERSELFBUYUSERMONEY',
								[
									{
										money_fen: money_fen, //必须|要购买余额(人民币，分)
										pay_method: 'weixinpay', //支付方式 weixinpay 微信支付、alipay 支付宝支付
										weixin_trade_type: 'APP'
									}
								]
							]
						},
						callback: function(data) {
							console.log('商家', data);
							if (
								fns.checkError(data, 's', function(erron, error) {
									uni.showToast({
										title: error,
										icon: 'none'
									});
									_this.pay_switch = false;
								})
							) {
								//7,已支付
								//发起支付
								console.log('发起支付', data.data.s.data);
								_this.requestPayment(data.data.s.data);
								//获取成功，无错误信息时
							}
						},
						error(err) {
							fns.err('提交订单失败', err, 1);
						}
					});
					// #endif
					
					//#ifdef  MP-WEIXIN
					  wx.login({
					  	//微信小程序登录获取code
					  	success(res) {
					  		// #ifdef MP-WEIXIN
					  		_this.code = res.code;
					  		// #endif
					  		// #ifdef APP-PLUS
					  		_this.code = res.authResult.openid;
					  		// #endif
					  		if (_this.code) {
					  			//获取到code生成订单
					  			// 发起网络请求
					  			console.log('发起网络请求');
					  			ef.submit({
					  				request: {
					  					s: [
					  						'APPLICATIONORDERSELFBUYUSERMONEY',
					  						[
					  							{
					  								money_fen: money_fen, //必须|要购买余额(人民币，分)
					  								// #ifdef MP-WEIXIN
					  								weixin_login_code: _this.code,
					  								pay_method: 'weixinpay', //支付方式 weixinpay 微信支付、alipay 支付宝支付
					  								weixin_trade_type: 'JSAPI',
					  								// #endif
					  								// #ifdef APP-PLUS
					  								weixin_login_openid: _this.code,
					  								pay_method: 'weixinpay', //支付方式 weixinpay 微信支付、alipay 支付宝支付
					  								weixin_trade_type: 'APP'
					  								// #endif
					  							}
					  						]
					  					]
					  				},
					  				callback: function(data) {
					  					console.log('商家', data);
					  					if (
					  						fns.checkError(data, 's', function(erron, error) {
					  							uni.showToast({
					  								title: error,
					  								icon: 'none'
					  							});
					  							_this.pay_switch = false;
					  						})
					  					) {
					  						//7,已支付
					  						//发起支付
					  						console.log('发起支付', data.data.s.data);
					  						_this.requestPayment(data.data.s.data);
					  						//获取成功，无错误信息时
					  					}
					  				},
					  				error: function(err) {
					  					console.log('出错啦', err);
					  					uni.showToast({
					  						title: JSON.stringify(err),
					  						icon: 'none'
					  					});
					  					_this.pay_switch = false;
					  				}
					  			});
					  		} else {
					  			_this.pay_switch = false;
					  			console.log('登录失败！' + res.errMsg);
					  			uni.showToast({
					  				title: '登录失败！' + res.errMsg,
					  				icon: 'none'
					  			});
					  		}
					  	},
					  	fail() {
					  		_this.pay_switch = false;
					  	}
					  });
					// #endif
					
					//#ifdef H5
					ef.submit({
						request: {
							s: ['SESSIONWEIXINACCESSTOKEN']
						},
						callback(data) {
							var dataList = fns.checkError(data, 's', function(errno, error) {
								uni.showToast({
									title: '请先确认微信授权',
									icon: 'none'
								})
							})
							console.log(dataList);
							if (dataList.s) {
								ef.submit({
									request: {
										s: ['APPLICATIONORDERSELFBUYUSERMONEY',
								[
									{
										money_fen: money_fen, //必须|要购买余额(人民币，分)
										pay_method: 'weixinpay', //支付方式 weixinpay 微信支付、alipay 支付宝支付
										weixin_trade_type: 'MPJSAPI',
										weixin_login_openid: dataList.s.openid
									}
								]
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
												package: 'prepay_id=' + ress.prepay_id, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
												signType: ress.sign_type, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
												paySign: ress.pay_sign, // 支付签名
											};
					
											console.log('getBrandWCPayRequest', getBrandWCPayRequest);
					
											function onBridgeReady() {
												WeixinJSBridge.invoke(
													'getBrandWCPayRequest', getBrandWCPayRequest,
													function(res) {
														if (res.err_msg == "get_brand_wcpay_request:ok") {
															uni.showToast({
																title: '支付成功',
																success() {
																	// setTimeout(function() {
																	// 	uni.navigateTo({
																	// 		url: '../../pagesB/my-order/my-order'
																	// 	});
																	// }, 2000)
																}
															})
														}
														if (res.err_msg == "get_brand_wcpay_request:fail") {
															uni.showToast({
																title: '支付失败',
																icon: 'none'
															})
														}
														if (res.err_msg == "get_brand_wcpay_request:cancel") {
															uni.showToast({
																title: '已取消支付',
																icon: 'none',
																success() {
																	setTimeout(function() {
																		uni.navigateBack({
																			delta: 1
																		});
																	}, 1500)
																}
															})
														}
													});
											}
					
											if (typeof WeixinJSBridge == "undefined") {
												if (document.addEventListener) {
													document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
												} else if (document.attachEvent) {
													document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
													document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
												}
											} else {
												onBridgeReady();
											}
					
										} else {
											console.log('提交订单失败', ress);
											_this.payWeChatPMJSAPI();
					
										}
										console.log('提交订单', ress);
					
									},
									error(err) {
										console.log('提交订单失败', err, 1);
									}
								});
					
					
							} else {
					
								console.log('location', location.href);
					
								//当 ACCESSTOKEN 不存在
								ef.left_token(function(left_token) {
									var notify_url = encodeURIComponent(location.href);
									var url = ef.api_server_url + "?" + encodeURI('data=[["SESSIONWEIXINAUTHORIZE",[{"notify_url":"' +
										notify_url + '"}]]]') + "&token=" + left_token;
									console.log(url);
									location.href = url;
								});
					
							}
						},
						error(err) {
							fns.err('err', err, 1)
						}
					})
					// #endif
					
				// }
			},			
			//app微信支付
			pay_APP_weixin() {
				var _this = this;
				ef.submit({
					request: {
						s: [
							'APPLICATIONORDERSELFBUYUSERMONEY',
							[
								{
									money_fen: _this.pay_money * 100, //必须|要购买余额(人民币，分)
									pay_method: 'weixinpay', //支付方式 weixinpay 微信支付、alipay 支付宝支付
									weixin_trade_type: 'APP'
								}
							]
						]
					},
					callback: function(data) {
						console.log('商家', data);
						if (
							fns.checkError(data, 's', function(erron, error) {
								uni.showToast({
									title: error,
									icon: 'none'
								});
								_this.pay_switch = false;
							})
						) {
							//7,已支付
							//发起支付
							console.log('发起支付', data.data.s.data);
							_this.requestPayment(data.data.s.data);
							//获取成功，无错误信息时
						}
					},
					error(err) {
						fns.err('提交订单失败', err, 1);
					}
				});
			},
			//微信小程序支付
			pay_mp_weixin() {
				var _this = this;
				wx.login({
					//微信小程序登录获取code
					success(res) {
						// #ifdef MP-WEIXIN
						_this.code = res.code;
						// #endif
						// #ifdef APP-PLUS
						_this.code = res.authResult.openid;
						// #endif
						if (_this.code) {
							//获取到code生成订单
							// 发起网络请求
							console.log('发起网络请求');
							ef.submit({
								request: {
									s: [
										'APPLICATIONORDERSELFBUYUSERMONEY',
										[
											{
												money_fen: _this.pay_money * 100, //必须|要购买余额(人民币，分)
												// #ifdef MP-WEIXIN
												weixin_login_code: _this.code,
												pay_method: 'weixinpay', //支付方式 weixinpay 微信支付、alipay 支付宝支付
												weixin_trade_type: 'JSAPI',
												// #endif
												// #ifdef APP-PLUS
												weixin_login_openid: _this.code,
												pay_method: 'weixinpay', //支付方式 weixinpay 微信支付、alipay 支付宝支付
												weixin_trade_type: 'APP'
												// #endif
											}
										]
									]
								},
								callback: function(data) {
									console.log('商家', data);
									if (
										fns.checkError(data, 's', function(erron, error) {
											uni.showToast({
												title: error,
												icon: 'none'
											});
											_this.pay_switch = false;
										})
									) {
										//7,已支付
										//发起支付
										console.log('发起支付', data.data.s.data);
										_this.requestPayment(data.data.s.data);
										//获取成功，无错误信息时
									}
								},
								error: function(err) {
									console.log('出错啦', err);
									uni.showToast({
										title: JSON.stringify(err),
										icon: 'none'
									});
									_this.pay_switch = false;
								}
							});
						} else {
							_this.pay_switch = false;
							console.log('登录失败！' + res.errMsg);
							uni.showToast({
								title: '登录失败！' + res.errMsg,
								icon: 'none'
							});
						}
					},
					fail() {
						_this.pay_switch = false;
					}
				});
			},
			requestPayment(wxArr) {
				var _this = this;
				_this.test = '开始了';
				var _this = this;
				console.log('支付参数' + JSON.stringify(wxArr));
				console.log('调起支付');
				_this.test = '调起支付';
			
				// #ifdef APP-PLUS
				var orderInfo = {
					appid: wxArr.appid,
					partnerid: wxArr.mch_id, //商户号
					prepayid: wxArr.prepay_id, //预支付交易会话ID
					package: 'Sign=WXPay', //扩展字段,暂填写固定值Sign=WXPay
					noncestr: wxArr.nonce_str, //随机字符串
					timestamp: wxArr.time_stamp, //时间戳
					sign: wxArr.pay_sign //签名
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
					orderInfo: wxArr,
					// #endif
					// #ifdef APP-PLUS
					provider: 'wxpay',
					orderInfo: JSON.stringify(orderInfo),
					// #endif
					success: function(res) {
						console.log('成功success:' + JSON.stringify(res));
						if (res.errMsg == 'requestPayment:ok') {
							_this.pay_switch = false;
							//支付成功是进行订单查询
							var out_time = 0;
							var timeTn = setInterval(function() {
								out_time++;
								if (out_time <= 30) {
									_this.pay_result_query(wxArr.order_id, function() {
										clearInterval(timeTn);
										_this.onload();
										uni.showToast({
											title: '充值成功',
											icon: 'none'
										});
									});
								} else {
									uni.showToast({
										title: '业务超时，如已支付，请稍后再个人页面查看是否到账',
										icon: 'none'
									});
								}
							}, 1000);
						} else {
							uni.showToast({
								title: '业务超时，如已支付，请稍后再个人页面查看是否到账',
								icon: 'none'
							});
							_this.pay_switch = false;
						}
					},
					fail: function(err) {
						_this.pay_switch = false;
						return false;
					}
				});
			},
			//支付结果查询（订单号）
			pay_result_query(order_id, fun) {
				console.log('正在查询');
				ef.submit({
					request: {
						s: [
							'APPLICATIONORDERSELFPAYSTATE',
							[
								{
									order_id: order_id
								}
							]
						]
					},
					callback: function(data) {
						// console.log('支付查询回调成功',data.data.s.data) ;return data.data.s.data;
						if (
							fns.checkError(data, 's', function(errno, error) {
								return false;
							})
						) {
							fun();
						}
					},
					error: function(err) {
						return 0;
					}
				});
			},
			
			gominxi(){
				uni.navigateTo({
					url:'../../pagesA/transactionDetail2/transactionDetail2?method=user_money'
				})
			},
			gopage(){
				uni.navigateTo({
					url:'../../pagesA/myPurse/myPurse'
				})
			},
		},
		onLoad() {
			
		},
	}
</script>

<style lang="scss">
	.block{
		width: 94%;
		padding: 20upx 3%;
		.title{
			width: 100%;
			font-size: 34upx;
		}
		.content{
			.my{
				width: 100%;
				height: 120upx;
				display: flex;
				align-items: center;
				font-size: 30upx;
				border-bottom: solid 1upx #eee;
			}
			.amount{
				width: 100%;
				
				.list{
					display: flex;
					justify-content: space-between;
					padding: 20upx 0;
					.box{
						width: 30%;
						height: 120upx;
						display: flex;
						justify-content: center;
						align-items: center;
						border-radius: 10upx;
						box-shadow: 0upx 5upx 20upx rgba(0,0,0,0.05);
						font-size: 36upx;
						background-color: #f1f1f1;
						color: 333;
						&.on{
							background-color: $uni-color-success;
							color: #fff;
						}
					}
				}
				.num{
					margin-top: 10upx;
					display: flex;
					justify-content: flex-end;
					align-items: center;
					.text{
						padding-right: 10upx;
						font-size: 30upx;
					}
					.input{
						width: 28.2vw;
						border-bottom: solid 2upx #999;
						
						justify-content: flex-end;
						align-items: center;
						input{
							margin: 0 20upx;
							height: 60upx;
							font-size: 30upx;
							color: $uni-color-success;
							justify-content: flex-end;
							align-items: center;
						}
					}
				}
			}
			.pay-list{
				width: 100%;
				border-bottom: solid 1upx #eee;
				.row{
					width: 100%;
					height: 120upx;
					display: flex;
					align-items: center;
					.left{
						width: 100upx;
						flex-shrink: 0;
						display: flex;
						align-items: center;
						image{
							width: 80upx;
							height: 80upx;
						}
					}
					.center{
						width: 100%;
						font-size: 30upx;
					}
					.right{
						width: 100upx;
						flex-shrink: 0;
						display: flex;
						justify-content: flex-end;
					}
				}
			}
		}
	}
	.pay{
		margin-top: 20upx;
		width: 100%;
		display: flex;
		justify-content: center;
		flex-wrap: wrap;
		.btn{
			width: 70%;
			height: 80upx;
			border-radius: 80upx;
			display: flex;
			justify-content: center;
			align-items: center;
			color: #fff;
			background-color: $uni-color-success;
			box-shadow: 0upx 5upx 10upx rgba(0,0,0,0.2);
		}
		.btn-tixi{
			color: $uni-color-success;
			background: #FFFFFF;
			border:1upx solid $uni-color-success;
		}
		.tis{
			margin-top: 10upx;
			width: 100%;
			font-size: 24upx;
			display: flex;
			justify-content: center;
			align-items: baseline;
			color: #999;
			.terms{
				color: #5a9ef7;
			}
		}
	}
</style>
