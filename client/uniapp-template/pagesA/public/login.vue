<template>
	<view class="container">
		<view class="left-bottom-sign"></view>
		<view class="back-btn yticon icon-zuojiantou-up" @click="navBack"></view>
		<view class="right-top-sign"></view>
		<!-- 设置白色背景防止软键盘把下部绝对定位元素顶上来盖住输入框等 -->
		<view class="wrapper">
			<view class="left-top-sign">LOGIN</view>
			<view class="welcome">
				欢迎回来！
			</view>
			<view class="input-content">
				<view class="input-item">
					<text class="tit">手机号码</text>
					<input 
						type="number" 
						:value="phone" 
						placeholder="请输入手机号码"
						maxlength="11"
						data-key="phone"
						@input="inputChange"
					/>
				</view>
				<view class="input-item">
					<text class="tit">密码</text>
					<input 
						type="mobile" 
						value="" 
						placeholder="8~24位不含特殊字符的数字、字母组合"
						placeholder-class="input-empty"
						maxlength="20"
						password 
						data-key="password"
						@input="inputChange"
						@confirm="toLogin"
					/>
				</view>
				<view class="section_verify" v-if="verifyCode.isNeed">
					<input class="verifyCodeText" type="text" v-model="verifyCode.code"  maxlength="6"  placeholder="请输入验证码" placeholder-style="font-size:26upx"/>
					<image class="verifyCodeImg" @click="getVerifyImage()" :src="verifyCode.codeImg"   ></image>
				</view>
			</view>
			<button class="confirm-btn" @click="toLogin" :disabled="logining">登录</button>
			<view class="forget-section" @click="toForget">
				忘记密码?
			</view>
		</view>
		<view class="register-section">
			还没有账号?
			<text @click="toRegist">马上注册</text>
		</view>
	</view>
</template>

<script>
	import {  
        mapMutations  
    } from 'vuex';
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	var ef = new eonfox();
	export default{
		data(){
			return {
				phone: '',
				password: '',
				logining: false,
				verifyCodeImage: '',
				verifyCode:{
					isNeed:false,
					codeImg:'',
					code:''
				},
			}
		},
		onLoad(){
			
		},
		methods: {
			...mapMutations(['login']),
			inputChange(e){
				const key = e.currentTarget.dataset.key;
				this[key] = e.detail.value;
			},
			navBack(){
				uni.switchTab({
				    url: '../../pages/index/index'
				});
			},
			toRegist(){
				uni.navigateTo({
					url:'../../pagesA/public/register'
				})
			},
			toForget(){
				uni.navigateTo({
					url:'../../pagesA/public/forget'
				})
			},
			async toLogin(){
				var _this=this
				var phone=this.phone,password=this.password;
				if (!/^1\d{10}$/.test(phone)){
					uni.showToast({
						title:'手机号有误',
						icon:'none'
					})
				}else if (password.length < 8 ||password.length > 24) {
					uni.showToast({
						title:'密码长度为8~24个字符',
						icon:'none'
					})
				}else {
					_this.isVerify()
					if(_this.verifyCode.isNeed==true){
						var req={ 
								phone: _this.phone, 
								password: _this.password,
								image_verify_code:_this.verifyCode.code,
								image_verify_key:'user/log_in' 
							}
					}else{
						var req={ 
								phone: _this.phone, 
								password: _this.password
							}
					}
					ef.submit({
						request: { s: ['USERLOGIN', [req]] },
						callback: function(data) {
							var result=data.data.s
							if(!result.errno){
								uni.showToast({
									title:'登录成功',
									icon:'success',
									success() {
										ef.submit({
											request:{
												u:['USERSELF',[{"oauth_id":_this.oauthid}]],
												p:['USERPHONESELFVERIFY']
											},
											callback:function(data){
												console.log(data)
												// return
												uni.setStorage({ //存进本地存储
													key:'userinfo',
													data:data
												})
											}
										})
										_this.bind()
										setTimeout(function(){
											uni.switchTab({
												url:
													'../../pages/user/user'
											});
										},1500)
									}
								})
								
							
							}else{
								if(_this.verifyCode.isNeed){
									_this.getVerifyImage()
								}
								uni.showToast({
									title:result.error,
									icon:'none'
								})
								
							}
							console.log(data);
							
						},
						error: function(err) {
							console.log('出错啦', err);
							uni.showToast({
								title:JSON.stringify(err),
								icon:'none'
							})
						}
					});
				} 
				
			},
			isVerify:function(phone){//检测是否需要验证码
				console.log('检测是否需要验证码');
				var that=this;
				var ef = new eonfox();
				ef.submit({
					request:{s:['USERLOGINIMAGEVERIFYCODESTATE',[{phone:that.phone}]]},
					callback:function(data){
						console.log('检测结果：',data);
						if(!data.errno){
							console.log('验证码s：',data.data.s);
							if(data.data.s.data){
									//需要验证码
									console.log('验证码需要：',data.data.s);
									that.verifyCode.isNeed=true,
									that.getVerifyImage()
							}else{
								return;
							}
						}else{
							console.log('出错啦', data);
								uni.showToast({
									title:data.data.error,
									icon:'none'
								})
						}
						
					},
					error:function(){
						console.log('出错啦', err);
							uni.showToast({
								title:'出错啦',
								icon:'none'
							})
					}
				})
			},
			getVerifyImage() {
				var _this = this;
				var ef = new eonfox();
				ef.left_token(function(left_token){
					//encodeURIComponent  encodeURI
					
					let verifyImage = ef.api_server_url+"?"+encodeURI('data=[["SESSIONIMAGEVERIFYCODESHOW",[{"image_verify_key":"user/log_in","bg_color_rand":0,"bg_color":[242,242,242],"length":5,"width":200,"height":44}]]]')+"&token="+left_token;
					_this.verifyCode.codeImg = verifyImage;
				});
			},
			//绑定
			bind(){
				var _this=this
				console.log('oauth');
				uni.getStorage({
					key:'oauth',
					success(re) {
						if(re.data){
							_this.bind_()
						}
					}
				})
			},
			bind_(){
				console.log('bind');
				var _this=this
							// #ifdef MP-WEIXIN
							wx.login({
								success(res) {
									wx.getUserInfo({
										success(re) {
											re.code=res.code;
											console.log('re:',re);
											ef.submit({
												request:{s:['USERSELFBINDWEIXIN',['applet',re]]},
												callback(data){
													console.log(data);
													fns.unionid()
													return
												},
												error(err){
													fns.err('err',err)
												}
											})
										}
									})
								}
							})
							// #endif
							// #ifdef APP-PLUS
							uni.showToast({
								title:'正在绑定',
								icon:'loading'
							})
							console.log('oauto star');
							uni.login({
							  provider: 'weixin',
							  success: function (loginRes) {
								  console.log('loginres:',typeof loginRes);
								  if (loginRes.errMsg=='login:ok') {
											// 获取用户信息
											uni.showToast({
												title:'获取信息',
												icon:'loading'
											})
											uni.getUserInfo({
											  provider: 'weixin',
											  success: function (infoRes) {
												  uni.showToast({
												  	title:'获取成功',
												  	icon:'loading'
												  })
												  ef.submit({
												  	request:{s:['USERSELFBINDWEIXIN',['app',infoRes.userInfo]]},
												  	callback(data){
												  		console.log(data);
														console.log(JSON.stringify(data));
														fns.unionid()
												  	},
												  	error(err){
														console.log(err);
												  	}
												  })
												  
												},
											  fail(err){
												  console.log(err);
											  }
											});
								  }else{
									 console.log(err);
								  }
								  
							  },
							  fail(err) {
							  	console.log(err);
							  }
							  
							});
							// #endif
			}
		},

	}
</script>

<style lang='scss'>
	page{
		background: #fff;
	}
	.container{
		padding-top: 115px;
		position:relative;
		width: 100vw;
		height: 100vh;
		overflow: hidden;
		background: #fff;
	}
	.wrapper{
		position:relative;
		z-index: 90;
		background: #fff;
		padding-bottom: 40upx;
	}
	.back-btn{
		position:absolute;
		left: 40upx;
		z-index: 9999;
		padding-top: var(--status-bar-height);
		top: 40upx;
		font-size: 40upx;
		color: $font-color-dark;
	}
	.left-top-sign{
		font-size: 120upx;
		color: $page-color-base;
		position:relative;
		left: -16upx;
	}
	.right-top-sign{
		position:absolute;
		top: 80upx;
		right: -30upx;
		z-index: 95;
		&:before, &:after{
			display:block;
			content:"";
			width: 400upx;
			height: 80upx;
			background: #b4f3e2;
		}
		&:before{
			transform: rotate(50deg);
			border-radius: 0 50px 0 0;
		}
		&:after{
			position: absolute;
			right: -198upx;
			top: 0;
			transform: rotate(-50deg);
			border-radius: 50px 0 0 0;
			/* background: pink; */
		}
	}
	.left-bottom-sign{
		position:absolute;
		left: -270upx;
		bottom: -320upx;
		border: 100upx solid #d0d1fd;
		border-radius: 50%;
		padding: 180upx;
	}
	.welcome{
		position:relative;
		left: 50upx;
		top: -90upx;
		font-size: 46upx;
		color: #555;
		text-shadow: 1px 0px 1px rgba(0,0,0,.3);
	}
	.input-content{
		padding: 0 60upx;
	}
	.input-item{
		display:flex;
		flex-direction: column;
		align-items:flex-start;
		justify-content: center;
		padding: 0 30upx;
		background:$page-color-light;
		height: 120upx;
		border-radius: 4px;
		margin-bottom: 50upx;
		&:last-child{
			margin-bottom: 0;
		}
		.tit{
			height: 50upx;
			line-height: 56upx;
			font-size: $font-sm+2upx;
			color: $font-color-base;
		}
		input{
			height: 60upx;
			font-size: $font-base + 2upx;
			color: $font-color-dark;
			width: 100%;
		}	
	}

	.confirm-btn{
		width: 630upx;
		height: 76upx;
		line-height: 76upx;
		border-radius: 50px;
		margin-top: 70upx;
		background: $uni-color-primary;
		color: #fff;
		font-size: $font-lg;
		&:after{
			border-radius: 100px;
		}
	}
	.forget-section{
		font-size: $font-sm+2upx;
		color: $font-color-spec;
		text-align: center;
		margin-top: 40upx;
	}
	.register-section{
		position:absolute;
		left: 0;
		bottom: 50upx;
		width: 100%;
		font-size: $font-sm+2upx;
		color: $font-color-base;
		text-align: center;
		text{
			color: $font-color-spec;
			margin-left: 10upx;
		}
	}
	.section_verify{
		width :100%;
		height :65px;
		margin :20upx auto;
		padding-left: 10px;
		background :#f8f6fc;
		border-radius :10upx;
		position: relative;
		top :5px;
		line-height:65px;
	}
		
	.verifyCodeText{
		width :90px;
		float :left;
		background: #f8f6fc;
		height :65px;
		line-height:65px;
	}
			
	.verifyCodeImg{
		width :50%;
		height: 65px;
		float :right;
	}
			
</style>
