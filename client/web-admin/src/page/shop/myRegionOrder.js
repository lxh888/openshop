av({
	id:'page-shop-myRegionOrder',//工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include : ["src/common/content.js"],//获取js文件
	extend : ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
	'export' : {
	template: "src/page/shop/myRegionOrder.html",},
	'import' : function(e){
			this.template(e.template);//绑定模版
		},
	
	main: function(){
	var _this = this;
	var config = { search:{},show_region_order:1 };
	
	var shop_order_index = -1;
	//搜索
	this.data.routerAnchorQuery('search', function(data){
		data = av.decodeURL(data);
		config.search = JSON.parse(data);
	});
	
	//排序
	this.data.routerAnchorQuery('sort', function(data){
		config.sort = [data];
	});
	
	//分页
	this.data.routerAnchorQuery('page', function(data){
		config.page = data;
	});
	
	//交易状态
	this.data.routerAnchorQuery('transaction_state', function(data){
		_this.data.transaction_state = data;
		config.search.transaction_state = data;//状态
	}, function(){
		_this.data.transaction_state = undefined;
	});
	//订单状态
	this.data.routerAnchorQuery('state', function(data){
		_this.data.state = data;
		config.search.state = data;//状态
	}, function(){
		_this.data.state = undefined;
	});
	//支付状态
	this.data.routerAnchorQuery('pay_state', function(data){
		_this.data.pay_state = data;
		config.search.pay_state = data;//状态
	}, function(){
		_this.data.pay_state = undefined;
	});
	
	//订单商品类型
	this.data.routerAnchorQuery('show_vip_goods_order', function(data){
		_this.data.show_vip_goods_order = data;
		config.search.show_vip_goods_order = data;//状态
		//shop_order_index = data;//状态

	}, function(){
		_this.data.show_vip_goods_order = undefined;
		//shop_order_index=-1;
	});

	//物流状态
	this.data.routerAnchorQuery('shipping_state', function(data){
		_this.data.shipping_state = data;
	}, function(){
		_this.data.shipping_state = undefined;
	});
	
	
	this.data.request.list= ['SHOPADMINORDERLIST', [config]];
	//console.log(shop_order_index);
	// if(shop_order_index==-1){
	// 	this.data.request.totalMoeny=["SHOPADMINORDERTOTALMONEY"]
	// }else{
	// 	this.data.request.totalMoeny=["SHOPADMINORDERTOTALMONEY",[{'shop_order_index':shop_order_index}]]
	// }
	
},
	event: {
		error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},
		

	},
	//数据对象
	data:{
		request: {},
	search:[
		'shop_order_id', 
		'user_id', 
		'user_nickname', 
		'user_phone',
		'user_address_consignee',
		'date_start',
		'date_end'
		],
		list:{
		data : [],
	},
	
	
	}
});