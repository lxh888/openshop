<!-- 交易明细 -->
<template>
	<view class="conter">	
	<view class="detail-box" v-for="(item,index) in detailNumber" :key='index'>
		<view class="box-title">
			<text>【{{item.comment}}】{{item.type_name}}</text><br>
			<text style="color: #888888; margin-left: 10upx;"> {{item.time}}</text>
		</view>
		<view class="box-label">
			{{item.method_name}}
		</view>
		<view class="box-number" :style="{color:item.style}">
			{{item.sign}}<text>{{item.value/100}} <text v-if="item.method_name=='用户积分'">积分</text> <text v-else=''> 元</text> </text>
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
				detailNumber:'',
				symbol:'',
				method:'',
				mch_id:'',
				api:'USERTRADEDETAILSELFLIST',
				page:1,
				loadingType: 0,
				contentText: {
					contentdown: "上拉显示更多",
					contentrefresh: "正在加载...",
					contentnomore: "没有更多数据了"
				}
			}
			},
			components:{uniLoadMore},
			 onShow: function () {
				 this.page=1
			  this.load();
			 
			},	
			onLoad(e) {
				console.log(e)
				if(e.mch_id){
					this.mch_id=e.mch_id
					console.log(this.mch_id);
					if(e.method=='merchant_money'){
						this.api='MERCHANTMONEYSELFLIST'
					}else if(e.method=='merchant_integral'){
						this.api='MERCHANTTRADEDETAILLIST'
					}
				}
				if(e.method){
					this.method=e.method
				}
				if(e.jifen){
					this.api='USERTRADEDETAIL'
					this.method='user_credit'
				}
			},
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
								s:[_this.api,[{page:_this.page,method:_this.method,merchant_id:_this.mch_id}]]
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
										console.log("详细",_this.detailNumber);
										for(var i in re){
											if(re[i].sign=='+'){
												re[i].style='green'
											}else{
												re[i].style='red'
											}
											if(re[i].method!='商家积分'&&re[i].method!='用户积分'){
												re[i].symbol="￥"
											}
											console.log(i,':',JSON.stringify(re[i]));
											_this.detailNumber.push(re[i])
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
	 methods:{
			 load(){
				 var _this=this;
				 uni.showLoading({
				 	title:'正在加载',
					success() {
						ef.submit({
							request: {
								s: [_this.api,[{page:1,method:_this.method,merchant_id:_this.mch_id}]]
							},
							callback: function(data){
								fns.success('加载完成',function(){
									var res=fns.checkError(data, "s", function(erron, error) {
											fns.err(error)
										})
									if (res.s.data) {
										console.log(res);
										_this.detailNumber=res.s.data;
										console.log("详细",_this.detailNumber);
										for(var i in _this.detailNumber){
											if(_this.detailNumber[i].sign=='+'){
												_this.detailNumber[i].style='green'
											}else{
												_this.detailNumber[i].style='red'
											}
											if(_this.detailNumber[i].method!='商家积分'&&_this.detailNumber[i].method!='用户积分'){
												_this.detailNumber[i].symbol="￥"
											}
											console.log(i,':',JSON.stringify(_this.detailNumber[i]));
										}
										if(res.s.data.length<10){
											_this.loadingType=2
										}
									}else{
										_this.loadingType=2
									}
								})				
							},
							 error: function(err){
							         console.log("出错啦", err);
							},
						});
					}
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
	.detail-box{
		width: 100%;
		font-size: 15px;
		border-bottom:1px solid #e8e8e8 ;
		display: flex;
		padding-top: 20upx;
		padding-bottom: 20upx;
	}
	.box-title{
		width: 55%;
		height: auto;
		margin-left: 2%;
		
	}
	.box-number{
		width: 20%;
		height: 50px;
		line-height: 80px;
		text-align: right;
		font-weight: 900;
		position: absolute;
		right: 20upx;
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
