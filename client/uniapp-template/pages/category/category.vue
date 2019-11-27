<template>
	<view class="content">
		<scroll-view scroll-y class="left-aside">
			<view v-for="item in flist" :key="item.id">
				<!-- <view v-if="tlist.filter(item2=>{return item.index === item2.index}).length" class="f-item b-b" :class="{active: item.index === currentId}" @click="tabtap(item)"> -->
				<view class="f-item b-b" :class="{active: item.index === currentId}" @click="tabtap(item)">
					<view >{{item.name}}</view>
				</view>
			</view>
		</scroll-view>
		<scroll-view scroll-with-animation scroll-y class="right-aside" @scroll="asideScroll" :scroll-top="tabScrollTop">
			<view v-for="item in slist" :key="item.id" class="s-list" :id="'main-'+item.id" v-if="true">
				<!-- <text class="s-item" v-if="tlist.filter(item2=>{return item.index === item2.index}).length" >{{item.name}}</text> -->
				<text class="s-item">{{item.name}}</text>
				<view class="t-list" v-if="tlist.length">
					<view @click="navToList(titem.id)" v-if="titem.pid === item.id" class="t-item" v-for="titem in tlist" :key="titem.id">
						<image :src="titem.picture"></image>
						<text>{{titem.name}}</text>
					</view>
				</view>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	var ef = new eonfox();
	export default {
		data() {
			return {
				sizeCalcState: false,
				tabScrollTop: 0,
				currentId: 1,
				flist: [],
				slist: [],
				tlist: [],
			}
		},
		onLoad(){
			let _this = this;
			ef.submit({
				request:{
					ss:['APPLICATIONTYPEOPTION',[{"module":"shop_goods_type","label":"APP首页菜单"}]],
					config:["APPLICATIONCONFIG"]
				},
				callback(res) {
					let qiniu_domain = res.data.config.data.qiniu_domain || '',list = [];
					 res.data.ss.data.map((item,index)=>{
						 let obj = {
							 id: item.type_id,
							 name: item.name,
							 index:index + 1
							};							
							_this.flist.push({...obj})
							_this.slist.push({...obj});
							if(Array.isArray(item.son)){
								let sonarr = item.son.map((item2,index2)=>{
									return {
										id: item2.type_id,
										pid:item2.type_parent_id,
										name: item2.name,
										picture:qiniu_domain + item2.image_id,
										type:3,
										index:index + 1
									}
								})
								// console.log(sonarr)
								_this.tlist.push(...sonarr);
							}
					})
				}
			});
		},
		methods: {
			async loadData(){
			},
			

			
			//一级分类点击
			tabtap(item){
				if(!this.sizeCalcState){
					this.calcSize();
				}
				this.currentId = item.index;
				this.tabScrollTop = this.slist[(item.index) - 1].top;
			},
			//右侧栏滚动
			asideScroll(e){
				if(!this.sizeCalcState){
					this.calcSize();
				}
				let scrollTop = e.detail.scrollTop;
				let tabs = this.slist.filter(item=>item.top <= scrollTop).reverse();
				if(tabs.length > 0){
					this.currentId = tabs[0].index;
				}
			},
			//计算右侧栏每个tab的高度等信息
			calcSize(){
				let h = 0;
				this.slist.forEach(item=>{
					let view = uni.createSelectorQuery().select("#main-" + item.id);
					view.fields({
						size: true
					}, data => {
						item.top = h;
						h += data.height;
						item.bottom = h;
					}).exec();
				})
				this.sizeCalcState = true;
			},
			navToList(tid){
				uni.navigateTo({
					url: `/pages/product/list?tid=${tid}`
				})
			}
		}
	}
</script>

<style lang='scss'>
	page,
	.content {
		height: 100%;
		background-color: #f8f8f8;
	}

	.content {
		display: flex;
	}
	.left-aside {
		flex-shrink: 0;
		width: 200upx;
		height: 100%;
		background-color: #fff;
	}
	.f-item {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 100%;
		height: 100upx;
		font-size: 28upx;
		color: $font-color-base;
		position: relative;
		&.active{
			color: $base-color;
			background: #f8f8f8;
			&:before{
				content: '';
				position: absolute;
				left: 0;
				top: 50%;
				transform: translateY(-50%);
				height: 36upx;
				width: 8upx;
				background-color: $base-color;
				border-radius: 0 4px 4px 0;
				opacity: .8;
			}
		}
	}

	.right-aside{
		flex: 1;
		overflow: hidden;
		padding-left: 20upx;
	}
	.s-item{
		display: flex;
		align-items: center;
		height: 70upx;
		padding-top: 8upx;
		font-size: 28upx;
		color: $font-color-dark;
	}
	.t-list{
		display: flex;
		flex-wrap: wrap;
		width: 100%;
		background: #fff;
		padding-top: 12upx;
		&:after{
			content: '';
			flex: 99;
			height: 0;
		}
	}
	.t-item{
		flex-shrink: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		flex-direction: column;
		width: 176upx;
		font-size: 26upx;
		color: #666;
		padding-bottom: 20upx;
		
		image{
			width: 140upx;
			height: 140upx;
		}
	}
</style>
