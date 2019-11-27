<!-- 我的推广 -->
<template>
	<view class="conter">	
	<view class="abox">
		<text class="a" style="float: left;">已分享 {{sharenumber}} 人</text>
		<text class="a" style="float: right; color: #0A98D5;" @click="myQRcode()">查看我的分享码</text>
	</view>
	<view class="detail-box clearFix" v-for="item in list">
		<view class="box-img">
			<image v-if='image_id' :src="qiniu + item.image_id+'?imageView2/0/h/60/w/60'"></image>
			<image  v-else src="../../static/missing-face.png" mode=""></image>
			
		</view>
		<view class="box-title">
			<view v-if="item.nickname!=''">{{item.nickname}}</view>
			<view v-else>昵称未填写</view>
			<view style="padding-top:15upx;color: #666666; font-size: 23upx;">{{item.time}}</view>
		</view>
		
		<view class="box-number">
		<view> {{item.phone}}</view>
		</view>
	</view>
	<uni-load-more :loadingType="loadingType"  :contentText="contentText"></uni-load-more>
	</view>
</template>

<script>
	import uniLoadMore from "@/components/uni-load-more/uni-load-more.vue"
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	var ef = new eonfox();
	export default {
		data(){
			return{
				list:[]	,
				sharenumber:0,
				qiniu:'',
				page:1,
				loadingType: 0,
				contentText: {
					contentdown: "上拉显示更多",
					contentrefresh: "正在加载...",
					contentnomore: "没有更多数据了"
				}
			}
			},
// 			 onShow: function () {
// 			  this.load(e);
// 			},	
			components:{uniLoadMore},
			onReachBottom() {
				if (this.loadingType !== 0) {
					return;
				}
				this.loadingType = 1;
				var _this=this;
				_this.page++
				uni.showLoading({
					title:'正在加载',
					success() {
						ef.submit({
							request:{
								s:['USERSELFSONLIST',[{page:_this.page}]]
							},
							callback:function(data){
								fns.success('加载完成',function(){
									console.log('more',data);
									var res=fns.checkError(data, "s", function(erron, error) {
											fns.err(error)
										})
									if (res.s.data && res.s.data.length) {
										console.log(res);
										var re=res.s.data
										console.log("详细",_this.list);
										for(var i of re){
											_this.list.push(i)
										}
										if(res.s.data.length<10){
											_this.loadingType=2
										}else{
											_this.loadingType=0
										}
									}else{
										_this.loadingType=2
									}
								})
							},
							error(err){
								fns.err('err',err,1)
							}
						})
					}
				})
			},
			 onLoad:function(){
					 var _this=this;
					 ef.submit({
					 	request: {
							s: ['USERSELFSONLIST',[{page:_this.page}]],
							 sharenumber:['USERSELFSONCOUNT'],
							 config:['APPLICATIONCONFIG'],//获取七牛云地址
					 	},
					 	callback: function(data){
							var list = fns.checkError(data,'s', function(erron, error){
								 uni.showToast({
									title:data.data.s.error,
									icon:'none'
								});
							});
							if(list.config && list.config.qiniu_domain){
								_this.qiniu=list.config.qiniu_domain
							}
							_this.list=list.s.data
							if(list.s.data.length<10){
								_this.loadingType=2
							}else{
								_this.loadingType=0
							}
							console.log("列表",list);
							if(list.sharenumber)
							_this.sharenumber=list.sharenumber
					 		},
					   error: function(err){
						   fns.err('',err,1)
						 }
					 });
			},
			
	 methods:{
		myQRcode(){
			uni.navigateTo({
				url:'../../pagesA/QRCode/QRCode'
			})
		}	
	}
	}
	

</script>

<style>
	.conter{
		width: 100%;
		height: auto;
		font-size: 28upx;
	}
	.abox{
		width: 100%;
		height: 50upx;
	}
	.a{
		display: block;
		color:rgb(102, 190, 255);
		font-size:32upx;
		padding: 10px;
	}
	.clearFix {
		zoom: 1;
	}
	.clearFix:after {
		content: ".";
		display: block;
		height: 0;
		clear: both;
		visibility: hidden;
	}
	.left{
		float: left;
	}
	.right{
		float: right;
	}
	.detail-box{
		display: flex;
		width: 100%;
		font-size: 15px;
		align-items: center;
		border-bottom:1px solid #e8e8e8 ;
	}
	.box-img{
		 margin-left: 10px;
		 width: 60px;
		 display: block;
		 height: 60px;
	 }
	.box-title{
		flex: 1;
		height: 60px;
		padding-top:20px;
		padding-left:3%;
	}
	.box-number{
		flex: 1;
		height: 60px;
		line-height: 80px;
		font-weight: 900;
		text-align: right;
		margin-right: 10px;
		margin-bottom: 20px;
	}
	.box-label{
		position: absolute;
		width:160upx;
		height: 40upx;
		border: #F1F1F3 1px solid;
		right: 5upx;
		text-align:center;
		border-radius:8px;
		background-color: #F1F1F3;
		font-size: 23upx;
		line-height: 40upx;
	}
	image{
		width:120rpx;
		height:120rpx;
		border-radius:100%;
	}
</style>
