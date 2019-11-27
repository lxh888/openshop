av({
	id:'page-shop-goodsWhenList',//工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include : ["src/common/content.js"],//获取js文件
	extend : ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
	'export' : {
	template: "src/page/shop/goodsWhenList.html",},
	'import' : function(e){
			this.template(e.template);//绑定模版
		},
	
	main: function(){
	var _this = this;
	var config = { search:{} };
	
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
	
	//全部商品状态
	this.data.routerAnchorQuery('shop_goods_state', function(data){
		_this.data.shop_goods_state = data;
		config.search.shop_goods_state = data;//状态
	}, function(){
		_this.data.shop_goods_state = undefined;
	});
	//全部限时状态
	this.data.routerAnchorQuery('state', function(data){
		_this.data.state = data;
		config.search.state = data;//状态
	}, function(){
		_this.data.state = undefined;
	});
	
	
	this.data.request.list= ['SHOPADMINGOODSWHENLIST', [config]];
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
		'shop_goods_id', 
		'shop_goods_name', 
		],
		list:{
		data : [],
		},
	submitLock :false,
	eventReSort:function(){
		var obj = $("[name='shop_goods_when_sort']");
			if(!obj || !obj.length) {
				return false;
			}
			var request_array = [];
			request_array.push(["SHOPADMINGOODSWHENEDITCHECK"]); //第一个是判断是否有编辑权限
			for(var i = 0; i < obj.length; i++) {
				request_array.push(["SHOPADMINGOODSWHENEDIT", [{
					shop_goods_id: obj[i].id,
					shop_goods_when_sort: obj[i].value
				}]]);
			}
			//提交数据
			this.submit({
				method: "edit",
				request: request_array,
				success: function(data) {
					//刷新页面
					av().run();
				}
			});
	},
	eventRemove: function(ele) {
		var _this = this;
		var ids = this.checkboxData('data-id');
		this.actionRemoveIds(ids, function() {
			var request_array = [];
			for(var i in ids) {
				request_array.push(["SHOPADMINGOODSWHENREMOVE", [{
					shop_goods_id: ids[i]
				}]]);
			}
			//提交数据
			_this.submit({
				method: "list",
				request: request_array,
				error: function() {
					_this.submitLock = false;
				},
				success: function() {
					_this.submitLock = false;
					//刷新页面
					av().compiler({reload: true}).render({
						refresh: true
					}).run();
				}
			});
		});
	},
	
	}
});

