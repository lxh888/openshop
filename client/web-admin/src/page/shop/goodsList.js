av({
	id: 'page-shop-goodsList',//工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include: ["src/common/content.js","src/page/shop/goodsImport/goodsImport.js"],//获取js文件
	extend: ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
	'export': {
		template: "src/page/shop/goodsList.html",
	},//引入模版，可以同时引入多个
	'import': function (e) {
		// this.data.templateTest = e.template2;//绑定模版
		this.template(e.template);//绑定模版
	},

	main: function () {

		var _this = this;
		var config = { search: {} };

		//搜索
		this.data.routerAnchorQuery('search', function (data) {
			data = av.decodeURL(data);
			config.search = JSON.parse(data);
		});

		//搜索 商品类别
		this.data.routerAnchorQuery('property', function (data) {
			_this.data.property = data;
			config.search.property = data;//状态
		}, function () {
			_this.data.property = undefined;
		});


		//搜索 商品是否限时
		this.data.routerAnchorQuery('when', function (data) {
			_this.data.when = data;
			config.search.when = data;//状态
		}, function () {
			_this.data.when = undefined;
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

		this.data.request.list = ['SHOPADMINGOODSLIST', [config]];
	},
	event: {
		error: function (error) {
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},

	},
	//数据对象
	data: {
		request: {},
		search: [
			'shop_goods_id',
			'shop_goods_name',
			'shop_goods_sn'
		],
		list: {
			data: [],
		},

		/**
		 * 更新售卖状态
		 * 
		 * @param {Object} bool
		 */
		eventSellState: function (ele, e, bool) {
			var ids = this.checkboxData('data-id');
			var _this = this;
			var state = bool;

			this.actionStateIds(ids, function () {

				var request_array = [];
				for (var i in ids) {
					request_array.push(["SHOPADMINGOODSEDIT", [{ shop_goods_id: ids[i], shop_goods_state: state }]]);
				}

				//提交数据
				_this.submit({

					method: "list",
					request: request_array,
					success: function (bool) {
						if (bool) {
							//刷新页面
							av().compiler("reload").render().run();
						}
					}

				});


			});

		},
		//商品排序
		eventGoodsSort: function () {
			var obj = this.inputData("sort");
			var request_array = [];
			request_array.push(["SHOPADMINGOODSEDITCHECK"]);//第一个是判断是否有编辑权限
			for (var i in obj) {
				request_array.push(["SHOPADMINGOODSEDIT", [{ shop_goods_id: obj[i].id, shop_goods_sort: obj[i].value }]]);
			}

			//提交数据
			this.submit({
				method: "edit",
				request: request_array,
				success: function (data) {
					//刷新页面
					av().run();
				}
			});

		},
		//回收
		eventRemove: function (ele) {
			var ids = this.checkboxData('data-id');
			var _this = this;
			this.actionRemoveIds(ids, function () {

				var request_array = [];
				for (var i in ids) {
					request_array.push(["SHOPADMINGOODSTRASH", [{ shop_goods_id: ids[i] }]]);
				}

				//提交数据
				_this.submit({
					method: "list",
					request: request_array,
					success: function (bool) {
						if (bool) {
							//刷新页面
							av().compiler("reload").render().run();
						}
					}
				});


			});
		},

		//喜乐淘导入商品
		eventImportGoods: function () {
			var _this = this;
			var project = av('page-shop-goodsList::import');
			//拷贝 将数据复制到新模版
			project.clone({	});
			
			//备份原始的提交按钮函数
			var keyupFunctions = av('common-event').data.keyupFunctions['common-content'];
			//替换为引入工程的提交按钮函数
			av('common-event').data.keyupFunctions['common-content'] = function() {
				project.data.eventSubmit();
			};
			layer.closeAll();
			var selector = 'add' + Date.parse(new Date());
			//页面层
			var layerid = layer.open({
				title: "<span class=\"glyphicon glyphicon-plus\"></span> 导入商家商品",
				type: 1,
				//offset: '0',
				//area: ["500px", '566px'], //宽高
				area: [($(window).width() > 500 ? 500 : $(window).width()) + "px", ($(window).height()>250?250:$(window).height()) + 'px'], //宽高
				content: '<div id="' + selector + '"></div>',
				end: function() {
					//销毁提交按钮提交函数，还原备份
					av('common-event').data.keyupFunctions['common-content'] = keyupFunctions;
				},
				success: function(){
					//渲染这个插件
					project.compiler("reload").selector('#' + selector).render("refresh");
					$('[input-focus="page-shop-goodsList::import"]').focus(); //获得焦点
				}
			});
			//成功的时候 回调
			project.data.successSubmitCallback = function(){
				av().run();
				layer.closeAll();
			}
			


		},
		eventImportGoodsDetail: function(shop_goods_id){
			var _this=this;
			layer.prompt({
				formType: 0,
				value: '',
				title: '请输API KEY',
				area: ['400px', '350px'] //自定义文本域宽高
			}, function(value, index, elem){
				//提交数据
				_this.submit({
					method:"submit",
					request:["MERCHANTIMPORTone",[{key:value,shop_goods_id:shop_goods_id}]],
					error:function(){
						layer.close(index);
					},
					success:function(id){
						layer.close(index);
					}
				});
			});
				
		}
	


		
	}
});