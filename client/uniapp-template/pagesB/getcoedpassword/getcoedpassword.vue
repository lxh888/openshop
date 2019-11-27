<template>
	<view class="content">
		<uni-password ref="secrity" @input="onInput" @confirm="onConfirms" v-show="dispaly">
			请输入6位支付密码
		</uni-password>
		<lausirCodeDialog  :show="showCodeDialog"  :len="6"  :autoCountdown="true"  :phone="phone" v-on:change="change"></lausirCodeDialog>
	</view>
</template>

<script>
	import uniPassword from '@/components/ku3gitxdx-payment-password/ku3gitxdx-payment-password.vue'
	import eonfox from '@/components/eonfox/eonfox.js';
	import lausirCodeDialog from '../../components/lausir-codedialog/lausir-codedialog.vue';
	import fns from '@/components/eonfox/fns.js';
	var ef = new eonfox();
	export default {
		data() {
			return {
				phone:'',
				showCodeDialog: true,
				code:'',
				dispaly:false,
				paymentPasswordDispaly:false,
			};
		},
		components:{
			lausirCodeDialog,uniPassword
		},
		onLoad() {
			this.load();
		},
		methods:{
			load(){
				this.dispaly = false;
				var that = this;
				ef.submit({
					request: {
						 phone:['USERPHONESELFVERIFY'],
					},
					callback: function(data) {
						var pphone=data.data.phone.data;
						that.phone=pphone[0].user_phone_id
						console.log('电话',that.phone)
						that.next();
					},
					error: function(err) {
						console.log('出错啦', err);
					}
				});
			},
			onConfirms(e) {
				var that=this
				let password = e.value;
				console.log('password',password,password.length);
				var ef = new eonfox();
					ef.submit({
						request: {s: ['USERSELFPAYPASSWORD',[{password:password,phone_verify_key:'reset_pay_password',phone_verify_code:that.code,phone:that.phone}]] },
						callback: function(data) {
							fns.checkError(data,'s',function(errno,error){
								uni.showToast({
									title:error,
									icon:'none'
								})
							})
							if(data.data.s.data){
								uni.showToast({
									title:'支付密码设置成功',
									icon:'none',
									success() {
										setTimeout(function(){
											uni.switchTab({
												url:'../../pages/user/user'
											})
										},1000)
									}
								})
								
								
							}
							  console.log(data);
							
						},
						error: function(err) {
							
						}
					});
			},
			next(){
				var that = this;
					ef.submit({
						request: {
							s: ['SESSIONPHONEVERIFYCODESEND', [{phone:that.phone,phone_verify_key:'reset_pay_password' }]]
						},
						callback: function(data) {
							console.log(data)
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
				
			},
			change:function(res){
				var _this=this
				if(res.type == 1){
					this.code = res.code;
					this.showCodeDialog = false;
					ef.submit({
						request:{s:['SESSIONPHONEVERIFYCODECHECK',[{phone:_this.phone, phone_verify_key:'reset_pay_password', phone_verify_code : _this.code}]]},
						callback:function(data){
							console.log(data.data.s);
							if( !data.errno && data.data.s.data ){
// 								uni.navigateTo({
// 									url:
// 										'../../pagesA/reset_password/reset_password?code=' +
// 										_this.code
// 								});
								_this.dispaly=true;
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
	.content{
		width:100%;
		height: auto;
	}
</style>
