<template>
	<view class="content">
		<view class="list-wraper logo-wraper" @click="uplodingImg(1)">
			<view class="tit">
				<text>点击上传商家logo</text>				
			</view>
			<view class="input-logo" >
				<image v-if="!logImg" src="http://mp.emshop.eonfox.com/zrhzstatic/muying/shop.png" mode=""></image>
				<image v-else :src="logImg" mode=""></image>
			</view>
		</view>
		<view class="list-wraper">
			<view class="tit">
				<text>商家名称：</text>				
			</view>
			<view class="input-all">
				<input class="inpt" type="text" value="" v-model="infor.name"  placeholder="请输入商家名称" />
			</view>
		</view>
		<view class="list-wraper">
			<view class="tit">
				<text>商家微信：</text>				
			</view>
			<view class="input-all">
				<input class="inpt" type="text" value="" v-model="infor.weixin"  placeholder="请输入商家微信" />
			</view>
		</view>
		<view class="list-wraper">
			<view class="tit">
				<text>商家手机号：</text>				
			</view>
			<view class="input-all">
				<input class="inpt" type="number" maxlength="11" v-model="infor.phone" value="" placeholder="请输入手机号" />
			</view>
		</view>
		<view class="list-wraper">
			<view class="tit">
				<text>商家类别：</text>				
			</view>
			<view class="input-all">
				<radio-group>
					 <label class="radio" v-for="(item,index) in shopsType" :key='index'><radio  value="r1"  class="radio" @click="gain(item.type_id)"/>{{item.name}}</label>
				</radio-group>
			</view>
		</view>
		<view class="list-wraper"  @click="showMulLinkageThreePicker">
			<view class="tips_left">
				<text>{{pickerText}}</text>
			</view>
			<view class="tips_right">
				<image src="../../static/right-arrow.png" ></image>
			</view>
		</view>
		
		<view class="tips"  @click="goPage">
			<view class="tips_left">
				<text>{{addressName}}</text>
			</view>
			<view class="tips_right">
				<image src="../../static/right-arrow.png" ></image>
			</view>
		</view>
		<view class="h-20upx"></view>
		
		<view class="list-wraper ind-wraper">
			<view class="tit">
				<text>商家介绍：</text>				
			</view>
			<view class="input-lg">
				<textarea class="lg-text" value=""  v-model="infor.info" placeholder="请输入..." />
			</view>
		</view>
		
		<view class="list-wraper logo-wraper"  @click="uplodingImg(2)">
			<view class="tit">
				<text>点击上传营业执照：</text>				
			</view>
			<view class="input-Image">
				<image v-if="!BusinessImg"  src="http://mp.emshop.eonfox.com/zrhzstatic/muying/yingyezhizhao.png" mode=""></image>
				<image v-else :src="BusinessImg" mode=""></image>
			</view>
		</view>
		
		<view class="btn-box">
			<button class="btn" @click="commit()">提交申请</button>
		</view>
		<mpvue-city-picker :themeColor="themeColor" ref="mpvueCityPicker" :pickerValueDefault="cityPickerValueDefault" @onConfirm="onConfirm"></mpvue-city-picker>
	</view>
</template>

<script>
	import mpvueCityPicker from '@/components/mpvue-citypicker/mpvueCityPicker.vue'
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	// #ifdef H5
	let jweixin = require('jweixin-module');
	// #endif
	var ef = new eonfox();
	export default {
		data() {
			return {
				cityPickerValueDefault: [0, 0, 1],
				themeColor: '#007AFF',
				pickerText: '选择省/市/区',
				addressName:'请选择商家地址',
				logImg:'',
				infor:{},
				BusinessImg:''  ,// 营业执照
				BusinessImgId:'',
				logImgID:'',
				shopsType:[]
			}
		},
		components: {
		    mpvueCityPicker
		},
		onLoad(){
			var _this=this
			ef.submit({
				request:{
					m:["APPLICATIONTYPEoption",[{"module":"merchant_type"}]],
				},
				callback: function(data) {
					var	datalist=fns.checkError(data,['m'],function(erron, error){
						uni.showToast({
							title: error,
							icon: 'none'
						})
					});
					console.log('回调成功', datalist);
					if(datalist.m){
						_this.shopsType=datalist.m[0].son
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
		methods: {
			goPage(){
				var _this = this
				uni.chooseLocation({
				    success: function (res) {
						console.log('res',res)
				        console.log('位置名称：' + res.name);
						_this.addressName=res.address +res.name
						_this.infor.address = res.address +res.name
						_this.infor.lat=res.latitude
						_this.infor.lng=res.longitude
				        console.log('经度：' + res.longitude);
						console.log('详细地址：' + res.address);
						console.log('纬度：' + res.latitude);
				    }
				});
			},
			gain(id){
				this.infor.type_id=id
				console.log('类别id',this.infor.type_id)
			},
			uplodingImg(name){
				var _this=this
				console.log(name)
				uni.chooseImage({
					count: 1,
					sizeType: ['compressed'],
					sourceType: ['album'],
					success: (res) => {
						console.log('chooseImage success, temp path is', res.tempFilePaths[0])
						var imageSrc = res.tempFilePaths[0]
						ef.left_token(function(left_token){
							//encodeURIComponent  encodeURI
							var uploadUrl=ef.api_server_url+"?"+encodeURI('data=[["APPLICATIONIMAGEUPLOADQINIU"]]')+"&token="+left_token;
							uni.uploadFile({
								url: uploadUrl,
								filePath: imageSrc,
								fileType: 'image',
								name: 'file',
								success: (res) => {
									console.log('上传成功:',res.data)
									var imageJson=JSON.parse(res.data)
									console.log(imageJson)
									uni.showToast({
										title: '上传成功',
										icon: 'success',
										duration: 1000,
										success() {
											if(name==1){
												_this.logImg = imageSrc
												_this.infor.logo_image_id=imageJson.data[0].data.image_id
											}else{
												_this.BusinessImg = imageSrc
												_this.infor.license_image_id=imageJson.data[0].data.image_id
											}
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
						
					}
				})
			},
			commit(){
				var _this=this
				console.log('表单',_this.infor)
				if(!_this.infor.logo_image_id){
					uni.showToast({
						title:'请选择店铺头像！',
						icon:'none'
					});
					return
				}
				else if(!_this.infor.name||!_this.infor.name.replace(/\s/g,"") ){
					uni.showToast({
						title:'请输入店铺名称！',
						icon:'none'
					});
					return
				}else if(!_this.infor.weixin||!_this.infor.weixin.replace(/\s/g,"") ){
					uni.showToast({
						title:'请输入商家微信号！',
						icon:'none'
					});
					return
				}else if(!_this.infor.phone||!_this.infor.phone.replace(/\s/g,"") ){
					uni.showToast({
						title:'请输入商家手机号！',
						icon:'none'
					});
					return
				}
				else if(!_this.isPoneAvailable(_this.infor.phone)){
					uni.showToast({
						title:'您输入的手机号格式不正确，请检查后重新输入！',
						icon:'none'
					});
					return
				}
				else if(!_this.infor.city){
					uni.showToast({
						title:'请选择省市区！',
						icon:'none'
					});
					return
				}
				else if(!_this.infor.city){
					uni.showToast({
						title:'请选择省市区！',
						icon:'none'
					});
					return
				}
				else if(!_this.infor.address){
					uni.showToast({
						title:'请选择线下商铺地点！',
						icon:'none'
					});
					return
				}
				else if(!_this.infor.info||!_this.infor.info.replace(/\s/g,"") ){
					uni.showToast({
						title:'请输入商家介绍！',
						icon:'none'
					});
					return
				}else if(!_this.infor.license_image_id){
					uni.showToast({
						title:'请选择营业执照！',
						icon:'none'
					});
					return
				}
				
				let form = Object.assign({},_this.infor)
				ef.submit({
					request:{
						s: ['MERCHANTSELFAPPLY', [form]]
					},
					callback: function(data) {
						var	datalist=fns.checkError(data,['s'],function(erron, error){
							uni.showToast({
								title: error,
								icon: 'none'
							})
							if(error=='您申请的商家正在审核，请稍后...'){
								setTimeout(function(){
									uni.switchTab({
										url:'../../pages/user/user'
									})
								},2500)
							}
						});
							 console.log('回调成功', datalist);
							 if(datalist.s){
								 uni.showToast({
								 	title:'申请成功，审核中',
									success() {
										setTimeout(function(){
											uni.switchTab({
												url:'../../pages/user/user'
											})
										},2000)
									}
								 })
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
			 // 判断是否为手机号
			  isPoneAvailable: function (pone) {
			    var myreg = /^[1][3,4,5,7,8][0-9]{9}$/;
			    if (!myreg.test(pone)) {
			      return false;
			    } else {
			      return true;
			    }
			  },
			
			onConfirm(e) {
				console.log(e);
				var label=e.label;
				this.pickerText = label;
				var labelArr=label.split('-');
				this.infor.province=labelArr[0];
				this.infor.city=labelArr[1];
				this.infor.district=labelArr[2];
				},
			showMulLinkageThreePicker() {
			    this.$refs.mpvueCityPicker.show()
			},
			openLocation(){
				// #ifdef H5
				jweixin.getLocation({
					type: 'wgs84',
					success: res => {
						const latitude = res.latitude;
						const longitude = res.longitude;
						jweixin.openLocation({
							latitude: latitude, // 纬度，浮点数，范围为90 ~ -90
							longitude: longitude, // 经度，浮点数，范围为180 ~ -180。
							name: '位置名', // 位置名
							
							scale: 16, // 地图缩放级别,整形值,范围从1~28。默认为最大
							infoUrl:"http://www.baidu.com"
						})
					},
					fail: () => {},
					complete: () => {}
				});
				
				// #endif
			}
		}
	}
</script>

<style>
	page{
		background: #F5F5F5;
		width: 100%;
		height: 100%;
	}
	.h-20upx{
		height: 20upx;
		display: flex;
	}
	.tips{
		display: flex;
		width: 100%;
		padding: 20upx 30upx;
		background-color: #FFFFFF;
	}
	.tips_left{
		width: 94%;
		font-size: 28upx;
		color: #de7d67;
	}
	.tips_right{
		width: 5%;
	}
	.tips_right image{
		width:40upx;
	    height: 40upx;
	}
	.btn{
		background-color: #f29b87;
		color: #FFFFFF;
	}
	.input-logo{
		width: 120upx;
		height: 120upx;
		line-height: 120upx;
		text-align: center;
		font-size: 27upx;
		color: #444444;
	}
	.input-logo image{
		width: 120upx;
		height: 120upx;
	}
	.input-Image{
		width: 420upx;
		height: 320upx;
		line-height: 320upx;
		text-align: center;
		font-size: 27upx;
		border: 1upx #CCCCCC solid;
		color: #444444;
	}
	.input-Image image{
		width: 420upx;
		height: 320upx;
	}
	.radio{
		font-size: 28upx;
		transform:scale(0.7)
	}
</style>
<style scoped lang="stylus" ref="stylesheet/stylus">
	.content
		width 100%
		display flex
		flex-direction column
		color #333333
		.list-wraper
			position relative
			display flex
			align-items center			
			width 100%
			padding 20upx 30upx
			background #FFFFFF
			.tit
				flex 1.5
				text
					font-size 28upx
					
			.input-all
				flex 4
				.inpt
					// background red
					padding-top 6upx
					font-size 28upx
					
		.logo-wraper
			.input-all
				text-align right
				image
					width 100upx
					height 100upx
				
		.list-wraper::after
			position absolute
			content ""
			width calc(100% - 60upx)
			height 2upx
			left 30upx
			bottom 0
			background #CCCCCC
		.ind-wraper
			flex-direction column
			justify-content left
			.tit
				width 100%				
			.input-lg
				width 100%
				padding 20upx 10upx
				// background red
				.lg-text
					font-size 24upx
					color #777777
		.btn-box
			width 100%;
			padding 40upx 30upx
</style>
