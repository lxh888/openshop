<template>
	<view class="conten">
		<view class="conten-top">
			<view class="conten-top-title">
				<text>{{ money / 100 }}</text>
			</view>
			<view class="conten-top-text"><text>可提现金额</text></view>
		</view>
		<view class="detail" @click="goDetail">
			<text>提现明细</text>
			<view class="detailedRight"><image src="http://rs.eonfox.cc/clzy/static/Arrow_right02.png" class="detailedRightImg"></image></view>
		</view>
		<view class="withdrawDeposit" v-show="!display">
			<view class="withdrawDeposit-title"><text>请输入提现金额</text></view>
			<text class="abposin">￥</text>
			<view class="withdrawDeposit-box">
				<input type="digit" v-model="moneyTx" placeholder="此处金额在100~10000间且每日提现不能超过三次" placeholder-style="font-size:24upx" />
			</view>
			<view class="withdrawDeposit-text"><text>请选择提现账户</text></view>
		</view>
		<view class="BopBox" v-show="display">
			<view class="informationBox">
				<view class="infor-title"><text>支付宝账号:</text></view>
				<view class="infor-input"><input type="text" value="" @input="inpID" :placeholder="ID" /></view>
			</view>
			<view class="informationBox">
				<view class="infor-title"><text>真实姓名:</text></view>
				<view class="infor-input"><input type="text" value="" @input="inpName" :placeholder="name" /></view>
			</view>
			<view class="affirmBut"><button type="primary" @click="next(0)">确认</button></view>
		</view>
		
		<view class="BopBox" v-show="wxdisplay">			
			<view class="informationBox">
				<view class="infor-title"><text>真实姓名:</text></view>
				<view class="infor-input"><input type="text" value="" @input="wxinpName" :placeholder="wxname" /></view>
			</view>
			<view class="informationBox">
				<view class="infor-title"><text>openid:</text></view>
				<view class="infor-input"><input type="text" value="" disabled="true" @input="wxopenid" :placeholder="openid" /></view>
			</view>
			<view class="affirmBut"><button type="primary" @click="next(1)">确认</button></view>
		</view>
		<!-- <view class="payw">
			<image src="http://rs.eonfox.cc/clzy/static/ali_pay.png" mode=""></image>
			<text class="txt" @click="payAli">支付宝</text>
			<text style="float: right; padding-right:40upx;color: #598fc7;" @click="addpayAli">添加支付宝账号信息</text>
		</view>
		<view class="pay" @click="payWx">
			<image src="http://rs.eonfox.cc/clzy/static/wechat.png" mode=""></image>
			<text class="txt">微信</text>
		</view> -->
		<view style="float:left;display: flex;width: 100%;flex-direction: column;" v-show="!display">
			<view class="payw" v-if="alipaysstate==1">
				<image src="http://rs.eonfox.cc/clzy/static/ali_pay.png" mode=""></image>
				<text class="txt" @click="popBox(0)">支付宝</text>
				<label class="radio"><radio value="r1" :checked="!choice" @click="xz('z')" /></label>
			</view>
			<view class="pay" v-if="weixinsstate==1">
				<image src="http://rs.eonfox.cc/clzy/static/wechat.png" mode=""></image>
				<text class="txt" @click="popBox(1)">微信</text>
				<label class="radio"><radio value="r2" :checked="choice" @click="xz('w')" /></label>
			</view>
		</view>
		<button class="confirm-btn" @click="WithdrawBut" v-show="!display">提现</button>
	</view>
</template>

<script>
import uniIcon from '@/components/uni-icon/uni-icon.vue';
import fns from '@/components/eonfox/fns.js';
import eonfox from '@/components/eonfox/eonfox.js';
var ef = new eonfox();
export default {
	data() {
		return {
			money: '0.00',
			moneyTx: '',
			Sofar: '',
			code: '',
			openid: '', //APPopenid
			wcopenid:'', //小程序code
			hWxopenid:'', //公众号openid
			ID: '', //支付宝账号
			name: '', //支付宝真实姓名
			wxname:'', //微信真实姓名
			choice: true,
			display: false,
			wxdisplay:false,
			isAuth: false,
			wxisAuth:false,
			alipaysstate:'',
			weixinsstate:''
		};
	},
	onLoad(){
		let that = this
		// #ifdef H5
		const value =  uni.getStorageSync('openid')
		console.log(value)
		if(value){
			that.openid = value
		}else{
			that.getuserToken();
		}
		// #endif
		// #ifdef MP-WEIXIN
			uni.login({
				provider:'weixin',
				success(res) {
					console.log(res,'信息')
					ef.submit({
						request:{
							huo:["SESSIONWEIXINAPPLETUSERINFO",[{"js_code":res.code}]]
						},
						callback:function(data){
							console.log(data.data.huo.data,'获取')
							that.openid = data.data.huo.data.openId
						}
					})
					return
					// uni.getUserInfo({
					// 	provider: 'weixin',
					// 	success(re) {
					// 		console.log(JSON.stringify(res.userInfo),'信息')
					// 		// console.log(re)
					// 		ef.submit({
					// 			request:{
					// 				huo:["SESSIONWEIXINAPPLETUSERINFO",[{"js_code":res.code,"weixin_data":re}]]
					// 			},
					// 			callback:function(data){
					// 				// console.log(data.data.huo.data.openId,'获取')
					// 				that.openid = data.data.huo.data.openId
					// 			}
					// 		})
					// 	}
					// })
					
				}
			})
		// #endif
		// #ifdef APP-PLUS
			uni.login({
				provider:'weixin',
				success(res) {
					// console.log(res)
					if (res.errMsg == 'login:ok') {
						// 获取用户信息
						uni.getUserInfo({
							provider: 'weixin',
							success: function(infoRes) {
								// console.log('info', JSON.stringify(infoRes.userInfo));
								that.openid = infoRes.userInfo.openId;
								
							},
							fail(err) {
								fns.err('err', err, 1);
							}
						});					
					}
				}
			})
		// #endif
	},
	onShow() {
		uni.setNavigationBarColor({
			frontColor: '#ffffff',
			backgroundColor: '#ff5252',
			animation: {
				duration: 400,
				timingFunc: 'easeIn'
			}
		});
		this.moneyQuery();
		var that = this;
		ef.submit({
			request: {
				u: ['USERSELFCONFIGALIPAY'],
				wx:['USERSELFCONFIGWEIXINPAY'],
				config:['APPLICATIONCONFIG']
			},
			callback: function(data) {
				// console.log('用户支付宝信息', data);
				var ulist = fns.checkError(data, ['u','wx','config'], function(errno, error) {
					uni.showToast({
						title: error,
						icon: 'none'
					});
				});
				
				// console.log('ulist', ulist);
				if(ulist.config){
					// console.log(ulist.config,'config')
					that.alipaysstate = ulist.config.alipay_withdraw_access.state;
					that.weixinsstate = ulist.config.weixin_pay_access.state
				}
				// #ifdef H5
				if(ulist.wx.trade_type == "MPJSAPI"){ //微信				
					that.wxname = ulist.wx.realname
					// that.openid = ulist.wx.openid
					that.wxisAuth = true;
				}
				// #endif
				// #ifdef APP-PLUS
				if(ulist.wx.trade_type == "APP"){ //微信
					that.wxname = ulist.wx.realname
					// that.openid = ulist.wx.openid
					that.wxisAuth = true;
				}
				// #endif
				// #ifdef MP-WEIXIN
				if(ulist.wx.trade_type == "JSAPI"){ //微信
					that.wxname = ulist.wx.realname
					// that.openid = ulist.wx.openid
					that.wxisAuth = true;
				}
				// #endif
				
				if (ulist.u.account) {//支付宝
					that.ID = ulist.u.account;
					that.name = ulist.u.realname;
					that.isAuth = true;
				}

				
			},
			error: function(err) {
				console.log('出错啦', err);
			}
		});
	},
	methods: {
		goDetail() {
			uni.navigateTo({
				url: '../../pagesA/transactionDetail3/transactionDetail3'
			});
		},
		payAli() {
			var _this = this;
			var money = _this.moneyTx * 100;
			ef.submit({
				request: {
					s: [
						'USERWITHDRAWSELFADD',
						[
							{
								type: 'user_money',
								value: money,
								method: 'alipay',								
								alipay_account: _this.ID, //支付宝账号
								alipay_realname: _this.name //真实姓名
							}
						]
					]
				},
				callback(data) {
					console.log(data);
					var re = fns.checkError(data, 's', function(errno, error) {
						uni.showToast({
							title: error,
							icon: 'none'
						});
					});
					if (data.data.s.errno == 0) {
						uni.showToast({
							title: '已发起提现，待审核',
							icon: 'none'
						});
						setTimeout(function() {
							uni.navigateBack({
								url: '../../pages/me/me'
							});
						}, 2000);
					}
				},
				error(err) {
					fns.err('调用提现错误', err, 1);
				}
			});
			this.moneyQuery();
		},
		addpayAli() {
			uni.navigateTo({
				url: '../../pagesA/addpayAli/addpayAli'
			});
		},
		payWx() {
			var _this = this;
			var money = _this.moneyTx * 100;
			console.log('提现金额', money);
			console.log('我的账户', _this.money);
			console.log(money <= 0, money > _this.money);
			if (money <= 0 || money > _this.money || !/^\d+(\.\d+)?$/.test(money)) {
				uni.showToast({
					title: '提现金额错误',
					icon: 'none'
				});
			} else {
				// #ifdef H5
				console.log('h5测试下')
				_this.tiXian(_this.openid);
				// #endif
				
				// #ifdef MP-WEIXIN
				_this.tiXian(_this.openid);				
				// #endif
				// #ifdef APP-PLUS
				_this.tiXian(_this.openid);
				// #endif
			}
		},		
		
		tiXian(openid) {
			var _this = this;
			var money = _this.moneyTx * 100;
			ef.submit({
				request: {
					s: [
						'USERWITHDRAWSELFADD',
						[
							{
								type: 'user_money',
								value: money,
								comment: '提现',
								method: 'weixinpay',
								// #ifdef H5
								weixin_login_openid: openid,
								weixin_trade_type: 'MPJSAPI',
								// #endif
								// #ifdef MP-WEIXIN
								weixin_login_openid: openid,
								weixin_trade_type: 'JSAPI',
								// #endif
								// #ifdef APP-PLUS
								weixin_login_openid: openid,
								weixin_trade_type: 'APP'
								// #endif
							}
						]
					]
				},
				callback(data) {
					var re = fns.checkError(data, 's', function(errno, error) {
						fns.err('err', error);
					});
					if (data.data.s && data.data.s.data) {
						uni.showToast({
							title: '已发起提现，待审核',
							success() {
								setTimeout(function() {
									uni.redirectTo({
										url: '../../pagesB/account/account'
									});
								}, 2000);
							}
						});
					}
					console.log('tixian', data);
				},
				error(err) {
					fns.err('调用提现错误', err, 1);
				}
			});
		},
		moneyQuery() {
			var _this = this;
			ef.submit({
				request: { s: ['USERMONEYSELFTOTAL'] },
				callback(data) {
					// console.log('我的预付款查询结果·', data);
					var re = fns.checkError(data, 's', function(errno, error) {
						uni.showToast({
							title: error
						});
					});
					console.log('filter', re);
					if (re) {
						_this.money = re.s;
					}
				},
				error(err) {
					fns.err('接口调用失败', err);
				}
			});
		},
		xz(fun) {
			var that = this;
			if (fun == 'z') {
				that.choice = false;
			} else {
				
				console.log('选择微信');
				that.choice = true;
			}
		},
		popBox(ty) {
			var that = this;
			if(ty == 0){ //支付宝
				that.display = !that.display;
			}else{ //微信
				that.wxdisplay = !that.wxdisplay;
				that.display = !that.display;
			}
			
		},
		inpID: function(event) {
			var that = this;
			that.ID = event.detail.value;
		},
		inpName: function(event) {
			var that = this;
			that.name = event.detail.value;
		},
		wxinpName:function(event2){
			console.log(event2)
			var that = this;
			that.wxname = event2.detail.value;
		},
		wxopenid:function(e){
			var that = this;
			that.wxopenid = e.detail.value;
		},
		next(typ) {
			var that = this;
			console.log(that.openid,'openid')
			that.display = !that.display;
			if(typ == 1){ //微信
				that.wxdisplay = !that.wxdisplay;
				// return
				ef.submit({
					request:{
						u: ['USERSELFCONFIGWEIXINPAY',
							[{
								realname: that.wxname,
								// #ifdef H5
								openid:that.openid,
								trade_type:'MPJSAPI',
								// #endif
								// #ifdef MP-WEIXIN
								openid:that.openid,
								trade_type:'JSAPI',
								// #endif
								// #ifdef APP-PLUS
								openid:that.openid,
								trade_type:'APP',
								// #endif
							}]
						]
					},
					callback: function(data) {
						console.log('用户微信信息', data);
						var ulist = fns.checkError(data, 'u', function(errno, error) {
							uni.showToast({
								title: error,
								icon: 'none'
							});
						});
						if (ulist.u) {
							uni.showToast({
								title: '保存成功',
								icon: 'none',
								success() {
									that.wxisAuth = true;
								}
							});
						}
					},
					error: function(err) {
						console.log('出错啦', err);
					}
				})
			}else{//支付宝
				ef.submit({
					request: {
						u: ['USERSELFCONFIGALIPAY', [{ account: that.ID, realname: that.name }]]
					},
					callback: function(data) {
						console.log('用户支付宝信息', data);
						var ulist = fns.checkError(data, 'u', function(errno, error) {
							uni.showToast({
								title: error,
								icon: 'none'
							});
						});
						if (ulist.u) {
							uni.showToast({
								title: '保存成功',
								icon: 'none',
								success() {
									that.isAuth = true;
								}
							});
						}
					},
					error: function(err) {
						console.log('出错啦', err);
					}
				});
			}
			
			
		},

		WithdrawBut() {
			console.log('点击提现按钮');
			
			var that = this;
			// if(!that.choice){
			// 	console.log('请选择提现方式')
			// 	return
			// }
			// true支付宝
			// if(that.choice==true){
			// 	that.payAli();
			// }
			// false微信
			if (that.moneyTx < 100) {
				fns.err('提现金额必须大于等于100元');
				return;
			}
			//
			if (that.choice == false) {
				console.log(11111);
				console.log('提现支付宝', that.isAuth);

				if (!that.isAuth) {
					uni.showToast({
						title: '请设置支付宝信息',
						icon: 'none',
						success() {
							that.display = !that.display;
							// that.wxdisplay = !that.wxdisplay;
						}
					});
					return;
				}
				if (that.ID == '') {
					that.display = !that.display;
					// that.wxdisplay = !that.wxdisplay;
				} else {
					uni.showModal({
						title: '提示',
						content: '你是否要提现' + that.moneyTx + '元到' + that.ID + '账号',
						success: function(res) {
							if (res.confirm) {
								that.payAli();
							} else if (res.cancel) {
								that.display = !that.display;
								// that.wxdisplay = !that.wxdisplay;
							}
						}
					});
				}
			} else {
				console.log('提现微信', that.wxisAuth);
				if (!that.wxisAuth) {
					uni.showToast({
						title: '请设置微信信息',
						icon: 'none',
						success() {
							that.display = !that.display;
							that.wxdisplay = !that.wxdisplay;
						}
					});
					return;
				}
				if (that.wxname == '') {
					that.display = !that.display;
					that.wxdisplay = !that.wxdisplay;
				} else {
					uni.showModal({
						title: '提示',
						content: '你是否要提现' + that.moneyTx + '元到' + that.wxname + '账号',
						success: function(res) {
							if (res.confirm) {
								that.payWx();
							} else if (res.cancel) {
								that.display = !that.display;
								// that.wxdisplay = !that.wxdisplay;
							}
						}
					});
					
				}
				// return
				
			}
		},
		getuserToken() { //h5授权
			let that = this;
			ef.submit({
				request:{
					token:['SESSIONWEIXINACCESSTOKEN'],
					// userinfo:['USERSELF']
				},
				callback: function(data) {
					let dataToken=fns.checkError(data,'token',function(errno,error){
						uni.showToast({
							title: '请先确认微信授权',
							icon:'none'
						})
					})
					
					if(dataToken){
						// console.log(dataToken.token,'123456')
						that.openid = dataToken.token.openid
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
				}
			})
		},
		//-----
	}
};
</script>

<style>
.conten {
	width: 100%;
	height: 100%;
}
.conten-top {
	width: 100%;
	height: 250upx;
	background-color: #ff5252;
	padding-top: 80upx;
	color: #ffff;
}
.conten-top-text {
	width: 100%;
	height: 50upx;
	text-align: center;
	font-size: 32upx;
	margin-top: 30upx;
}
.conten-top-text text {
	font-size: 32upx;
	color: #ffffff;
}
.conten-top-title {
	width: 100%;
	height: 50upx;
	text-align: center;
}
.conten-top-title text {
	font-size: 56upx;
	color: #ffffff;
}
.detail {
	float: left;
	width: calc(100% - 20upx);
	height: 100upx;
	border-bottom: 1px solid #dedbdb;
	line-height: 100upx;
	padding-left: 20upx;
	font-size: 32upx;
}
.withdrawDeposit {
	width: 100%;
	height: 300upx;
	font-size: 28upx;
	float: left;
}
.withdrawDeposit-title {
	width: 90%;
	color: #666666;
	margin-left: 10%;
	line-height: 100upx;
	height: 100upx;
}
.withdrawDeposit-box {
	width: 87%;
	height: 70upx;
	background-color: #e8e8e8;
	margin-left: 10%;
	margin-top: 10upx;
	line-height: 70upx;
}
.withdrawDeposit-box input {
	padding: 10upx 0 0 5upx;
}
.withdrawDeposit-text {
	width: 100%;
	height: 70upx;
	text-align: center;
	line-height: 70upx;
	margin-top: 30upx;
}
.payw {
	/* float: left; */
	flex: 1;
	width: calc(100% - 30upx);
	height: 100upx;
	border-top: 1px #f5f5f5 solid;
	border-bottom: 1px #f5f5f5 solid;
	line-height: 100upx;
	font-size: 28upx;
	margin-left: 30upx;
}
.pay {
	/* float: left; */
	flex: 1;
	width: calc(100% - 30upx);
	height: 100upx;
	border-bottom: 1px #dedbdb solid;
	line-height: 100upx;
	font-size: 28upx;
	margin-left: 30upx;
	margin-bottom: 80upx;
}
.abposin {
	position: absolute;
	width: 40upx;
	height: 40upx;
	margin-left: 15upx;
	font-size: 48upx;
	margin-top: 8upx;
}
image {
	width: 48upx;
	height: 48upx;
	margin: 20upx 20upx;
	float: left;
}
.txt {
	width: 510upx;
	height: 100upx;
	line-height: 100upx;
	float: left;
}
.radio {
	float: right;
	padding-right: 50upx;
}
.confirm-btn {
	width: 95%;
	/* height :55px; */
	color: #fff;
	margin-left: 2.5%;
	background-color: #458b74;
	border-radius: 4px;
}

/* 弹出框样式 */
.BopBox {
	width: 100%;
	height: 400upx;
	position: absolute;
	background-color: #ffffff;
	z-index: 1000;
}

.BopBox-title {
	width: 100%;
	height: 60upx;
	font-size: 32upx;
	text-align: center;
}
.informationBox {
	width: 100%;
	padding: 5upx;
	height: 80upx;
	line-height: 80upx;
	border-bottom: #eee 1px solid;
	font-size: 28upx;
}
.infor-title {
	float: left;
	width: 25%;
	height: 80upx;
	text-align: right;
}
.infor-input {
	padding-left: 10upx;
	width: 70%;
	height: 50upx;
	float: left;
}
.infor-input input {
	width: 100%;
	float: left;
	height: 80upx;
	line-height: 80upx;
}
.affirmBut {
	width: 95%;
	height: 60upx;
	margin-top: 80upx;
	margin-left: 2.5%;
}
.detailedRight {
	width: 44px;
	position: relative;
	right: 10px;
	top: 5px;
	float: right;
	text-align: right;
}

.detailedRightImg {
	width: 20px;
	height: 20px;
}
</style>
