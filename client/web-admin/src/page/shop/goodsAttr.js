av({

	id: 'page-shop-goodsAttr',
	include: [
		"src/common/content.js", 
		"src/page/shop/goodsAttr/spuEdit.js",
		"src/page/shop/goodsAttr/spuAdd.js",
		"src/page/shop/goodsAttr/skuAdd.js",
		"src/page/shop/goodsAttr/skuEdit.js"
	],
	extend: ["common-content"],
	'export': {
		template: "src/page/shop/goodsAttr.html",
		skuTemplate: "src/page/shop/goodsAttr/sku.html",
		spuTemplate: "src/page/shop/goodsAttr/spu.html",
	},
	'import': function(e) {
		this.template(e.template);
		this.data.skuTemplate = e.skuTemplate;
		this.data.spuTemplate = e.spuTemplate;
	},
	main: function(){
		//从路由里读取id参数
		this.data.queryId = (function() {
			try {
				return av.router().anchor.query.id;
			} catch(e) {
				return '';
			}
		}());
		//如果路由里有id参数
		if( this.data.queryId ){
			this.data.getShopGoodsId = this.data.queryId;//将参数赋给商品id
		}
		
		var _this = this;
		var config = {
			search: {}
		};

		//排序
		this.data.routerAnchorQuery('sort', function(data) {
			config.sort = [data];
		});

		//分页
		this.data.routerAnchorQuery('page', function(data) {
			config.page = data;
		});
		
		//若商品id存在则获取商品信息
		if( this.data.getShopGoodsId ){
			config.search.shop_goods_id = this.data.getShopGoodsId;
			
			this.data.request.getShopGoodsData = ["SHOPADMINPGOODSGET", [{
				shop_goods_id: this.data.getShopGoodsId
			}]];
			
			//规格列表
			this.data.request.list = ["SHOPADMINGOODSSKULIST", [config]]
			//属性信息
			this.data.request.shop_goods_spu_option = ["SHOPADMINGOODSSPUOPTION", [{
				"sort": ["sort_asc", "insert_time_asc"],
				search: {
					shop_goods_id: this.data.getShopGoodsId
				}
			}]]
		}else{
			this.data.getShopGoodsData = null;
			this.data.list = {data:null};
			this.data.shop_goods_spu_option = null;
		}
		//如果是易淘则请求角色数据
		if( this.data.applicationCheckYitaoshop() )
		this.data.request.adminOption=["ADMINOPTION",[{sort: ["sort_asc"]}]];
	},
	event: {
		error: function(error) {
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/hop-goodsSpu_sku/').request();
		},
		//当锚点路由更新时
        routerChange : function(){
        	var queryId = (function() {
				try {
					return av.router().anchor.query.id;
				} catch(e) {
					return '';
				}
			}());
			
			//console.log( this.data.queryId, queryId );
        	if(this.data.queryId != queryId){
        		this.data.queryId = queryId;
        		return false;
        	}else{
        		av().run();
        	}
        	//console.log( av.router() );
        },
        
        //当渲染的完成之后
		renderEnd: function(){
			//注册选中状态
			this.data.checkboxRegister("checkbox", "checkbox-all");
			//注册选中状态
			this.data.checkboxRegister("checkbox-spu");
		},
		loadEnd: function() {
			//初始化时如果获取到了商品数据，将其赋给商品变量
			if( this.data.getShopGoodsData ) {
				this.data.getShopGoodsError = '';
				if(this.data.queryId != this.data.getShopGoodsId){
					//将商品id 给路由
					av.router(av.router(), {anchor:{query:{id:this.data.getShopGoodsId}}}).request();
				}
			} else {
				//this.data.getShopGoodsError = '未获取到商品信息，请检查商品id是否正确！';
				this.data.getShopGoodsData = null;
			}
		}
	},
	data: {
		request: {},
		list: {
			data: [],
		},
		
		queryId:'',
		getShopGoodsId: '',
		getShopGoodsError: '', //获取商品数据时的错误信息
		getShopGoodsData: null, //商品数据		
		nowMenu: 'sku', //当前列表
		skuTemplate:'',
		spuTemplate:'',
		eventChangeNowMenu: function(ele, e, i) {
			if( i == 'spu'){
				this.nowMenu = 'spu';
			}else{
				this.nowMenu = 'sku';
			}
		},
		//检查应用是否是易淘
		/*yitaoshopCheck: function(){
			var objectRequestAPI = new requestAPI();
			if( objectRequestAPI.application() == 'yitaoshop' || objectRequestAPI.application() == 'yitaoshop_test' ){
				return true;
			}else{
				return false;
			}
		},*/
		//获取单个商品信息
		eventGetSingleGoodsInfo: function(ele, e) {
			//console.log(getShopGoodsId);
			var _this = this;
			//如果输入框的值有值时才将新值赋给getShopGoodsId
			if($(ele).val() != undefined)
				_this.getShopGoodsId = $(ele).val();
			if(_this.getShopGoodsId == '') {
				_this.getShopGoodsError = '';
				_this.getShopGoodsData = null;
				return false;
			}
			var form_input = {};
			form_input.shop_goods_id = _this.getShopGoodsId;
			//请求单个商品信息
			var requestAPIObject = new requestAPI();
			requestAPIObject.abort();
			//提交数据
			requestAPIObject.submit({
				request: {
					g: ["SHOPADMINPGOODSGET", [form_input]], //SHOPADMINGOODSQUERY
					list: ["SHOPADMINGOODSSKULIST", [{
						search: {
							shop_goods_id: form_input.shop_goods_id
						}
					}]],
					shop_goods_spu_option: ["SHOPADMINGOODSSPUOPTION", [{
						"sort": ["sort_asc", "insert_time_asc"],
						search: {
							shop_goods_id: form_input.shop_goods_id
						}
					}]]
				},
				callback: function(r) {
					if((function() {
							try {
								return r.data.g.errno;
							} catch(e) {
								return false;
							}
						}())) {
						_this.getShopGoodsError = r.data.g.error;
						_this.getShopGoodsData = null;
						list = {
							data: []
						};
					} else {
						_this.getShopGoodsError = '';
						//如果返回的数组长度为0
						if(r.data.g.data.length == 0) {
							_this.getShopGoodsData = null;
							_this.getShopGoodsError = '未获取到商品信息，请检查商品id是否正确！';
						} else {
							_this.getShopGoodsData = r.data.g.data;
							_this.getShopGoodsError = '';
							//将商品id 给路由
							av.router(av.router(), {anchor:{query:{id:form_input.shop_goods_id}}}).request();
						}
						

						_this.list = (function() {
							try {
								return r.data.list.data;
							} catch(e) {
								return false;
							}
						}());
						_this.shop_goods_spu_option = (function() {
							try {
								return r.data.shop_goods_spu_option.data;
							} catch(e) {
								return false;
							}
						}());
					}
				},
			});
		},
		
		//修改SPU排序
		eventSpuSort: function() {
			var obj = $("[name='shop_goods_spu_sort']");
			if(!obj || !obj.length) {
				return false;
			}
			var request_array = [];
			request_array.push(["SHOPADMINGOODSSPUEDITCHECK"]); //第一个是判断是否有编辑权限
			for(var i = 0; i < obj.length; i++) {
				request_array.push(["SHOPADMINGOODSSPUEDIT", [{
					shop_goods_spu_id: obj[i].id,
					shop_goods_spu_sort: obj[i].value
				}]]);
			}
			//提交数据
			this.submit({
				method: "edit",
				request: request_array,
				success: function(data) {
					//刷新页面
					av().render("refresh").run();
				}
			});
		},
		//删除商城商品规格属性
		eventSpuRemove: function(ele) {
			var _this = this;
			var ids = this.checkboxData('data-id', 'checkbox-spu');
			this.actionRemoveIds(ids, function() {
				var request_array = [];
				for(var i in ids) {
					request_array.push(["SHOPADMINGOODSSPUREMOVE", [{
						shop_goods_spu_id: ids[i]
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
						av().compiler("reload").render("refresh").run();
					}
				});
			});
		},
		
		
		//编辑SPU
		eventSpuEdit: function(ele, e, shop_goods_spu_id) {
			//循环属性列表获取当前编辑属性的数据
			var shop_goods_spu_attr = null;
			var isBreak = false; //当找到当前编辑对象立即跳出循环
			if(this.shop_goods_spu_option && typeof this.shop_goods_spu_option == 'object') {
				for(var i in this.shop_goods_spu_option) {
					if(this.shop_goods_spu_option[i].shop_goods_spu_id == shop_goods_spu_id) {
						shop_goods_spu_attr = this.shop_goods_spu_option[i];
						isBreak = true;
					}
					if(isBreak) break;
					for(var j in this.shop_goods_spu_option[i].son) {
						if(this.shop_goods_spu_option[i].son[j].shop_goods_spu_id == shop_goods_spu_id) {
							shop_goods_spu_attr = this.shop_goods_spu_option[i].son[j];
							isBreak = true;
						}
						if(isBreak) break;
					}
				}
			}
			if(!shop_goods_spu_attr) {
				layer.msg('属性数据异常', {
					icon: 5,
					time: 2000
				});
				return false;
			}

			var AVproject = av('page-shop-goodsAttr::spuEdit');
			//拷贝 将数据复制到新模版
			AVproject.clone({
				shop_goods_spu_attr: shop_goods_spu_attr, //当前属性的数据
				shopGoodsSpuOption: this.shop_goods_spu_option,

			});

			AVproject.data.shop_goods_spu_id = shop_goods_spu_attr.shop_goods_spu_id;
			AVproject.data.shop_goods_spu_name = shop_goods_spu_attr.shop_goods_spu_name;
			
			//备份原始的提交按钮函数
			var common_content_keyupFunctions = av('common-event').data.keyupFunctions['common-content'];
			//替换为引入工程的提交按钮函数
			av('common-event').data.keyupFunctions['common-content'] = function() {
				AVproject.data.eventSubmit();
			};
			layer.closeAll();
			var selector = 'eventSpuEdit' + Date.parse(new Date());
			//页面层
			var layerid = layer.open({
				title: "<span class=\"glyphicon glyphicon-edit\"></span> 编辑商品规格属性",
				type: 1,
				//offset: '0',
				//area: ["500px", '566px'], //宽高
				area: [($(window).width() > 500 ? 500 : $(window).width()) + "px", ($(window).height() - 50) + 'px'], //宽高
				content: '<div id="' + selector + '"></div>',
				end: function() {
					//销毁提交按钮提交函数，还原备份
					av('common-event').data.keyupFunctions['common-content'] = common_content_keyupFunctions;
				},
				success: function() {
					//渲染这个插件

					AVproject.compiler("reload").selector('#' + selector).render("refresh");
					$('[input-focus="page-shop-goodsAttr::spuEdit"]').focus(); //失去焦点
				}
			});

			//成功的时候 回调
			AVproject.data.successSubmitCallback = function(){
				av().run();
				layer.closeAll();
			}
		},
		
		//添加SPU
		eventSpuAdd: function(ele, e, shop_goods_spu_id) {

			var AVproject = av('page-shop-goodsAttr::spuAdd');
			//拷贝 将数据复制到新模版
			AVproject.clone({
				shop_goods_id: this.getShopGoodsId,
				shopGoodsSpuOption: this.shop_goods_spu_option,
			});
			
			AVproject.data.shop_goods_spu_parent_id = shop_goods_spu_id;

			//备份原始的提交按钮函数
			var common_content_keyupFunctions = av('common-event').data.keyupFunctions['common-content'];
			//替换为引入工程的提交按钮函数
			av('common-event').data.keyupFunctions['common-content'] = function() {
				AVproject.data.eventSubmit();
			};
			layer.closeAll();
			var selector = 'eventSpuAdd' + Date.parse(new Date());
			//页面层
			var layerid = layer.open({
				title: "<span class=\"glyphicon glyphicon-plus\"></span> 添加商品规格属性",
				type: 1,
				//offset: '0',
				//area: ["500px", '566px'], //宽高
				area: [($(window).width() > 500 ? 500 : $(window).width()) + "px", ($(window).height() - 50) + 'px'], //宽高
				content: '<div id="' + selector + '"></div>',
				end: function() {
					//销毁提交按钮提交函数，还原备份
					av('common-event').data.keyupFunctions['common-content'] = common_content_keyupFunctions;
				},
				success: function() {
					//渲染这个插件

					AVproject.compiler("reload").selector('#' + selector).render("refresh");
					$('[input-focus="page-shop-goodsAttr::spuAdd"]').focus(); //失去焦点
				}
			});
			
			//成功的时候 回调
			AVproject.data.successSubmitCallback = function(){
				av().run();
				layer.closeAll();
			}
		},
		
		//添加SKU
		eventSkuAdd: function(ele, e) {

			var AVproject = av('page-shop-goodsAttr::skuAdd');
			//拷贝 将数据复制到新模版
			AVproject.clone({
				shop_goods_id: this.getShopGoodsId,
				shop_goods_property: this.getShopGoodsData.shop_goods_property,
				shopGoodsSpuOption: this.shop_goods_spu_option,
				applicationConfig: this.applicationConfig,
				shop_goods_index:this.getShopGoodsData.shop_goods_index,
			});
			
			if(this.adminOption){
				AVproject.clone({
					adminOption:this.adminOption
				});
			}
			//备份原始的提交按钮函数
			var common_content_keyupFunctions = av('common-event').data.keyupFunctions['common-content'];
			//替换为引入工程的提交按钮函数
			av('common-event').data.keyupFunctions['common-content'] = function() {
				AVproject.data.eventSubmit();
			};
			layer.closeAll();
			var selector = 'eventSkuAdd' + Date.parse(new Date());
			//页面层
			var layerid = layer.open({
				title: "<span class=\"glyphicon glyphicon-plus\"></span> 添加商品规格",
				type: 1,
				//offset: '0',
				area: [($(window).width() > 800 ? 800 : $(window).width()) + "px", ($(window).height() - 50) + 'px'], //宽高
				content: '<div id="' + selector + '"></div>',
				end: function() {
					//销毁提交按钮提交函数，还原备份
					av('common-event').data.keyupFunctions['common-content'] = common_content_keyupFunctions;
				},
				success: function() {
					//渲染这个插件

					AVproject.compiler("reload").selector('#' + selector).render("refresh");
					$('[input-focus="page-shop-goodsAttr::skuAdd"]').focus(); //失去焦点
				}
			});

			//成功的时候 回调
			AVproject.data.successSubmitCallback = function(){
				av().run();
				layer.closeAll();
				//layer.close(layerid);
			}
		},
		//编辑SKU
		eventSkuEdit: function(ele, e, shop_goods_sku_id) {
			//循环属性列表获取当前编辑规格的数据
			var shop_goods_sku = null;
			var isBreak = false; //当找到当前编辑对象立即跳出循环
			if(this.list.data && typeof this.list.data == 'object') {
				for(var i in this.list.data) {
					if(this.list.data[i].shop_goods_sku_id == shop_goods_sku_id) {
						shop_goods_sku = this.list.data[i];
						isBreak = true;
					}
					if(isBreak) break;
				}
			}
			if(!shop_goods_sku) {
				layer.msg('规格数据异常', {
					icon: 5,
					time: 2000
				});
				return false;
			}
			var AVproject = av('page-shop-goodsAttr::skuEdit');
			//游客没有id，转换一下
			var shop_goods_sku_admin_id
			
			if(shop_goods_sku.shop_goods_sku_admin_id=='')
			{shop_goods_sku_admin_id='visitor';}
			else{
				shop_goods_sku_admin_id=shop_goods_sku.shop_goods_sku_admin_id;
			}
			//拷贝 将数据复制到新模版
			AVproject.clone({
				shop_goods_id: this.getShopGoodsId,
				shop_goods_property: this.getShopGoodsData.shop_goods_property,
				shop_goods_sku: shop_goods_sku,
				shopGoodsSpuOption: this.shop_goods_spu_option,
				applicationConfig: this.applicationConfig,
				shop_goods_sku_id: shop_goods_sku_id,
				admin_id:shop_goods_sku_admin_id,
				shop_goods_index:this.getShopGoodsData.shop_goods_index,
			});
			if(this.adminOption){
				AVproject.clone({
					adminOption:this.adminOption
				});
			}

			//备份原始的提交按钮函数
			var common_content_keyupFunctions = av('common-event').data.keyupFunctions['common-content'];
			//替换为引入工程的提交按钮函数
			av('common-event').data.keyupFunctions['common-content'] = function() {
				AVproject.data.eventSubmit();
			};
			layer.closeAll();
			var selector = 'eventSkuEdit' + Date.parse(new Date());
			//页面层
			var layerid = layer.open({
				title: "<span class=\"glyphicon glyphicon-edit\"></span> 编辑商品规格",
				type: 1,
				//offset: '0',
				//area: ["500px", '566px'], //宽高
				area: [($(window).width() > 800 ? 800 : $(window).width()) + "px", ($(window).height() - 50) + 'px'], //宽高
				content: '<div id="' + selector + '"></div>',
				end: function(){
					//销毁提交按钮提交函数，还原备份
					av('common-event').data.keyupFunctions['common-content'] = common_content_keyupFunctions;
				},
				success: function() {
					//渲染这个插件
					AVproject.compiler("reload").selector('#' + selector).render("refresh");
					$('[input-focus="page-shop-goodsAttr::skuEdit"]').focus(); //失去焦点
				}
			});

			//成功的时候 回调
			AVproject.data.successSubmitCallback = function(){
				av().run();
				layer.closeAll();
				//layer.close(layerid);
			}
		},
		//删除商城商品规格
		eventSkuRemove: function(ele) {
			var _this = this;
			var ids = this.checkboxData('data-id');
			this.actionRemoveIds(ids, function() {
				var request_array = [];
				for(var i in ids) {
					request_array.push(["SHOPADMINGOODSSKUREMOVE", [{
						shop_goods_sku_id: ids[i]
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
						av().render("refresh").run();
					}
				});
			});
		},
		
	}
});