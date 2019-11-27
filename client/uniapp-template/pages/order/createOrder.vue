<template>
	<view>
		<!-- 地址 -->
		<navigator url="/pages/address/address?source=1" class="address-section">
			<view class="order-content">
				<text class="yticon icon-shouhuodizhi"></text>
				<view class="cen">
					<view class="top">
						<text class="name">{{addressData.consignee}}</text>
						<text class="mobile">{{addressData.phone}}</text>
					</view>
					<text class="address">{{addressData.province}}-{{addressData.city}}-{{addressData.district}}  {{addressData.details}}</text>
				</view>
				<text class="yticon icon-you"></text>
			</view>

	</navigator>

		<view class="goods-section" v-if="goodsList" v-for="(v, index) in goodsList" :key="index">
			<view class="g-header b-b">
				<!-- <image class="logo" src="http://duoduo.qibukj.cn/./Upload/Images/20190321/201903211727515.png"></image> -->
				<text class="name" v-if="v.merchant_name">{{v.merchant_name}}</text>
				<text class="name" v-else >商家自营</text>
			</view>
			<!-- 商品列表 -->
			<!-- 人民币商品 -->
			<view class="g-item"  v-if="v.property_money.goods" v-for="(item, index1) in v.property_money.goods"
			 :key="index1">
				<image :src="qiniu + item.image_id" mode=""></image>
				<view class="right">
					<text class="title clamp">{{ item.name }}</text>
					<text class="spec">{{item.spu_string }}</text>
					<view class="price-box">
						<text class="price" v-if="item.property == 1">{{ item.price / 100 }} 积分 <text v-if="item.additional_money">+￥{{item.additional_money/100}}元</text></text>
						<text v-else>{{ item.price / 100 }} 元  <text v-if="item.additional_credit">+ {{item.additional_credit/100}}积分</text></text>
						<text class="number">x {{ item.number }}</text>
					</view>
				</view>
			</view>
			<!-- <view class="g-item"  v-if="goodsList.property_money.goods" v-for="(item, index) in goodsList.property_money.goods"
			 :key="index">
				<image :src="qiniu + item.image_id" mode=""></image>
				<view class="right">
					<text class="title clamp">{{ item.name }}</text>
					<text class="spec">{{item.spu_string }}</text>
					<view class="price-box">
						<text class="price" v-if="item.property == 1">{{ item.price / 100 }} 积分 <text v-if="item.additional_money">+￥{{item.additional_money/100}}元</text></text>
						<text v-else>{{ item.price / 100 }} 元  <text v-if="item.additional_credit">+ {{item.additional_credit/100}}积分</text></text>
						<text class="number">x {{ item.number }}</text>
					</view>
				</view>
			</view> -->
			<!-- 积分商品 -->
			<view class="g-item" v-if="v.property_credit.goods" v-for="(item2, index2) in v.property_credit.goods"
			 :key="index2">
				<image :src="qiniu + item2.image_id" mode=""></image>
				<view class="right">
					<text class="title clamp">{{ item2.name }}</text>
					<text class="spec">{{item2.spu_string }}</text>
					<view class="price-box">
						<text class="price" v-if="item2.property == 1">{{ item2.price / 100 }} 积分</text>
						<text v-else>{{ item2.price / 100 }} 元</text>
						<text class="number">x {{ item2.number }}</text>
					</view>
				</view>
			</view>
		</view>

		<!-- 优惠明细 -->
		<view class="yt-list">
			<view class="yt-list-cell b-b">
				<view class="cell-icon">
					券
				</view>
				<text class="cell-tit clamp">优惠券</text>
				
				<text v-if="couponMoney.length>0 && couponIntegral.length>0" class="cell-tip active"  @click="toggleMask('show')">
					{{couponMoneyMsg}}
				</text>
				<text v-else  class="cell-tip ">暂无优惠券可用</text>
				<text class="cell-more wanjia wanjia-gengduo-d"></text>
			</view>
			<!-- <view class="yt-list-cell b-b">
				<view class="cell-icon hb">
					减
				</view>
				<text class="cell-tit clamp">商家促销</text>
				<text class="cell-tip disabled">暂无可用优惠</text>
			</view> -->
		</view>
		<!-- 金额明细 -->
		<view class="yt-list">
			<!-- <view class="yt-list-cell b-b">
				<text class="cell-tit clamp">商品金额</text>
				<text class="cell-tip">￥179.88</text>
			</view>
			<view class="yt-list-cell b-b">
				<text class="cell-tit clamp">优惠金额</text>
				<text class="cell-tip red">-￥35</text>
			</view> -->
			<view class="yt-list-cell b-b">
				<text class="cell-tit clamp">选择快递</text>
				<picker class="cell-tip active" :range="expressType" :value="index"  @change="pickerType" >
						<text>{{expressType[index]}}</text>
				</picker>
			</view>
			<view class="yt-list-cell desc-cell">
				<text class="cell-tit clamp">备注</text>
				<input class="desc" type="text" v-model="comment" placeholder="请填写备注信息" placeholder-class="placeholder" />
			</view>
		</view>
		
		<!-- 底部 -->
		<view class="footer">
			<view class="price-content">
				<text>实付款</text>
				<text class="price-tip">￥</text>
				<text class="price">{{total_money/100}}</text>
			</view>
			<text class="submit" @click="indent()">提交订单</text>
		</view>
		
		<!-- 优惠券面板 -->
		<view class="mask" :class="maskState===0 ? 'none' : maskState===1 ? 'show' : ''" @click="toggleMask">
			<view class="mask-content" @click.stop.prevent="stopPrevent">
				<!-- 优惠券页面，仿mt -->
				<view class="coupon-item" v-if="couponMoney" v-for="(item,index) in couponMoney" :key="index" @click="getcoupon(item.coupon_id,item.discount,item.type)">
					<view class="con">
						<view class="left">
							<text class="title">{{item.name}}</text>
							<text class="time">有效期至2019-06-30</text>
						</view>
						<view class="right">
							<text class="price" v-if="item.type==1">{{item.discount}} 元</text>
							<text class="price" v-if="item.type==2">{{item.discount}}代金</text>
							<text class="price" v-if="item.type==3">{{item.discount}}抵扣</text>
							<text  style="font-size: 36upx;color: #fa436a;" v-if="item.type==4">{{item.discount}}折</text>
							<text>{{item.min}}元-{{item.max}}元使用</text>
						</view>
						
						<view class="circle l"></view>
						<view class="circle r"></view>
					</view>
					<!-- <text class="tips">限新用户使用</text> -->
				</view>
				<view class="coupon-item" v-if="couponIntegral" v-for="(item,index) in couponIntegral" :key="index" @click="getcouponjifen(item.coupon_id,item.discount,item.type)">
					<view class="con">
						<view class="left">
							<text class="title">{{item.name}}</text>
							<text class="time">有效期至2019-06-30</text>
						</view>
						<view class="right">
							<text class="price" v-if="item.type==1">{{item.discount}} 积分优惠券</text>
							<text class="price" v-if="item.type==2">{{item.discount}}积分优惠券</text>
							<text class="price" v-if="item.type==3">{{item.discount}}积分优惠券</text>
							<text  style="font-size: 36upx;color: #fa436a;" v-if="item.type==4">{{item.discount}}折</text>
							<text>{{item.min}}元-{{item.max}}积分使用</text>
						</view>
						
						<view class="circle l"></view>
						<view class="circle r"></view>
					</view>
					<!-- <text class="tips">限新用户使用</text> -->
				</view>
			</view>
		</view>

	</view>
</template>

<script>
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	import bestPaymentPassword from '@/components/best-payment-password/best-payment-password.vue'
	var ef = new eonfox();
	export default {
		data() {
			return {
				maskState: 0, //优惠券面板显示状态
				comment: '', //备注
				payType: 1, //1微信 2支付宝
				addressData:{}, //地址
				addressId:'',
				qiniu:'',//七牛云
				expressType: [], //快递类型
				expressIId: [], //快递id集合
				expressId: '', //快递id
				index:0,
				cart_values:[],
				goodsList:[], //商品
				total_credit:'', //积分
				total_money:'',  //金额
				couponMoney:[],  //关于人民币的优惠券
				couponIntegral:[], //关于积分的优惠券
				couponMoneyID:'',
				couponIntegralID:'',
				couponMoneyMsg:'选择优惠券',
				couponIntegralMsg:'',//选中积分优惠券信息
				merchant_id:'' //店铺id
			}
		},
		components: {
			bestPaymentPassword
		},
		onLoad(open){
			console.log('数据',open)
			var _this=this
			_this.addressId = open.addressId
			
			if(open.cartId){
				var cartval = open.cartId.split(',');
				uni.setStorage({
				    key: 'cartval_key',
				    data: cartval,
				    success: function () {
				        console.log('success');
				    }
				});
				
			}
			
		},
		onShow() {
			var _this=this
			uni.getStorage({
			    key: 'cartval_key',
			    success: function (res) {
					_this.cart_values = res.data;
			        console.log(res.data);
					_this.load()
			    }
			});
			
		},
		methods: {
			load(){
				var that=this
				ef.submit({
					request:{
						address: ['USERADDRESSSELFGET', [{id:that.addressId}]], //地址信息
						config: ['APPLICATIONCONFIG'], //配置信息
						express: ['APPLICATIONSHIPPINGSELFOPTIONS', [{module: 'shop_order'}]] ,//快递信息
						
					},
					callback(data) {
						var dataList = fns.checkError(data, ['address','config'], function(errno, error) {
							// fns.err(error);
							if(error=='没有数据'){
								uni.showToast({
									title:'没有地址，请选择',
									icon:'none'
								})
							}
						});
						console.log(dataList);
						//地址
						if(dataList.address){
							that.addressData=dataList.address
						}
						if(!that.addressId){
							that.addressId=that.addressData.id
						}
						//七牛云
						if (dataList.config && dataList.config.qiniu_domain) {
							that.qiniu = dataList.config.qiniu_domain;
						}
						//快递
						var arr = []
						if (dataList.express) {
							arr = dataList.express
						}
						arr.forEach(item => {
							that.expressType.push(item.name+' 邮费：'+item.price/100+'元')
							that.expressIId.push(item.id)
						})
						that.expressId = that.expressIId[0]
						
						that.commodity()
					},
					error(err) {
						fns.err('err', err, 1);
					}
				})
			},
			commodity(){
				var that=this
				ef.submit({
					request:{
						msg: ['SHOPORDERSELFCONFIRM', [{
							cart_id: that.cart_values,
							address_id: that.addressId,
							shop: [{
								shipping_id: that.expressId,
							}]
						}]], //订单信息
					},
					callback(data) {
						var datalist = fns.checkError(data, ['msg'], function(errno, error) {
							if(error=='购物车ID异常，数据为空'){
								uni.showToast({
									title:'已下单',
									icon:'none',
									success() {
										setTimeout(function(){
											uni.redirectTo({
												url:'order'
											})
										},1500)
									}
								})
							}
								fns.err(error);
							});
						//商品信息
						 console.log('商品信息',datalist)
						if (datalist.msg && datalist.msg.shop[0]) {
							that.goodsList = datalist.msg.shop;
							console.log('goodsList', that.goodsList);
						}
						//商品价格
						if (datalist.msg && datalist.msg && datalist.msg.statistic) {
							that.total_credit = datalist.msg.statistic.total_credit
							that.total_money = datalist.msg.statistic.total_money
						}
						//获取优惠券
						if (that.total_credit > 0 && that.total_money > 0) {
							that.getTicketMoney()
							that.getTicketIntegral()
						}
						if (that.total_credit > 0 && that.total_money == 0) {
							that.getTicketIntegral()
						}
						if (that.total_credit == 0 && that.total_money > 0) {
							that.getTicketMoney()
						}
						//店铺商品
						if(datalist.msg && datalist.msg.shop &&datalist.msg.shop[0].merchant_id){
							that.merchant_id=datalist.msg.shop[0].merchant_id
						}
						console.log('商家id',that.merchant_id)
					}
				})
			},
			getTicketMoney() {
				//USERCOUPONSELFAVAILABLELIST
				//"(money)[int][二选一][-][金额]": "要获取可用优惠券的订单人民币金额",
				//"(credit)[int][二选一][-][积分数量]": "要获取可用优惠券的订单积分"
				var that = this
				ef.submit({
					request: {
						ticket: ['USERCOUPONSELFAVAILABLELIST', [{
							money: that.total_money
						}]] //快递信息
					},
					callback(data) {
						var datalist = fns.checkError(data, ['ticket'], function(errno, error) {
							fns.err(error);
						})
						console.log('优惠券信息moeny', datalist)
						var arr = []
						if (datalist.ticket) {
							arr = datalist.ticket
							that.couponMoney = datalist.ticket
							console.log('优惠券',that.couponMoney)
						}
					}
				})
			},
			getTicketIntegral() {
				var that = this
				ef.submit({
					request: {
						ticket: ['USERCOUPONSELFAVAILABLELIST', [{
							credit: that.total_credit
						}]] //快递信息
					},
					callback(data) {
						var datalist = fns.checkError(data, ['ticket'], function(errno, error) {
							fns.err(error);
						})
						console.log('优惠券信息ticket', datalist)
			
						var arr = []
						if (datalist.ticket) {
							that.couponIntegral = datalist.ticket
							console.log('优惠券',that.couponIntegral)
						}
						
					}
				})
			},
			indent(){
				var that = this;
				ef.submit({
					request: {
						msg: ['SHOPORDERSELFFOUND', [{
							user_address_id: that.addressId,
							shops: [{
								comment: that.comment,
								shipping_id: that.expressId,
								money_coupon_id: that.couponMoneyID,
								credit_coupon_id: that.couponIntegralID,
								shop_id:that.merchant_id,
								shop_cart_ids: that.cart_values,
							}]
						}]] //订单信息
					},
					callback(data) {
						var datalist = fns.checkError(data, ['msg'], function(errno, error) {
							fns.err(error);
						});
						console.log('datalist',datalist)
						if (datalist.msg && datalist.msg.order_id) {
							// that.oorderId = datalist.msg.order_id;
							// that.order_credit = datalist.msg.order_credit; // 提交订单后所需积分
							// that.order_money = datalist.msg.order_money; //提交订单后所需人民币
							// that.display_pay = true;
							// that.commentShow = true
							 
							var order_id = datalist.msg.order_id
							var money=datalist.msg.order_money
							var credit=datalist.msg.order_credit
							
							//order_id 订单id  money价格   credit积分  type支付状态  moenytype人民币支付状态   jifentype积分支付状态
							uni.redirectTo({
								url:
									'../money/pay?order_id='+
									order_id +
									'&money=' +
									money +
									'&credit=' +
									credit
							})
						}
						
						
					},
					error(err) {
						fns.err('err', err, 1);
					}
				});
				
			},
			pickerType(e) {
				console.log('picker发送选择改变，携带值为', e.target.value)
				this.index = e.target.value
				this.expressId = this.expressIId[this.index]
				this.commodity()
			},
			//显示优惠券面板
			toggleMask(type){
				let timer = type === 'show' ? 10 : 300;
				let	state = type === 'show' ? 1 : 0;
				this.maskState = 2;
				setTimeout(()=>{
					this.maskState = state;
				}, timer)
			},
			numberChange(data) {
				this.number = data.number;
			},
			changePayType(type){
				this.payType = type;
			},
			submit(){
				uni.redirectTo({
					url: '/pages/money/pay'
				})
			},
			stopPrevent(){},
			getcoupon(id,discount,type){
				this.couponMoneyID=id
				if(type==1){
					this.couponMoneyMsg=discount+'元'
				}
				if(type==2){
					this.couponMoneyMsg=discount+'代金'
				}
				if(type==3){
					this.couponMoneyMsg=discount+'抵扣'
				}
				if(type==4){
					this.couponMoneyMsg=discount+'折'
				}
				this.maskState=0
			},
			getcouponjifen(id,discount,type){
				this.couponIntegralID=id
				if(type==1){
					this.couponMoneyMsg=discount+'积分优惠券'
				}
				if(type==2){
					this.couponMoneyMsg=discount+'积分优惠券'
				}
				if(type==3){
					this.couponMoneyMsg=discount+'积分优惠券'
				}
				if(type==4){
					this.couponMoneyMsg=discount+'折'
				}
				this.maskState=0
			}
		}
	}
</script>

<style lang="scss">
	page {
		background: $page-color-base;
		padding-bottom: 100upx;
	}

	.address-section {
		padding: 30upx 0;
		background: #fff;
		position: relative;

		.order-content {
			display: flex;
			align-items: center;
		}

		.icon-shouhuodizhi {
			flex-shrink: 0;
			display: flex;
			align-items: center;
			justify-content: center;
			width: 90upx;
			color: #888;
			font-size: 44upx;
		}

		.cen {
			display: flex;
			flex-direction: column;
			flex: 1;
			font-size: 28upx;
			color: $font-color-dark;
		}

		.name {
			font-size: 34upx;
			margin-right: 24upx;
		}

		.address {
			margin-top: 16upx;
			margin-right: 20upx;
			color: $font-color-light;
		}

		.icon-you {
			font-size: 32upx;
			color: $font-color-light;
			margin-right: 30upx;
		}

		.a-bg {
			position: absolute;
			left: 0;
			bottom: 0;
			display: block;
			width: 100%;
			height: 5upx;
		}
	}

	.goods-section {
		margin-top: 16upx;
		background: #fff;
		padding-bottom: 1px;

		.g-header {
			display: flex;
			align-items: center;
			height: 84upx;
			padding: 0 30upx;
			position: relative;
		}

		.logo {
			display: block;
			width: 50upx;
			height: 50upx;
			border-radius: 100px;
		}

		.name {
			font-size: 30upx;
			color: $font-color-base;
			margin-left: 24upx;
		}

		.g-item {
			display: flex;
			margin: 20upx 30upx;

			image {
				flex-shrink: 0;
				display: block;
				width: 140upx;
				height: 140upx;
				border-radius: 4upx;
			}

			.right {
				flex: 1;
				padding-left: 24upx;
				overflow: hidden;
			}

			.title {
				font-size: 30upx;
				color: $font-color-dark;
			}

			.spec {
				font-size: 26upx;
				color: $font-color-light;
			}

			.price-box {
				display: flex;
				align-items: center;
				font-size: 32upx;
				color: $font-color-dark;
				padding-top: 10upx;

				.price {
					margin-bottom: 4upx;
				}
				.number{
					font-size: 26upx;
					color: $font-color-base;
					margin-left: 20upx;
				}
			}

			.step-box {
				position: relative;
			}
		}
	}
	.yt-list {
		margin-top: 16upx;
		background: #fff;
	}

	.yt-list-cell {
		display: flex;
		align-items: center;
		padding: 10upx 30upx 10upx 40upx;
		line-height: 70upx;
		position: relative;

		&.cell-hover {
			background: #fafafa;
		}

		&.b-b:after {
			left: 30upx;
		}

		.cell-icon {
			height: 32upx;
			width: 32upx;
			font-size: 22upx;
			color: #fff;
			text-align: center;
			line-height: 32upx;
			background: #f85e52;
			border-radius: 4upx;
			margin-right: 12upx;

			&.hb {
				background: #ffaa0e;
			}

			&.lpk {
				background: #3ab54a;
			}

		}

		.cell-more {
			align-self: center;
			font-size: 24upx;
			color: $font-color-light;
			margin-left: 8upx;
			margin-right: -10upx;
		}

		.cell-tit {
			flex: 1;
			font-size: 26upx;
			color: $font-color-light;
			margin-right: 10upx;
		}

		.cell-tip {
			font-size: 26upx;
			color: $font-color-dark;

			&.disabled {
				color: $font-color-light;
			}

			&.active {
				color: $base-color;
			}
			&.red{
				color: $base-color;
			}
		}

		&.desc-cell {
			.cell-tit {
				max-width: 90upx;
			}
		}

		.desc {
			flex: 1;
			font-size: $font-base;
			color: $font-color-dark;
		}
	}
	
	/* 支付列表 */
	.pay-list{
		padding-left: 40upx;
		margin-top: 16upx;
		background: #fff;
		.pay-item{
			display: flex;
			align-items: center;
			padding-right: 20upx;
			line-height: 1;
			height: 110upx;	
			position: relative;
		}
		.icon-weixinzhifu{
			width: 80upx;
			font-size: 40upx;
			color: #6BCC03;
		}
		.icon-alipay{
			width: 80upx;
			font-size: 40upx;
			color: #06B4FD;
		}
		.icon-xuanzhong2{
			display: flex;
			align-items: center;
			justify-content: center;
			width: 60upx;
			height: 60upx;
			font-size: 40upx;
			color: $base-color;
		}
		.tit{
			font-size: 32upx;
			color: $font-color-dark;
			flex: 1;
		}
	}
	
	.footer{
		position: fixed;
		left: 0;
		bottom: 0;
		z-index: 995;
		display: flex;
		align-items: center;
		width: 100%;
		height: 90upx;
		justify-content: space-between;
		font-size: 30upx;
		background-color: #fff;
		z-index: 998;
		color: $font-color-base;
		box-shadow: 0 -1px 5px rgba(0,0,0,.1);
		.price-content{
			padding-left: 30upx;
		}
		.price-tip{
			color: $base-color;
			margin-left: 8upx;
		}
		.price{
			font-size: 36upx;
			color: $base-color;
		}
		.submit{
			display:flex;
			align-items:center;
			justify-content: center;
			width: 280upx;
			height: 100%;
			color: #fff;
			font-size: 32upx;
			background-color: $base-color;
		}
	}
	
	/* 优惠券面板 */
	.mask{
		display: flex;
		align-items: flex-end;
		position: fixed;
		left: 0;
		top: var(--window-top);
		bottom: 0;
		width: 100%;
		background: rgba(0,0,0,0);
		z-index: 9995;
		transition: .3s;
		
		.mask-content{
			width: 100%;
			min-height: 30vh;
			max-height: 70vh;
			background: #f3f3f3;
			transform: translateY(100%);
			transition: .3s;
			overflow-y:scroll;
		}
		&.none{
			display: none;
		}
		&.show{
			background: rgba(0,0,0,.4);
			
			.mask-content{
				transform: translateY(0);
			}
		}
	}

	/* 优惠券列表 */
	.coupon-item{
		display: flex;
		flex-direction: column;
		margin: 20upx 24upx;
		background: #fff;
		.con{
			display: flex;
			align-items: center;
			position: relative;
			height: 120upx;
			padding: 0 30upx;
			&:after{
				position: absolute;
				left: 0;
				bottom: 0;
				content: '';
				width: 100%;
				height: 0;
				border-bottom: 1px dashed #f3f3f3;
				transform: scaleY(50%);
			}
		}
		.left{
			display: flex;
			flex-direction: column;
			justify-content: center;
			flex: 1;
			overflow: hidden;
			height: 100upx;
		}
		.title{
			font-size: 32upx;
			color: $font-color-dark;
			margin-bottom: 10upx;
		}
		.time{
			font-size: 24upx;
			color: $font-color-light;
		}
		.right{
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			font-size: 26upx;
			color: $font-color-base;
			height: 100upx;
		}
		.price{
			font-size: 44upx;
			color: $base-color;
			&:before{
				content: '￥';
				font-size: 34upx;
			}
		}
		.tips{
			font-size: 24upx;
			color: $font-color-light;
			line-height: 60upx;
			padding-left: 30upx;
		}
		.circle{
			position: absolute;
			left: -6upx;
			bottom: -10upx;
			z-index: 10;
			width: 20upx;
			height: 20upx;
			background: #f3f3f3;
			border-radius: 100px;
			&.r{
				left: auto;
				right: -6upx;
			}
		}
	}

</style>
