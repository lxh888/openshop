<template>
	<view class="content">
		<view class="navbar" :style="{position:headerPosition,top:headerTop}">
			<view class="nav-item" :class="{current: filterIndex === 0}" @click="tabClick(0)">
				上架时间
			</view>
			<view class="nav-item" :class="{current: filterIndex === 1}" @click="tabClick(1)">
				销量优先
			</view>
			<view class="nav-item" :class="{current: filterIndex === 2}" @click="tabClick(2)">
				<text>价格</text>
				<view class="p-box">
					<text :class="{active: priceOrder === 1 && filterIndex === 2}" class="yticon icon-shang"></text>
					<text :class="{active: priceOrder === 2 && filterIndex === 2}" class="yticon icon-shang xia"></text>
				</view>
			</view>
			<text class="cate-item yticon icon-fenlei1" @click="toggleCateMask('show')"></text>
		</view>
		<view class="goods-list">
			<view 
				v-for="(item, index) in goodsList" :key="index"
				class="goods-item"
				@click="navToDetailPage(item)"
			>
				<view class="image-wrapper">
					<image :src="qiniu + item.image_id" mode="aspectFill"></image>
				</view>
				<text class="title clamp">{{item.name}}</text>
				<view class="price-box">
					<text class="price">{{item.price_min/100}}</text>
					<text>已售 {{item.sales}}</text>
				</view>
			</view>
		</view>
		<uni-load-more :status="loadingType"></uni-load-more>
		
		
		<view class="cate-mask" :class="cateMaskState===0 ? 'none' : cateMaskState===1 ? 'show' : ''" @click="toggleCateMask">
			<view class="cate-content" @click.stop.prevent="stopPrevent" @touchmove.stop.prevent="stopPrevent">
				<scroll-view scroll-y class="cate-list">
					<view v-for="(item,index) in cateList" :key="index">
						<view class="cate-item b-b two">{{item.allData.name}}</view>
						<view 
							v-for="(tItem,index2) in item.allData.son" :key="index2" 
							class="cate-item b-b" 
							:class="{active: tItem.type_id==cateId}"
							@click="changeCate(tItem.type_id)">
							{{tItem.name}}
						</view>
					</view>
				</scroll-view>
			</view>
		</view>
		
	</view>
</template>

<script>
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	var ef = new eonfox();
	export default {
		components: {
			uniLoadMore	
		},
		data() {
			return {
				cateMaskState: 0, //分类面板展开状态
				headerPosition:"fixed",
				headerTop:"0px",
				loadingType: 'more', //加载更多状态
				filterIndex: 0, 
				cateId: 0, //已选三级分类id
				priceOrder: 0, //1 价格从低到高 2价格从高到低
				cateList: [],
				goodsList: [],
				qiniu:'',
				search:{},
				type_id:'',
				sort:[],
				page:10
			};
		},
		onLoad(options){
			let that =this;
			// console.log(options.tid)
			if(options.tid){
				that.type_id=options.tid;
			}
			//通过搜索过来的
			if(options.searchText){
				that.searchText=options.searchText;
			}
			// this.getgoodsList();
			// #ifdef H5
			//this.headerTop = document.getElementsByTagName('uni-page-head')[0].offsetHeight+'px';
			this.headerTop='44px';
			// #endif
			this.cateId = options.tid;
			// this.loadCateList(options.fid,options.sid);
			this.getgoodsList(that.type_id);
		},
		onPageScroll(e){
			//兼容iOS端下拉时顶部漂移
			if(e.scrollTop>=0){
				this.headerPosition = "fixed";
			}else{
				this.headerPosition = "absolute";
			}
		},
		//下拉刷新
		onPullDownRefresh(){
			this.loadData('refresh');
		},
		//加载更多
		onReachBottom(){
			this.loadData();
		},
		methods: {
			// 获取数据
			getgoodsList(cateId){				
				let that = this
				ef.submit({
					request:{
						goods:['SHOPGOODSLIST',[{"size":that.page,search:{"type_id":cateId,keywords:that.searchText},"sort":that.sort}]],
						// "goods":["SHOPGOODSLIST",[{"size":20}]],
						config:["APPLICATIONCONFIG"],
						navList:['APPLICATIONTYPEOPTION',[{"module":"shop_goods_type","label":"APP首页菜单"}]]
					},
					callback:function(data){
						// console.log(data,'数据')
						let qiniu_domain = data.data.config.data.qiniu_domain || '';
						that.qiniu = qiniu_domain;
						if(data.data.navList.data){
							// console.log(data.data.navList.data)
							
							let catlist = data.data.navList.data.map((item,index)=>{
								return{									
									allData:item									
								}	
								
							})	
							// console.log(catlist,'查看')
							that.cateList.push(...catlist)							
						}
						
						
						if(data.data.goods&&data.data.goods.data.data.length){
							var list=data.data.goods.data.data							
							for(let i=0;i<list.length;i++){								
								that.goodsList=data.data.goods.data.data
							}
						}
					}
				})
			},
			//加载分类
			async loadCateList(fid, sid){
				// let list = await this.$api.json('cateList');
				// let cateList = list.filter(item=>item.pid == fid);
				// 
				// cateList.forEach(item=>{
				// 	let tempList = list.filter(val=>val.pid == item.id);
				// 	item.child = tempList;
				// })
				// this.cateList = cateList;
			},
			//加载商品 ，带下拉刷新和上滑加载
			async loadData(type='add', loading) {
				let _this = this
				//没有更多直接返回
				if(type === 'add'){
					if(this.loadingType === 'nomore'){
						return;
					}
					this.loadingType = 'loading';
				}else{
					this.loadingType = 'more'
				}
				

				//筛选，测试数据直接前端筛选了
				if(_this.filterIndex === 0){
					_this.sort = [];
				}
				if(_this.filterIndex === 1){
					_this.sort = ['sales_desc'];
					// goodsList.sort((a,b)=>Number(b.sales) - Number(a.sales))
				}
				if(_this.filterIndex === 2){
					if(_this.priceOrder == 1){
						_this.sort = ['price_min_asc'];
					}else{
						_this.sort = ['price_min_desc'];
					}
					
				}
				
				_this.page = _this.page+10;
			
				ef.submit({
					request:{
						goods:['SHOPGOODSLIST',[{size:_this.page,search:{"type_id":_this.type_id,keywords:_this.searchText},sort:_this.sort}]],
						// "goods":["SHOPGOODSLIST",[{"size":_this.page,"sort":_this.sort}]],
						config:["APPLICATIONCONFIG"]
					},
					callback:function(data){
						// console.log(data.data.goods.data)
						// console.log(data.data.goods.data.data.length)
						let page_size = data.data.goods.data.page_size;
						let row_count = data.data.goods.data.row_count;
						
						let qiniu_domain = data.data.config.data.qiniu_domain || '';
						_this.qiniu = qiniu_domain;
						// this.goodsList.concat(data.data.goods.data.data);
						// console.log(GoodsList)
						if(data.data.goods&&data.data.goods.data.data.length){
							var list=data.data.goods.data.data							
							for(let i=0;i<list.length;i++){
								// _this.searchList.push(list[i])
								// console.log(_this.searchList);
								_this.goodsList=data.data.goods.data.data
							}
						}
						// let goodsList = data.data.goods.data.data.map(item=>{
						// 	return {
						// 		...item,
						// 		image:qiniu_domain + item.image_id,
						// 		price:item.price_min
						// 	}
						// });
						// if(type === 'refresh'){
						// 	_this.goodsList = [];
						// }
						// //筛选，测试数据直接前端筛选了
						// if(_this.filterIndex === 1){
						// 	goodsList.sort((a,b)=>Number(b.sales) - Number(a.sales))
						// }
						// if(_this.filterIndex === 2){
						// 	goodsList.sort((a,b)=>{
						// 		if(_this.priceOrder == 1){
						// 			return a.price - b.price;
						// 		}
						// 		return b.price - a.price;
						// 	})
						// }
						// if(type === 'refresh'){
						// 	_this.goodsList = [];
						// 	
						// }
						// _this.goodsList = _this.goodsList.concat(goodsList);
						
						
						// console.log(_this.goodsList)
						
						//判断是否还有下一页，有是more  没有是nomore(测试数据判断大于20就没有了)
						// _this.loadingType  = goodsList.length >= 9 ? 'more' : 'nomore';
						_this.loadingType  = Number(row_count) >= page_size ? 'more' : 'nomore';
						
						if(type === 'refresh'){
							if(loading == 1){
								uni.hideLoading()
							}else{
								uni.stopPullDownRefresh();
							}
						}
					}
				})
				
				
				
				
				// let goodsList = await this.$api.json('goodsList');
				
			},
			//筛选点击
			tabClick(index){
				if(this.filterIndex === index && index !== 2){
					return;
				}
				this.filterIndex = index;
				if(index === 2){
					this.priceOrder = this.priceOrder === 1 ? 2: 1;
				}else{
					this.priceOrder = 0;
				}
				uni.pageScrollTo({
					duration: 300,
					scrollTop: 0
				})
				this.loadData('refresh', 1);
				uni.showLoading({
					title: '正在加载'
				})
			},
			//显示分类面板
			toggleCateMask(type){
				let timer = type === 'show' ? 10 : 300;
				let	state = type === 'show' ? 1 : 0;
				this.cateMaskState = 2;
				setTimeout(()=>{
					this.cateMaskState = state;
				}, timer)
			},
			//分类点击
			changeCate(item){
				// console.log(item)
				// return
				this.type_id = item;
				this.cateId = item;
				this.toggleCateMask();
				uni.pageScrollTo({
					duration: 300,
					scrollTop: 0
				})
				this.page= 10
				this.getgoodsList(item)
				// this.loadData('refresh', 1);
				// uni.showLoading({
				// 	title: '正在加载'
				// })
				
				
			},
			//详情
			navToDetailPage(item){
				// console.log(item)
				
				let id = item.id;
				uni.navigateTo({
					url: `/pagesA/product/product?id=${id}`
				})
			},
			stopPrevent(){}
		},
	}
</script>

<style lang="scss">
	page, .content{
		background: $page-color-base;
	}
	.content{
		padding-top: 96upx;
	}

	.navbar{
		position: fixed;
		left: 0;
		top: var(--window-top);
		display: flex;
		width: 100%;
		height: 80upx;
		background: #fff;
		box-shadow: 0 2upx 10upx rgba(0,0,0,.06);
		z-index: 10;
		.nav-item{
			flex: 1;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100%;
			font-size: 30upx;
			color: $font-color-dark;
			position: relative;
			&.current{
				color: $base-color;
				&:after{
					content: '';
					position: absolute;
					left: 50%;
					bottom: 0;
					transform: translateX(-50%);
					width: 120upx;
					height: 0;
					border-bottom: 4upx solid $base-color;
				}
			}
		}
		.p-box{
			display: flex;
			flex-direction: column;
			.yticon{
				display: flex;
				align-items: center;
				justify-content: center;
				width: 30upx;
				height: 14upx;
				line-height: 1;
				margin-left: 4upx;
				font-size: 26upx;
				color: #888;
				&.active{
					color: $base-color;
				}
			}
			.xia{
				transform: scaleY(-1);
			}
		}
		.cate-item{
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100%;
			width: 80upx;
			position: relative;
			font-size: 44upx;
			&:after{
				content: '';
				position: absolute;
				left: 0;
				top: 50%;
				transform: translateY(-50%);
				border-left: 1px solid #ddd;
				width: 0;
				height: 36upx;
			}
		}
	}

	/* 分类 */
	.cate-mask{
		position: fixed;
		left: 0;
		top: var(--window-top);
		bottom: 0;
		width: 100%;
		background: rgba(0,0,0,0);
		z-index: 95;
		transition: .3s;
		
		.cate-content{
			width: 630upx;
			height: 100%;
			background: #fff;
			float:right;
			transform: translateX(100%);
			transition: .3s;
		}
		&.none{
			display: none;
		}
		&.show{
			background: rgba(0,0,0,.4);
			
			.cate-content{
				transform: translateX(0);
			}
		}
	}
	.cate-list{
		display: flex;
		flex-direction: column;
		height: 100%;
		.cate-item{
			display: flex;
			align-items: center;
			height: 90upx;
			padding-left: 30upx;
 			font-size: 28upx;
			color: #555;
			position: relative;
		}
		.two{
			height: 64upx;
			color: #303133;
			font-size: 30upx;
			background: #f8f8f8;
		}
		.active{
			color: $base-color;
		}
	}

	/* 商品列表 */
	.goods-list{
		display:flex;
		flex-wrap:wrap;
		padding: 0 30upx;
		background: #fff;
		.goods-item{
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
		.price-box{
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding-right: 10upx;
			font-size: 24upx;
			color: $font-color-light;
		}
		.price{
			font-size: $font-lg;
			color: $uni-color-primary;
			line-height: 1;
			&:before{
				content: '￥';
				font-size: 26upx;
			}
		}
	}
	

</style>
