<template>
	<view class="container">
		<!-- 空白页 -->
		<view v-if="!hasLogin || empty===true" class="empty">
			<image src="/static/emptyCart.jpg" mode="aspectFit"></image>
			<view v-if="hasLogin" class="empty-tips">
				空空如也
				<navigator class="navigator" v-if="hasLogin" url="../index/index" open-type="switchTab">随便逛逛></navigator>
			</view>
			<view v-else class="empty-tips">
				空空如也
				<view class="navigator" @click="navToLogin">去登陆></view>
			</view>
		</view>
		<view v-else>
			<!-- 列表 -->
			<view class="cart-list">
				<view v-for="(a,b) in cartList" :key="b">
					<block v-if="cartList" v-for="(item, index) in a.goods" :key="item.id">
						<view
							class="cart-item" 
							:class="{'b-b': index!==a.goods.length-1}"
						>
							<view class="image-wrapper">
								<image :src="qiniu+item.image_id" 
									class="loaded"
									mode="aspectFill" 
								></image>
								<view 
									class="yticon icon-xuanzhong2 checkbox"
									:class="{checked: hook>-1&&item.checked}"
									@click="check('item', b,index)"
								></view>
							</view>
							<view class="item-right">
								<text class="clamp title">{{item.name}}</text>
								<text class="clamp attr">{{item.spu_string}}</text>
								<text class="price">¥{{item.price/100}}</text>
								<uni-number-box 
									class="step"
									:min="1" 
									:max="item.stock"
									:value="item.number>item.stock?item.stock:item.number"
									:isMax="item.number>=item.stock?true:false"
									:isMin="item.number===1"
									:index="index"
									@eventChange="numberChange"
								></uni-number-box>
							</view>
							<text class="del-btn yticon icon-fork" @click="deleteCartItem(item.cart_id)"></text>
						</view>
					</block>
				</view>
				
			</view>
			<!-- 底部菜单栏 -->
			<view class="action-section">
				<view class="checkbox">
					<image 
						:src="allChecked?'/static/selected.png':'/static/select.png'" 
						mode="aspectFit"
						@click="check('all')"
					></image>
					<view class="clear-btn" :class="{show: allChecked}" @click="clearCart">
						清空
					</view>
				</view>
				<view class="total-box">
					<text class="price">¥{{total}}</text>
					<!-- <text class="coupon">
						已优惠
						<text>74.35</text>
						元
					</text> -->
				</view>
				<button type="primary" class="no-border confirm-btn" @click="createOrder">去结算</button>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';
	import uniNumberBox from '@/components/uni-number-box.vue'
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	var ef = new eonfox();
	export default {
		components: {
			uniNumberBox
		},
		data() {
			return {
				qiniu:'',//七牛
				total: 0, //总价格
				allChecked: false, //全选状态  true|false
				empty: false, //空白页现实  true|false
				cartList: [],
				hasLogin:false,
				hook:0,//通过索引修改数组属性不会重新绑定数据，所以通过hook变量的修改来带动
			};
		},
		onLoad(){
			// this.loadData();
		},
		onShow(){
			this.loadData();
		},
		methods: {
			
			//请求数据
			async loadData(){
				uni.showLoading({
					title:"加载中...",
					mask:true
				})
				var _this=this;
				ef.submit({
					request: { 
						s:['USERSELF'],  //个人信息，判断是否登录
						carList:['SHOPCARTSELFDATA'],   //购物车列表 
						config: ['APPLICATIONCONFIG'], //配置信息
						address:['USERADDRESSSELFGET'], // 收货地址
						express:['APPLICATIONSHIPPINGSELFOPTIONS',[{module:'shop_order'}]] ,//快递信息
						orderMsg:['SHOPORDERGETNUMLIST'], //订单人
						},
					callback: function(data) {
						var data=fns.checkError(data,'s',function(errno,error){
							
						})
						if(data.s){
							_this.hasLogin=true;
						}
						console.log(data);
						//七牛云地址
						if(data.config && data.config.qiniu_domain){
							_this.qiniu=data.config.qiniu_domain
						}
						//购物车信息
						if(data.carList && data.carList.shop && data.carList.shop[0].goods){
							_this.cartList=data.carList.shop
							_this.empty=false;
							for (let index1 in _this.cartList) {
								_this.cartList[index1].goods.forEach(item=>{
									item.checked = true;
								})
							}
							
							_this.allChecked = true;
							_this.calcTotal();
							//_this.check('all')
						}else{
							_this.empty=true;
						}
						
						// if(data.carList.status==1){
						// 	_this.displayShow=false
						// }if(data.carList.status==0){
						// 	_this.displayShow=true
						// }
						// console.log('displayShow',_this.displayShow)
						// 
						//快递类型
						// if(data.express){
						// 	var arr = data.express
						// }
						// 
						// arr.forEach(item=>{
						// 	_this.expressType.push(item.name)
						// 	_this.expressId.push(item.id)
						// })
						// 
						// if(data.address && data.address!=''){
						// 		var address=data.address
						// 			_this.userAddress=address.province+'-'+address.city+'-'+address.district;
						// 			_this.user.province=address.province;
						// 			_this.user.city=address.city;
						// 			_this.user.district=address.district;
						// 			_this.user.address=address.address;
						// 			_this.user.consignee=address.consignee;
						// 			_this.user.phone=address.phone;
						// 			_this.address_id=address.id
						// 			console.log('address',_this.userAddress);
						// }
						// if(data.orderMsg){
						// 	_this.orderMsg = data.orderMsg
						// 	console.log('orderMsg',_this.orderMsg)
						// }
						uni.hideLoading();
						
					},
					error: function(err) {
						fns.err('err',err,1)
					}
				});
				// let list = await this.$api.json('cartList'); 
				// let cartList = list.map(item=>{
				// 	item.checked = true;
				// 	return item;
				// });
				// this.cartList = cartList;
				// this.calcTotal();  //计算总价
			},
		
			navToLogin(){
				// #ifdef H5
					uni.navigateTo({
						url: '../../pagesA/public/login'
					})
				// #endif
				// #ifndef H5
					uni.navigateTo({
						url: '../../pagesA/threelogin/threelogin'
					})
				// #endif
				
			},
			 //选中状态处理
			check(type, pIndex,index){
				console.log(1,type,index)
				var _this=this;
				//通过索引修改数组属性不会重新绑定数据，所以通过hook变量的修改来带动
				if(type === 'item'&& _this.cartList[pIndex].goods[index].checked){
					_this.cartList[pIndex].goods[index].checked =false;
					this.allChecked=false;
					this.hook=this.hook+1;
					console.log(3,_this.cartList[pIndex].goods[index].checked)
				}else if(type === 'item'){
					console.log(2)
					_this.cartList[pIndex].goods[index].checked =true;
					
					 var condition=true
					for(let good in _this.cartList[pIndex].goods){
						if(!_this.cartList[pIndex].goods[good].checked){condition=false;break;}
					}
					if(condition){
						_this.allChecked=true;
					}else{
						_this.allChecked=false;
					}
					this.hook=this.hook+1;
				}else{
					const checked = !this.allChecked
					for (let i in _this.cartList) {
						_this.cartList[i].goods.forEach(item=>{
							item.checked = checked;
						})
					}
					
					this.allChecked = checked;
				}
				this.calcTotal();
			},
			//数量
			numberChange(data){
				var _this=this;
				uni.showLoading({
					title:'加载中...',
					icon:'none'
				})
				ef.submit({
						request: { 
							res:['SHOPCARTSELFEDIT',[{id:_this.cartList[0].goods[data.index].cart_id,number:data.number}]],   //购物车列表 
							},
						callback: function(data) {
							var data=fns.checkError(data,'res',function(errno,error){
								
							})
							_this.loadData();
							uni.hideLoading();
						},
						error: function(err) {
							fns.err('err',err,1)
						}
					});
			},
			//删除
			deleteCartItem(id){
				uni.showLoading({
					title:'加载中...'
				})
				var _this = this;
				ef.submit({
					request:{
						result:['SHOPCARTSELFREMOVE',[{cart_ids:[id]}]]
					},
					callback(data){
						if(fns.checkError(data,'result',function(errno,error){	
								fns.err(error)
							}))
						{
							fns.success('清理成功',function(){
								_this.loadData();
							})
						}
						
					},
					error(err){
						fns.err('err',err,1)
					}
				});
			},
			
			//清空
			clearCart(){
				uni.showModal({
					content: '清空购物车？',
					success: (e)=>{
						var deleteArr=[];
						for (let a in this.cartList) {
							for(var index in this.cartList[a].goods){
								deleteArr.push(this.cartList[a].goods[index].cart_id);
							}
						}
						
						uni.showLoading({
							title:'加载中...'
						})
						var _this = this;
						ef.submit({
							request:{
								result:['SHOPCARTSELFREMOVE',[{cart_ids:deleteArr}]]
							},
							callback(data){
								if(fns.checkError(data,'result',function(errno,error){	
										fns.err(error)
									}))
								{
									fns.success('清理成功',function(){
										_this.loadData();
									})
								}
								
							},
							error(err){
								fns.err('err',err,1)
							}
						});
					}
				})
			},
			//计算总价
			calcTotal(){
				let list = this.cartList[0].goods;
				if(list.length === 0){
					this.empty = true;
					return;
				}
				let total = 0;
				let checked = true;
				for (let index1 in this.cartList) {
					this.cartList[index1].goods.forEach(item=>{
						if(item.checked === true){
							total += (item.price/100) * item.number;
						}else if(checked === true){
							checked = false;
						}
					})
				}
				
				this.allChecked = checked;
				this.total = Number(total.toFixed(2));
			},
			//创建订单
			createOrder(){
				var _this=this;
				var goodsData = [];
				for (let i in _this.cartList) {
					_this.cartList[i].goods.forEach(item=>{
						if(item.checked){
							goodsData.push( item.cart_id)
						}
					})
				}
				// list.forEach(item=>{
				// 	if(item.checked){
				// 		goodsData.push( item.cart_id)
				// 	}
				// })
				if(goodsData.length==0){
					uni.showToast({
						title:"未选择任何商品",
						icon:'none'
					})
					return
				}
				console.log('goodsData',goodsData)
				uni.navigateTo({
					url: `/pages/order/createOrder?cartId=`+goodsData
					// url: `/pages/order/createOrder?data=${JSON.stringify({
					// 	goodsData: goodsData
					// })}`
				})
				// this.$api.msg('跳转下一页 sendData');
			}
		}
	}
</script>

<style lang='scss'>
	.container{
		padding-bottom: 134upx;
		/* 空白页 */
		.empty{
			position:fixed;
			left: 0;
			top:0;
			width: 100%;
			height: 100vh;
			padding-bottom:100upx;
			display:flex;
			justify-content: center;
			flex-direction: column;
			align-items:center;
			background: #fff;
			image{
				width: 240upx;
				height: 160upx;
				margin-bottom:30upx;
			}
			.empty-tips{
				display:flex;
				font-size: $font-sm+2upx;
				color: $font-color-disabled;
				.navigator{
					color: $uni-color-primary;
					margin-left: 16upx;
				}
			}
		}
	}
	/* 购物车列表项 */
	.cart-item{
		display:flex;
		position:relative;
		padding:30upx 40upx;
		.image-wrapper{
			width: 230upx;
			height: 230upx;
			flex-shrink: 0;
			position:relative;
			image{
				border-radius:8upx;
			}
		}
		.checkbox{
			position:absolute;
			left:-16upx;
			top: -16upx;
			z-index: 8;
			font-size: 44upx;
			line-height: 1;
			padding: 4upx;
			color: $font-color-disabled;
			background:#fff;
			border-radius: 50px;
		}
		.item-right{
			display:flex;
			flex-direction: column;
			flex: 1;
			overflow: hidden;
			position:relative;
			padding-left: 30upx;
			.title,.price{
				font-size:$font-base + 2upx;
				color: $font-color-dark;
				height: 40upx;
				line-height: 40upx;
			}
			.attr{
				font-size: $font-sm + 2upx;
				color: $font-color-light;
				height: 50upx;
				line-height: 50upx;
			}
			.price{
				height: 50upx;
				line-height:50upx;
			}
		}
		.del-btn{
			padding:4upx 10upx;
			font-size:34upx; 
			height: 50upx;
			color: $font-color-light;
		}
	}
	/* 底部栏 */
	.action-section{
		/* #ifdef H5 */
		margin-bottom:100upx;
		/* #endif */
		position:fixed;
		left: 30upx;
		bottom:30upx;
		z-index: 95;
		display: flex;
		align-items: center;
		width: 690upx;
		height: 100upx;
		padding: 0 30upx;
		background: rgba(255,255,255,.9);
		box-shadow: 0 0 20upx 0 rgba(0,0,0,.5);
		border-radius: 16upx;
		.checkbox{
			height:52upx;
			position:relative;
			image{
				width: 52upx;
				height: 100%;
				position:relative;
				z-index: 5;
			}
		}
		.clear-btn{
			position:absolute;
			left: 26upx;
			top: 0;
			z-index: 4;
			width: 0;
			height: 52upx;
			line-height: 52upx;
			padding-left: 38upx;
			font-size: $font-base;
			color: #fff;
			background: $font-color-disabled;
			border-radius:0 50px 50px 0;
			opacity: 0;
			transition: .2s;
			&.show{
				opacity: 1;
				width: 120upx;
			}
		}
		.total-box{
			flex: 1;
			display:flex;
			flex-direction: column;
			text-align:right;
			padding-right: 40upx;
			.price{
				font-size: $font-lg;
				color: $font-color-dark;
			}
			.coupon{
				font-size: $font-sm;
				color: $font-color-light;
				text{
					color: $font-color-dark;
				}
			}
		}
		.confirm-btn{
			padding: 0 38upx;
			margin: 0;
			border-radius: 100px;
			height: 76upx;
			line-height: 76upx;
			font-size: $font-base + 2upx;
			background: $uni-color-primary;
			box-shadow: 1px 2px 5px rgba(217, 60, 93, 0.72)
		}
	}
	/* 复选框选中状态 */
	.action-section .checkbox.checked,
	.cart-item .checkbox.checked{
		color: $uni-color-primary;
	}
</style>
