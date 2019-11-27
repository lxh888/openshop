<template>
	<view class="content">
		<view class="top">
			长按图片保存，分享出去<br>
			
		</view>
		<view class="top">
			
		</view>
		<!-- #ifdef APP-PLUS -->
		<view class="box">
			<view class="box_top">
				APP推荐码，请使用APP扫描二维码
			</view>
			<view class="img" @click="img">
			   <image :src="imgsrc" mode=""></image>
		    </view>
		</view>
		<!-- #endif -->
		<!-- #ifdef MP-WEIXIN -->
		<view class="box">
			<view class="box_top">
				小程序推荐码，请使用小程序扫描二维码
			</view>
			<view class="img" @click="WxImage">
			   <image :src="imgwx" mode=""></image>
		    </view>
		</view>
		<!-- #endif -->
		<!-- #ifdef H5 -->
		<view class="box">
			<view class="box_top">
				网页版推荐码，请使用手机浏览器扫描二维码
			</view>
			<view class="img" @click="WebImage">
			   <image :src="imgweb" mode=""></image>
		    </view>
		</view>
		<!-- #endif -->
		
	</view>
</template>

<script>
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	var ef = new eonfox();
	export default {
		data() {
			return {
				imgsrc:'',
				imgs:[],
				imgwx:'',
				imgwxs:[],
				imgweb:'',
				imgwebs:[]
			}
		},
		onLoad() {
			var that=this
				ef.submit({
				request: { 
					s:['USERSELF'],  //个人信息，判断是否登录
					},
				callback: function(data) {
					var dataLsit=fns.checkError(data,'s',function(errno,error){
						uni.showToast({
							title:'请先登录',
							icon:'none',
							success() {
								setTimeout(function(){
									uni.reLaunch({
										url:'../../pagesA/register/register'
									});
									return
									// uni.reLaunch({
									// 	url:'../../pagesA/threelogin/threelogin'
									// })
								},1000)
							}
						})
					})
					
					// #ifdef H5
					
					that.imgweb = ef.api_server_url+"?"+encodeURI('data=[["USERSIGNUPRECOMMENDPOSTER",[{"type":"web","user_id":"'+dataLsit.s.user_id+'"}]]]')+"&temp=1&application="+ef.application;
					// #endif
					//#ifdef APP-PLUS
					that.imgsrc = ef.api_server_url+"?"+encodeURI('data=[["USERSIGNUPRECOMMENDPOSTER",[{"type":"app","user_id":"'+dataLsit.s.user_id+'"}]]]')+"&temp=1&application="+ef.application;
					// #endif
					//#ifdef MP-WEIXIN
					that.imgwx = ef.api_server_url+"?"+encodeURI('data=[["USERSIGNUPRECOMMENDPOSTER",[{"type":"applet","user_id":"'+dataLsit.s.user_id+'","weixin_applet_config":{"width":"280","height":"280"}}]]]')+"&temp=1&application="+ef.application;
					
					// #endif
					
					that.imgwxs.push(that.imgwx)
					that.imgs.push(that.imgsrc)
					that.imgwebs.push(that.imgweb)
					
				},
				error: function(err) {
					fns.err('err',err,1)
				}
			});
			
			// //#ifdef  H5
			// ef.left_token(function(left_token){
			// 	uni.downloadFile({
			// 		url:ef.api_server_url+"?"+encodeURI('data=[["USERSIGNUPSELFRECOMMENDPOSTER",[{"type":"web","weixin_applet_config":{"width":"260"}}]]]')+"&token="+left_token,
			// 		success: (res) => {
			// 			console.log(res);
			// 			that.imgweb=res.tempFilePath
			// 			that.imgwebs.push(res.tempFilePath)
			// 		}
			// 	});
			// });
			// // #endif
		},
		methods:{
			img(){
				var that=this
				 uni.previewImage({
					urls:that.imgs
				});
			},
			WxImage(){
				var that=this
				 uni.previewImage({
					urls:that.imgwxs,
				});
			},
			WebImage(){
				var that=this
				 uni.previewImage({
					urls:that.imgwebs,
				});
			}
		}
	}
</script>

<style>
	.content{
		width: 100%;
		font-size: 28upx;
		padding-top: 20upx;
		color: #7D7D7D;
	}
	.top{
		width: 90%;
		margin-left: 10%;
		margin-top: 30upx;
	}
	.img{
		width: 99%;
		height: 1110upx;
		margin: auto;
		border: 1upx #E1E1E1 solid;
		display: flex;
	}
	.img image{
		width:100%;
		height: 100%;
	}
	.box{
		width: 86%;
		height: 1200upx;
		margin-left: 7%;
		border: 1upx #f29b87 solid;
		margin-top: 50upx;
		margin-bottom: 40upx;
	}
	.box_top{
		height: 80upx;
		background-color: #f29b87;
		color: #FFFFFF;
		line-height: 80upx;
		text-align: center;
	}
</style>

