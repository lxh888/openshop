av({
	id: 'page-shop-orderList', //工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include: ["src/common/content.js", "src/page/shop/orderDetails/checkArea.js"], //获取js文件
	extend: ["common-content"], //继承该js，只获取不继承无法获取该对象的属性
	'export': {
		template: "src/page/shop/orderList.html",
	},
	'import': function(e) {
		this.template(e.template); //绑定模版
	},

	main: function() {
		var _this = this;
		var config = {
			search: {}
		};

		//搜索
		this.data.routerAnchorQuery('search', function(data) {
			data = av.decodeURL(data);
			config.search = JSON.parse(data);
		});

		//排序
		this.data.routerAnchorQuery('sort', function(data) {
			config.sort = [data];
		});

		//分页
		this.data.routerAnchorQuery('page', function(data) {
			config.page = data;
		});

		//交易状态
		this.data.routerAnchorQuery('transaction_state', function(data) {
			_this.data.transaction_state = data;
			config.search.transaction_state = data; //状态
		}, function() {
			_this.data.transaction_state = undefined;
		});
		//订单状态
		this.data.routerAnchorQuery('state', function(data) {
			_this.data.state = data;
			config.search.state = data; //状态
		}, function() {
			_this.data.state = undefined;
		});
		//支付状态
		this.data.routerAnchorQuery('pay_state', function(data) {
			_this.data.pay_state = data;
			config.search.pay_state = data; //状态
		}, function() {
			_this.data.pay_state = undefined;
		});

		//物流状态
		this.data.routerAnchorQuery('shipping_state', function(data) {
			_this.data.shipping_state = data;
			config.search.shipping_state = data; //状态
		}, function() {
			_this.data.shipping_state = undefined;
		});

		this.data.request.list = ['SHOPADMINORDERLIST', [config]];

		//定义筛选
		this.data.search = [
			'shop_order_id',
			'user_id',
			'user_nickname',
			'user_phone',
			'user_address_consignee',
			'date_start',
			'date_end',
		];
		
		// 优利 区域列表
		if( this.data.applicationCheckYouli() ){
			this.data.request.areaList = ['SHOPORDERSELFREGIONIDLIST'];
			this.data.search.push('user_officer_name','user_officer_id', 'write_off_user_phone');
		}
		
		//易淘的代理地区选项
		if( this.data.applicationCheckYitaoshop() ){
			this.data.search.push('agentRangeSelect');
		}

	},
	event: {
		error: function(error) {
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},

	},
	//数据对象
	data: {
		request: {},
		search: [],
		list: {
			data: [],
		},

		//回收
		eventTrash: function(ele) {
			var ids = this.checkboxData('data-id');
			var _this = this;
			if(ids.length < 1) {
				layer.msg("请选择要操作的数据", {
					icon: 5,
					time: 2000
				});
				return false;
			}
			//回收站
			layer.msg('你确定要将数据丢进回收站么？(' + ids.length + '条数据)', {
				time: 0 //不自动关闭
					,
				btn: ['确定', '取消'],
				yes: function(index) {
					layer.close(index);
					var request_array = [];
					for(var i in ids) {
						request_array.push(["SHOPADMINORDERTRASH", [{
							shop_order_id: ids[i]
						}]]);
					}
					//提交数据
					_this.submit({
						method: "list",
						request: request_array,
						success: function(bool) {
							if(bool) {
								//刷新页面
								av().run();
							}
						}
					});
				}
			});
		},
		/*
		 优利 选择区域
		 * */
		projectArea: {},
		eventArea: function(shop_order_id) {
			var _this = this;
			console.log('shop_order_id', shop_order_id)
			layer.open({
				type: 1,
				shadeClose: true,
				shade: false,
				title: '选择区域',
				area: ['500px', '200px'], //自定义文本域宽高
				//content: $('#agentRegionOption') ,
				content: '<div id="checkArea"></div>',
				btn: ["确定", "取消"],
				success: function() {
					//如果工程已存在，不再重复创建
					if(!_this.projectArea[shop_order_id]) {
						_this.projectArea[shop_order_id] = av('orderDetails::checkArea').new();
					}
					_this.projectArea[shop_order_id].clone({
						areaList: _this.areaList,
					}).render('reload');
					console.log('_this.areaList', _this.areaList)
					av('orderDetails::checkArea')
				},
				yes: function(index) {
					var form_input = {};
					//订单id  物流状态  物流单号  快递类型
					form_input.region_id = _this.projectArea[shop_order_id].data.shop_order_area;
					form_input.shop_order_id = shop_order_id;
					console.log(form_input);
					try {
						if(form_input.region_id == null || form_input.region_id == -1) {
							throw '请选择区域!';
						}
					} catch(error) {
						layer.msg(error, {
							icon: 5,
							time: 2000
						});
						return;
					}
					//提交数据
					_this.submit({

						method: "submit",
						request: ["SHOPORDERSELFEDITWRITEOFFADDRESS", [form_input]],
						success: function(bool) {
							if(bool) {
								layer.close(index);
								//刷新页面
								av().run();
							}
						}
					});
				},
				btn2: function(index) {
					layer.close(index);
				}

			});
		},
		eventHexiaoOrder: function(shop_order_id){
			var _this=this;
		
			//提交数据
			_this.submit({
				method:"submit",
				request:["SHOPADMINORDERWRITEOFF",[{shop_order_id:shop_order_id}]],
				success:function(bool){
					if(bool) {
						//刷新页面
						av().compiler("reload").render().run();
					}
				}
			});
				
		}

	}
});