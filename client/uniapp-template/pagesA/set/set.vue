<template>
	<view class="container">
		<view class="list-cell b-b m-t" @click="navTo('../../pagesA/info/info')" hover-class="cell-hover" :hover-stay-time="50">
			<text class="cell-tit">个人资料</text>
			<text class="cell-more yticon icon-you"></text>
		</view>
		<view class="list-cell b-b" @click="goAddress()" hover-class="cell-hover" :hover-stay-time="50">
			<text class="cell-tit">收货地址</text>
			<text class="cell-more yticon icon-you"></text>
		</view>
		<view class="list-cell" @click="gorenzhen" hover-class="cell-hover" :hover-stay-time="50">
			<text class="cell-tit">实名认证</text>
			<text class="cell-more yticon icon-you"></text>
		</view>
		
		<!-- #ifdef APP-PLUS -->
		<view class="list-cell m-t">
			<text class="cell-tit">消息推送</text>
			<switch checked color="#fa436a" @change="switchChange" />
		</view>
		<!-- #endif -->
		<view class="list-cell m-t b-b" @click="clearCache" hover-class="cell-hover" :hover-stay-time="50">
			<text class="cell-tit">清除缓存</text>
			<text class="cell-more yticon icon-you"></text>
		</view>
		<!-- #ifdef APP-PLUS -->
		<view class="list-cell">
			<text class="cell-tit">检查更新</text>
			<text class="cell-tip">当前版本 1.0.3</text>
			<text class="cell-more yticon icon-you"></text>
		</view>
		<!-- #endif -->
		<view class="list-cell log-out-btn" @click="toLogout">
			<text class="cell-tit">退出登录</text>
		</view>
	</view>
</template>

<script>
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	let ef = new eonfox();
	import {  
	    mapMutations  
	} from 'vuex';
	export default {
		data() {
			return {
				reznumber:''
			};
		},
		onShow() {
			this.getdata()
		},
		methods:{
			...mapMutations(['logout']),
			
			navTo(url){
				uni.navigateTo({
					url
				})
				// this.$api.msg(`跳转到${url}`);
			},
			
			getdata(){
				let that = this;
				ef.submit({
					request:{
						rz:['USERIDENTITYSELFSTATE']
					},
					callback:function(data){
						let dataList= fns.checkError(data, 'rz', function(errno, error) {
							uni.showToast({
								title:error,
								icon:"none"
							})
						})
						if(dataList){
							// console.log(dataList.rz)
							that.reznumber = dataList.rz							
						}
					}
				})
			},
			//退出登录
			toLogout(){
				uni.showModal({
				    content: '确定要退出登录么',
				    success: (e)=>{
				    	if(e.confirm){
				    		ef.submit({
				    			request: { s: ['USERLOGOUT'] },
				    			callback: function(data) {
				    				uni.clearStorage();
				    				uni.showToast({
				    					title: '退出成功',
				    					icon: 'success'
				    				});
				    				setTimeout(function(){
				    					uni.reLaunch({
				    						url:
				    							'../../pages/index/index'
				    					});
				    					// ereee
				    					
				    				},1000);
				    			},
				    			error: function(err) {
				    				uni.clearStorage();
				    				console.log('出错啦', err);
				    				uni.showToast({
				    					title:JSON.stringify(err),
				    					icon:'none'
				    				})
				    				uni.reLaunch({
				    					url:
				    						'../../pages/index/index'
				    				});
				    			}
				    		});
				    	}
				    }
				});
			},
			//switch
			switchChange(e){
				let statusTip = e.detail.value ? '打开': '关闭';
				this.$api.msg(`${statusTip}消息推送`);
			},
			goAddress(){
				uni.navigateTo({
					url:'../../pages/address/address'
				})
			},
			gorenzhen(){//认证
				let that = this;
				if(that.reznumber == '0'){
					uni.showToast({
						title: '申请失败，可重新申请',
						icon:'none'
					});
					setTimeout(function(){
						uni.navigateTo({
							url:'../../pagesA/cash-certification/cash-certification'
						})
					},2000)
					return
				}
				if(that.reznumber == '1'){
					uni.showToast({
						title: '您已实名认证成功！',
						icon:'none'
					});
					return
				}
				if(that.reznumber == '2'){
					uni.showToast({
						title:'正在审核。。。',
						icon:'none'
					})
					return
				}				
				uni.navigateTo({
					url:'../../pagesA/cash-certification/cash-certification'
				})
			},
			clearCache(){
				function random(lower, upper) {
				    return Math.floor(Math.random() * (upper - lower)) + lower;
				}
				// console.log(random(1000,3000))
				uni.showLoading({
					title:'清理中。。。',
				})				
				setTimeout(function(){
					uni.hideLoading()
					uni.showToast({
						title:'清理完成'
					})
				},random(1000,3000))
				
			}			
		}
	}
</script>

<style lang='scss'>
	page{
		background: $page-color-base;
	}
	.list-cell{
		display:flex;
		align-items:baseline;
		padding: 20upx $page-row-spacing;
		line-height:60upx;
		position:relative;
		background: #fff;
		justify-content: center;
		&.log-out-btn{
			margin-top: 40upx;
			.cell-tit{
				color: $uni-color-primary;
				text-align: center;
				margin-right: 0;
			}
		}
		&.cell-hover{
			background:#fafafa;
		}
		&.b-b:after{
			left: 30upx;
		}
		&.m-t{
			margin-top: 16upx; 
		}
		.cell-more{
			align-self: baseline;
			font-size:$font-lg;
			color:$font-color-light;
			margin-left:10upx;
		}
		.cell-tit{
			flex: 1;
			font-size: $font-base + 2upx;
			color: $font-color-dark;
			margin-right:10upx;
		}
		.cell-tip{
			font-size: $font-base;
			color: $font-color-light;
		}
		switch{
			transform: translateX(16upx) scale(.84);
		}
	}
</style>
