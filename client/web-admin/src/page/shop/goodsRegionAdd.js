av({

	id: 'page-shop-goodsRegionAdd',
	include: ["src/common/content.js", 'src/module/citypicker/citypicker.js'],
	extend: ["common-content"],
	'export': {
		template: "src/page/shop/goodsRegionAdd.html"
	},
	'import': function(e) {
		this.template(e.template);
	},
	main: function() {
		//接收id参数
		this.data.getShopGoodsId = (function() {try {return av.router().anchor.query.id;} catch(e) {return '';}}());
		
	},
	event: {

		error: function(error) {
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/shop-goodsRegionAdd/').request();
		},
		renderEnd: function() {
			//调用layer的选择时间插件
			//开始时间

		},
		
		loadEnd: function() {
			this.render("refresh");
			this.data.initSelectedScope();
			av('module-citypicker').data.defaultLabel(['四川省', '绵阳市', '游仙区']);
			av('module-citypicker').render("refresh"); //渲染 城市选择器
			
			this.data.eventGetSingleGoodsInfo( $('[name="goods_id"]')[0] );
		}

	},
	data: {
		request: {},
		state: undefined,
		list: {
			data: [],
		},
		
		getShopGoodsId: '',
		getShopGoodsError: '', //获取商品数据时的错误信息
		getShopGoodsData: null, //商品数据
		submitLock: false, //默认无提交锁
		//获取单个商品信息
		eventGetSingleGoodsInfo: function(ele, e) {
			var _this = this;
			_this.getShopGoodsId = $(ele).val();
			if(_this.getShopGoodsId == '') {
				_this.getShopGoodsError = '';
				_this.getShopGoodsData = null;
				return false;
			}
			var form_input = {};
			form_input.shop_goods_id = _this.getShopGoodsId;
			
			var requestAPIObject = new requestAPI();
			requestAPIObject.abort();
			//提交数据
			requestAPIObject.submit({
				request : {
					g:["SHOPADMINGOODSQUERY",[form_input]],
					},
				callback:function(r){
					if( (function(){try{ return r.data.g.errno;}catch(e){return false;}}()) ){
						_this.getShopGoodsError = r.data.g.error;
						_this.getShopGoodsData = null;
					}else{
						_this.getShopGoodsError = '';
						_this.getShopGoodsData = r.data.g.data;
					}
				},
			});
			//请求单个商品信息
			/*this.submit({
				alert:false,
				method: "submit",
				request: ["SHOPADMINGOODSQUERY", [form_input]],
				error: function(r) {
					_this.getShopGoodsError = r;
					_this.getShopGoodsData = null;
				},
				success: function(r) {
					_this.getShopGoodsError = '';
					_this.getShopGoodsData = r;
				}
			});*/
		},
		
		
		/************************省市区选择相关	*************************/
		selectedScope: 1,
		//选中的范围
		eventSelectedScope: function(ele, e) {
			this.selectedScope = $(ele).val();
			this.initSelectedScope();

			console.log('eventSelectedScope', $(ele).val());
		},
		//初始化
		initSelectedScope: function() {

			if(this.selectedScope == 1) {
				av('module-citypicker').data.provinceShow = true;
				av('module-citypicker').data.cityShow = false;
				av('module-citypicker').data.areaShow = false;
			}

			if(this.selectedScope == 2) {
				av('module-citypicker').data.provinceShow = true;
				av('module-citypicker').data.cityShow = true;
				av('module-citypicker').data.areaShow = false;
			}

			if(this.selectedScope == 3) {
				av('module-citypicker').data.provinceShow = true;
				av('module-citypicker').data.cityShow = true;
				av('module-citypicker').data.areaShow = true;
			}
			//console.log('44444444444', av('module-citypicker').data.pickerValue );
			//console.log( 'initSelectedScope', this.selectedScope );
		},

		//按回车键时提交
		keyupFunction: function() {
			this.eventSubmit();
		},

		eventSubmit: function() {
			var _this = this;
			if(_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}

			var form_input = {};
			//商品范围 省市区
			form_input.shop_goods_region_scope = $.trim($('[name="agent_region_scope"]').val());

			if(form_input.shop_goods_region_scope == 1) {
				form_input.shop_goods_region_province = av('module-citypicker').data.provinceLabel;
			} else
			if(form_input.shop_goods_region_scope == 2) {
				form_input.shop_goods_region_province = av('module-citypicker').data.provinceLabel;
				form_input.shop_goods_region_city = av('module-citypicker').data.cityLabel;
			} else
			if(form_input.shop_goods_region_scope == 3) {
				form_input.shop_goods_region_province = av('module-citypicker').data.provinceLabel;
				form_input.shop_goods_region_city = av('module-citypicker').data.cityLabel;
				form_input.shop_goods_region_district = av('module-citypicker').data.areaLabel;
			}
			// "shop_goods_region_info": "商品信息",0087cf50c750716b8f2dd215478872476634
			form_input.shop_goods_id = this.getShopGoodsId; //商品ID
			form_input.shop_goods_region_state = $('[name="agent_region_state"]').is(':checked') ? 0 : 1; //商品状态0|1
		

			try {

				if(form_input.shop_goods_region_province == '' && form_input.shop_goods_region_city && form_input.shop_goods_region_district)
					throw "省市区选择不合法";
				if(form_input.shop_goods_id == '')
					throw '商品id不能为空';
				if(this.getShopGoodsError != '')
					throw this.getShopGoodsError;
			} catch(err) {
				layer.msg(err, {
					icon: 5,
					time: 2000
				});
				return _this.submitLock = false;
			}

			//console.log(form_input);
			//return false;
			//提交数据
			this.submit({
				method: "submit",
				request: ["SHOPADMINGOODSREGIONADD", [form_input]],
				error: function() {
					_this.submitLock = false;
				},
				success: function() {
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});

		}

	}

});