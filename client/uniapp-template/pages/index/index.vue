<template>
	<view class="container">
		<!-- 小程序头部兼容 -->
		<!-- #ifdef MP -->
		<view class="mp-search-box">
			<input class="ser-input" type="text" value="输入关键字搜索" disabled @click="openSearch()"/>
		</view>
		<!-- #endif -->
		
		<!-- 头部轮播 -->
		<view class="carousel-section">
			<!-- 标题栏和状态栏占位符 -->
			<view class="titleNview-placing"></view>
			<!-- 背景色区域 -->
			<view class="titleNview-background" :style="{backgroundColor:titleNViewBackground}"></view>
			<swiper class="carousel" circular @change="swiperChange">
				<swiper-item v-for="(item, index) in carouselList" :key="index" class="carousel-item" >
					<image :src="qiniu+item.image_id" @click="item_click(item.json)"/>
				</swiper-item>
			</swiper>
			<!-- 自定义swiper指示器 -->
			<view class="swiper-dots">
				<text class="num">{{swiperCurrent+1}}</text>
				<text class="sign">/</text>
				<text class="num">{{swiperLength}}</text>
			</view>
		</view>
		<!-- 分类 -->
		<view class="cate-section">
			
			<view class="cate-item" v-for="(item,index) in category" :key="index">
				<image :src="qiniu+item.image_id" @click="toGoodsList(item.type_id)"></image>
				<text @click="toGoodsList(item.type_id)">{{item.name}}</text>
			</view>
			
		</view>
		
		<!-- <view class="ad-1">
			<image src="/static/temp/ad1.jpg" mode="scaleToFill"></image>
		</view> -->
		
		<!-- 秒杀楼层 -->
		<view class="seckill-section m-t">
			<view class="s-header">
				<image class="s-img" src="/static/temp/secskill-img.jpg" mode="widthFix"></image>
				<!-- <text class="tip">8点场</text>
				<text class="hour timer">07</text>
				<text class="minute timer">13</text>
				<text class="second timer">55</text>-->
				<text class="yticon icon-you"></text> 
			</view>
			<scroll-view class="floor-list" scroll-x>
				<view class="scoll-wrapper">
					<view 
						v-for="(item, index) in limitedTimeGoods" :key="index"
						class="floor-item"
						@click="navToDetailPage(item.id)"
					>
						<image :src="qiniu+item.image_id" mode="aspectFill"></image>
						<text class="title clamp">{{item.name}}</text>
						<text class="price">￥{{item.price_min/100}}</text>
					</view>
				</view>
			</scroll-view>
		</view>
		
		<!-- 团购楼层 -->
		<view class="f-header m-t">
			<image src="/static/temp/h1.png"></image>
			<view class="tit-box">
				<text class="tit">精品团购</text>
				<text class="tit2">Boutique Group Buying</text>
			</view>
			<text class="yticon icon-you"></text>
		</view>
		<view class="group-section">
			<swiper class="g-swiper" :duration="500">
				<swiper-item
					class="g-swiper-item"
					v-for="(item, index) in groupBuyingGoods" :key="index"
					v-if="index%2 === 0"
					
				>
					<view class="g-item left" @click="navToGroupDetailPage(item.id)">
						<image :src="qiniu+item.image_id" mode="aspectFill"></image>
						<view class="t-box">
							<text class="title clamp">{{item.name}}</text>
							<view class="price-box">
								<text class="price">￥{{item.group_price/100}}</text> 
								<text class="m-price">￥{{item.market_price_min/100}}</text> 
							</view>
							
							<view class="pro-box">
							  	<view class="progress-box">
							  		<progress :percent="(item.group_people_now/item.group_people)*100" activeColor="#fa436a" active stroke-width="6" />
							  	</view>
								<text>{{item.group_people}}人成团</text>
							</view>
						</view>
						            
					</view>
					<view class="g-item right"  v-if="groupBuyingGoods.length>1 && index<=groupBuyingGoods.length-2" @click="navToGroupDetailPage(groupBuyingGoods[index+1].id)">
						<image :src="qiniu+groupBuyingGoods[index+1].image_id" mode="aspectFill"></image>
						<view class="t-box">
							<text class="title clamp">{{groupBuyingGoods[index+1].title}}</text>
							<view class="price-box">
								<text class="price">￥{{groupBuyingGoods[index+1].group_price/100}}</text> 
								<text class="m-price">￥{{groupBuyingGoods[index+1].market_price_min/100}}</text> 
							</view>
							<view class="pro-box">
							  	<view class="progress-box">
							  		<progress :percent="item.group_people_now/item.group_people" activeColor="#fa436a" active stroke-width="6" />
							  	</view>
								<text>{{groupBuyingGoods[index+1].group_people}}人成团</text>
							</view>
						</view>
					</view>
				</swiper-item>

			</swiper>
		</view>
		
		
		
		<!-- 分类推荐楼层 -->
		<!-- <view class="f-header m-t">
			<image src="/static/temp/h1.png"></image>
			<view class="tit-box">
				<text class="tit">分类精选</text>
				<text class="tit2">Competitive Products For You</text>
			</view>
			<text class="yticon icon-you"></text>
		</view>
		<view class="hot-floor">
			<view class="floor-img-box">
				<image class="floor-img" src="https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1553409398864&di=4a12763adccf229133fb85193b7cc08f&imgtype=0&src=http%3A%2F%2Fb-ssl.duitang.com%2Fuploads%2Fitem%2F201703%2F19%2F20170319150032_MNwmn.jpeg" mode="scaleToFill"></image>
			</view>
			<scroll-view class="floor-list" scroll-x>
				<view class="scoll-wrapper">
					<view 
						v-for="(item, index) in goodsList" :key="index"
						class="floor-item"
						@click="navToDetailPage(item)"
					>
						<image :src="item.image" mode="aspectFill"></image>
						<text class="title clamp">{{item.title}}</text>
						<text class="price">￥{{item.price}}</text>
					</view>
					<view class="more">
						<text>查看全部</text>
						<text>More+</text>
					</view>
				</view>
			</scroll-view>
		</view>
		<view class="hot-floor">
			<view class="floor-img-box">
				<image class="floor-img" src="https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1553409984228&di=dee176242038c2d545b7690b303d65ea&imgtype=0&src=http%3A%2F%2Fhbimg.b0.upaiyun.com%2F5ef4da9f17faaf4612f0d5046f4161e556e9bbcfdb5b-rHjf00_fw658" mode="scaleToFill"></image>
			</view>
			<scroll-view class="floor-list" scroll-x>
				<view class="scoll-wrapper">
					<view 
						v-for="(item, index) in goodsList" :key="index"
						class="floor-item"
						@click="navToDetailPage(item)"
					>
						<image :src="item.image3" mode="aspectFill"></image>
						<text class="title clamp">{{item.title}}</text>
						<text class="price">￥{{item.price}}</text>
					</view>
					<view class="more">
						<text>查看全部</text>
						<text>More+</text>
					</view>
				</view>
			</scroll-view>
		</view>
		<view class="hot-floor">
			<view class="floor-img-box">
				<image class="floor-img" src="https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1553409794730&di=12b840ec4f5748ef06880b85ff63e34e&imgtype=0&src=http%3A%2F%2Fimg.zcool.cn%2Fcommunity%2F01dc03589ed568a8012060c82ac03c.jpg%40900w_1l_2o_100sh.jpg" mode="scaleToFill"></image>
			</view>
			<scroll-view class="floor-list" scroll-x>
				<view class="scoll-wrapper">
					<view 
						v-for="(item, index) in goodsList" :key="index"
						class="floor-item"
						@click="navToDetailPage(item)"
					>
						<image :src="item.image2" mode="aspectFill"></image>
						<text class="title clamp">{{item.title}}</text>
						<text class="price">￥{{item.price}}</text>
					</view>
					<view class="more">
						<text>查看全部</text>
						<text>More+</text>
					</view>
				</view>
			</scroll-view>
		</view> -->

		<!-- 猜你喜欢 -->
		<view class="f-header m-t">
			<image src="/static/temp/h1.png"></image>
			<view class="tit-box">
				<text class="tit">商品精选</text>
				<text class="tit2">Guess You Like It</text>
			</view>
			<text class="yticon icon-you"></text>
		</view>
		
		<view class="guess-section">
			<view 
				v-for="(item, index) in selectedGoods" :key="index"
				class="guess-item"
				@click="navToDetailPage(item.id)"
			>
				<view class="image-wrapper">
					<image :src="qiniu+item.image_id" mode="aspectFill"></image>
				</view>
				<text class="title clamp">{{item.name}}</text>
				<text class="price">￥{{item.price_min/100}}</text>
			</view>
		</view> 
		

	</view>
</template>

<script>
import eonfox from '@/components/eonfox/eonfox.js';
import fns from '@/components/eonfox/fns.js';
var ef = new eonfox();
	export default {
		// components: {
		// 	eonfox
		// },
		data() {
			return {
				titleNViewBackground: '',
				swiperCurrent: 0,
				swiperLength: 0,
				carouselList: [],//轮播图
				qiniu:'',//七牛云地址
				category:[],//首页分类
				limitedTimeGoods:[],//限时商品
				groupBuyingGoods:[],//团购商品
				selectedGoods:[],//商品精选
				goodsList: []
			};
		},

		onLoad() {
			this.loadData();
		},
		methods: {
			//轮播图点击事件
			item_click(json) {
				let _this = this;
				if (json) {
					let str = JSON.parse(json)
					console.log('轮播图参数', json)
					switch (str['type']){
						case 'goods':
							uni.navigateTo({
								url: '../../pagesA/product/product?id=' + str['id']
							})
							break;
						case 'shop':
							uni.navigateTo({
								url: '../../pagesA/BusinessDetails/BusinessDetails?id=' + str['id']
							})
							break;
						
						default:
							fns.err('该类型参数暂未做处理')
							break;
					}
				} else {
					console.log('json参数为空')
				}
			},
			
			toGoodsList(type_id){
				uni.navigateTo({
					url: '/pages/product/list?tid='+type_id
				});
			},
			openSearch(){
				uni.navigateTo({
					url:'../../pagesA/search/search'
				})
				//this.$api.msg('功能开发中，敬请期待');
			},
			/**
			 * 请求静态数据只是为了代码不那么乱
			 * 分次请求未作整合
			 */
			async loadData() {
				//let carouselList = await this.$api.json('carouselList');
				//this.titleNViewBackground = carouselList[0].background;
				// this.swiperLength = carouselList.length;
				// this.carouselList = carouselList;
				// let goodsList = await this.$api.json('goodsList');
				// this.goodsList = goodsList || [];
				
				var _this=this;
				uni.showLoading({
					title:'加载中...',
					mask: true
				})
				ef.submit({
					request: {
						images: ['APPLICATIONSLIDESHOW', [{ module: 'home', label: 'APP' }]],
						config: ['APPLICATIONCONFIG'],
						integral: ['APPLICATIONTYPEOPTION', [{ module: 'home' }]], //分类
						a: ['APPLICATIONTYPEOPTION', [{ module: 'shop_goods_type', label: 'APP首页专区' }]], //分类菜单
						s: ['APPLICATIONTYPEOPTION', [{ module: 'shop_goods_type', label: 'APP首页菜单' }]], //分类菜单
						whenGoodsList: ['SHOPGOODSLIST', [{ search: { when: 1 } }]], //限时商品
						shopGoodsList: ['SHOPGOODSLIST', [{ search: { group: 1 } }]], //团购商品
						Selected: ["SHOPGOODSLIST",[{sort:['sort_asc'],search:{"type":{"label":"Selected","module":"shop_goods_type"}}}]], //分类菜单
					},
					callback(data) {
						var dataList = fns.checkError(data, ['config', 'integral', 'whenGoodsList', 'images','s'], function(errno, error) {
							console.log(errno, error);
						});
						console.log('xxxxxxxxxx', dataList);
						//七牛云地址
						if (dataList.config && dataList.config.qiniu_domain) {
							_this.qiniu = dataList.config.qiniu_domain;
						}
						//轮播图
						if (dataList.images) {
							_this.carouselList = dataList.images;
							_this.swiperLength = dataList.images.length;
							if(_this.swiperLength>=1){
								_this.titleNViewBackground = dataList.images[0].name;
							}
						}
						//首页分类
						if (dataList.s) {
							_this.category = dataList.s;
						}
						//限时商品
						if (dataList.whenGoodsList && dataList.whenGoodsList.data) {
							_this.limitedTimeGoods = dataList.whenGoodsList.data;
						}
						//拼团商品
						if (dataList.shopGoodsList && dataList.shopGoodsList.data) {
							// countDown();
							var goodsList = dataList.shopGoodsList.data;
							//拼团时间
							goodsList.forEach(item => {
								var countDownObj = _this.countDown(item.group_start_time, item.group_end_time);
								Object.assign(item, countDownObj);
							});
							_this.groupBuyingGoods = goodsList;
						}
						//精选商品
						if (dataList.Selected && dataList.Selected.data) {
							_this.selectedGoods = dataList.Selected.data;
						}
						
				// 		if (dataList.a) {
				// 			/* var typeSonList = [];
				// 				
				// 				for( var i in dataList.a ){
				// 					if( dataList.a[i].son ){
				// 						typeSonList = typeSonList.concat(dataList.a[i].son);
				// 					}
				// 				} */
				// 			if (dataList.a[0] && dataList.a[0].son) {
				// 				_this.classList = dataList.a[0].son;
				// 			}
				// 		}
				// 
				
				// 
				// 		if (dataList.integral) {
				// 			_this.integral = dataList.integral;
				// 		}
				// 		
				// 		
				uni.hideLoading();
				// 		// #ifdef APP-PLUS
				// 		console.log('检测版本更新');
				
				// 		if (dataList.config && dataList.config.app_android_version && dataList.config.app_android_version.number) {
				// 			var Versionnumber = dataList.config.app_android_version.number;
				// 			var info = dataList.config.app_android_version.info;
				// 			var name = dataList.config.app_android_version.name;
				// 			var download = dataList.config.app_android_version.download;
				// 		}
				// 		_this.VersionNumber = plus.runtime.version;
				
				// 		uni.getSystemInfo({
				// 			success: res => {
				// 				console.log(res.platform);
				// 				//检测当前平台，如果是安卓则启动安卓更新
				// 				if (res.platform == 'android') {
				// 					if (Versionnumber != _this.VersionNumber) {
				// 						uni.showModal({
				// 							title: '版本已更新',
				// 							content: info + name,
				// 							showCancel: true,
				// 							confirmText: '确认更新',
				// 							success: function(res) {
				// 								if (res.confirm) {
				// 									setTimeout(function() {
				// 										plus.runtime.openURL(download);
				// 									}, 2000);
				// 								} else if (res.cancel) {
				// 									plus.runtime.quit();
				// 									console.log('用户点击取消');
				// 								}
				// 							}
				// 						});
				// 					}
				// 				}
				// 				if (res.platform == 'ios') {
				// 					if (Versionnumber != _this.VersionNumber) {
				// 						uni.showModal({
				// 							title: '版本已更新',
				// 							content: info + name,
				// 							showCancel: false,
				// 							confirmText: '确认更新',
				// 							success: function(res) {
				// 								if (res.confirm) {
				// 									setTimeout(function() {
				// 										plus.runtime.openURL('https://apps.apple.com/cn/app/%E5%88%9B%E8%81%94%E4%BC%97%E7%9B%8A/id1452979739');
				// 									}, 2000);
				// 								} else if (res.cancel) {
				// 									plus.runtime.quit();
				// 									console.log('用户点击取消');
				// 								}
				// 							}
				// 						});
				// 					}
				// 				}
				// 			}
				// 		});
				
				// 		// #endif
					}
				});
			},
			countDown(start_time, end_time) {
				var cDown = end_time - start_time;
				if (cDown > 0) {
					var d = Math.floor(cDown / 60 / 60 / 24);
					var h = Math.floor((cDown / 60 / 60) % 24);
					var m = Math.floor((cDown / 60) % 60);
					var s = Math.floor(cDown % 60);
				} else {
					return false;
				}
				return {
					timeDay: d,
					timeHour: h,
					timeMinite: m,
					timeScond: s
				};
				console.log(d);
			},
			//轮播图切换修改背景色
			swiperChange(e) {
				const index = e.detail.current;
				this.swiperCurrent = index;
				this.titleNViewBackground = this.carouselList[index].name;
			},
			//详情页
			navToDetailPage(id) {
				if(!id){
					uni.showToast({
						title:"商品id异常，请刷新重试",
						icon:none
					})
				}else{
					uni.navigateTo({
						url: `/pagesA/product/product?id=`+id
					})
				}
				
			},
			//团购商品详情页
			navToGroupDetailPage(id) {
				if(!id){
					uni.showToast({
						title:"商品id异常，请刷新重试",
						icon:none
					})
				}else{
					uni.navigateTo({
						url: `/pagesA/product/groupProduct?id=`+id
					})
				}
				
			},
		},
		// #ifndef MP
		
		// 标题栏input搜索框点击
		onNavigationBarSearchInputClicked: async function(e) {
			this.openSearch();
			// this.$api.msg('功能开发中，敬请期待');
		},
		//点击导航栏 buttons 时触发
		onNavigationBarButtonTap(e) {
			const index = e.index;
			if (index === 0) {
				this.$api.msg('功能开发中，敬请期待');
			} else if (index === 1) {
				this.$api.msg('功能开发中，敬请期待');
				// // #ifdef APP-PLUS
				// const pages = getCurrentPages();
				// const page = pages[pages.length - 1];
				// const currentWebview = page.$getAppWebview();
				// currentWebview.hideTitleNViewButtonRedDot({
				// 	index
				// });
				// // #endif
				// uni.navigateTo({
				// 	url: '/pages/notice/notice'
				// })
			}
		}
		// #endif
	}
</script>

<style lang="scss">
	/* #ifdef MP */
	.mp-search-box{
		position:absolute;
		left: 0;
		top: 30upx;
		z-index: 9999;
		width: 100%;
		padding: 0 80upx;
		.ser-input{
			flex:1;
			height: 56upx;
			line-height: 56upx;
			text-align: center;
			font-size: 28upx;
			color:$font-color-base;
			border-radius: 20px;
			background: rgba(255,255,255,.6);
		}
	}
	page{
		.cate-section{
			position:relative;
			z-index:5;
			border-radius:16upx 16upx 0 0;
			margin-top:-20upx;
		}
		.carousel-section{
			padding: 0;
			.titleNview-placing {
				padding-top: 0;
				height: 0;
			}
			.carousel{
				.carousel-item{
					padding: 0;
				}
			}
			.swiper-dots{
				left:45upx;
				bottom:40upx;
			}
		}
	}
	/* #endif */
	
	
	page {
		background: #f5f5f5;
	}
	.m-t{
		margin-top: 16upx;
	}
	/* 头部 轮播图 */
	.carousel-section {
		position: relative;
		padding-top: 10px;

		.titleNview-placing {
			height: var(--status-bar-height);
			padding-top: 44px;
			box-sizing: content-box;
		}

		.titleNview-background {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 426upx;
			transition: .4s;
		}
	}
	.carousel {
		width: 100%;
		height: 350upx;

		.carousel-item {
			width: 100%;
			height: 100%;
			padding: 0 28upx;
			overflow: hidden;
		}

		image {
			width: 100%;
			height: 100%;
			border-radius: 10upx;
		}
	}
	.swiper-dots {
		display: flex;
		position: absolute;
		left: 60upx;
		bottom: 15upx;
		width: 72upx;
		height: 36upx;
		background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAYAAADDhn8LAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTMyIDc5LjE1OTI4NCwgMjAxNi8wNC8xOS0xMzoxMzo0MCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTk4MzlBNjE0NjU1MTFFOUExNjRFQ0I3RTQ0NEExQjMiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTk4MzlBNjA0NjU1MTFFOUExNjRFQ0I3RTQ0NEExQjMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTcgKFdpbmRvd3MpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6Q0E3RUNERkE0NjExMTFFOTg5NzI4MTM2Rjg0OUQwOEUiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6Q0E3RUNERkI0NjExMTFFOTg5NzI4MTM2Rjg0OUQwOEUiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4Gh5BPAAACTUlEQVR42uzcQW7jQAwFUdN306l1uWwNww5kqdsmm6/2MwtVCp8CosQtP9vg/2+/gY+DRAMBgqnjIp2PaCxCLLldpPARRIiFj1yBbMV+cHZh9PURRLQNhY8kgWyL/WDtwujjI8hoE8rKLqb5CDJaRMJHokC6yKgSCR9JAukmokIknCQJpLOIrJFwMsBJELFcKHwM9BFkLBMKFxNcBCHlQ+FhoocgpVwwnv0Xn30QBJGMC0QcaBVJiAMiec/dcwKuL4j1QMsVCXFAJE4s4NQA3K/8Y6DzO4g40P7UcmIBJxbEesCKWBDg8wWxHrAiFgT4fEGsB/CwIhYE+AeBAAdPLOcV8HRmWRDAiQVcO7GcV8CLM8uCAE4sQCDAlHcQ7x+ABQEEAggEEAggEEAggEAAgQACASAQQCCAQACBAAIBBAIIBBAIIBBAIABe4e9iAe/xd7EAJxYgEGDeO4j3EODp/cOCAE4sYMyJ5cwCHs4rCwI4sYBxJ5YzC84rCwKcXxArAuthQYDzC2JF0H49LAhwYUGsCFqvx5EF2T07dMaJBetx4cRyaqFtHJ8EIhK0i8OJBQxcECuCVutxJhCRoE0cZwMRyRcFefa/ffZBVPogePihhyCnbBhcfMFFEFM+DD4m+ghSlgmDkwlOgpAl4+BkkJMgZdk4+EgaSCcpVX7bmY9kgXQQU+1TgE0c+QJZUUz1b2T4SBbIKmJW+3iMj2SBVBWz+leVfCQLpIqYbp8b85EskIxyfIOfK5Sf+wiCRJEsllQ+oqEkQfBxmD8BBgA5hVjXyrBNUQAAAABJRU5ErkJggg==);
		background-size: 100% 100%;

		.num {
			width: 36upx;
			height: 36upx;
			border-radius: 50px;
			font-size: 24upx;
			color: #fff;
			text-align: center;
			line-height: 36upx;
		}

		.sign {
			position: absolute;
			top: 0;
			left: 50%;
			line-height: 36upx;
			font-size: 12upx;
			color: #fff;
			transform: translateX(-50%);
		}
	}
	/* 分类 */
	.cate-section {
		display: flex;
		justify-content: space-around;
		align-items: center;
		flex-wrap:wrap;
		padding: 30upx 20upx; 
		background: #fff;
		.cate-item {
			display: flex;
			flex-direction: column;
			align-items: center;
			padding-top: 20upx;
			width: 142upx;
			font-size: $font-sm + 2upx;
			color: $font-color-dark;
		}
		/* 原图标颜色太深,不想改图了,所以加了透明度 */
		image {
			width: 88upx;
			height: 88upx;
			margin-bottom: 14upx;
			border-radius: 50%;
			opacity: .7;
			box-shadow: 4upx 4upx 20upx rgba(250, 67, 106, 0.3);
		}
	}
	.ad-1{
		width: 100%;
		height: 210upx;
		padding: 10upx 0;
		background: #fff;
		image{
			width:100%;
			height: 100%; 
		}
	}
	/* 秒杀专区 */
	.seckill-section{
		padding: 4upx 30upx 24upx;
		background: #fff;
		.s-header{
			display:flex;
			align-items:center;
			height: 92upx;
			line-height: 1;
			.s-img{
				width: 140upx;
				height: 30upx;
			}
			.tip{
				font-size: $font-base;
				color: $font-color-light;
				margin: 0 20upx 0 40upx;
			}
			.timer{
				display:inline-block;
				width: 40upx;
				height: 36upx;
				text-align:center;
				line-height: 36upx;
				margin-right: 14upx;
				font-size: $font-sm+2upx;
				color: #fff;
				border-radius: 2px;
				background: rgba(0,0,0,.8);
			}
			.icon-you{
				font-size: $font-lg;
				color: $font-color-light;
				flex: 1;
				text-align: right;
			}
		}
		.floor-list{
			white-space: nowrap;
		}
		.scoll-wrapper{
			display:flex;
			align-items: flex-start;
		}
		.floor-item{
			width: 150upx;
			margin-right: 20upx;
			font-size: $font-sm+2upx;
			color: $font-color-dark;
			line-height: 1.8;
			image{
				width: 150upx;
				height: 150upx;
				border-radius: 6upx;
			}
			.price{
				color: $uni-color-primary;
			}
		}
	}
	
	.f-header{
		display:flex;
		align-items:center;
		height: 140upx;
		padding: 6upx 30upx 8upx;
		background: #fff;
		image{
			flex-shrink: 0;
			width: 80upx;
			height: 80upx;
			margin-right: 20upx;
		}
		.tit-box{
			flex: 1;
			display: flex;
			flex-direction: column;
		}
		.tit{
			font-size: $font-lg +2upx;
			color: #font-color-dark;
			line-height: 1.3;
		}
		.tit2{
			font-size: $font-sm;
			color: $font-color-light;
		}
		.icon-you{
			font-size: $font-lg +2upx;
			color: $font-color-light;
		}
	}
	/* 团购楼层 */
	.group-section{
		background: #fff;
		.g-swiper{
			height: 650upx;
			padding-bottom: 30upx;
		}
		.g-swiper-item{
			width: 100%;
			padding: 0 30upx;
			display:flex;
		}
		image{
			width: 100%;
			height: 460upx;
			border-radius: 4px;
		}
		.g-item{
			display:flex;
			flex-direction: column;
			overflow:hidden;
		}
		.left{
			flex: 1.2;
			margin-right: 24upx;
			.t-box{
				padding-top: 20upx;
			}
		}
		.right{
			flex: 0.8;
			flex-direction: column-reverse;
			.t-box{
				padding-bottom: 20upx;
			}
		}
		.t-box{
			height: 160upx;
			font-size: $font-base+2upx;
			color: $font-color-dark;
			line-height: 1.6;
		}
		.price{
			color:$uni-color-primary;
		}
		.m-price{
			font-size: $font-sm+2upx;
			text-decoration: line-through;
			color: $font-color-light;
			margin-left: 8upx;
		}
		.pro-box{
			display:flex;
			align-items:center;
			margin-top: 10upx;
			font-size: $font-sm;
			color: $font-base;
			padding-right: 10upx;
		}
		.progress-box{
			flex: 1;
			border-radius: 10px;
			overflow: hidden;
			margin-right: 8upx;
		}
	}
	/* 分类推荐楼层 */
	.hot-floor{
		width: 100%;
		overflow: hidden;
		margin-bottom: 20upx;
		.floor-img-box{
			width: 100%;
			height:320upx;
			position:relative;
			&:after{
				content: '';
				position:absolute;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
				background: linear-gradient(rgba(255,255,255,.06) 30%, #f8f8f8);
			}
		}
		.floor-img{
			width: 100%;
			height: 100%;
		}
		.floor-list{
			white-space: nowrap;
			padding: 20upx;
			padding-right: 50upx;
			border-radius: 6upx;
			margin-top:-140upx;
			margin-left: 30upx;
			background: #fff;
			box-shadow: 1px 1px 5px rgba(0,0,0,.2);
			position: relative;
			z-index: 1;
		}
		.scoll-wrapper{
			display:flex;
			align-items: flex-start;
		}
		.floor-item{
			width: 180upx;
			margin-right: 20upx;
			font-size: $font-sm+2upx;
			color: $font-color-dark;
			line-height: 1.8;
			image{
				width: 180upx;
				height: 180upx;
				border-radius: 6upx;
			}
			.price{
				color: $uni-color-primary;
			}
		}
		.more{
			display:flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;
			flex-shrink: 0;
			width: 180upx;
			height: 180upx;
			border-radius: 6upx;
			background: #f3f3f3;
			font-size: $font-base;
			color: $font-color-light;
			text:first-child{
				margin-bottom: 4upx;
			}
		}
	}
	/* 猜你喜欢 */
	.guess-section{
		display:flex;
		flex-wrap:wrap;
		padding: 0 30upx;
		background: #fff;
		.guess-item{
			display:flex;
			flex-direction: column;
			width: 48%;
			padding-bottom: 40upx;
			&:nth-child(2n+1){
				margin-right: 4%;
			}
		}
		.image-wrapper{
			width: 100%;
			height: 330upx;
			border-radius: 3px;
			overflow: hidden;
			image{
				width: 100%;
				height: 100%;
				opacity: 1;
			}
		}
		.title{
			font-size: $font-lg;
			color: $font-color-dark;
			line-height: 80upx;
		}
		.price{
			font-size: $font-lg;
			color: $uni-color-primary;
			line-height: 1;
		}
	}
	

</style>
