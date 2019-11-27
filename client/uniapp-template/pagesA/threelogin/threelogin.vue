<template>
	<view>
		<view>
			<image src="../../static/close.svg"  style="float: right;width: 30px;height: 30px;opacity: 0.8;padding: 15px;box-sizing:initial;" @click="close_"></image>
		</view>
		<view class="conter">
			
			<view class="conter-text">
				微信快捷登录
			</view>
			<view class="conter-img" style="position: relative;">
				<image src="http://rs.eonfox.cc/clzy/static/WeChats.png" mode="" style="position: absolute;"></image>
				<button @click="oauth_()" open-type="getUserInfo" style="background-color: #ffffff;border: none;opacity: 0;height: 65px;">
				</button>
			</view>
		</view>
	</view>
</template>

<script>
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	var ef=new eonfox()
	export default{
		data(){
			return{
				code:''
			}
		},
		onLoad() {

		},
		methods:{
			//--
			close_(){
				 // #ifdef H5
					uni.reLaunch({
						url:'../../pagesA/public/register'
					})
				// #endif
				// #ifndef H5
					uni.reLaunch({
						url:'../../pagesA/public/register'
					});
				// #endif
				
			},
			oauth_(){
				uni.showLoading({
					title:'正在登陆'
				})
				var _this=this
				// #ifdef MP-WEIXIN
				wx.getSetting({
				  success(res) {
					if (!res.authSetting['scope.userInfo']) {
					  wx.authorize({
						scope: 'scope.userInfo',
						success() {
							console.log('已同意授权');
							// 用户已经同意
							fns.oauth_()
						  _this.login();
						}
					  })
					}else{
						
						console.log('已授权');
						fns.oauth_()
						_this.login()
					}
				  }
				})
				return;
				// #endif
				// #ifdef APP-PLUS
				fns.oauth_()
				 _this.login();
				// #endif
				
			},
			login(){
				console.log('star');
				var _this=this
							// #ifdef MP-WEIXIN
							wx.login({
								success(res) {
									wx.getUserInfo({
										success(re) {
											re.code=res.code;
											console.log('re:',re);
											ef.submit({
												request:{s:['USERLOGINWEIXIN',['applet',re]]},
												callback(data){
													console.log(data);
													if(fns.checkError(data,'s',function(errno,error){
														uni.showToast({
															title:error,
															icon:'none',
															success() {
																setTimeout(function(){
																	uni.reLaunch({
																		url:'../../pagesA/public/register'
																	})
																},1000)
															}
														})
													})){
														uni.showToast({
																title:'登陆成功',
																success() {
																	fns.unionid()
																	setTimeout(function(){
																		uni.reLaunch({
																			url:'../../pages/user/user'
																		})
																	},1000)
																}
														})
													}
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
							console.log('oauto star');
							uni.login({
							  provider: 'weixin',
							  success: function (loginRes) {
								  console.log('loginres:',typeof loginRes);
								  if (loginRes.errMsg=='login:ok') {
											// 获取用户信息
											uni.getUserInfo({
											  provider: 'weixin',
											  success: function (infoRes) {
												  ef.submit({
												  	request:{s:['USERLOGINWEIXIN',['app',infoRes.userInfo]]},
												  	callback(data){
												  		console.log(data);
														console.log(JSON.stringify(data));
												  		if(fns.checkError(data,'s',function(errno,error){
												  			uni.showToast({
												  				title:error,
												  				icon:'none',
												  				success() {
												  					setTimeout(function(){
												  						uni.reLaunch({
												  							url:'../../pagesA/public/register'
												  						})
												  					},1000)
												  				}
												  			})
												  		})){
												  			console.log('登陆成功');
															fns.unionid()
												  			uni.showToast({
												  				title:'登陆成功',
												  				success() {
												  					setTimeout(function(){
												  						uni.reLaunch({
												  							url:'../../pages/user/user'
												  						})
												  					},1000)
												  				}	
												  			})
												  		}
												  	},
												  	error(err){
														fns.err('err',err,1)
												  	}
												  })
												  
												},
											  fail(err){
												 fns.err('err',err,1)
											  }
											});
								  }else{
									  fns.err('err','err',1)
								  }
								  
							  },
							  fail(err) {
							  	fns.err('err','login',1)
							  }
							  
							});
							// #endif
			},
			//--
		}
	}
</script>

<style>
	
	.conter{
		width: 100%;
		height: 100%;
		padding-top: 450upx;
	}
	.conter-text{
		width: 100%;
		height: 40upx;
		text-align: center;
		font-size: 32upx;
		color: #333333;
		
	}
	
	.conter-img image{
		width:100upx;
		height:100upx;
		margin-top: 40upx;
		left: 325upx;
	
	}
	.button{
		background-color: transparent;
	}
</style>
