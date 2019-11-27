<!-- 预付款交易明细 -->
<template>
	<view class="conter">	
	<!-- <view class="detail-box" v-for="">
		<view class="box-title">
			<text>【】</text><br>
			<text style="margin-top:15upx;color: #666666; font-size: 23upx;"></text>
		</view>
		<view class="box-label">
			
		</view>
		<view class="box-number">
		<text>  </text><text></text>
		</view>
	</view> -->
	<view style="width: 100vw;background-color: #f8f8f8;">
		<view class="div" v-for="item in list">		
			<!-- <text class="state" v-if="item.state==0" style="color: red;">提现失败</text>
			<text class="state" v-if="item.state==1" style="color: green;">提现成功</text>
			<text class="state" v-if="item.state==2">提现发起</text> -->
			<view style="text-align: center;">提现金额:</view>
			<view class="money">￥{{item.value /100}}</view>
			<view class="info">
				<text class="hr"></text>
			</view>
			<view class="info">
				<text class="infoTitle">提现类型：</text>
				<text class="infoContent">{{item.type}}</text>
			</view>
			<view class="info">
				<text class="infoTitle">提现方式：</text>
				<text class="infoContent">{{item.method}}</text>
			</view>
			<view class="info">
				<text class="infoTitle">提现时间：</text>
				<text class="infoContent">{{item.time}}</text>
			</view>
			<view class="info" v-if="item.state==1">
				<text class="infoTitle">通过时间：</text>
				<text class="infoContent">{{item.pass_time}}</text>
			</view>
			<view class="info" v-if="item.state==0">
				<text class="infoTitle">失败时间：</text>
				<text class="infoContent">{{item.fail_time}}</text>
			</view>
			<view class="info" v-if="item.state==0">
				<text class="infoTitle">失败原因：</text>
				<text class="infoContent" style="color: red;">{{item.fail_info}}</text>
			</view>
			
			<view class="state_box" v-if="item.state==1">
				<text class="stat">通过</text>
			</view>
			<view class="state_box" v-if="item.state==0">
				<text class="stat stat1">失败</text>
			</view>
			<view class="state_box" v-if="item.state==2">
				<text class="stat stat2">审核中</text>
			</view>			
		</view>
		
		
	</view>
	</view>
</template>
<script>
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	var ef = new eonfox();
	export default {
		data(){
			return{
				list:[],
			}
			},	
			 onLoad:function(e){
					 var _this=this;
					 ef.submit({
					 	request: {
							s:['USERWITHDRAWSELFLIST']
					 	},
					 	callback: function(data){
							var data = fns.checkError(data,'s', function(errno, error){
								 fns.err(error)
							});
							console.log("提现列表",data);
							
							if(data.s)
							console.log(1)
								console.log("列表list",data.s);
								var list=data.s;
// 								for (var i in list) {
// 									if(list[i].)
// 								}
								_this.list=list
					 		},
					   error: function(err){
							         fns,err('',err,1)
						 }
					 });
				
				
			},
			
	 methods:{
			
	}
	}
	

</script>

<style>
	page{
		    background-color: rgb(248, 248, 248);
			border-top: 2upx solid #FFFFFF;
	}
	.conter{
		width: 100%;
		height: 100%;
		font-size: 28upx;
	}
	.div{
		width: 80%;margin: 30upx auto;background: #fff;padding: 5%;
		
	}
	.state{
		font-size: 16px;font-weight: 600;
	}
	.money{
		text-align:center;font-weight: 900;font-size: 25px;
	}
	.hr{
		text-align: center;color: #f8f8f8;width: 100%;
	}
	.info{
		height: 25px;
	}
	.infoTitle{
		width:32%;
		float:left;
	}
	.infoContent{
		
	}
	.state_box{
		width: 100%;
		text-align: center;
		padding: 20upx 0;
	}
	.stat{
		padding: 10upx 20upx;
		border-radius: 10upx;
		background: #09BB07;
		color: #FFFFFF;
	}
	.stat1{
		background: #FF0000;
	}
	.stat2{
		background: #FFB400;
	}
	.detail-box{
		width: 100%;
		height: 60px;
		font-size: 15px;
		padding:15px;
		border-bottom:1px solid #e8e8e8 ;
	}
	.box-title{
		width: 60%;
		height: 60px;
		float: left;
		padding-top: 5px;
		
	}
	.box-number{
		width: 20%;
		float: right;
		height: 60px;
		line-height: 80px;
		text-align: right;
		padding-right: 25px;
		font-weight: 900;
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
</style>
