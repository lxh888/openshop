<template>
	<view class="content">
		<view class="head" >
			<view class="backstyle" >
			</view>
			<image :src="imgA" mode="widthFix"></image>
		</view>
		<!-- 商家内容标题部分 -->
		<view class="content-title">
			<view class="title-img"  @click="magnifyThree">
				<image :src="imgA"  mode=""></image>
			</view>
			<view class="title-font">
				<view class="fontBox">
					{{name}}
				</view>
				<view class="fontInfo">
					{{info}}
				</view>
			<!-- 	<view class="fontBox" >
					<uni-icon v-for="(n,ind) in star" type="star-filled"  size="15" class="star-filled" color="8e8e8e" :key="ind"></uni-icon>
				</view> -->
				<!-- <view class="fontBox">
					{{distance}}
				</view> -->
			</view>
			<view class="title-btn">
				<view v-if="favorite" @click="goCollect(2)">
					<text  class="yticon icon-shoucang2" style="color: #ffba00 ; font-size: 35px;"  ></text> 
				</view>
				<view v-else @click="goCollect(1)">
					<text  class="yticon icon-shoucang2" style="color: #ffffff; font-size: 35px;" ></text><!-- 收藏1 取消收藏2 -->
				</view>
				<view @click="showEvaluateLayer()">
					<text  class="yticon icon-pinglun-copy" style="color: #ffffff ; font-size: 35px;"  ></text>
				</view>
				<!-- <image src="http://muyingshop.eonfox.com/icon/evaluate.png" mode=""
				style="width:80upx;height:80upx" @click="showEvaluateLayer()"
				></image> -->
			</view>
		</view>
		<view class="BigBOX" v-show="dispalyImgThree" @click="clickshowThree">
			 <image :src="imgA" mode="" ></image>
		</view>
		<view class="tab" >
			<view class="tab-item" :class="selectIndex==1||selectIndex==5?'tab-item-checked':''" @click="changTab(1)">
				商品
			</view>
			<view class="tab-item" :class="selectIndex==2?'tab-item-checked':''" @click="changTab(2)">
				分类
			</view>
			<view class="tab-item" v-if="reviewNum<99" :class="selectIndex==3?'tab-item-checked':''" @click="changTab(3)">
				评价({{reviewNum}})
			</view>
			<view class="tab-item" v-else :class="selectIndex==3?'tab-item-checked':''" @click="changTab(3)">
				评价(99+)
			</view>
			<view class="tab-item" :class="selectIndex==4?'tab-item-checked':''" @click="changTab(4)">
				商家
			</view>
		</view>
		<view class="bbox">
			
		</view>
		<view v-show="selectIndex==1">
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
		</view>
		<view  v-show="selectIndex==2">
			<view class="categroy" >
				<view class="categroy-item" v-if="category" v-for="(item,index) in category" :key="index" @click="toCategoryList(item.type_id)">
					<image class="categroy-item-img" :src="qiniu+item.image_id" mode=""></image>
					<text class="categroy-item-text">{{item.name}}</text>
				</view>
				
			</view>
		</view>
		<view  v-show="selectIndex==3">
			<!-- 商家位置 -->
			<view class="evaluateTitle" >
				<view class="evaluateTitle-left">
					<view class="evaluateTitle-leftBack">
					</view>
					店铺评价
				</view>
				<view class="evaluateTitle-right" v-if="pingjia" @click="toCommentList()">
					全部评价({{reviewNum}})
					<image src="../../static/right-arrow.png" style="width:22upx;height:22upx;" mode="" @click.stop=""></image>
				</view>
				
			</view>
			<view class="evaluateContainer" v-if="pingjia && pingjia.data.length>0">
				<view class="evaluate-item" v-if="pingjia" v-for="(item,index) in pingjia.data" :key="index">
					<image class="evaluate-itemlogo" v-if="item.logo" :src="qiniu+item.logo"   mode=""></image>
					<image class="evaluate-itemlogo" v-else src="../../static/missing-face.png"   mode=""></image>
					<view class="evaluate-item-content">
						<view class="evaluate-item-content-user">
							<text class="title" v-if="item.nick">
								{{item.nick}}
							</text>
							<text v-else>未设置昵称</text>
							<text class="date">
								{{item.time}}
							</text>
						</view>
						<view class="evaluate-item-content-text">
							{{item.value}}
						</view>
					</view>
				</view>
				
			</view>
			<view class="evaluateContainer" v-else>
				暂无评价
			</view>
			
		</view>
		<view  v-show="selectIndex==4">
			<!-- 商家位置 -->
			<view class="addressBox" @click="openMap()">
				<view class="addressBox-left">
					<image src="../../static/position.png" style="width:34upx;height:34upx;" mode=""></image>
					<view class="content-content">{{BusinessAddress}}</view>
				</view>
				<view class="addressBox-right">
					<image src="../../static/wechat.png" style="width:44upx;height:44upx;" mode="" @click.stop="showContactInfo()"></image>
					<image src="../../static/iphone.png" style="width:44upx;height:44upx;" mode="" @click.stop="makePhoneCall(phone)"></image>
				</view>
				
				<!-- <view class="addressFont">
					商家电话：{{phone}}
				</view> -->
			</view>
			<view class="bbox">
				
			</view>
			<!-- 商家风采 -->
			<view class="imgBox">
				<view class="imgBoxImg" v-show="dispaly" @click="magnifyTwo">
					<image :src="imgB" mode=""></image>
				</view>
	<!-- 			<view class="imgBoxImg" v-show="dispalyx">
					<image :src="imgA" mode=""></image>
				</view> -->
				<view class="imgBoxImg" v-for="(item,index) in sjimg" :key="index">
					<image :src="address+item" mode=""  @click="magnify(index)"></image>
				</view>
			</view>
			<view class="BigBOX" v-show="dispalyImgTwo" @click="clickshowTwo">
				 <image :src="imgB" mode="" ></image>
			</view>
			<view class="BigBOX"  @click="clickshow" v-show="dispalyImg">
				 <image :src="address+sjimg[imgIndex]" mode=""  ></image>
			</view>
		</view>
		<view v-show="selectIndex==5">
			<view class="goods-list">
				<view 
					v-for="(item, index) in categoodsList" :key="index"
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
			<uni-load-more :status="loadingcategoryType"></uni-load-more>
		</view>
		<view class="mask1" v-show="contactInfoDisplay" @click="showContactInfo()">
			
		</view>
		<view class="contactInfo" v-show="contactInfoDisplay">
			<text class="contactInfo-title">识别二维码添加店家微信</text>
			<image src="../../static/VRCode.png" mode="" style="width: 318upx;height:318upx;"></image>
			<text>{{name}}</text>
			<text>微信号</text>
			<text>客服电话：0816-23132123</text>
		</view>
		<view class="mask1" v-show="evaluateLayer" @click="showEvaluateLayer()">
			
		</view>
		<view class="evaluateLayer" v-show="evaluateLayer">
			<text>请输入评价内容</text>
			<textarea v-model="evaluate" placeholder="请输入评价信息" />
			<view class="evaluateLayerBtn">
				<view class="cancel" @click="showEvaluateLayer()">
					取消
				</view>
				<view class="confirm" @click="submitEvaluate()">
					确定
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import uniIcon from "@/components/uni-icon/uni-icon.vue";
	import eonfox from "@/components/eonfox/eonfox.js"
	import fns from '@/components/eonfox/fns.js';
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	// #ifdef H5
	let jweixin = require('jweixin-module');
	// #endif
	var ef = new eonfox();
	export default {
		data(){
			return {
				id:'',
			    name:'',
				info:'',
				license_image_id:'',
				logimg:'',
				phone:'',
				star:'',
				imgA:'',
				imgB:'',
				sjimg:[],
				address:'',
				Latitude:'',
				Longitude:'',
				BusinessAddress:'',
				dispaly:true,
				dispalyx:true,
				distance:'',
				dispalyImg:false,
				index:0,
				imgIndex:'',
				dispalyImgTwo:false,
				dispalyImgThree:false,
				contactInfoDisplay:false,//是否展示商家联系方式
				selectIndex:1,//当前选中栏
				shopLatitude:'',
				shopLongitude:'',
				loadingType: 'more', //加载更多状态
				filterIndex: 0, 
				priceOrder: 0, //1 价格从低到高 2价格从高到低
				goodsList: [],
				qiniu:'',
				search:{},
				sort:[],
				page:10,
				category:null,
				loadingcategoryType: 'more', //分类加载更多状态
				categoryPage:10,//分类商品分页
				categoryType:'',//分类类型
				categoodsList:[],
				evaluateLayer:false,//评价弹窗
				evaluate:'',//评价内容
				pingjia:'',//评价列表
				reviewNum:0,//评价数量
				lon:'',
				lat:'',
				favorite: false//收藏
			}
		},
		
		methods:{
			toCommentList(){
				var _this=this;
				if(_this.id){
					uni.navigateTo({
						url: '/pagesA/commentList/commentList?type=merchant&id='+_this.id
					});
				}
				else{
					uni.showToast({
						title:"店铺id异常",
						icon:'none'
					})
				}
			},
			submitEvaluate(){
				var _this=this;
				if(_this.evaluate.trim()==''){
					uni.showToast({
						title:"请输入评价内容",
						icon:'none'
					})
					return
				}
				else{
					uni.showLoading({
						title:'加载中...',
						mask:true,
						success() {
							ef.submit({
								request:{
									evalute:['USERCOMMENTSELFADD',[{module:'merchant',key:_this.id,value:_this.evaluate}]]
								},
								callback:function(data){
									var dataList = fns.checkError(data, ['evalute'], function(errno, error) {
										
									});
									console.log('dataList',dataList.evalute);
									if(dataList.evalute){
										_this.showEvaluateLayer();
										uni.showToast({
											title: '评价成功'
										});
										setTimeout(function(){
											_this.loadSource();
										},1500)
									}
								}
							})
						}
					})
				}
			},
			showEvaluateLayer(){
				this.evaluateLayer=!this.evaluateLayer;
			},
			toCategoryList(typeid){
				this.changTab(5);
				this.categoryType=typeid;
				this.loadCateGoryData();
			},
			//详情
			navToDetailPage(item){
				// console.log(item)
				
				let id = item.id;
				uni.navigateTo({
					url: `/pages/product/product?id=${id}`
				})
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
						goods:['SHOPGOODSLIST',[{size:_this.page,search:{"type_id":_this.type_id,merchant_id:_this.id},sort:_this.sort}]],
						config:["APPLICATIONCONFIG"]
					},
					callback:function(data){
						console.log("busin",data)
						let page_size = data.data.goods.data.page_size;
						let row_count = data.data.goods.data.row_count;
						
						let qiniu_domain = data.data.config.data.qiniu_domain || '';
						_this.qiniu = qiniu_domain;
						if(data.data.goods&&data.data.goods.data.data.length){
							var list=data.data.goods.data.data							
							for(let i=0;i<list.length;i++){
								_this.goodsList=data.data.goods.data.data
							}
						}
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
			},
			//加载分类商品 ，带下拉刷新和上滑加载
			async loadCateGoryData(type='add', loading) {
				let _this = this
				//没有更多直接返回
				if(type === 'add'){
					if(this.loadingcategoryType === 'nomore'){
						return;
					}
					this.loadingcategoryType = 'loading';
				}else{
					this.loadingcategoryType = 'more'
				}
				
			
				
				_this.categoryPage = _this.categoryPage+10;
			
				ef.submit({
					request:{
						goods:['SHOPGOODSLIST',[{size:_this.page,search:{merchant_id:_this.id,"type_id":_this.categoryType}}]],
						config:["APPLICATIONCONFIG"]
					},
					callback:function(data){
						let page_size = data.data.goods.data.page_size;
						let row_count = data.data.goods.data.row_count;
						
						let qiniu_domain = data.data.config.data.qiniu_domain || '';
						_this.qiniu = qiniu_domain;
						if(data.data.goods&&data.data.goods.data.data.length){
							var list=data.data.goods.data.data							
							for(let i=0;i<list.length;i++){
								_this.categoodsList=data.data.goods.data.data
							}
						}
						//判断是否还有下一页，有是more  没有是nomore(测试数据判断大于20就没有了)
						// _this.loadingType  = goodsList.length >= 9 ? 'more' : 'nomore';
						_this.loadingcategoryType  = Number(row_count) >= page_size ? 'more' : 'nomore';
						
						if(type === 'refresh'){
							if(loading == 1){
								uni.hideLoading()
							}else{
								uni.stopPullDownRefresh();
							}
						}
					}
				})
			},
			//唤起拨号
			makePhoneCall(phone){
				uni.makePhoneCall({phoneNumber:phone});
			},
			//展示商家联系方式
			showContactInfo(){
				this.contactInfoDisplay=!this.contactInfoDisplay;
			},
			//切换Tab
			changTab(index){
				this.selectIndex=index;
			},
			//收藏
			goCollect(type){
				var _this=this
				console.log('打印',type)
				// 收藏1,取消收藏2
				if(type==1){
					ef.submit({
						request: {
							collect:['USERCOLLECTIONSELFADD',[{module:'merchant',key:_this.id}]]
						},
						callback: function(data){
							
							if(fns.checkError(data,'collect',function(errno,error){
								uni.showToast({
									title:error,
									icon:'none'
								})
							})){
								_this.loadSource()
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
				}else{
					ef.submit({
						request: {
							collect:['USERCOLLECTIONSELFREMOVE',[{id:_this.favorite ,module:'merchant',key:_this.id}]]
						},
						callback: function(data){
							
							if(fns.checkError(data,'collect',function(errno,error){
								uni.showToast({
									title:error,
									icon:'none'
								})
							})){
								_this.loadSource()
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
			openMap(){
				var _this=this
				uni.openLocation({
					latitude: parseFloat(_this.shopLatitude),
					longitude: parseFloat(_this.shopLongitude),
					name:_this.name,
					address:_this.BusinessAddress,
					success: function () {
						console.log('success');
					}
				});
				// uni.getLocation({
				// 	type: 'gcj02', //返回可以用于uni.openLocation的经纬度
				// 	success: function (res) {
				// 		const latitude = res.latitude;
				// 		const longitude = res.longitude;
				// 		
				// 	}
				// });
			},
			//点击获取图片的下标
			magnify:function(index){
				console.log('点击了',index);
				this.imgIndex=index;
				this.dispalyImg=true;
				this.dispalyImgTwo=false;
				this.dispalyImgThree=false;
			},
			//点击关闭图片
			clickshow(){
				this.dispalyImg=false;
			},
			magnifyTwo(){
				this.dispalyImgTwo=true;
				this.dispalyImg=false;
				this.dispalyImgThree=false;
			},
			magnifyThree(){
				this.dispalyImgThree=true;
				this.dispalyImgTwo=false;
				this.dispalyImg=false;
			},
			clickshowTwo(){
				this.dispalyImgTwo=false;
			},
			clickshowThree(){
				this.dispalyImgThree=false;
			},
			returnto() {
				uni.navigateBack({
					delta:1
				})
			},
			loadSource(){
				var that=this;
				uni.showLoading({
					title:'加载中...'
				})
				ef.submit({
					request:{
						 s: ['MERCHANTGET',[{merchant_id:that.id}]],//,lon:that.lon,lat:that.lat
						config: ['APPLICATIONCONFIG'],
						category:['APPLICATIONTYPEOPTION',[{'module':'merchant_goods_type','merchant_id':that.id}]],
						pingjia:['USERCOMMENTLIST',[{module:'merchant',key:that.id,size:10}]],
						reviewNum:['USERCOMMENTGETNUM',[{module:"merchant",id:that.id}]],
						col:['USERCOLLECTIONSELFISCOLLECTION',[{module:"merchant",key:that.id}]]  //查询店铺是否收藏
					},
					callback: function(data) {
						console.log('详细内容', data);
						if (fns.checkError(data, "s", function(erron, error) {
							uni.showToast({
								title: error,
								icon: 'none'
							})
						}));
						that.address=data.data.config.data.qiniu_domain;//获取七牛的域名
						that.name=data.data.s.data.name;
						that.info=data.data.s.data.info;
						that.phone=data.data.s.data.phone;
						that.star=parseInt(data.data.s.data.star);
						that.license_image_id=data.data.s.data.license_image_id;
						that.logimg=data.data.s.data.logo_image_id;
						that.sjimg=data.data.s.data.merchant_img;//商家其它图片
						console.log('sjimg',that.sjimg);
						that.distance=data.data.s.data.distance; //距离
						that.BusinessAddress=data.data.s.data.address //商家地址
						that.shopLatitude=data.data.s.data.latitude
						that.shopLongitude=data.data.s.data.longitude
						console.log('商品分类',data.data.category.data)
						if(data.data.category.data){
							that.category=data.data.category.data
						}
						console.log('img',that.logimg)
						if(data.data.s.data){
							that.imgA=that.address+that.logimg;//商家log
							that.imgB=that.address+that.license_image_id;//商家营业执照
						}
						if(!that.license_image_id){
							that.dispaly=false;
						}else{
							that.dispaly=true;
						}
						if(!that.logimg){
							that.dispalyx=false;
						}else{
							that.dispalyx=true;
						}
						if(data.data.pingjia.data){
							that.pingjia=data.data.pingjia.data
						}
						//是否收藏
						 if(data.data.col){
							 that.favorite=data.data.col.data
						 }
						console.log('favorite',that.favorite)
						that.reviewNum=data.data.reviewNum.data.count;
						uni.hideLoading();
					},
				
				
							
					error: function(err) {
						uni.showToast({
							title:'出错啦',
							icon:'none'
						})
					}
						
				});
				// // #ifdef APP-PLUS||MP-WEIXIN
				// uni.getLocation({ 
				//    type: 'wgs84',
				//    success: function (res){
				// 	   console.log('res:',res);
				// 	   that.Latitude=res.latitude;
				// 	   that.Longitude=res.longitude;
				// 	   console.log('res:', that.Latitude);
				// 	    console.log('res:', that.Longitude);
				// 		ef.submit({
				// 			request:{
				// 				s: ['MERCHANTGET',[{merchant_id:that.id,lon:res.longitude,lat:res.latitude}]],
				// 				config: ['APPLICATIONCONFIG'],
				// 				category:['APPLICATIONTYPEOPTION',[{'module':'merchant_goods_type','merchant_id':that.id}]],
				// 				pingjia:['USERCOMMENTLIST',[{module:'merchant',key:that.id,size:10}]],
				// 				reviewNum:['USERCOMMENTGETNUM',[{module:"merchant",id:that.id}]]
				// 			},
				// 			callback: function(data) {
				// 				console.log('详细内容', data);
				// 				if (fns.checkError(data, "s", function(erron, error) {
				// 					uni.showToast({
				// 						title: error,
				// 						icon: 'none'
				// 					})
				// 				}));
				// 				that.address=data.data.config.data.qiniu_domain;//获取七牛的域名
				// 				that.name=data.data.s.data.name;
				// 				that.info=data.data.s.data.info;
				// 				that.phone=data.data.s.data.phone;
				// 				// that.star=parseInt(data.data.s.data.star);
				// 				that.license_image_id=data.data.s.data.license_image_id;
				// 				that.logimg=data.data.s.data.logo_image_id;
				// 				that.sjimg=data.data.s.data.merchant_img;//商家其它图片
				// 				console.log('sjimg',that.sjimg);
				// 				that.distance=data.data.s.data.distance; //距离
				// 				that.BusinessAddress=data.data.s.data.address //商家地址
				// 				that.shopLatitude=data.data.s.data.latitude
				// 				that.shopLongitude=data.data.s.data.longitude
				// 				console.log('img',that.logimg)
				// 				if(data.data.s.data){
				// 					that.imgA=that.address+that.logimg;//商家log
				// 					that.imgB=that.address+that.license_image_id;//商家营业执照
				// 				}
				// 				console.log('商品分类',data.data.category.data)
				// 				if(data.data.category.data){
				// 					that.category=data.data.category.data
				// 				}
				// 				if(!that.license_image_id){
				// 					that.dispaly=false;
				// 				}else{
				// 					that.dispaly=true;
				// 				}
				// 				if(!that.logimg){
				// 					that.dispalyx=false;
				// 				}else{
				// 					that.dispalyx=true;
				// 				}
				// 				if(data.data.pingjia.data){
				// 					that.pingjia=data.data.pingjia.data
				// 				}
				// 				that.reviewNum=data.data.reviewNum.data.count;
				// 				uni.hideLoading();
				// 			},
						
						
									
				// 			error: function(err) {
				// 				uni.showToast({
				// 					title:'出错啦',
				// 					icon:'none'
				// 				})
				// 			}
								
				// 		});
				//     },
				// });
				// // #endif
				
				// // #ifdef H5
				
				// jweixin.ready(function(){
				// 	console.log('jweixin',jweixin)
				// 	jweixin.getLocation({
				// 	   type: 'wgs84',
				// 	   success: function (res){
				// 		   console.log('res:',res);
				// 		   that.Latitude=res.latitude;
				// 		   that.Longitude=res.longitude;
				// 		   console.log('res:', that.Latitude);
				// 			console.log('res:', that.Longitude);
				// 			ef.submit({
				// 				request:{
				// 					s: ['MERCHANTGET',[{merchant_id:that.id,lon:res.longitude,lat:res.latitude}]],
				// 					config: ['APPLICATIONCONFIG'],
				// 					category:['APPLICATIONTYPEOPTION',[{'module':'merchant_goods_type','merchant_id':that.id}]],
				// 					pingjia:['USERCOMMENTLIST',[{module:'merchant',key:that.id,size:10}]],
				// 					reviewNum:['USERCOMMENTGETNUM',[{module:"merchant",id:that.id}]]
				// 				},
				// 				callback: function(data) {
				// 					console.log('详细内容', data);
				// 					if (fns.checkError(data, "s", function(erron, error) {
				// 						uni.showToast({
				// 							title: error,
				// 							icon: 'none'
				// 						})
				// 					}));
				// 					that.address=data.data.config.data.qiniu_domain;//获取七牛的域名
				// 					that.name=data.data.s.data.name;
				// 					that.info=data.data.s.data.info;
				// 					that.phone=data.data.s.data.phone;
				// 					that.star=parseInt(data.data.s.data.star);
				// 					that.license_image_id=data.data.s.data.license_image_id;
				// 					that.logimg=data.data.s.data.logo_image_id;
				// 					that.sjimg=data.data.s.data.merchant_img;//商家其它图片
				// 					console.log('sjimg',that.sjimg);
				// 					that.distance=data.data.s.data.distance; //距离
				// 					that.BusinessAddress=data.data.s.data.address //商家地址
				// 					that.shopLatitude=data.data.s.data.latitude
				// 					that.shopLongitude=data.data.s.data.longitude
				// 					console.log('商品分类',data.data.category.data)
				// 					if(data.data.category.data){
				// 						that.category=data.data.category.data
				// 					}
				// 					console.log('img',that.logimg)
				// 					if(data.data.s.data){
				// 						that.imgA=that.address+that.logimg;//商家log
				// 						that.imgB=that.address+that.license_image_id;//商家营业执照
				// 					}
				// 					if(!that.license_image_id){
				// 						that.dispaly=false;
				// 					}else{
				// 						that.dispaly=true;
				// 					}
				// 					if(!that.logimg){
				// 						that.dispalyx=false;
				// 					}else{
				// 						that.dispalyx=true;
				// 					}
				// 					if(data.data.pingjia.data){
				// 						that.pingjia=data.data.pingjia.data
				// 					}
				// 					that.reviewNum=data.data.reviewNum.data.count;
				// 					uni.hideLoading();
				// 				},
							
							
										
				// 				error: function(err) {
				// 					uni.showToast({
				// 						title:'出错啦',
				// 						icon:'none'
				// 					})
				// 				}
									
				// 			});
				// 		},
				// 		fail(e) {
				// 			console.log('fail',e)
				// 		},
				// 	});
				// })
				// // #endif
			}
		},
		onLoad:function(e){
			console.log('传过来的值',e)
				var that=this;
				that.id=e.id;
				// that.lon=e.lon;
				// that.lat=e.lat;
				that.loadData();
				that.loadSource();
		},
		//下拉刷新
		onPullDownRefresh(){
			this.loadData('refresh');
		},
		//加载更多
		onReachBottom(){
			this.loadData();
		},
		components:{
			uniIcon,
			uniLoadMore
		},
	}
</script>

<style>
	.content{
		font-size:28upx;
		width:100%;
		height:auto;
	}
	.head{
		width: 750upx;
		height: 300upx;
		overflow: hidden;
		position: relative;
	}
	.head image{
		width: 750upx;
		height: 180upx;
	}
	.backstyle{
		position: absolute;
		width: 100%;
		height: 100%;
		background-size: cover;
		background-color: rgba(0,0,0,0.4);
		background-repeat: no-repeat;
		
	}
	.content-title{
		width: 100%;
		height:auto;
		display: flex;
		margin-top: -120upx;
		position: relative;
		background-color: rgba(0,0,0,0.5);
	}
	.content-content{
		padding-left: 16upx;
	}
	.title-img{
		float:left;
		width: 130upx;
		height:130upx;
		margin-left: 30upx;
		margin-right: 30upx;
		margin-top: 10upx;
	}
	.title-img image{
		width: 130upx;
		height:130upx;
		border-radius: 6upx;
	}
	.title-font{
		width: 440upx;
		height: 150upx;
		float: left;
		margin-top: 10upx;
		color: #FFF;
	}
	.title-btn{
		width:170upx;
		display: flex;
		justify-content: center;
		align-items: center;
		margin-right: 15upx;
	}
	.title-btn view{
		width: 70upx;
		height: 70upx;
		margin-left: 15upx;
	}
	.fontBox{
		width: 100%;
		height: 50upx;
		line-height: 50upx;
		font-size: 36upx;
	}
	.fontInfo{
		font-size: 24upx;
	}
	.bbox{
		width: 100%;
		height:20upx;
		background-color: #e4e2e2;
	}
	.addressBox{
		width: 690upx;
		height:auto;
		padding-top: 20upx;
		padding-bottom: 20upx;
		padding-left:30upx;
		display: flex;
		justify-content: space-between;
	}
	.addressBox-left{
		display: flex;
	}
	.addressBox-right{
		display: flex;
		width:148upx;
		justify-content: space-between;
	}
	.imgBox{
		width: 750upx;
		padding:0 30upx;
		height: auto;
		display: flex;
		flex-wrap: wrap;
	}
	.imgBoxImg{
		margin-top:40upx;
		margin-left: 5upx;
		margin-right: 5upx;
		width: 220upx;
		height:220upx;
	}
	.imgBoxImg image{
		width: 220upx;
		height:220upx;
		border-radius: 10upx;
	}
	.BigBOX{
		position:fixed;
		float: left;
		top:0upx;
		width:100%;
		height:1600upx;
		background-color: #101010;
		z-index:5;
	}
	.BigBOX image{
		float: left;
		width: 100%;
		height: 600upx;
		margin-top: 200upx;
	}
	.tab{
		height:90upx;
		display: flex;
		justify-content: space-between;
	}
	.tab-item{
		width: 25%;
		height:90upx;
		display: flex;
		justify-content: center;
		align-items: center;
	}
	.tab-item-checked{
		border-bottom:solid 4upx #F8C6B5 ;
	}
	.mask1 {  
		position: fixed;  
		top:0;  
		left:0;  
		z-index:4;  
		width:100%;  
		height:100vh;  
		background:rgba(0,0,0,0.4);  
	}  
	.contactInfo{
		z-index:5;  
		position: fixed;
		width:500upx;
		height:570upx;
		background-color: #fff;
		border-radius:10upx ;
		top:25vh;
		left: 125upx;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: space-around;
		font-size: 28upx;
	}
	
	.contactInfo-title{
		
		font-size: 32upx;
		font-weight: 500;
	}
	.evaluateTitle{
		display: flex;
		width: 730upx;
		height: 80upx;
		align-items: center;
		justify-content: space-between;
	}
	.evaluateTitle-left{
		font-size: 32upx;
		align-items: center;
		display: flex;
	}
	.evaluateTitle-leftBack{
		width:20upx;
		height: 34upx;
		background-color:#F8C6B5 ;
		margin-right: 20upx;
	}
	.evaluateTitle-right{
		display: flex;
		font-size: 24upx;
		align-items: center;
	}
	.evaluateContainer{
		width: 710upx;
		padding-left: 40upx;
	}
	.evaluate-item{
		border-bottom: solid 2upx #BFBFBF;
		display: flex;
		padding: 20upx 0;
	}
	.evaluate-itemlogo{
		width: 80upx;
		height: 80upx;
		border-radius: 40upx;
	}
	.evaluate-item-content{
		width:590upx;
		padding-left: 20upx;
		font-size: 28upx;
	}
	.evaluate-item-content-user{
		display: flex;
		flex-direction: column;
		height: 80upx;
		justify-content: space-between;
	}
	.evaluate-item-content-user .title{
		font-size: 28upx;
	}
	.evaluate-item-content-user .date{
		font-size: 24upx;
	}
	.evaluate-item-content-text{
		padding-top: 20upx;
	}
	
	.evaluateLayer{
		z-index:5;  
		position: fixed;
		width:600upx;
		height:570upx;
		background-color: #fff;
		border-radius:10upx ;
		top:25vh;
		left: 75upx;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: space-around;
		font-size: 28upx;
	}
	.evaluateLayer text{
		margin-top: 20upx;
	}
	.evaluateLayer textarea{
		width: 560upx;
		padding: 20upx;
		border: solid 1upx #111A34;
		border-radius: 10upx;
	}
	.evaluateLayerBtn{
		width: 560upx;
		display: flex;
		justify-content:space-around;
	}
	.evaluateLayerBtn .confirm{
		width: 200upx;
		height: 80upx;
		background-color: #5193ff;
		color: #FFF;
		display: flex;
		justify-content: center;
		align-items: center;
		border-radius: 10upx;
	}
	.evaluateLayerBtn .cancel{
		width: 200upx;
		height: 80upx;
		background-color: #cccccc;
		color: #FFF;
		display: flex;
		justify-content: center;
		align-items: center;
		border-radius: 10upx;
	}
	.categroy{
		width:750upx;
		padding: 20upx 20upx;
		display:flex;
		flex-wrap: wrap;
	}
	.categroy-item{
		width: 174.5upx;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		margin-top: 50upx;
	}
	.categroy-item-img{
		width: 80upx;
		height:80upx;
	}
	.categroy-item-text{
		margin-top: 20upx;
	}
	
	
</style>
<style lang="scss">
	/* 商品列表 */
	.goods-list{
		display:flex;
		flex-wrap:wrap;
		padding: 30upx 30upx 0;
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
