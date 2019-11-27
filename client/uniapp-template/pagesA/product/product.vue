<template>
	<view class="container">
		<view class="carousel">
			<swiper indicator-dots circular=true duration="400">
				<swiper-item class="swiper-item" v-for="(item,index) in imgList" :key="index">
					<view class="image-wrapper">
						<image
							:src="qiniu+item" 
							class="loaded" 
							mode="aspectFill"
						></image>
					</view>
				</swiper-item>
			</swiper>
		</view>
		
		<view class="introduce-section">
			<text class="title">{{goodsInfo.name}}</text>
			<view class="price-box">
				<text class="price-tip">¥</text>
				<text class="price" v-if="goodsInfo.goods_group">{{goodsInfo.goods_group.price/100}}</text>
				
				<text class="price" v-else-if="goodsInfo.price_min==goodsInfo.price_max">{{goodsInfo.price_min/100}}</text>
				<text class="price" v-else>{{goodsInfo.price_min/100}}~{{goodsInfo.price_max/100}}</text>
				<text class="m-price" v-if="goodsInfo.market_price_min==goodsInfo.market_price_max">¥{{goodsInfo.market_price_min/100}}</text>
				<text class="m-price" v-else>¥{{goodsInfo.market_price_min/100}}~{{goodsInfo.market_price_max/100}}</text>
				<!-- <text class="coupon-tip">7折</text> -->
			</view>
			<!-- <view class="bot-row">
				<text>销量: 108</text>
				<text>库存: 4690</text>
				<text>浏览量: 768</text>
			</view> -->
		</view>
		
		<!--  分享 -->
		<!-- <view class="share-section" @click="share">
			<view class="share-icon">
				<text class="yticon icon-xingxing"></text>
				 返
			</view>
			<text class="tit">该商品分享可领49减10红包</text>
			<text class="yticon icon-bangzhu1"></text>
			<view class="share-btn">
				立即分享
				<text class="yticon icon-you"></text>
			</view>
			
		</view> -->
		
		<view class="c-list">
			<view class="c-row b-b" @click="toggleSpec('show')">
				<text class="tit">购买类型</text>
				<view class="con">
					<text class="selected-text" v-for="(sItem, sIndex) in specSelected" :key="sIndex">
						{{sItem.name}}
					</text>
				</view>
				<text class="yticon icon-you"></text>
			</view>
			<!-- <view class="c-row b-b">
				<text class="tit">优惠券</text>
				<text class="con t-r red">领取优惠券</text>
				<text class="yticon icon-you"></text>
			</view> -->
			<!-- <view class="c-row b-b">
				<text class="tit">促销活动</text>
				<view class="con-list">
					<text>新人首单送20元无门槛代金券</text>
					<text>订单满50减10</text>
					<text>订单满100减30</text>
					<text>单笔购买满两件免邮费</text>
				</view>
			</view>
			<view class="c-row b-b">
				<text class="tit">服务</text>
				<view class="bz-list con">
					<text>7天无理由退换货 ·</text>
					<text>假一赔十 ·</text>
				</view>
			</view> -->
		</view>
		
		<!-- 评价 -->
		<view class="eva-section">
			<view class="e-header" @click="toCommentList()">
				<text class="tit">评价</text>
				<text>({{reviewNum}})</text>
				<text class="tip"></text>
				<!-- <text class="tip">好评率 100%</text> -->
				<text class="yticon icon-you"></text>
			</view> 
			<view class="eva-box" v-if="reviewList.length>0" v-for="(item,index) in reviewList" :key="index">
				<image class="portrait" :src="qiniu+item.logo" mode="aspectFill"></image>
				<view class="right">
					<text class="name">{{item.nick}}</text>
					<text class="con">{{item.value}}</text>
					<view class="bot">
						<!-- <text class="attr">购买类型：XL 红色</text> -->
						<text></text>
						<text class="time">{{item.time}}</text>
					</view>
				</view>
			</view>
		</view>
		
		<view class="detail-desc">
			<view class="d-header">
				<text>图文详情</text>
			</view>
			<rich-text :nodes="goodsInfo.details"></rich-text>
			<view  v-if="imgNoList"  v-for="(item,index) in imgNoList" class="imgNoLists" :key='index' :style="{'height':item.autoHeight+'px','width':getSystemInfo.windowWidth+'px'}">
				<image :src="qiniu+item.image_id" :style="{'height':item.autoHeight+'px','width':getSystemInfo.windowWidth+'px'}" mode="aspectFit" class="imgNo_img"></image>
				
			</view>
			
		</view>
		
		<!-- 底部操作菜单 -->
		<view class="page-bottom">
			<navigator url="/pages/index/index" open-type="switchTab" class="p-b-btn">
				<text class="yticon icon-xiatubiao--copy"></text>
				<text>首页</text>
			</navigator>
			<navigator url="/pages/cart/cart" open-type="switchTab" class="p-b-btn">
				<text class="yticon icon-gouwuche"></text>
				<text>购物车</text>
			</navigator>
			<view class="p-b-btn" :class="{active: favorite}" @click="toFavorite">
				<text class="yticon icon-shoucang"></text>
				<text>收藏</text>
			</view>
			
			<view class="action-btn-group">
				<button type="primary" class=" action-btn no-border buy-now-btn" @click="toggleSpec('addnow')">立即购买</button>
				<button type="primary" class=" action-btn no-border add-cart-btn" @click="toggleSpec('add')">加入购物车</button>
			</view>
		</view>
		
		
		<!-- 规格-模态层弹窗 -->
		<view 
			class="popup spec" 
			:class="specClass"
			@touchmove.stop.prevent="stopPrevent"
			@click="toggleSpec('show')"
		>
			<!-- 遮罩层 -->
			<view class="mask"></view>
			<view class="layer attr-content" @click.stop="stopPrevent">
				<view class="a-t">
					<image :src="qiniu+imgList[0]"></image>
					<view class="right">
						<text class="price" v-if="selectGoodsSku">¥{{selectGoodsSku.price/100}}</text>
						<text class="price" v-else>未选择规格或者该规格不存在</text>
						<text class="stock" v-if="selectGoodsSku">库存：{{selectGoodsSku.stock}}件</text>
						<text class="stock" v-else>库存：未知</text>
						<view class="selected">
							已选：
							<text v-if="selectGoodsSku" class="selected-text" v-for="(sItem, sIndex) in specSelected" :key="sIndex">
								{{sItem.name}}
							</text>
						</view>
					</view>
				</view>
				<view v-for="(item,index) in goods_spu" :key="index" class="attr-list">
					<text>{{item.name}}</text>
					<view class="item-list">
						<text 
							v-for="(childItem, childIndex) in item.son" 
							
							:key="childIndex" class="tit"
							:class="{selected: childItem.class_change}"
							@click="selectSpec(childIndex, index)"
						>
							{{childItem.name}}
						</text>
					</view>
				</view>
				<button class="btn" @click="addCar">完成</button>
			</view>
		</view>
		<!-- 分享 -->
		<share 
			ref="share" 
			:contentHeight="580"
			:shareList="shareList"
		></share>
	</view>
</template>

<script>
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	var ef = new eonfox();
	import share from '@/components/share';
	export default{
		components: {
			share
		},
		data() {
			return {
				goodsID:'',//商品id
				qiniu:'',//七牛云域名
				goodsInfo:'',//商品信息
				reviewList:[],//评价
				getSystemInfo:'',//屏幕宽度
				imgNoList:[],//图片描述
				specClass: 'none',
				specSelected:[],//已选规格
				
				favorite: true,//收藏
				shareList: [],
				imgList: [],//轮播图片
				goods_spu:[],//规格
				goods_sku:[],//商品
				selectGoodsSku:'',//选中规格的信息
				tanchuceng:'',
				reviewNum:0//评价数量
			};
		},
		async onLoad(options){
			var _this=this;
			if(options.id){
				_this.goodsID=options.id;
			}
			else{
				uni.showToast({
					title:"商品id异常，请刷新重试",
					icon:none,
					success() {
						setTimeout(function(){uni.navigateBack({})},1500);
					}
				})
				
			}
			uni.getSystemInfo({
				success: function(res) {
					_this.getSystemInfo = res;
				}
			});
			console.log('id',options.id)
			uni.showLoading({
				title: '加载中...',
				mask: true
			});
			ef.submit({
				request: {
					config: ['APPLICATIONCONFIG'],
					goods: ['SHOPGOODSGET',[{id:_this.goodsID}]],
					imgInfos: ['SHOPGOODSIMAGE',[{id:_this.goodsID, "sort":{"orderby":"sort_asc"}}]],
					collect:['USERCOLLECTIONSELFGET',[{module:'shop_goods',key:_this.goodsID}]],
					address:['USERADDRESSSELFGET'],
					imgNo:['SHOPGOODSIMAGE',[{id:_this.goodsID, "sort":{"orderby":"sort_asc"}}]],
					review:['USERCOMMENTLIST',[{module:"shop_goods",key:_this.goodsID,size:1}]], //that.id  key:'1'
					express:['APPLICATIONSHIPPINGSELFOPTIONS',[{module:'shop_order'}]] ,//快递信息
					discounts: ['USERCOUPONSELFLIST', [{ state: 1 }]],  //优惠卷信息
					reviewNum:['USERCOMMENTGETNUM',[{module:"shop_goods",id:_this.goodsID}]]
				},
				callback: function(data){
					var dataList=fns.checkError(data,'goods',function(errno,error){
						uni.showToast({
							title:error,
							icon:'none'
						})
					})
					console.log('打印所有参数',data);
					
					//评论列表
					_this.reviewList=data.data.review.data.data;
					_this.reviewNum=data.data.reviewNum.data.count;
					//获取七牛的域名
					if(dataList.config && dataList.config.qiniu_domain){
						_this.qiniu=dataList.config.qiniu_domain;
					}
					
					
					// if(dataList.address && dataList.address!=''){
					// 		var address=dataList.address
					// 		that.userAddress=address.province+'-'+address.city+'-'+address.district;
					// 			that.toFrom=address.province+address.city+address.district+address.details;
					// 			that.address_id=address.id
					// 			console.log('address',address);
					// }
					
					//快递类型
					// var arr =[]
					// if(dataList.express){
					// 	arr = dataList.express
					// 	console.log("快递",arr)
					// }
					// 
					// arr.forEach(item=>{
					// 	that.expressType.push(item.name)
					// 	that.expressId.push(item.id)
					// })
					
					//商品信息
					if (dataList.goods) {
						var goods=dataList.goods
						// that.allInfo = goods;
					    console.log('goods',goods);
					   // _this.details=goods.details//商品介绍
					   // _this.goodsId = goods.id; //商品id
					   // _this.goodsDetails=goods
					   _this.goodsInfo=goods;
					   _this.imgList=goods.goods_image;//图片
					   var spu=goods.goods_spu;
					   var sku=goods.goods_sku;
					   console.log('spu',spu);
					   //循环规格，加上是否选中字段，如果只有一个规格默认选中
					   if(spu.length>0){
						   for(var i=0; i<spu.length;i++){
							   for(var m=0; m<spu[i].son.length;m++){
								   for (var k=0; k<sku.length;k++) {
										if(sku[k].spu_id.indexOf(spu[i].son[m].id) != -1 ){
											spu[i].son[m].class_change=false;
										}
								   }
							   }
	   // 						   i.class_change='selecteds';
	   // 						   item.push(i)
						   }
							console.log('s',goods.goods_sku);
							_this.goods_spu=goods.goods_spu;//商品尺寸颜色优惠等信息
							_this.goods_sku=goods.goods_sku//商品的库存
					   }
					   //如果商品规格只有一个
					   if(sku.length==1&&spu.length==1){
						    for(var i=0; i<_this.goods_spu.length;i++){
							   for(var m=0; m<_this.goods_spu[i].son.length;m++){
								   for (var k=0; k<sku.length;k++) {
										if(sku[k].spu_id.indexOf(_this.goods_spu[i].son[m].id) != -1 ){
											_this.goods_spu[i].son[m].class_change=true;
											_this.specSelected.push({name:_this.goods_spu[i].son[m].name,ParentKey:0})
										}
								   }
							   }
   // 						   i.class_change='selecteds';
   // 						   item.push(i)
						   }
						   _this.selectGoodsSku=sku[0];
						   
						   console.log('_this.selectGoodsSku',_this.selectGoodsSku)
					   }
					  
					}
					//图片详情
					if(dataList.imgNo){
						//等比例算出高度
						for(var img in dataList.imgNo){
							dataList.imgNo[img].autoHeight = 
							((_this.getSystemInfo.windowWidth/dataList.imgNo[img].width) * dataList.imgNo[img].height).toFixed(0);
						}
						_this.imgNoList = dataList.imgNo;
					}
					   
					//	  that.rewardMoney = goods.reward_money;//单品获取奖励
					//    _this.goodsWhen = goods.goods_when; // 是否是限时商品
					//    
					//    _this.property = goods.property;
					//    // console.log(that.goodsInfos.details);
					//    _this.goodsInfos.description=goods.description;//商品介绍
					//    
					//    that.shop_id=goods.shop_id//商家id
					//    
					   
					//    if(goods.goods_sku&&goods.goods_sku[0]&&goods.goods_sku[0].price){
					//    						   that.money_=goods.goods_sku[0].price
					//    }
					
					//    // goods.goods_sku.son=item
					//   
					//   // console.log("spu/sku/in",that.goods_spu,that.goods_sku);
					// }
					// console.log('imginfo',dataList.imgInfos);
					// if(dataList.imgInfos.length){
					// 	that.imgInfos=dataList.imgInfos
					// }
					console.log('获取收藏',dataList.collect);
					if(dataList.collect){
						_this.favorite=true
					}else{
						_this.favorite=false
					}
					uni.hideLoading();
			     },
			     error: function(err){
					 fns.err('err',err)
			             console.log("出错啦", err);
			    },
			});
			
		},
		
		
		methods:{
			toCommentList(){
				var _this=this;
				if(_this.goodsID){
					uni.navigateTo({
						url: '/pagesA/commentList/commentList?type=shop_goods&id='+_this.goodsID
					});
				}
				else{
					uni.showToast({
						title:"商品id异常",
						icon:'none'
					})
				}
			},
			//规格弹窗开关
			toggleSpec(str) {
				console.log('222222222',str)
				this.tanchuceng=str;
				if(this.specClass === 'show'){
					this.specClass = 'hide';
					setTimeout(() => {
						this.specClass = 'none';
					}, 250);
				}else if(this.specClass === 'none'){
					this.specClass = 'show';
				}
			},
			//选择规格
			selectSpec(childIndex, i){
				var _this=this;
				let list = this.goods_spu;
				//无选中规格时直接加入
				if(_this.specSelected.length==0){
					_this.goods_spu[i].son[childIndex].class_change=true;
					_this.specSelected.push({name:list[i].son[childIndex].name,ParentKey:i})
				}
				else{
					//当前规格已经选中,则移除
					if(list[i].son[childIndex].class_change){
						list[i].son[childIndex].class_change=false;
						_this.specSelected.forEach(function(item,index){
							if(item.name==list[i].son[childIndex].name){
								_this.specSelected.splice(index,1)
							}
						})
					}
					else{
						var ParentExist=false;
						_this.specSelected.forEach(function(item,index){
							if(item.ParentKey==i){//说明当前父级分类已经存在，更换选中name
							console.log('测试测试',_this.goods_spu[i].son.length);
								for (var counti=0;counti<_this.goods_spu[i].son.length;counti++) {//清空当前父级分类下已选中内容
									console.log('测试测试111',counti);
									_this.goods_spu[i].son[counti].class_change=false;
								}
								_this.goods_spu[i].son[childIndex].class_change=true
								_this.specSelected[index].name=list[i].son[childIndex].name
								ParentExist=true;
								//item.name=skuObj.name;
							}
						})
						if(!ParentExist){
							_this.specSelected.push({name:list[i].son[childIndex].name,ParentKey:i})
							_this.goods_spu[i].son[childIndex].class_change=true;
						}
					}
				}
				_this.selectGoodsSku="";
				//遍历规格商品
				_this.goods_sku.forEach(function(item,index){
					let condition=false;
					let condition1=false;
					var spuarr=item.spu_id.split(',');
					spuarr.pop();spuarr.shift();//去首尾空值
					console.log('spuarr',spuarr);
					if(_this.specSelected.length==spuarr.length){
						//循环spuid值
						for(let spuid in spuarr){
							//循环规格
							for (var spu = 0; spu < _this.goods_spu.length; spu++) {
								for (var num = 0; num < _this.goods_spu[spu].son.length; num++) {
									console.log('213',num,_this.goods_spu[spu].son[num].class_change);
									if(_this.goods_spu[spu].son[num].class_change&&spuarr[spuid]==_this.goods_spu[spu].son[num].id){
										console.log('num',_this.goods_spu[spu].son[num].id,spuarr[spuid],);
										condition=true;
										break;
									}
									// else if(_this.goods_spu[spu].son[num].class_change){
									// 	console.log('mun')
									// 	condition=false;
									// 	break;
									// }
								}
								
								if(condition){
									break;
								}
							}
							if(!condition){
								condition1=false;
								break;
							}else{
								condition1=true;
								condition=false;
							}
							console.log('tren',spuid,condition1)
						}
						if(condition1){
							_this.selectGoodsSku=item;
							throw new Error("跳出foreach")
						} 
					}
					
					
				})
				console.log('_this.selectGoodsSku',_this.selectGoodsSku);
			},
			//判断用户是否登录方法
			jugLogin(callbackRun){
				var _this = this;
					ef.submit({
					request:{
						jugLogin:['USERSELF']
					},
					callback(data){
						var address=fns.checkError(data,'jugLogin',function(errno,error){
							uni.showToast({
								title:'请先登录',
								icon:'none',
								success() {
									//showToast 参数duration指定提示显示时间 默认1500 所以跳页延时1500
									var ac = setTimeout(function(){
										uni.reLaunch({
													url:'../../pagesA/threelogin/threelogin'
												})
										// uni.reLaunch({
										// 			url:'../../pages/register/register'
										// 		})
									},1500);
								}
							})
						});
						//如果已经登陆，执行回调
						if(address)callbackRun();
					},
					error(err){
						fns.err('err',err,1)
					}
				})
			},
			addCar(){
				var _this=this;
				
				if(_this.tanchuceng=='show'){
					_this.toggleSpec('show');
					return
				}
				if(_this.tanchuceng=='addnow'){
					_this.buy();
					return
				}
				console.log(_this.selectGoodsSku)
				_this.jugLogin(function(){
					if(_this.selectGoodsSku){
						uni.showLoading({
							title: '正在为您加入到购物车',
							mask: true
						});
						ef.submit({
							request:{
								s:['SHOPCARTSELFADD',[{
									goods_sku_id:_this.selectGoodsSku.id,
									number:1,
									//recommend_user_id:_this.inUserId//推荐人id
									}]]
							},
							callback(data){
								if(fns.checkError(data,'s',function(errno,error){
									fns.err(error)
								})){
									
									fns.success('已加入购物车',function(){
										uni.hideLoading()
										_this.toggleSpec('show');
										setTimeout(function(){
											uni.showToast({
												title:'已经加入购物车',
												icon:'none'
											})
											// uni.removeStorage({
											// 	key:"recommendInfo",
											// 	success:function(res){
											// 		console.log('success')
											// 	}
											// })
										})
										
									})
									
								}
							}
						})
					}else{
						uni.showToast({
							title: '您未选择规格或者该商品规格不存在！',
							icon:'none',
							mask:true,
						});
					}
				});
				
				
			},
			//分享
			share(){
				this.$refs.share.toggleMask();	
			},
			//收藏
			toFavorite(){
				var _this=this;
				if(!_this.favorite){
					ef.submit({
						request: {
							collect:['USERCOLLECTIONSELFADD',[{module:'shop_goods',key:_this.goodsID}]]
						},
						callback: function(data){
							if(fns.checkError(data,'collect',function(errno,error){
								uni.showToast({
									title:error,
									icon:'none'
								})
							})){
								uni.showToast({
									title:'收藏成功',
									icon:'none',
									success() {
										_this.favorite=true
									}
								})
							}
						},
						error(err){
							fns.err('',err,1)
						}
					})
				}
				else{
					ef.submit({
						request: {
							collect:['USERCOLLECTIONSELFREMOVE',[{module:'shop_goods',key:_this.goodsID}]]
						},
						callback: function(data){
							if(fns.checkError(data,'collect',function(errno,error){
								uni.showToast({
									title:error,
									icon:'none'
								})
							})){
								uni.showToast({
									title:'取消收藏',
									icon:'none',
									success() {
										_this.favorite=false
									}
								})
							}
						},
						error(err){
							fns.err('',err,1)
						}
					})	
				}
			},
			buy(){
				var _this=this;
				_this.jugLogin(function(){
					if(_this.selectGoodsSku){
						ef.submit({
							request: {
								s: ['SHOPCARTSELFADD',[{
										goods_sku_id: _this.selectGoodsSku.id,
										number: 1,
										buy_now:1
										}]
								]
							},
							callback(data) {
								var dataList = fns.checkError(data, 's', function(errno, error) {
									uni.showToast({
										title: error,
										icon: 'none'
									});
								});
								_this.toggleSpec('addnow');
								uni.showToast({
									title:'已经加入购物车',
									icon:'none'
								})
								console.log(dataList,'加入购物车')
								var cart_values=dataList.s
								console.log(1,dataList.s);
								uni.navigateTo({
									url: '/pages/order/createOrder?cartId='+cart_values,
								});
							}
						});
					}else{
						uni.showToast({
							title: '您未选择规格或者该商品规格不存在！',
							icon:'none',
							mask:true,
						});
					}
				});
				
				
			},
			stopPrevent(){}
		},

	}
</script>

<style lang='scss'>
	.imgNo_img{
		float:left;
	}
	page{
		background: $page-color-base;
		padding-bottom: 160upx;
	}
	.icon-you{
		font-size: $font-base + 2upx;
		color: #888;
	}
	.carousel {
		height: 722upx;
		position:relative;
		swiper{
			height: 100%;
		}
		.image-wrapper{
			width: 100%;
			height: 100%;
		}
		.swiper-item {
			display: flex;
			justify-content: center;
			align-content: center;
			height: 750upx;
			overflow: hidden;
			image {
				width: 100%;
				height: 100%;
			}
		}
		
	}
	
	/* 标题简介 */
	.introduce-section{
		background: #fff;
		padding: 20upx 30upx;
		
		.title{
			font-size: 32upx;
			color: $font-color-dark;
			height: 50upx;
			line-height: 50upx;
		}
		.price-box{
			display:flex;
			align-items:baseline;
			height: 64upx;
			padding: 10upx 0;
			font-size: 26upx;
			color:$uni-color-primary;
		}
		.price{
			font-size: $font-lg + 2upx;
		}
		.m-price{
			margin:0 12upx;
			color: $font-color-light;
			text-decoration: line-through;
		}
		.coupon-tip{
			align-items: center;
			padding: 4upx 10upx;
			background: $uni-color-primary;
			font-size: $font-sm;
			color: #fff;
			border-radius: 6upx;
			line-height: 1;
			transform: translateY(-4upx); 
		}
		.bot-row{
			display:flex;
			align-items:center;
			height: 50upx;
			font-size: $font-sm;
			color: $font-color-light;
			text{
				flex: 1;
			}
		}
	}
	/* 分享 */
	.share-section{
		display:flex;
		align-items:center;
		color: $font-color-base;
		background: linear-gradient(left, #fdf5f6, #fbebf6);
		padding: 12upx 30upx;
		.share-icon{
			display:flex;
			align-items:center;
			width: 70upx;
			height: 30upx;
			line-height: 1;
			border: 1px solid $uni-color-primary;
			border-radius: 4upx;
			position:relative;
			overflow: hidden;
			font-size: 22upx;
			color: $uni-color-primary;
			&:after{
				content: '';
				width: 50upx;
				height: 50upx;
				border-radius: 50%;
				left: -20upx;
				top: -12upx;
				position:absolute;
				background: $uni-color-primary;
			}
		}
		.icon-xingxing{
			position:relative;
			z-index: 1;
			font-size: 24upx;
			margin-left: 2upx;
			margin-right: 10upx;
			color: #fff;
			line-height: 1;
		}
		.tit{
			font-size: $font-base;
			margin-left:10upx;
		}
		.icon-bangzhu1{
			padding: 10upx;
			font-size: 30upx;
			line-height: 1;
		}
		.share-btn{
			flex: 1;
			text-align:right;
			font-size: $font-sm;
			color: $uni-color-primary;
		}
		.icon-you{
			font-size: $font-sm;
			margin-left: 4upx;
			color: $uni-color-primary;
		}
	}
	
	.c-list{
		font-size: $font-sm + 2upx;
		color: $font-color-base;
		background: #fff;
		.c-row{
			display:flex;
			align-items:center;
			padding: 20upx 30upx;
			position:relative;
		}
		.tit{
			width: 140upx;
		}
		.con{
			flex: 1;
			color: $font-color-dark;
			.selected-text{
				margin-right: 10upx;
			}
		}
		.bz-list{
			height: 40upx;
			font-size: $font-sm+2upx;
			color: $font-color-dark;
			text{
				display: inline-block;
				margin-right: 30upx;
			}
		}
		.con-list{
			flex: 1;
			display:flex;
			flex-direction: column;
			color: $font-color-dark;
			line-height: 40upx;
		}
		.red{
			color: $uni-color-primary;
		}
	}
	
	/* 评价 */
	.eva-section{
		display: flex;
		flex-direction: column;
		padding: 20upx 30upx;
		background: #fff;
		margin-top: 16upx;
		.e-header{
			display: flex;
			align-items: center;
			height: 70upx;
			font-size: $font-sm + 2upx;
			color: $font-color-light;
			.tit{
				font-size: $font-base + 2upx;
				color: $font-color-dark;
				margin-right: 4upx;
			}
			.tip{
				flex: 1;
				text-align: right;
			}
			.icon-you{
				margin-left: 10upx;
			}
		}
	}
	.eva-box{
		display: flex;
		padding: 20upx 0;
		.portrait{
			flex-shrink: 0;
			width: 80upx;
			height: 80upx;
			border-radius: 100px;
		}
		.right{
			flex: 1;
			display: flex;
			flex-direction: column;
			font-size: $font-base;
			color: $font-color-base;
			padding-left: 26upx;
			.con{
				font-size: $font-base;
				color: $font-color-dark;
				padding: 20upx 0;
			}
			.bot{
				display: flex;
				justify-content: space-between;
				font-size: $font-sm;
				color:$font-color-light;
			}
		}
	}
	/*  详情 */
	.detail-desc{
		background: #fff;
		margin-top: 16upx;
		.d-header{
			display: flex;
			justify-content: center;
			align-items: center;
			height: 80upx;
			font-size: $font-base + 2upx;
			color: $font-color-dark;
			position: relative;
				
			text{
				padding: 0 20upx;
				background: #fff;
				position: relative;
				z-index: 1;
			}
			&:after{
				position: absolute;
				left: 50%;
				top: 50%;
				transform: translateX(-50%);
				width: 300upx;
				height: 0;
				content: '';
				border-bottom: 1px solid #ccc; 
			}
		}
	}
	
	/* 规格选择弹窗 */
	.attr-content{
		padding: 10upx 30upx;
		.a-t{
			display: flex;
			image{
				width: 170upx;
				height: 170upx;
				flex-shrink: 0;
				margin-top: -40upx;
				border-radius: 8upx;;
			}
			.right{
				display: flex;
				flex-direction: column;
				padding-left: 24upx;
				font-size: $font-sm + 2upx;
				color: $font-color-base;
				line-height: 42upx;
				.price{
					font-size: $font-lg;
					color: $uni-color-primary;
					margin-bottom: 10upx;
				}
				.selected-text{
					margin-right: 10upx;
				}
			}
		}
		.attr-list{
			display: flex;
			flex-direction: column;
			font-size: $font-base + 2upx;
			color: $font-color-base;
			padding-top: 30upx;
			padding-left: 10upx;
		}
		.item-list{
			padding: 20upx 0 0;
			display: flex;
			flex-wrap: wrap;
			text{
				display: flex;
				align-items: center;
				justify-content: center;
				background: #eee;
				margin-right: 20upx;
				margin-bottom: 20upx;
				border-radius: 100upx;
				min-width: 60upx;
				height: 60upx;
				padding: 0 20upx;
				font-size: $font-base;
				color: $font-color-dark;
			}
			.selected{
				background: #fbebee;
				color: $uni-color-primary;
			}
		}
	}
	
	/*  弹出层 */
	.popup {
		position: fixed;
		left: 0;
		top: 0;
		right: 0;
		bottom: 0;
		z-index: 99;
		
		&.show {
			display: block;
			.mask{
				animation: showPopup 0.2s linear both;
			}
			.layer {
				animation: showLayer 0.2s linear both;
			}
		}
		&.hide {
			.mask{
				animation: hidePopup 0.2s linear both;
			}
			.layer {
				animation: hideLayer 0.2s linear both;
			}
		}
		&.none {
			display: none;
		}
		.mask{
			position: fixed;
			top: 0;
			width: 100%;
			height: 100%;
			z-index: 1;
			background-color: rgba(0, 0, 0, 0.4);
		}
		.layer {
			position: fixed;
			z-index: 99;
			bottom: 0;
			width: 100%;
			min-height: 40vh;
			border-radius: 10upx 10upx 0 0;
			background-color: #fff;
			.btn{
				height: 66upx;
				line-height: 66upx;
				border-radius: 100upx;
				background: $uni-color-primary;
				font-size: $font-base + 2upx;
				color: #fff;
				margin: 30upx auto 20upx;
			}
		}
		@keyframes showPopup {
			0% {
				opacity: 0;
			}
			100% {
				opacity: 1;
			}
		}
		@keyframes hidePopup {
			0% {
				opacity: 1;
			}
			100% {
				opacity: 0;
			}
		}
		@keyframes showLayer {
			0% {
				transform: translateY(120%);
			}
			100% {
				transform: translateY(0%);
			}
		}
		@keyframes hideLayer {
			0% {
				transform: translateY(0);
			}
			100% {
				transform: translateY(120%);
			}
		}
	}
	
	/* 底部操作菜单 */
	.page-bottom{
		position:fixed;
		left: 30upx;
		bottom:30upx;
		z-index: 95;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 690upx;
		height: 100upx;
		background: rgba(255,255,255,.9);
		box-shadow: 0 0 20upx 0 rgba(0,0,0,.5);
		border-radius: 16upx;
		
		.p-b-btn{
			display:flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			font-size: $font-sm;
			color: $font-color-base;
			width: 96upx;
			height: 80upx;
			.yticon{
				font-size: 40upx;
				line-height: 48upx;
				color: $font-color-light;
			}
			&.active, &.active .yticon{
				color: $uni-color-primary;
			}
			.icon-fenxiang2{
				font-size: 42upx;
				transform: translateY(-2upx);
			}
			.icon-shoucang{
				font-size: 46upx;
			}
		}
		.action-btn-group{
			display: flex;
			height: 76upx;
			border-radius: 100px;
			overflow: hidden;
			box-shadow: 0 20upx 40upx -16upx #fa436a;
			box-shadow: 1px 2px 5px rgba(219, 63, 96, 0.4);
			background: linear-gradient(to right, #ffac30,#fa436a,#F56C6C);
			margin-left: 20upx;
			position:relative;
			&:after{
				content: '';
				position:absolute;
				top: 50%;
				right: 50%;
				transform: translateY(-50%);
				height: 28upx;
				width: 0;
				border-right: 1px solid rgba(255,255,255,.5);
			}
			.action-btn{
				display:flex;
				align-items: center;
				justify-content: center;
				width: 180upx;
				height: 100%;
				font-size: $font-base ;
				padding: 0;
				border-radius: 0;
				background: transparent;
			}
		}
	}
	.imgNoLists{
		width:auto;
		height:auto;
	}
</style>
