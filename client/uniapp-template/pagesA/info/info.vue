<template>
	<view>
		<!-- <cmd-nav-bar back title="信息设置"></cmd-nav-bar> -->
		<cmd-page-body type="top">
			<cmd-transition name="fade-up">
				<view>
					<cmd-cel-item title="头像" slot-right arrow @click="change(0)" v-if="headpic" ><cmd-avatar :src="headpic.length>50?headpic:config + headpic"></cmd-avatar></cmd-cel-item>
					<cmd-cel-item title="头像" slot-right arrow @click="change(0)" v-else><cmd-avatar src="https://avatar.bbs.miui.com/images/noavatar_small.gif"></cmd-avatar></cmd-cel-item>
					<view class="item-list" @click="change(1)">
						<text class="tit">昵称</text>
						<text class="text">{{ nickname }}</text>
						<uni-icon class="arrowRight" type="arrowright" size="30"></uni-icon>
					</view>
					<view class="item-list">
						<text class="tit">手机号</text>
						<text class="text">{{ tel }}</text>
						<uni-icon class="arrowRight" type="arrowright" size="30"></uni-icon>
					</view>
					<view class="item-list">
						<text class="tit">证件号码</text>
						<text class="text">{{ idnumber }}</text>
						<uni-icon class="arrowRight" type="arrowright" size="30"></uni-icon>
					</view>
					
					<!-- 修改个人信息 -->
					<!-- <view class="item-list" @click="changeinformation">
						<text class="tit">修改个人信息</text>
						<uni-icon class="arrowRight" type="arrowright" size="30"></uni-icon>
					</view> -->
					<view class="item-list" @click="goGetcoedpassword">
						<text class="tit">设置支付密码</text>
						<uni-icon class="arrowRight" type="arrowright" size="30"></uni-icon>
					</view>
					<view class="item-list" @click="alterPassword">
						<text class="tit">修改登录密码</text>
						<uni-icon class="arrowRight" type="arrowright" size="30"></uni-icon>
					</view>
				</view>
			</cmd-transition>
		</cmd-page-body>
		<view class="content">
		    <chunLei-modal v-model="value" :mData="inputData" :type="type" @onConfirm="onConfirm" navMask>
		    </chunLei-modal>
		 </view>
	</view>
</template>

<script>
// import cmdNavBar from "@/components/cmd-nav-bar/cmd-nav-bar.vue"
import cmdPageBody from '@/components/cmd-page-body/cmd-page-body.vue';
import cmdTransition from '@/components/cmd-transition/cmd-transition.vue';
import cmdCelItem from '@/components/cmd-cell-item/cmd-cell-item.vue';
import cmdAvatar from '@/components/cmd-avatar/cmd-avatar.vue';
import uniIcon from '@/components/uni-icon/uni-icon.vue';
import eonfox from '@/components/eonfox/eonfox.js';
import fns from '@/components/eonfox/fns.js';
var ef = new eonfox();
export default {
	components: {
		// cmdNavBar,
		uniIcon,
		cmdPageBody,
		cmdTransition,
		cmdCelItem,
		cmdAvatar
	},

	data() {
		return {
			nickname: '未设置',
			tel: '',
			headpic:'',
			idnumber:'未认证',
			config:'',
			inputData:{
			  title:'修改昵称',
			  content:[
			  {title:'昵称',content:'',type:'text',placeholder:'请输入姓名'}
			  ]
			},
			type:'input',
			value:false
		};
	},
	onShow() {
		this.load();
	},
	mounted() {},

	methods: {
		change(type){
			// console.log(type)
			let that = this;
			switch (type){
				case 0: //换头像
				that.changeHead();
					break;
				case 1: //换名字
				that.value = true
					break;
				
				default:
					break;
			}
		},
		changeHead(){
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
										_this.headpic = imageSrc
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
		},
		changeName(){
			let that = this;
			
		},
		onConfirm(e){
			let that = this
			ef.submit({
				request:{
					sav: ['USERSELFEDIT', [{ user_nickname:e[0].content}]]
				},
				callback: function(data) {
					// console.log('回调成功', data);
					var	fns_=fns.checkError(data,['sav'],function(erron, error){
						uni.showToast({
							title: error,
							icon: 'none'
						})
					})
					if(fns_.sav == true){
						// console.log(fns_.sav)
						uni.showToast({
							title:'修改成功',
							icon:'none',
							success() {
								setTimeout(function (){
									that.load()
								},1500);
							}
						});
					}
				}
			})
			// console.log(e[0].content)
		},
		load() {
			var that = this;
			ef.submit({
				request:{
					s:["USERSELF"],
					p:["USERPHONESELFVERIFY"],
					config:['APPLICATIONCONFIG']
				},
				callback:function(data){
					// console.log(data)
					let dataInfo = fns.checkError(data,['s','p','config'],function(errno,error){
						uni.showToast({
							title:error,
							icon:'none'
						})						
					})
					console.log(dataInfo)
					let qiniu = dataInfo.config.qiniu_domain
					that.config = qiniu
					if(dataInfo.s){
						
						that.nickname = dataInfo.s.user_nickname
						if(dataInfo.s.user_logo_image_id){
							that.headpic = dataInfo.s.user_logo_image_id
						}
					}
					//身份证号码 
					if(dataInfo.s && dataInfo.s.user_identity && dataInfo.s.user_identity.user_identity_card_number){
						that.idnumber = dataInfo.s.user_identity.user_identity_card_number
					}
					if(dataInfo.p){
						console.log('1111',that.tel)
						that.tel = dataInfo.p[0].user_phone_id
					}
				}
			})
		},
		changeinformation(){
			uni.navigateTo({
				url: '../../pagesB/changeInformation/changeInformation'
			})
		},
		goGetcoedpassword(){
			uni.navigateTo({
				url:'../../pagesB/getcoedpassword/getcoedpassword'
			})
		},
		alterPassword() {
			uni.navigateTo({
				url: '../../pagesB/forget-password/forget-password'
			})
		},
		/**
		 * 点击触发
		 * @param {Object} type 跳转页面名或者类型方式
		 */
		/* 
		
		2019年10月25日10:50:58  psc
		fnClick(type) {
			if (type == 'modify') {
				uni.navigateTo({
					url: '../../pages/user/modify/modify'
				});
			}
		} */
	}
};
</script>

<style>
.btn-logout {
	margin-top: 100upx;
	width: 80%;
	border-radius: 50upx;
	font-size: 16px;
	color: #fff;
	background: linear-gradient(to right, #365fff, #36bbff);
}

.btn-logout-hover {
	background: linear-gradient(to right, #365fdd, #36bbfa);
}
.item-list {
	display: flex;
	/* flex-direction: column; */
	padding: 20rpx 0;
	margin: 0 20rpx;
	width: calc(100% - 40rpx);
	position: relative;
	font-size: 32rpx;
	color: #111a34;
}
.item-list::after {
	position: absolute;
	left: 0rpx;
	width: 710rpx;
	height: 2rpx;
	bottom: 0;
	content: '';
	background: #e2e4ea;
}
.item-list text {
	flex: 1;
	line-height: 2;
}
.item-list .text {
	text-align: right;
	/* line-height: 2; */
}
.arrowRight {
	flex: 0.1;
	text-align: right;
}
</style>
