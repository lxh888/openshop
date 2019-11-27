<template>
	<view class="center-body">
		<view class="body-input">
			<view class="input-title">
				+86
			</view>
			<view class="input-int">
				<input type="text" v-model="phone" placeholder="请输入手机号"/>
			</view>
		</view>
		<view class="body-btn">
			<button type="primary" @click="next()">下一步</button>
		</view>
		<lausirCodeDialog :show="showCodeDialog" :len="6" :autoCountdown="true" :phone="phone" v-on:change="change"></lausirCodeDialog>
	</view>
</template>

<script>
	import eonfox from '@/components/eonfox/eonfox.js';
	import lausirCodeDialog from '@/components/lausir-codedialog/lausir-codedialog.vue';
	var ef = new eonfox();
	export default {
		data() {
			return {
				phone:'',
				showCodeDialog: false,
				code:''
			};
		},
		components:{
			lausirCodeDialog,

		},
		methods:{
			next(){
				var that = this;
				if(this.phone == "") {
					return;
				} else if(this.phone.length == 11) {
					ef.submit({
						request: {
							s: ['SESSIONPHONEVERIFYCODESEND', [{ phone: this.phone,phone_verify_key:'reset_password' }]]
						},
						callback: function(data) {
							console.log(data)
							console.log(1)
							if (data.data.s.errno == 0) {
								console.log(that.phone);
								console.log(that.code);
								that.showCodeDialog = true;
							} if (data.data.s.errno == 1) {								
								uni.showModal	
								({
									title:data.data.s.error
								});
							}
							else {
								that.getverifSwitch=false;
								return;
							}
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
			change:function(res){
				var _this=this
				if(res.type == 1){
					this.code = res.code;
					this.showCodeDialog = false;
					ef.submit({
						request:{s:['SESSIONPHONEVERIFYCODECHECK',[{phone:_this.phone,phone_verify_key:'reset_password',phone_verify_code:_this.code}]]},
						callback:function(data){
							console.log(data.data.s);
							if(!data.errno&&data.data.s.data){
								uni.navigateTo({
									url:
										'../../pagesA/reset_password/reset_password?phone=' +
										_this.phone +
										'&code=' +
										_this.code
								});
								
							}else{
								uni.showToast({
									title:'验证码错误',
									icon:'none'
								})
							}
						},error:function(err){
							uni.showToast({
								title:JSON.stringify(err),
								icon:'none'
							})
						}
					})
				}else if(res.type == -1){
					this.code ="";
					this.showCodeDialog = false;
				}else{
					setTimeout(function(){
						res.resendCall()
					},3000)
				}
				
				
			},
		
			
		}
	}
</script>

<style>
	.center-body{
		width: 90%;
		margin-left: 5%;
		margin-top: 30px;
	}
	.body-input{
		width: 100%;
		height: 40px;
		margin-bottom: 15px;
		background-color: #f1f1f3;
	}
	.input-title{
		width:10%;
		height: 40px;
		line-height: 40px;
		font-size: 14px;
		float: left;
	}
	.input-int{
		width:89%;
		height: 40px;
		font-size: 15px;
		float: right;
	}
	.input-int input{
		height: 40px;
		line-height: 40px;
	}
	.body-btn button{
		background-color:#F76968;
	}
</style>
