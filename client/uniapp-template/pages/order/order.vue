<template>
	<view>
		 <best-payment-password :show="pay_password" digits="6" @submit="checkPwd" @cancel="togglePayment" ></best-payment-password>
		<view class="uni-tab-bar" >
			<scroll-view id="tab-bar" class="uni-swiper-tab" scroll-x :scroll-left="scrollLeft" style="position: fixed;z-index: 9;background-color: #fff;">
				<view :class="['swiper-tab-list',tabIndex==0 ? 'active1' : '']"  :data-current="0"  @click="tapTab(0)" >
					全部
				 </view>
				 <view :class="['swiper-tab-list',tabIndex==1 ? 'active1' : '']" :data-current="1"  @click="tapTab(1)" >
					待付款
				 </view>
				 <view :class="['swiper-tab-list',tabIndex==2 ? 'active1' : '']" :data-current="2"  @click="tapTab(2)" >
					待发货
				 </view>
				  <view :class="['swiper-tab-list',tabIndex==3 ? 'active1' : '']" :data-current="3"  @click="tapTab(3)" >
				 	待收货
				 </view>
				 <view :class="['swiper-tab-list',tabIndex==4 ? 'active1' : '']" :data-current="4"  @click="tapTab(4)" >
				 	评论
				 </view>
			</scroll-view>
			
			<swiper :current="0" class="swiper-box" duration="300" @change="changeTab" style="height: 100vh;padding-top: 100rpx;">
				<swiper-item   style="height: 100%;"  >
					<scroll-view
					 class="list-scroll-content" 
						scroll-y
						@scrolltolower="loadData"
					>
					<view class="contentBox" v-for="(item,index) in orderList" :key="index">
					<view class="lineback" ></view>
					  <view class="Box_top">
						  <view class="Box_top_left">
							{{item.insert_time}}
						  </view>
						  <view class="Box_top_right">
							{{item.status}}
						  </view>
					  </view>
					  <view class="Box_center" v-for="(v,index2) in item.shop_goods" :key="index2">
						<view class="Box_center_left">
							<image :src="qiniu+v.image_id" mode=""></image>
						</view>
						<view class="Box_center_center">
							<text>{{v.name}}</text><br>
							<text style="color:#888888;">{{v.spu_string}}</text>
						</view>
						<view class="Box_center_right">
							<text v-if="v.property==1">{{v.price/100}} 积分</text>
							<text v-else="">¥{{v.price/100}}</text>
							<text class="commodityNumBlock">x {{v.number}}</text>
						</view>
					  </view>
					  <view class="Box_bootom">
							<view class="Box_bootom_text">
								<text>共  <text style="color: #333333; font-size: 32upx;"> {{item.shop_goods.length}} </text> 件 </text>
								<text>实付款 : </text>
								<text class="priceRed" style="margin-right: 20upx;">{{item.credit/100}}积分</text>
								<text class="priceRed">￥{{item.money/100}}</text>
								<view>
									<text class="priceRed" v-if="item.pay_money_state==0 && item.pay_state==2">还有人民币未支付,请支付 {{item.money/100}} 元</text>
									<text class="priceRed" v-if="item.pay_credit_state==0 && item.pay_state==2">还有积分未支付,请支付 {{item.credit/100}} 积分</text>
								</view>
							</view>
							<view class="Box_bootom_btn">
								<text> 运费：{{item.shipping_price/100}} 元</text>
							</view>
					   </view>
					 <view class="Box_bootom">
						<view class="Box_bootom_text">
							
						</view>
						<view class="Box_bootom_btn">
							<text class="paybtn" v-if="item.state == 1 && item.pay_state == 0" @click="cancel_order(item.id)">取消订单</text>
							<text class="paybtn" v-if="item.state == 0" @click="Del(item.id)">删除订单</text>
							<text class="paybtn nowPay" v-if="item.pay_state !=1 && item.state==1" @click="pay(item.id,item.money,item.credit,item.pay_state,item.pay_money_state,item.pay_credit_state)">立即付款</text>
							<text class="paybtn" v-if="item.pay_state == 1 && item.shipping_state == 0 ">等待卖家发货</text>
							<text class="paybtn nowPay" v-if="item.shipping_state == 2" @click="logistics(item.id)">查看物流信息</text>
							<text class="paybtn nowPay" v-if="item.shipping_state == 2" @click="affirm(item.id)">确认收货</text>
							<text class="paybtn nowPay" v-if="item.shipping_state == 1 && item.comment_state == 0" @click="gocomment(item.id)">评论</text>
							<text class="paybtn" v-if="item.shipping_state == 1 && item.comment_state == 1 ">已评论</text>
						</view>
					  </view>
					</view>
					<!-- <view @click="loadMore()" class="load">
						{{loadingText}}
					</view> -->
					<uni-load-more :status="loadingType"></uni-load-more>
					</scroll-view>
				</swiper-item>
			</swiper>
		</view>
		<view class="mask_content" v-if="pay_display">
			<!-- 支付方式页面 -->
			<view class="pay-methods" >
				<view class="methods-box">
					<view class="title-box">
						<text>请选择支付方式</text>
						<image src="http://rs.eonfox.cc/clzy/static/closeBtn.png" mode="" @click="pay_display=!pay_display"></image>
					</view>
					<view class="pay-time-box">
						<text class="price">￥{{order_money/100}} 元</text>
					</view>
					<view class="ali-pay" :class="{active:(payMounted==1)}" @click="payMounted=1">
						<image src="http://rs.eonfox.cc/clzy/static/unknown.png" mode="" class="selectone"></image>
						<text>余额支付 (剩余:{{balance/100}})</text>
						<image v-if="payMounted==1" class="select" src="http://rs.eonfox.cc/clzy/static/choose.png" mode=""></image>
					</view>
					<!-- #ifdef APP-PLUS -->
					<view class="ali-pay" :class="{active:(payMounted==2)}" @click="payMounted=2">
						<image src="http://rs.eonfox.cc/clzy/static/ali_pay.png" mode="" class="selectone"></image>
						<text >支付宝</text>
						<image v-if="payMounted==2" class="select" src="http://rs.eonfox.cc/clzy/static/choose.png" mode=""></image>
					</view>
					<!-- #endif -->
					<view class="ali-pay"  :class=" {active:(payMounted==3)}" @click="payMounted=3">
						<image src="http://rs.eonfox.cc/clzy/static/wechat.png" mode="" class="selectone"></image>
						<text >微信</text>
						<image v-if="payMounted==3" class="select" src="http://rs.eonfox.cc/clzy/static/choose.png" mode=""></image>
					</view>
				</view>
				<view class="submit-btn" @click="pay_display=false,pay_submit()">
					<text>确认支付</text>
				</view>
			</view>
		</view>
		<view class="mask" v-show="commentShow" @click="showConditionPage()"></view>
		
		<view class="fixeBox" v-show="commentShow">
			<view class="fixeBoxBox">
				<view class="fixeBoxLeft">
					<textarea value="" placeholder="请填写评论" v-model="Comment"/>
				</view>
				<view class="fixeBoxRight" @click="issue">
					发布评论
				</view>
			</view>
		</view>
	</view>
</template>
<script>
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	import bestPaymentPassword from '@/components/best-payment-password/best-payment-password.vue'
	import uniIcon from "@/components/uni-icon/uni-icon.vue";
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	var ef = new eonfox();
	export default {
		components: {
			uniIcon,
			bestPaymentPassword,
			uniLoadMore
		},
		data() {
			return {
				qiniu:'',//七牛云地址
				scrollLeft: 0,
				isClickChange: false,
				tabIndex: 0,
				pay_state:'',//支付状态 筛选分类条件
				orderList:'',
				page:10,//页数
				maxSize:0,//最大页数
				type:'',//订单类型
				loadingText:'点击加载更多',
				
				pay_display:false,
				pay_password:false,
				payMounted:1,
				order_money:0, //订单money
				order_id:'',
				commentShow:false,
				Comment:'',
				CommentID:'',
				balance:'',//余额
				loadingType:'more'
			}
		},
		onLoad: function(e) {
			console.log(e);
			var that=this;
			that.type=e.type
			
			if(that.type==1){
				that.tapTab(1);
				that.pay_state=1;//待付款
				that.tabIndex=1
			}
			if(that.type==2){
				that.tapTab(2);
				that.pay_state=2;//待发货
				that.tabIndex=2
			}
			if(that.type==3){
				that.tapTab(3);
				that.pay_state=3;//待收货
				that.tabIndex=3
			}
			if(that.type==4){
				that.tapTab(4);
				that.pay_state=4;//评论
				that.tabIndex=4
			}
			this.load()
		},
		onPageScroll(scrollTop){
			console.log(scrollTop)
		},
		onReachBottom(){
			console.log('1111111111')
			// var _this=this;
			// console.log(_this.maxSize,_this.page);
			// if(_this.maxSize<=_this.page) {
			// 	console.log('return')
			// 	return}
			// //如果当前页数小于最大页数 才加载更多
			// if(_this.maxSize>_this.page){
			// 	console.log("重新加载数据");
				
			// 	_this.page=_this.page+5;
			// 	_this.loadingType="loading";//加载中
			// 	_this.load();
			// 	if(_this.maxSize>_this.page){
			// 		_this.loadingType="more";//加载更多
			// 	}else{
			// 		_this.loadingType="noMore-";//没有更多数据
			// 	}
			// }
		},
		methods: {
			loadData(){
				var _this=this;
				console.log(_this.maxSize,_this.page);
				if(_this.maxSize<=_this.page) {
					console.log('return')
					return}
				//如果当前页数小于最大页数 才加载更多
				if(_this.maxSize>_this.page){
					console.log("重新加载数据");
					
					_this.page=_this.page+5;
					_this.loadingType="loading";//加载中
					_this.load();
					if(_this.maxSize>_this.page){
						_this.loadingType="more";//加载更多
					}else{
						_this.loadingType="noMore-";//没有更多数据
					}
				}
			},
			loadMore(){
				var _this=this;
				console.log(_this.maxSize,_this.page);
				if(_this.maxSize<=_this.page) {
					console.log('return')
					return}
				//如果当前页数小于最大页数 才加载更多
				if(_this.maxSize>_this.page){
					console.log("重新加载数据");
					
					_this.page=_this.page+5;
					_this.loadingText="加载中";//加载中
					_this.load();
					if(_this.maxSize>_this.page){
						_this.loadingText="点击加载更多";//加载更多
					}else{
						_this.loadingText="没有更多数据";//没有更多数据
					}
				}
			},
			load(){
				var _this=this
				uni.showLoading({
					title:'正在加载',
					success() {
						ef.submit({
							request:{
								config:['APPLICATIONCONFIG'],
								list:['SHOPORDERSELFLIST',[{state:_this.pay_state,size:_this.page}]],
								Balance: ['USERMONEYSELFTOTAL']
							},
							callback(data){
								console.log('加载订单列表',data.data);
								fns.success('加载完成',function(){
									var res=fns.checkError(data,['list','Balance'],function(errno,error){
										fns.err(error)
									})
									//七牛云
									if(res.config &&res.config.qiniu_domain){
										_this.qiniu=res.config.qiniu_domain
									}
									//订单数据
									if(res.list && res.list.data){
										_this.orderList=res.list.data
										console.log('订单数据',_this.orderList);
									}
									_this.maxSize=res.list.row_count;
									if(_this.maxSize>_this.page){
										_this.loadingText="点击加载更多";//加载更多
									}else{
										_this.loadingText="没有更多数据";//没有更多数据
									}
									if(res.Balance){
										_this.balance=res.Balance
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
			togglePayment(){
				this.pay_password=false
			},
			async changeTab(e) {
				let index = e.detail.current;
				if (this.isClickChange) {
					this.tabIndex = index;
					this.isClickChange = false;
					return;
				}
				let tabBar = await this.getElSize("tab-bar"),
					tabBarScrollLeft = tabBar.scrollLeft;
				let width = 0;

				for (let i = 0; i < index; i++) {
					let result = await this.getElSize(this.tabBars[i].id);
					width += result.width;
				}
				let winWidth = uni.getSystemInfoSync().windowWidth,
					nowElement = await this.getElSize(this.tabBars[index].id),
					nowWidth = nowElement.width;
				if (width + nowWidth - tabBarScrollLeft > winWidth) {
					this.scrollLeft = width + nowWidth - winWidth;
				}
				if (width < tabBarScrollLeft) {
					this.scrollLeft = width;
				}
				this.isClickChange = false;
				this.tabIndex = index; //一旦访问data就会出问题
			},
			getElSize(id) { //得到元素的size
				return new Promise((res, rej) => {
					uni.createSelectorQuery().select('#' + id).fields({
						size: true,
						scrollOffset: true
					}, (data) => {
						res(data);
					}).exec();
				});
			},
			//点击tab-bar
			async tapTab(index) { 
				var _this=this;
				if (_this.tabIndex === index) {
					return false;
				} else {
					let tabBar = await _this.getElSize("tab-bar"),
						tabBarScrollLeft = tabBar.scrollLeft; //点击的时候记录并设置scrollLeft
					_this.scrollLeft = tabBarScrollLeft;
					_this.isClickChange = true;
					_this.tabIndex = index;
				}
				if(index==0) _this.pay_state='';//全部
				else if(index==1) _this.pay_state=1;//待付款
				else if(index==2)_this.pay_state=2;//待核销
				else if(index==3)_this.pay_state=3;//已核销
				else if(index==4)_this.pay_state=4;//评论
				_this.list={};
				_this.page=5;//初始化分页size
				_this.load();
			},
			pay(order_id, money, credit, type, moenytype, jifentype){
				console.log('点击支付按钮', order_id, money, credit, type,moenytype,jifentype);
				//order_id 订单id  money价格   credit积分  type支付状态  moenytype人民币支付状态   jifentype积分支付状态
				uni.navigateTo({
					url:
						'../money/pay?order_id='+
						order_id +
						'&money=' +
						money +
						'&credit=' +
						credit +
						'&type=' + 
						type +
						'&moenytype='+
						moenytype +
						'&jifentype='+
						jifentype
						
				})
				return
				var _this = this;
				_this.order_money=money,
				_this.order_id=order_id
				// _this.pay_display=true
				_this.order_credit=credit
				console.log('order_id',order_id)
				if(_this.jifentype == 0){
					console.log('打印',_this.pay_password)
					_this.pay_password=true
					uni.showToast({
						title:'请先支付积分',
						icon:'none'
					})
					_this.ttype=2
				}
				else{
					_this.pay_display=true
				}
				
				
			},
			
			pay_submit(){
				
				var _this=this;
						// return
						switch (_this.payMounted){
							case 1:
							if(_this.balance>0){
								_this.pay_method='user_money'
								_this.pay_password=true
								_this.ttype=1
							}else{
								uni.showToast({
									title:'余额不足',
									icon:'none'
								})
							}
							
							// _this.imprest();
								break;
							case 2:
							_this.pay_method='alipay'
							_this.pay_alipay();
							_this.pay_pass_display=false;
								break;
							case 3: //微信支付
							//#ifdef H5
							   _this.payWeChatPMJSAPI()
							//#endif
							
							// #ifdef MP-WEIXIN
							_this.pay_method='weixinpay'
							_this.pay_weixin();
							// #endif
							// #ifdef APP-PLUS
							_this.pay_App_weixin();
							// #endif
							_this.pay_pass_display=false;
								break;	
							case 4:
							_this.pay_method='user_credit'
							_this.pay_password=true
							_this.ttype=2
							// _this.imprest();
								break;
						}
			},
			
			//微信公众号支付
			payWeChatPMJSAPI(){
				var _this = this;
				ef.submit({
						request:{
							s:['SESSIONWEIXINACCESSTOKEN']
						},
						callback(data){
							var dataList=fns.checkError(data,'s',function(errno,error){
								uni.showToast({
									title: '请先确认微信授权',
									icon:'none'
								})
							})
							console.log(dataList);
							if(dataList.s){
								ef.submit({
									request: {
										s:[ 'SHOPORDERSELFPAYMENT',
										[{order_id:_this.order_id,
											pay_method: 'weixinpay',
											// pay_password:_this.password,
											weixin_login_openid: dataList.s.openid,
											weixin_trade_type: 'MPJSAPI'
										}] 
										]
									},
									
									callback(data) {
										console.log('调起微信支付', data);
										var dataList = fns.checkError(data, 's', function(errno, error) {
											uni.showToast({
												title: error,
												icon: 'none'
											});
										});
										var ress = dataList.s;
										if (ress) {
											console.log('ress', ress);
											
											var getBrandWCPayRequest = {
											        appId: ress.appid,
													timeStamp: String(ress.time_stamp), // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
													nonceStr: ress.nonce_str, // 支付签名随机串，不长于 32 位
													package: 'prepay_id='+ress.prepay_id, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
													signType: ress.sign_type, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
													paySign: ress.pay_sign, // 支付签名
											      };
											
											console.log('getBrandWCPayRequest', getBrandWCPayRequest);
											function onBridgeReady(){
											   WeixinJSBridge.invoke(
											      'getBrandWCPayRequest', getBrandWCPayRequest,
											    function(res){
													// uni.showToast({
													// 	title:JSON.stringify(res),
													// 	icon:'none',
													// 	duration:10000
													// })
											      if( res.err_msg == "get_brand_wcpay_request:ok" ){
											     
														
														uni.showToast({
															title:'支付成功',
															success() {
																setTimeout(function(){
																	uni.switchTab({
																	url: '../order/order'
																});
																},2000)
															}
														})
											      } 
												  if(res.err_msg == "get_brand_wcpay_request:fail" ){
													    uni.showToast({
													    	title:'支付失败',
															icon:'none'
													    })
												  }
												  if(res.err_msg == "get_brand_wcpay_request:cancel"){
													  uni.showToast({
													  	title:'已取消支付',
														icon:'none'
													  })
												  }
											   }); 
											}
											
											if (typeof WeixinJSBridge == "undefined"){
											   if( document.addEventListener ){
											       document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
											   }else if (document.attachEvent){
											       document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
											       document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
											   }
											}else{
											   onBridgeReady();
											}
											
										}else{
											console.log('提交订单失败', ress);
											_this.payWeChatPMJSAPI();
											
										}
										console.log('提交订单', ress);
										
									},
									error(err) {
										console.log('提交订单失败', err, 1);
									}
								});
								
								
							}else{
								
								console.log('location', location.href);
								
								//当 ACCESSTOKEN 不存在
								ef.left_token(function(left_token){
									var notify_url = encodeURIComponent(location.href);
									var url = ef.api_server_url+"?"+encodeURI('data=[["SESSIONWEIXINAUTHORIZE",[{"notify_url":"'+notify_url+'"}]]]')+"&token="+left_token;
									console.log(url);
									location.href = url;
								});
								
							}
						},
						error(err){
							fns.err('err',err,1)
						}
					})
			},
			
			pay_App_weixin(){
				var _this = this;
				var shopOrderSelfAddConfig = {
					pay_method:'weixinpay',
					order_id:_this.order_id
				};
			
						shopOrderSelfAddConfig.weixin_trade_type = "APP";
					
							// 发起网络请求
							console.log('发起网络请求');
							ef.submit({
								request: {
									s: ['SHOPORDERSELFPAYMENT',[shopOrderSelfAddConfig]]
								},
								callback(data){
									console.log('调起微信支付',data)
									/* uni.showToast({
										title:data,
										icon:'none',
										duration: 30000
									}); */
									var shopOrderSelfAddData = fns.checkError(data,'s',function(errno,error){
										uni.showToast({
											title:error,
											icon:'none',
											duration: 3000
										});
									});
									
									if( !shopOrderSelfAddData ){
										return false;
									}
									
									console.log('提交订单', shopOrderSelfAddData);
									_this.requestPayment(shopOrderSelfAddData.s);
									
								},
								error(err){
									fns.err('提交订单失败',err,1)
								}
							});
			
			},
			//微信支付  (第一步)
			pay_weixin(){
				var _this = this;
				var shopOrderSelfAddConfig = {
					pay_method:'weixinpay',
					order_id:_this.order_id
				};
				wx.login({
					//微信小程序登录获取code
					success(res) {
						console.log("wx.login"+JSON.stringify(res));
						
						// #ifdef MP-WEIXIN
						_this.code = res.code;
						shopOrderSelfAddConfig.weixin_login_code = res.code;
						shopOrderSelfAddConfig.weixin_trade_type = "JSAPI";
						// #endif
						// #ifdef APP-PLUS
						if( res.authResult && res.authResult.openid ){
							_this.openid = res.authResult.openid;
							shopOrderSelfAddConfig.weixin_login_openid = res.authResult.openid;
						}
						shopOrderSelfAddConfig.weixin_trade_type = "APP";
						// #endif
						
						if( res.code || (res.authResult && res.authResult.openid) ){
							// 发起网络请求
							console.log('发起网络请求');
							ef.submit({
								request: {
									s: ['SHOPORDERSELFPAYMENT', [shopOrderSelfAddConfig]]
								},
								callback(data){
									console.log('调起微信支付',data)
									/* uni.showToast({
										title:data,
										icon:'none',
										duration: 30000
									}); */
									var shopOrderSelfAddData = fns.checkError(data,'s',function(errno,error){
										uni.showToast({
											title:error,
											icon:'none',
											duration: 3000
										});
									});
									
									if( !shopOrderSelfAddData ){
										return false;
									}
									
									console.log('提交订单', shopOrderSelfAddData);
									_this.requestPayment(shopOrderSelfAddData.s);
									
								},
								error(err){
									fns.err('提交订单失败',err,1)
								}
							});
							
						} else {
							_this.isDisable = false;
							console.log('登录失败！' + res.errMsg );
							console.log('登录失败2444！' + JSON.stringify(res) );
							
							uni.showToast({
								title: '登录失败！' + res.errMsg,
								icon: 'none',
								//duration: 30000
							});
						}
						
					}
				});
			},
			//调起支付（微信）
			requestPayment(wxArr) {
				var _this = this;
				_this.test='开始了'
				var _this = this;
					console.log('支付参数'+JSON.stringify(wxArr));
					console.log('调起支付');
					_this.test='调起支付'
				
				// #ifdef APP-PLUS
				var orderInfo = {
					appid:wxArr.appid,
					partnerid:wxArr.mch_id,//商户号
					prepayid:wxArr.prepay_id,//预支付交易会话ID
					package:'Sign=WXPay',//扩展字段,暂填写固定值Sign=WXPay
					noncestr:wxArr.nonce_str,//随机字符串
					timestamp:wxArr.time_stamp,//时间戳	
					sign:wxArr.pay_sign,//签名
				};
				// #endif
				
				uni.requestPayment({
					// #ifdef MP-WEIXIN
					provider: 'wxpay',
					timeStamp: String(wxArr.time_stamp),
					nonceStr: wxArr.nonce_str,
					package: 'prepay_id=' + wxArr.prepay_id,
					signType: wxArr.sign_type,
					paySign: wxArr.pay_sign,
					orderInfo:wxArr,
					// #endif
					// #ifdef APP-PLUS
					provider:'wxpay',
					/* appid:wxArr.appid,
					partnerid:wxArr.mch_id,//商户号
					prepayid:wxArr.prepay_id,//预支付交易会话ID
					package:'Sign=WXPay',//扩展字段,暂填写固定值Sign=WXPay
					noncestr:wxArr.nonce_str,//随机字符串
					timestamp:wxArr.time_stamp,//时间戳	
					sign:wxArr.sign,//签名 */
					orderInfo: orderInfo,
					// #endif
					success: function(res) {
						_this.test = '支付成功?'
						console.log('支付成功success:' , JSON.stringify(res));
						// return
						if (res.errMsg == 'requestPayment:ok') {
							_this.test='支付成功'
							//支付成功是进行订单查询
							
							uni.showToast({
								title: '支付成功',
								icon: 'none',
								success() {
									setTimeout(function(){
										_this.load()
									},1500)
								}
							})	
						} 
					},
					fail: function(err) {
						_this.test = '调用支付失败'+JSON.stringify(err);
						console.log('调用支付失败',JSON.stringify(err));
						/* uni.showToast({
							title: "调用支付失败："+JSON.stringify(err),
							icon:'none',
							duration: 100000
						}); */
						return false;
					},
				});
			},
			//支付宝支付
			pay_alipay(){
				var _this=this;
				_this.pay_password=false;
				ef.submit({
						request: {
							s: ['SHOPORDERSELFPAYMENT', [{
								order_id:_this.order_id,
								pay_method:'alipay', //支付方式 weixinpay 微信支付、alipay 支付宝支付
								alipay_trade_type:'APP'
							} ]]
						},
						callback(data){
							console.log('调起支付宝支付',data)
							fns.checkError(data,'s',function(errno,error){
								uni.showToast({
									title:error,
									icon:'none'
								})
							})
							var ali=data.data.s.data.alipay
							if(ali){
								uni.requestPayment({
										provider: 'alipay',
										orderInfo:ali,
										success: function (res) {
											console.log('success:' + JSON.stringify(res));
											uni.showToast({
												title:'支付成功',
												success() {
													setTimeout(function(){
														_this.load()
													},1500)
													
												}
											})
										},
										fail: function (err) {
											console.log('fail:' + JSON.stringify(err));
										}
									});
							}
						},
						error(err){
							fns.err('提交订单失败',err,1)
						}
			
				})
			},
			
			checkPwd(pwd){
				var _this=this
				_this.password=pwd
					if (_this.ttype == 1) {
						_this.paymoeny(_this.password)
					}
					if (_this.ttype == 2) {
						_this.payjifen(_this.password)
					}
				
			},
			//余额支付
			paymoeny(password){
				var _this=this
				ef.submit({
					request:{
						s:['SHOPORDERSELFPAYMENT',[{order_id:_this.order_id,pay_method:'user_money',pay_password:password}]]
					},
					callback(data) {
					    const dataList=fns.checkError(data, 's', function(errno, error) {
						uni.showToast({
							title: error,
							icon: 'none'
						});
						})
						console.log('da...',dataList)
						
						if(dataList.s.order_id){
							_this.password=''
							uni.showToast({
								title:'支付成功',
								success() {
									setTimeout(function(){
										_this.pay_password= false;
										_this.load();
									},1500)
								}
							})
						
						}
						
						console.log('支付结果');
					}
				})
					
			},
			payjifen(password){
				var _this=this
				ef.submit({
					request:{
						s:['SHOPORDERSELFPAYMENT',[{order_id:_this.order_id,pay_method:'user_credit',pay_password:password}]]
					},
					callback(data) {
					    const dataList=fns.checkError(data, 's', function(errno, error) {
						uni.showToast({
							title: error,
							icon: 'none'
						});
						})
						console.log('da...',dataList)
						if(dataList.s.order_id){
							_this.pay_display = true;
						    _this.password=''
							if( _this.order_money>0){
								uni.showToast({
									title:'您还有资金未支付',
									icon:'none'
								})
							  _this.pay_password=false
							}
							else{
								uni.showToast({
									title:'支付成功',
									success() {
										setTimeout(function(){
											_this.load();
										},1500)
									}
								})
								
							}
							
						}
						
						console.log('支付结果');
					}
				})
					
			},
			 tabsChange(index){
				this.current = index
				console.log('this.current',this.current)
				this.load()
			},
			cancel_order(id){
				var that=this;
				uni.showModal({
					title: '警告',
					content: '确认取消订单吗',
					success: function (res) {
						if (res.confirm) {
							console.log('用户点击确定');
							ef.submit({
								request:{
									cancel:['SHOPORDERSELFCANCEL',[{id:id}]],//取消订单
								},
								callback(data){
									fns.checkError(data, 'cancel', function(errno, error) {
									uni.showToast({
										title: error,
										icon: 'none'
									});
									})
									console.log("www",data)
										uni.showToast({
											title:'已取消',
											success() {
												setTimeout(function(){
													that.load()
												},1500)
											}
										})
								},
								error(err){
									fns.err('err',err,1)
								}
							})
							
						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					}
				});
			},
			Del(id){
				var that=this;
				uni.showModal({
					title: '警告',
					content: '确认删除吗',
					success: function (res) {
						if (res.confirm) {
							console.log('用户点击确定');
							ef.submit({
								request:{
									del:['SHOPORDERSELFREMOVE',[{id:id}]],//删除订单
								},
								callback(data){
									fns.checkError(data,'del', function(errno, error) {
									uni.showToast({
										title: error,
										icon: 'none'
									});
									})
									console.log("删除",data)
									uni.showToast({
										title:'已删除',
										success() {
										setTimeout(function(){
											that.load()
										},1500)
											
										}
									})
								},
								error(err){
									fns.err('err',err,1)
								}
							})
							
						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					}
				});
			},
			affirm(id){
				var that=this;
				uni.showModal({
					title: '提示',
					content: '确认收货吗',
					success: function (res) {
						if (res.confirm) {
							console.log('用户点击确定');
							ef.submit({
								request:{
									del:['SHOPORDERSELFRECEIVE',[{id:id}]],//删除订单
								},
								callback(data){
									fns.checkError(data,'del', function(errno, error) {
									uni.showToast({
										title: error,
										icon: 'none'
									});
									})
									console.log("收货",data)
									uni.showToast({
										title:'已收货',
										success() {
												setTimeout(function(){
												that.load()
											},1500)
										}
									})
									
								},
								error(err){
									fns.err('err',err,1)
								}
							})
							
						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					}
				});
			},
			//查看物流信息
			logistics(id){
				uni.navigateTo({
					url:'../../pagesB/my-order/logistics?id='+id
				})
			},
			//显示评论输入框
			gocomment(id){
				this.commentShow=true;
				this.CommentID=id;
			},
			showConditionPage(){
				this.commentShow=!this.commentShow
			},
			//发布评论
			issue(){
				var that=this
				ef.submit({
					request:{
						comm:['SHOPORDERSELFCOMMENT',[{id:that.CommentID,comment:that.Comment}]],//添加评论
					},
					callback(data){
						console.log("评论",data)
						uni.showToast({
							title:'评论成功',
							success() {
									setTimeout(function(){
									that.load()
								},1500)
								that.commentShow=false
							}
						})
						
						  
					},
					error(err){
						fns.err('err',err,1)
					}
				})
			},
		}
	}
</script>

<style>
	/* uni.css - 通用组件、模板样式库，可以当作一套ui库应用 */
	@import '../../common/uni.css';
	swiper-item{
		overflow:auto;
	}
	page{
		background-color: #FFF;
	}
	.contentBox{
	}
	.uni-swiper-tab{
		box-shadow: 0 1px 5px rgba(0,0,0,.06);
		border-bottom: #FFFFFF;
	}
	.swiper-tab-list{
		width: 80upx;
		margin-left: 35upx;
		margin-right: 35upx;
		height: 65upx;
	}
	.list-scroll-content{
	 height: calc(100% - 10px);
	}
	.active1{
		color: #f73c76;
		font-weight: 600;
		border-bottom: 5upx #f73c76 solid;
	}
	.Box_top{
		width: 96%;
		padding: 0 2%;
		height: 80upx;
		border-bottom: 1upx #f3f3f3 solid;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}
	.Box_top_left{
		color: #333333;
	}
	.Box_top_right{
		color: #fa436a;
	}
	.Box_center{
		width: 100%;
		display: flex;
		border-bottom: #f3f3f3 solid 1upx;
		height: auto;
		
	}
	.Box_center_left{
		width: 25%;
		height: 180upx;
		margin-left: 2%;
		margin-bottom: 30upx;
	}
	.Box_center_left image{
		width: 100%;
		height: 100%;
		border-radius: 10upx;
		margin-top: 20upx;
		
	}
	.Box_center_center{
		width: 50%;
		padding: 2%;
		font-size: 28upx;
		color: #303133;
	}
	.Box_center_right{
		color: #444444;
		width: 20%;
		padding-top: 50upx;
		text-align: center;
	}
	.commodityNumBlock{
		display: block;
	}
	.Box_bootom{
		width: 98%;
		padding: 0 2%;
		height: 100upx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		border-bottom:#f5f5f5 solid 1upx;
	}
	.Box_bootom_text{
		text-align: right;
		color: #909399;
	}
	.Box_bootom_btn{
		text-align: right;
	}
	.priceRed{
			color: #fa436a;
			font-size: 32upx;
			font-weight: 550;
			padding-left: 10upx;
		}
			
	.commodityBtn{
		color: #5E5E5E;
		text-align: right;
		display: block;
	}
		
	.paybtn{
		padding: 5px 13px;
		border-radius: 13px;
		width:50px;
		height: 80upx;
		text-align:center;
		font-size: 26upx;
		color:#303133;
		border: 1px solid rgba(0,0,0,.2);
		margin-left: 50upx;
		
	}
			
	.nowPay{
		background: #fff9f9;
		color: #fa436a;
		border: 1px #f7bcc8 solid;
	}
	.load{
		width: 100%;
		height: 80upx;
		text-align: center;
		float: left;
		line-height: 80upx;
		margin-bottom: 20upx;
		font-size: 28upx;
		background: #f1f1f1;
	}
	/* 评论 */
	.fixeBox{
		width:100%;
		height:230upx;
		position: fixed;
		bottom: 0upx;
		background: #FFFFFF;
		display: flex;
		z-index: 8;
	}
	.fixeBoxBox{
		position: relative;
		width: 100%;
		height: 200upx;
		display: flex;
		margin-top: 15upx;
		margin-bottom: 15upx;
	}
	
	.fixeBoxLeft{
		float: left;
		width: 74%;
		margin-left: 3%;
		height: 100%;
		border: #D9D9D9 1upx solid;
	}
	.fixeBoxLeft textarea{
		padding-top: 10upx;
		float: left;
		width: 100%;
		height: 100%;
	}
	.fixeBoxRight{
		border-radius: 20upx;
		position: absolute;
		width: 20%;
		height: 60upx;
		line-height: 60upx;
		background: #E44545;
		color:#FFFFFF;
		right: 10upx;
		margin-top: 60upx;
		text-align: center;
	}
	
	/* 选择支付方式 */
	.mask_content{
		width: 100%;
		height:auto;
		background-color: #FFFFFF;
		position: fixed;
		bottom: 0upx;
		padding-top: 10upx;
	}
	.title-box{
		width: 100%;
		height: 80upx;
		display: flex;
		justify-content: space-between;
		border-bottom: 1upx #DBD8D5 solid;
	}
	.title-box text{
		width: 50%;
	}
	.title-box image{
		width: 24px;
		height: 24px;
	}
	.pay-time-box{
		width: 100%;
		text-align: center;
		color: #D23535;
		height: 80upx;
		line-height: 80upx;
		border-bottom: 1upx #DBD8D5 solid;
	}
	.ali-pay{
		width: 100%;
		height: 80upx;
		padding: 20upx 10upx 30upx;
		border-bottom: 1px solid #ccc;
		display: flex;
	}
	.ali-pay image{
		width :24px;
		height: 24px;
	}
	.ali-pay text{
		width: 70%;
		padding-left: 10%;
	}
	.select{
		float: right;
	}
	.submit-btn{
		width: 100%;
		height: 80upx;
		line-height: 80upx;
		background-color:#E31436;
		color: #FFFFFF;
		text-align: center;
	}
	.mask {
		position: fixed;  
		top:0;  
		left:0;  
		z-index:4;  
		width:100%;  
		height:100vh;  
		background:rgba(0,0,0,0.5);  
	} 
	.lineback{
		width: 100%;
		height:20upx;
		background-color: #f8f8f8;
	}
	.methods-box{
		background-color: #F8F8F8;
	}
</style>

