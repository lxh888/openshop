<template>
<view class="content">
	<!-- @click="goAssembleDetail(item.group_order_id)" -->
	<view class="wholeWrapper" v-if="groupLists"  v-for="(item,index) in groupLists"  :key='index'>
		
		<view v-if="item.pay_price != 0"> 
			<view class="spikeTopWrapper">
				<!-- <view class="spikeTimes left">{{}}</view> -->
				<view class="spikePersons" v-if="item.state==1" >
					拼团成功
				</view>
				<view class="spikePersons" v-else>拼团中，还差{{item.people-item.people_now}}人成团</view>
				
				<!-- <view class="spikePersons right">拼团成功</view> -->
			</view>
			<view class="spikeContentWrapper">
				<view class="spikeConImgWrapper">	
					<image :src="qiniu + item.shop_goods.image_id+'?imageView2/0/w/80/h/80'" alt="" class="spikeConImg"></image>
					<!-- <img src="../../static/index-pages/goods.png" alt="" class="spikeConImg"> -->
				</view>
				<view class="spikeConWordWrapper">
					<text class="spikeConTitleWord">
						{{item.shop_goods.shop_goods_name}}
					</text>
					<view class="spikeconSaleInfo">
						<view class="spikeConNum">x1</view>
						<view class="spikeConMoney">
							<text class="moenyWord">实付:</text>
							<text class="Money">￥{{item.price/100}}</text>
						</view>
					</view>
				</view>
			</view>
		</view>
		
	</view>
</view>
</template>

<script>
	import eonfox from "@/components/eonfox/eonfox.js"
	import fns from '@/components/eonfox/fns.js'
	var ef = new eonfox();
	export default{
		
		data(){
			return {
				qiniu:'',
				groupLists:[]
			}
		},
		methods:{
			// goAssembleDetail(order_id){
			// 	uni.navigateTo({
			// 		url:'../assembleOrderDetail/assembleOrderDetail?order_id='+order_id
			// 	})
			// }
		},
		onLoad(){
			var _this = this;
			ef.submit({
					request: {
						config: ['APPLICATIONCONFIG'],//获取七牛云地址
						groupList:['SHOPORDERGROUPSELFLIST']
					},
					callback: function(data) {
						var res = fns.checkError(data, ['config','groupList'], function(errno, error) {
							fns.err(error)
						});
						if (!res) {
							return false;
						}
						console.log("回调成功", res);
						
						// 获取七牛云地址
						if (res.config && res.config.qiniu_domain) {
							_this.qiniu = res.config.qiniu_domain
						}
						if(res.groupList && res.groupList){
							_this.groupLists = res.groupList.data;
							console.log("1111111111",_this.groupLists)
						}
						
					},
					error: function(err) {
						uni.showToast({
							title: '出错啦',
							icon: 'none'
						});
					}
				})
			}

	}
</script>

<style>
	/* 浮动 */
	.clearFix:after {
		content: ".";
		display: block;
		height: 0;
		clear: both;
		visibility: hidden;
	}
	
	.left {
		float: left;
	}
	
	.right {
		float: right;
	}
	
	.clearFix {
		zoom: 1;
	}
	.content{
		width: 100%;
	}
	.spikeTopWrapper{
		padding: 20upx;
	}
	.spikeTimes{
		font-size: 28upx;
		color:#444444;
	}
	.spikePersons{
		font-size: 32upx;
		color:rgb(247, 97, 97);
	}
	
	.spikeContentWrapper {
		display: flex;
		width: 100%;
		padding: 20upx 0;
		border-bottom: 2upx solid rgb(217, 217, 217);
		border-top: 2upx solid rgb(217, 217, 217);
	}
	
	.spikeContentWrapper .spikeConImgWrapper {
		width: 200upx;
		height: 200upx;
		margin-left: 20upx;
		border: 2upx solid rgb(217, 217, 217);;
	}
	
	.spikeContentWrapper .spikeConImg {
		width: 200upx;
		height: 200upx;
	}
	
	.spikeConWordWrapper{
		flex: 1;
		width: 100%;
		overflow: auto;
		margin: 0 20upx;
		font-size: 32upx;
		position: relative;
	}
	.spikeConWordWrapper .spikeConTitleWord{
		display: block;
		color: #333333;
		width: 100%;
		letter-spacing: 0;
		font-size: 28upx;
	}
	.spikeconSaleInfo{
		position: absolute;
		right: 0;
		bottom: 15upx;
		text-align: right;
	}
	.spikeconSaleInfo .spikeConNum{
		color: #999999;
	}
	.spikeConMoney{
		margin-top: 10upx;
	}
	.spikeConMoney .moenyWord{
		margin-right: 10upx;
	}
	.spikeConMoney .Money{
		color:rgb(247, 97, 97) ;
	}
	
	
	
	
	
</style>
