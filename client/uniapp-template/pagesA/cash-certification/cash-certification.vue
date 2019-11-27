<template>
	<view class="content">		
		<!-- <view class="mask" v-if="CertificateStatus!='2'">
			<cmd-result-page src="https://gw.alipayobjects.com/zos/rmsportal/HWuSTipkjJRfTWekgTUG.svg" text="等待审核" subtext="已提交申请"></cmd-result-page>
		</view> -->
		<view class="box-cont" >
			<view class="name"><input type="text" placeholder="请输入姓名" @blur="blurname" /></view>
					<view class="name"><input type="text" placeholder="请输入身份证号码" maxlength="18" @blur="blurnumber" /></view>
					<view style="padding-left: 50upx;" v-show="numberError"><text style="color:#DD524D;">身份证号码有误</text></view>
					<view style="padding-left:10upx;">
						<text style="color:#DD524D;">{{ time }}</text>
					</view>
					<view class="idbox">
						<view class="idboxTitle">身份证照片(正面)</view>
						<view class="idboxConten">
							<view class="idboxConten_left" @click="uploadIdentityImg('front')">
								<image src="http://mp.emshop.eonfox.com/zrhzstatic/muying/camera.png"></image>
								<view class="idboxConten_left_text">选择身份证照片</view>
							</view>
							<view class="idboxConten_right">
								<image v-if="JSONS.front_image_id" :src="qiniuaddress + JSONS.front_image_id" mode="aspectFill" />
								<image v-else src="http://mp.emshop.eonfox.com/zrhzstatic/muying/idcard1.png"></image>
							</view>
						</view>
						<!-- <view class="idboxFront" @click="uploadIdentityImg('front')">
							<image  v-if="JSONS.front_image_id" :src="qiniuaddress+JSONS.front_image_id" mode="aspectFill"/>
							<view v-else>
								<view style="font-size:140upx;">+</view>
								<view>正面照片</view>
								<view>(文字清晰，四角周全)</view>
							</view>
						</view> -->
						<view class="idboxTitle">身份证照片(背面)</view>
						<view class="idboxConten">
							<view class="idboxConten_left" @click="uploadIdentityImg('back')">
								<image src="http://mp.emshop.eonfox.com/zrhzstatic/muying/camera.png"></image>
								<view class="idboxConten_left_text">选择身份证照片</view>
							</view>
							<view class="idboxConten_right">
								<image v-if="JSONS.back_image_id" :src="qiniuaddress + JSONS.back_image_id" mode="aspectFill" />
								<image v-else src="http://mp.emshop.eonfox.com/zrhzstatic/muying/idcard2.png"></image>
							</view>
						</view>
						<!-- <view class="idboxBack" @click="uploadIdentityImg('back')">
							<image v-if="JSONS.back_image_id" :src="qiniuaddress+JSONS.back_image_id" mode="aspectFill"  />
							<view v-else>
								<view style="font-size:140upx;">+</view>
								<view>背面照片</view>
								<view>(文字清晰，四角周全)</view>
							</view>
						</view> -->
					</view>
					<view class="area">
						<view class="areaBox" @click="showMulLinkageTwoPicker">
							<view class="areaBox_left">
								{{ Address }}
							</view>
							<view class="areaBox_right"><image src="http://mp.emshop.eonfox.com/zrhzstatic/muying/back.png"></image></view>
						</view>
						<view class="detailAreaBox">
							<textarea type="text" placeholder="详细地址：如道路、门牌号、小区、楼栋号、单元室等" class="homeRight" v-model="addresst" v-show="inputDisplay"></textarea>
						</view>
					</view>
			<mpvue-picker  ref="mpvuePicker" @onConfirm="onConfirm" @onCancel="onCancel"></mpvue-picker>
			<mpvue-city-picker  ref="mpvueCityPicker"  @onCancel="onCancel" @onConfirm="onConfirm"></mpvue-city-picker>
			<button type="primary" class="primary" @click="next">确认提交</button>
		</view>
		
		
	</view>
</template>

<script>
	import cmdResultPage from "@/components/cmd-result-page/cmd-result-page.vue"
import mpvuePicker from '@/components/mpvue-picker/mpvuePicker.vue';
import mpvueCityPicker from '@/components/mpvue-citypicker/mpvueCityPicker.vue';
import city from '../../common/city.data.js';
import eonfox from '@/components/eonfox/eonfox.js';
import fns from '@/components/eonfox/fns.js';
var ef = new eonfox();
export default {
	data() {
		return {
			img_index: 'headImgOne',
			img_type: 'front',
			province: [],
			province_index: 0,
			province_data: '',
			citys: [],
			citys_index: 0,
			city_data: '',
			username: '',
			cardNumber: '',
			address: '',
			addresss: '',
			addressc: '',
			addresst: '',
			headImgOne: '',
			headImgTwo: '',
			Front_image_id: '',
			Back_image_id: '',
			display: true,
			displayx: true,
			numberError: false,
			time: '',
			CertificateStatus: 0,
			JSONS: {},
			qiniuaddress: '',
			phoneShow: true,
			phoneShowTwo: false,
			Address: '选择地址',
			Sprovince: '',
			Scity: '',
			Sdistrict: '',
			inputDisplay: true
		};
	},
	onLoad() {
		for (var i in city) {
			this.province.push(city[i].label);
		} //省数组赋值
		var cityChildren = city[this.province_index].children; //获取该省城市
		console.log(cityChildren);
		for (var i in cityChildren) {
			this.citys.push(cityChildren[i].label);
		} //城市数组赋值
	},
	onShow() {
		var that = this;
		ef.submit({
			request: {
				s: ['APPLICATIONCONFIG'],
				Authentication: ['USERIDENTITYSELFSTATE'],
				Editstate: ['USERIDENTITYSELFEDITGET'],
				config: ['APPLICATIONCONFIG']
			},
			callback: function(data) {
				console.log(data);
				var dataList = fns.checkError(data, ['s', 'Authentication'], function(erron, error) {
					uni.showToast({
						title: error,
						icon: 'none'
					});
				});
				//判断当前是否为编辑状态
				const editstate = data.data.Editstate.data;
				if (editstate) {
					that.JSONS = editstate;
					console.log('JSONS', that.JSONS);
				}
				//七牛云地址
				if (data.data.config && data.data.config.data.qiniu_domain) {
					that.qiniuaddress = data.data.config.data.qiniu_domain;
				}
				console.log(dataList.Authentication,'审核状')
				if (dataList.Authentication == 1) {
					that.CertificateStatus = 1;
					console.log('审核状态', that.CertificateStatus);
				}
				if (that.CertificateStatus == 1) {
					console.log('时间', data);
					var times = data.data.s.data.user_identity.expire_time / 3600;
					that.time = '认证失效时间还有' + times / 24 + '天' + (times % 24) + '小时';
					console.log('时间', that.time);
				}
				//通过审核后
			},
			error: function(err) {
				console.log('出错啦', err);
			}
		});
	},
	components: {
		mpvuePicker,
		mpvueCityPicker,
		cmdResultPage
	},
	methods: {
		//选择地址
		onConfirm(e) {
			this.inputDisplay = true;
			console.log(e);
			this.Address = e.label;
			var arr = this.Address.split('-');
			console.log('arr', arr);
			this.Sprovince = arr[0];
			this.Scity = arr[1];
			this.Sdistrict = arr[2];
			console.log(this.Sprovince, this.Scity, this.Sdistrict);
		},
		onCancel() {
			this.inputDisplay = true;
		},
		//三级联动
		showMulLinkageTwoPicker() {
			this.inputDisplay = false;
			this.$refs.mpvueCityPicker.show();
			this.displayCity = !this.displayCity;
		},
		error(e) {
			fns.err(e.detail);
		},
		photo(type) {
			var _this = this;
			wx.getSetting({
				success(res) {
					console.log('auth', res.authSetting);
					if (!res.authSetting['scope.camera']) {
						wx.authorize({
							scope: 'scope.camera',
							success() {
								console.log('已同意授权');
								// 用户已经同意
								console.log('已同意授权', type);
								_this.takePhoto(type);
							}
						});
					} else {
						console.log('已授权');
						console.log('已同意授权', type);
						_this.takePhoto(type);
					}
				}
			});
		},
		takePhoto(type) {
			console.log('........', type);
			var _this = this;
			console.log('+++++++++相机分割线+++++++++++++++++++++++++');
			const ctx = uni.createCameraContext();
			console.log('ctx', ctx);
			ctx.takePhoto({
				quality: 'high',
				success: res => {
					console.log('takephoto-re', res.tempImagePath);
					var img = res.tempImagePath;
					ef.left_token(function(left_token) {
						//encodeURIComponent  encodeURI
						// var uploadUrl=ef.api_server_url+"?"+encodeURI('data=[["USERIDENTITYSELFUPLOAD",[{"type":"'+_this.img_type+'"}]]]')+"&token="+left_token;
						const uploadUrl = ef.api_server_url + '?' + encodeURI(`data=[["USERIDENTITYSELFUPLOAD",[{"type":"${type}"}]]]`) + '&token=' + left_token;
						uni.uploadFile({
							url: uploadUrl,
							filePath: img,
							fileType: 'image',
							name: 'file',
							success: res => {
								console.log('上传完成:', res);
								res = JSON.parse(res.data);
								console.log(res);
								res = res.data[0];

								// 是否成功
								if (res.errno == 0) {
									// 判断图片类型
									if (type == 'front') {
										_this.JSONS.front_image_id = res.data;
									} else {
										_this.JSONS.back_image_id = res.data;
									}
									uni.showToast({
										title: '上传成功',
										success() {
											_this.phoneShow = !_this.phoneShow;
											_this.phoneShowTwo = !_this.phoneShowTwo;
										}
									});
									console.log('JSONS.front_image_id',_this.JSONS.front_image_id)
								} else {
									uni.showToast({
										title: res.error,
										icon: 'none'
									});
								}
							},
							fail: err => {
								console.log('uploadImage fail', err);
								uni.showModal({
									content: err.errMsg,
									showCancel: false
								});
							}
						});
					});
				},
				fail() {
					fns.err('打开相机失败');
				},
				complete() {
					console.log('打开相机');
				}
			});
			console.log('+++++++++相机分割线+++++++++++++++++++++++++');
		},
		provinceChange: function(e) {
			console.log('省index:', e.target.value);
			this.province_index = e.target.value; //以选择序号省显示改变
			this.province_data = city[e.target.value].label; //省赋值
			console.log('province:', city[e.target.value].label);
			this.citys = []; //先清空城市
			this.citys_index = 0;
			var cityChildren = city[e.target.value].children;
			for (var i in cityChildren) {
				this.citys.push(cityChildren[i].label);
			} //城市数组更换
		},
		cityChange: function(e) {
			this.citys_index = e.target.value;
			this.city_data = city[this.province_index].children[this.citys_index].label;
			console.log('市：', e.target.value);
			console.log('市：', this.city_data);
		},
		next() {
			var that = this;
			// that.address=that.addresss+that.addressc+that.addresst;
			console.log('姓名', that.username);
			console.log('姓名', that.cardNumber);
			console.log('姓名', that.address);
			ef.submit({
				request: {
					s: [
						'USERIDENTITYSELFADD',
						[
							{
								real_name: that.username,
								card_number: that.cardNumber,
								card_address: that.addresst,
								province: that.Sprovince,
								city: that.Scity,
								district: that.Sdistrict
							}
						]
					],
					y: ['USERSELF']
				},
				callback: function(data) {
					console.log(data);
					var fns_ = fns.checkError(data, ['s'], function(erron, error) {
						fns.err('认证', error);
						return;
					});
					if (data.data.s.errno == 0) {
						uni.showToast({
							title: '已提交，等待审核',
							success() {
								setTimeout(function() {
									uni.navigateBack({
										delta: 1
									});
								}, 2000);
							}
						});
					}
				},
				error: function(err) {
					fns.err('', err, 1);
				}
			});
		},
		blurname: function(event) {
			this.username = event.target.value;
		},
		blurnumber: function(event) {
			var that = this;
			that.cardNumber = event.target.value;
			console.log('身份证号码');
			if (!/[0-9]{18}/.test(that.cardNumber)) {
				that.numberError = true;
			} else {
				that.numberError = false;
			}
		},
		bluraddresss: function(event) {
			this.addresss = event.target.value;
		},
		bluraddresc: function(event) {
			this.addressc = event.target.value;
		},
		bluraddress: function(event) {
			this.addresst = event.target.value;
		},
		// 上传身份证
		uploadIdentityImg(type) {
			const _this = this;
			uni.chooseImage({
				count: 1,
				sizeType: ['compressed'],
				success: res => {
					wx.showLoading({
						title: '上传中'
					});
					console.log('chooseImage success, temp path is', res.tempFilePaths[0]);
					const file = res.tempFilePaths[0];
					ef.left_token(function(left_token) {
						//encodeURIComponent  encodeURI
						const uploadUrl = ef.api_server_url + '?' + encodeURI(`data=[["USERIDENTITYSELFUPLOAD",[{"type":"${type}"}]]]`) + '&token=' + left_token;
						uni.uploadFile({
							url: uploadUrl,
							filePath: file,
							fileType: 'image',
							name: 'file',
							success: res => {
								console.log('上传完成1:', res);
								res = JSON.parse(res.data);
								console.log(res);
								res = res.data[0];
								// 是否成功
								if (res.errno == 0) {
									// 判断图片类型
									if (type === 'front') {
										_this.JSONS.front_image_id = res.data;
									} else {
										_this.JSONS.back_image_id = res.data;
									}
									uni.showToast({
										title: '上传成功'
									});
								} else {
									uni.showToast({
										title: res.error,
										icon: 'none'
									});
								}
							},
							fail: err => {
								console.log('uploadImage fail', err);
								uni.showModal({
									content: err.errMsg,
									showCancel: false
								});
							}
						});
					});
				},
				fail: err => {
					console.log('chooseImage fail', err);
					uni.showToast({
						title: '你取消了图片上传',
						icon: 'none',
						duration: 2000,
						success() {
							_this.display = !_this.display;
						}
					});
				}
			});
		},
		ModifyThePicture() {
			var _this = this;
			_this.display = !_this.display;
			uni.chooseImage({
				count: 1,
				sizeType: ['compressed'],
				// sourceType: ['album'],
				success: res => {
					console.log('chooseImage success, temp path is', res.tempFilePaths[0]);
					var imageSrc = res.tempFilePaths[0];
					ef.left_token(function(left_token) {
						//encodeURIComponent  encodeURI
						var uploadUrl = ef.api_server_url + '?' + encodeURI('data=[["USERIDENTITYSELFUPLOAD",[{"type":"front"}]]]') + '&token=' + left_token;
						uni.uploadFile({
							url: uploadUrl,
							filePath: imageSrc,
							fileType: 'image',
							name: 'file',
							success: res => {
								console.log('上传完成:', res);

								var dataList = fns.checkError(JSONS.parse(res.data), ['0'], function(erron, error) {
									console.log('上传错误', res.data);
									uni.showToast({
										title: error,
										icon: 'none'
									});
								});
								console.log('dataList:', dataList[0]);
								_this.Front_image_id = dataList[0];
								if (dataList[0]) {
									uni.showToast({
										title: '上传成功',
										icon: 'none',
										duration: 1000,
										success() {
											_this.headImgOne = imageSrc;
										}
									});
								}
							},
							fail: err => {
								console.log('uploadImage fail', err);
								uni.showModal({
									content: err.errMsg,
									showCancel: false
								});
							}
						});
					});
				},
				fail: err => {
					console.log('chooseImage fail', err);
					uni.showToast({
						title: '你取消了图片上传',
						icon: 'none',
						duration: 2000,
						success() {
							_this.display = !_this.display;
						}
					});
				}
			});
		},
		ModifyThePictureTwo() {
			var _this = this;
			_this.displayx = !_this.displayx;
			uni.chooseImage({
				count: 1,
				sizeType: ['compressed'],
				sourceType: ['album'],
				success: res => {
					console.log('chooseImage success, temp path is', res.tempFilePaths[0]);
					var imageSrc = res.tempFilePaths[0];
					ef.left_token(function(left_token) {
						//encodeURIComponent  encodeURI
						var uploadUrl = ef.api_server_url + '?' + encodeURI('data=[["USERIDENTITYSELFUPLOAD",[{"type":"back"}]]]') + '&token=' + left_token;
						uni.uploadFile({
							url: uploadUrl,
							filePath: imageSrc,
							fileType: 'image',
							name: 'file',
							success: res => {
								console.log('上传完成:', res);

								var dataList = fns.checkError(JSONS.parse(res.data), ['0'], function(erron, error) {
									console.log('上传错误', res.data);
									uni.showToast({
										title: error,
										icon: 'none'
									});
								});
								console.log('dataList:', dataList[0]);
								_this.Back_image_id = dataList[0];
								if (dataList[0]) {
									uni.showToast({
										title: '上传成功',
										icon: 'none',
										duration: 1000,
										success() {
											_this.headImgTwo = imageSrc;
										}
									});
								}
							},
							fail: err => {
								console.log('uploadImage fail', err);
								uni.showModal({
									content: err.errMsg,
									showCancel: false
								});
							}
						});
					});
				},
				fail: err => {
					console.log('chooseImage fail', err);
					uni.showToast({
						title: '你取消了图片上传', //JSONS.stringify(err)
						icon: 'none',
						duration: 2000,
						success() {
							_this.displayx = !_this.displayx;
						}
					});
				}
			});
		}
	}
};
</script>

<style>
page{
	background-color: #F5F5F5;
}
.homeRight{
	width: 100%;
	height:100%;
}
.content{
	font-size: 28upx
}
.name{
	display: flex;
	width: 690upx;
	margin-left :30upx;
	border-radius: 10upx;
	height: 80upx;
	justify-content :center;
	align-items :center  ;
	flex-direction: row;
	padding-left :10upx;
	background-color :#FFFFFF;
	margin-top :20upx;
}
.primary{
	width :690upx;
	margin: 40px auto;
	color: #fff;
	background:linear-gradient(to right, #F29B87,#F8C6B5);
	z-index: 999;
}

.name input{
	width: 100%;
	height:40upx;
	line-height: 40upx;
	float:left;
	font-size: 28upx;
}
 textarea{
	font-size: 28upx;
}



.idbox{
	width: 690upx;
	margin-left: 30upx;
}
.idboxTitle{
	width: 100%;
	height: 80upx;
	line-height: 80upx;
	font-size: 30upx;
	color: #333333;
}
.idboxConten{
	width: 100%;
	/* height: 230upx; */
	background-color: #FFFFFF;
	border-radius: 10upx;
	padding: 30upx 0;
	display: flex;
}
.idboxConten_left{
	width: 200upx;
	height: 200upx;
	background-color: #F1F1F1;
	margin-left: 30upx;
}
.idboxConten_left image{
	width: 120upx;
	height: 100upx;
	margin-left: 40upx;
	margin-top: 20upx;
}
.idboxConten_left_text{
	width: 100%;
	height: 40upx;
	margin-top: 20upx;
	text-align: center;
	color: #333333;
}
.idboxConten_right{
	width: 320upx;
	height: 200upx;
	margin-left: 90upx;
}
.idboxConten_right image{
	width: 100%;
	height: 100%;
}
.area{
	width: 690upx;
	margin-left: 30rpx;
	background-color: #FFFFFF;
	border-radius: 10upx;
	margin-top: 30upx;
	padding-left: 3%;
}
.areaBox{
	width: 100%;
	height: 80upx;
	line-height: 80upx;
	border-bottom: #E4E4E4 solid 1upx;
	display: flex;
}
.areaBox_left{
	width: 94%;
	color: #333333;
}
.areaBox_right{
	width: 6%;
}
.areaBox_right image{
	width: 20upx;
	height: 25upx;
}
.detailAreaBox{
	width: 100%;
	height: 110upx;
	color: #333333;
	padding-top: 10upx;
}
.mask{
	top: 0;
	left: 0;
	background: #F5F5F5;
	width: 100%;
	height: 100vh;
	position: absolute;
	z-index: 1000;
}
</style>
