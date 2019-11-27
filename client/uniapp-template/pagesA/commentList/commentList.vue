<template>
	<view class="content">
		<view class="box" v-for="item in reviewList">
			<view class="boximg">
				<image v-if="item.logo" :src="address+item.logo" mode="" class="pl_headImg"></image>
				<image v-else src="../../static/user.png" mode="" class="pl_headImg"></image>
			</view>
			<view class="font">
				<view style="font-size: 30upx; color: #333333;"> {{item.nick}}</view>
				<view style="color: #BEBEBE;"> {{item.time}}</view>
				<view class="pl">
					{{item.value}}
				</view>
			</view>
			
		</view>
		<uni-load-more :status="loadingType"></uni-load-more>
	</view>
</template>

<script>
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	var ef = new eonfox();
	export default {
		data(){
			return{
				id:'',
				type:'',
				address:'',
				reviewList:'',
				pagesize:10,
				loadingType: 'more', //加载更多状态
			}
		},
		components:{uniLoadMore},
		onLoad(e) {
			var that=this;
			that.id=e.id;
			that.type=e.type;
			that.loadData();
		},
		//下拉刷新
		onPullDownRefresh(){
			this.loadData('refresh');
		},
		//加载更多
		onReachBottom(){
			this.loadData();
		},
		methods:{
			loadData(type){
				var that=this;
				uni.showLoading({
					title:'加载中...',
					mask:true
				})
				ef.submit({
					request: {
						review:['USERCOMMENTLIST',[{module:that.type,key:that.id,size:that.pagesize}]] ,//that.id
						config: ['APPLICATIONCONFIG']
					},
					callback: function(data){
						var dataList=fns.checkError(data,['review','config'],function(errno,error){
							uni.showToast({
								title:error,
								icon:'none'
							})
						})
						if(dataList.config && dataList.config.qiniu_domain){
							that.address=dataList.config.qiniu_domain;//获取七牛的域名
						}
						if(dataList.review.data){
							that.reviewList=dataList.review.data
						}
						console.log('打印评论列表',dataList)
						let page_size = dataList.review.page_size;
						let row_count = dataList.review.row_count;
						that.loadingType  = Number(row_count) >= page_size ? 'more' : 'nomore';
						console.log('leix',that.loadingType)
						that.pagesize = that.pagesize+10;
						uni.hideLoading()
						if(type === 'refresh'){
							if(loading == 1){
								uni.hideLoading()
							}else{
								uni.stopPullDownRefresh();
							}
						}
					},
					error: function(err){
						 fns.err('err',err)
					         console.log("出错啦", err);
						}
					})
			},
			
		}
	}
</script>

<style>
	.content{
		width: 100%;
		height: auto;
		font-size:24upx;
		color: #555555;
		
	}
	.box{
		width: 98%;
		height: auto;
		padding-top: 10upx;
		padding-bottom: 10upx;
		border-bottom: 1upx #F1F1F3 solid;
		display: flex;
		padding-left: 2%;
	}
	.boximg{
		width:100upx;
		height:100upx;
	
	}
	.boximg image{
		width: 100%;
		height: 100%;
		border-radius: 50%;
	}
	.font{
		width:600upx;
		height:auto;
		margin-left: 20upx;
		margin-right: 20upx;
		word-wrap:break-word;
	}
	.pl{
		width:600upx;
		word-wrap:break-word;
	}
</style>
