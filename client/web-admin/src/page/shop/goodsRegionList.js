av({

	id: 'page-shop-goodsRegionList',
	include: ["src/common/content.js"],
	extend: ["common-content"],
	'export': { template: "src/page/shop/goodsRegionList.html" },
	'import': function (e) {
		this.template(e.template);
	},
	main: function () {
		var _this = this;
		var config = { search: {} };

		//搜索
		this.data.routerAnchorQuery('search', function (data) {
			data = av.decodeURL(data);
			config.search = JSON.parse(data);
		});

		//排序
		this.data.routerAnchorQuery('sort', function (data) {
			config.sort = [data];
		});

		//分页
		this.data.routerAnchorQuery('page', function (data) {
			config.page = data;
		});

		//状态 
		this.data.routerAnchorQuery('state', function (data) {
			_this.data.state = data;
			config.search.state = data;//状态
		}, function () {
			_this.data.state = undefined;
		});

		//商品状态 
		this.data.routerAnchorQuery('shop_goods_state', function (data) {
			_this.data.shop_goods_state = data;
			config.search.shop_goods_state = data;//状态
		}, function () {
			_this.data.shop_goods_state = undefined;
		});


		this.data.request.list = ["SHOPADMINGOODSREGIONLIST", [config]]
		if (config.search.shop_goods_id) {
			this.data.request.data = ["SHOPADMINGOODSQUERY", [{ shop_goods_id: config.search.shop_goods_id }]];
		} else {
			this.data.request.data = undefined;
			this.data.data = null;//值也要置空
		}

	},
	event: {

		error: function (error) {
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},
		loadEnd: function () {
			//this.render("refresh");

		}

	},
	data: {
		request: {},
		state: undefined,
		shop_goods_state: undefined,
		search: [
			'shop_goods_id',
			'shop_goods_name',
			'scope',
			'shop_goods_region_province',
			'shop_goods_region_city',
			'shop_goods_region_district',
			
		],
		data: undefined,//一条商品数据
		list: {
			data: [],
		},
		//正常
		eventPass: function(ele, e){
			var ids = this.checkboxData('data-id');
			var _this = this;
			this.actionStateIds(ids, function(){
				var request_array = [];
				for(var i in ids){
					request_array.push(["SHOPADMINGOODSREGIONEDIT", [{shop_goods_region_id:ids[i],shop_goods_region_state:1}]]);
				}
				
				//提交数据
				_this.submit({
					method:"list",
					request:request_array,
					success:function(bool){
						if(bool){
							//刷新页面
							av().compiler("reload").render().run();
						}
					}
				});
			});
		},
		//失败
		eventFail: function(ele, e){
			var ids = this.checkboxData('data-id');
			var _this = this;
			
			this.actionStateIds(ids, function(){
				var request_array = [];
				for(var i in ids){
					request_array.push(["SHOPADMINGOODSREGIONEDIT", [{shop_goods_region_id:ids[i],shop_goods_region_state:0}]]);
				}
				//提交数据
				_this.submit({
					method:"list",
					request:request_array,
					success:function(bool){
						if(bool){
							//刷新页面
							av().compiler("reload").render().run();}
					}
				});
			});
		},
		//删除
		eventRemove: function(ele) {
			var ids = this.checkboxData('data-id');
			var _this = this;
			this.actionRemoveIds(ids, function() {
				var request_array = [];
				for(var i in ids) {
					request_array.push(["SHOPADMINGOODSREGIONREMOVE", [{shop_goods_region_id: ids[i]}]]);
				}
				//提交数据
				_this.submit({
					method: "list",
					request: request_array,
					success: function(bool) {
						if(bool) {
							//刷新页面
							av().compiler("reload").render().run();
						}
					}
				});
			});
		},
		
	}
});