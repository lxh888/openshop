<template>
	<view class="content">
		<!-- 头像 -->
		<view class="head-box">
			<view class="box-title">
				<text>头像</text>
			</view>
			<view class="box-inp" @click="ModifyThePicture">
			 <text style="float: right;color: grey;">点击上传头像</text>
			 <image :src="headImg" mode="" style="float: right;"></image>
			</view>
		</view>
		<!-- 昵称 -->
		<view class="head-box">
			<view class="box-title">
				<text>昵称</text>
			</view>
			<view class="box-inp">
				<input type="text" maxlength=15  :placeholder="userNmae" @input="confirminput" /> <!-- v-model="userNmae" --> 
			</view>
		</view>
		<button type="primary" @click="next">提交修改</button>
	</view>
</template>

<script>
  import eonfox from '@/components/eonfox/eonfox.js';
  import fns from '@/components/eonfox/fns.js';
  var ef = new eonfox();
  export default {
    data() {
		return {
			    userNmae :"",
				headImg:'../../static/user.png',
				imageID:'',
				address:''
			};
        },
		onLoad:function() {
			this.load();
		},
		methods:{
			confirminput:function(event){
				var that=this;
				that.userNmae = event.detail.value;
			},
			next(event){	
				var that=this;
				
				ef.submit({
					request:{
						s: ['USERSELFEDIT', [{ user_nickname:that.userNmae}]]
					},
					callback: function(data) {
						console.log('回调成功', data);
						var	fns_=fns.checkError(data,['s'],function(erron, error){
// 							uni.showToast({
// 								title: error,
// 								icon: 'none'
// 							})
							// return;
							});
						     if(data.data.s){
						     	uni.showToast({
						     		title:'修改成功',
						     		icon:'none',
									success() {
										setTimeout(function (){
											uni.navigateBack({
												delta:1
											})
										},3000);
									}
						     	});
						     	
						     }
							
							
							
							},

					error: function(err) {
						uni.showToast({
							title:'出错啦',
							icon:'none'
						})
					}
					
				});
			},
			load(){
				var  __this = this;
				ef.submit({
					request: {
						u:['USERSELF'],
						config:['APPLICATIONCONFIG']
					},
					callback: function(data) {
						console.log("改变",data);
						var dataList = fns.checkError(data,['u'], function(erron, error){	
						});
						
						if(dataList.config && dataList.config.qiniu_domain){
							__this.address=dataList.config.qiniu_domain;
						}
						
						console.log('个人信息', data);
						if(data.data.u.data.user_nickname==''){
							__this.userNmae="未设置，请输入昵称"
						}
						else if(data.data.u.data.user_nickname){
							__this.userNmae =data.data.u.data.user_nickname;
						}
						//头像
						if(data.data.u.data.user_logo_image_id!=""){
							__this.imageID=data.data.u.data.user_logo_image_id;
							if(__this.imageID.length>50){
								__this.headImg + __this.imageID
							}else{
								__this.headImg=__this.address+__this.imageID;
							}
							
							console.log('头像图片',__this.headImg);
							
						}else if(data.data.u.data.user_logo_image_id==""){
							__this.headImg='../../static/user.png'
							console.log("没有头像信息")
						}
					},
					error: function(err) {
						uni.showToast({
							title:'出错啦',
							icon:'none'
						})
					}
					
				});
			},
			ModifyThePicture(){
				var _this=this
				uni.chooseImage({
					count: 1,
					sizeType: ['compressed'],
					sourceType: ['album'],
					success: (res) => {
						console.log('chooseImage success, temp path is', res.tempFilePaths[0])
						var imageSrc = res.tempFilePaths[0]
						ef.left_token(function(left_token){
							//encodeURIComponent  encodeURI
							var uploadUrl=ef.api_server_url+"?"+encodeURI('data=[["USERSELFLOGOUPLOAD"]]')+"&token="+left_token;
							uni.uploadFile({
								url: uploadUrl,
								filePath: imageSrc,
								fileType: 'image',
								name: 'file',
								success: (res) => {
									console.log('上传成功:',res.data)
									uni.showToast({
										title: '上传成功',
										icon: 'success',
										duration: 1000,
										success() {
											_this.headImg = imageSrc
										}
									})
								},
								fail: (err) => {
									console.log('uploadImage fail', err);
									uni.showModal({
										content: err.errMsg,
										showCancel: false
									});
								}
							});
						});
					},
					fail: (err) => {
						console.log('chooseImage fail', err)
						// uni.showToast({
						// 	title:JSON.stringify(err),
						// 	icon: 'none',
						// 	duration:2000
						// })
					}
				})
			}
		}
	}
   
</script>

<style>
	.content{
		width:94%;
		height: auto;
		margin-left: 2%;
		font-size: 15px;
	}
	.head-box{
		width: 100%;
		height: 60px;
		border-bottom:1px solid #e8e8e8 ;
		line-height: 60px;
	}
	.box-title{
		width: 30%;
		height: 60px;
		line-height: 60px;
		float: left;
	}
	.box-inp{
		width: 50%;
		height: 60px;
		line-height: 60px;
		float: right;
		text-align: right;
	}
	.box-inp input{
		float: left;
		width: 100%;
		height:60px;
		
	}
	
	button{
		margin-top: 80px;
		background :#F76968;
		color: #fff;
	}
	image{
		width:40px;
		height:40px;
		border-radius :50%;
		margin-top: 10px;
		}
</style>
