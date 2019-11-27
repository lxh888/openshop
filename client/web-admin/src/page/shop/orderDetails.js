av({
	id:'page-shop-orderDetails',//工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include : ["src/common/content.js","src/page/shop/orderDetails/shippingSen.js","src/page/shop/orderDetails/checkArea.js"],//获取js文件
	extend : ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
	'export' : {
	template: "src/page/shop/orderDetails.html",},
	'import' : function(e){
			this.template(e.template);//绑定模版
		},
	
	main: function(){
		this.data.shop_order_id = (function(){try{ return av.router().anchor.query.id;}catch(e){return '';}}());
		if( !this.data.shop_order_id ){
			return av.router(av.router().url, '#/shop-orderList/').request();
		}
		this.data.request.data = ['SHOPADMINORDERDETAILS', [{shop_order_id:this.data.shop_order_id}]];
		this.data.request.expressType=['APPLICATIONADMINSHIPPINGOPTIONS',[{module:'express_order_shipping'}]];
		if( this.data.applicationCheckYouli() ){
			this.data.request.areaList=['SHOPORDERSELFREGIONIDLIST'];
		}
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
		data: null,
		shop_order_id : "",
		submitLock:false,
		//确定发货
		// eventSubmitShippingSend:function(){
		// 	var _this=this;
		// 	layer.prompt({
		// 		title: '确认发货，请输入运单号', 
		// 		formType: 2, 
		// 		area: ['500px', '200px'] //自定义文本域宽高
		// 	}, function(text, index){
		// 			layer.close(index);
		// 			//提交数据
		// 			_this.submit({
		// 				method:"submit",
		// 				request:["SHOPADMINORDERSHIPPING", [{
		// 								shop_order_id:_this.shop_order_id, 
		// 								shop_order_shipping_state:2, 
		// 								shop_order_shipping_no:text
		// 							}]],
		// 				error:function(){
		// 					// _this.submitLock = false;
		// 				},
		// 				success:function(){
		// 					// _this.submitLock = false;
		// 					//刷新页面
		// 					//av().compiler("reload").render().run();
		// 					av().run();
		// 				}
		// 			});
			    	
		// 	});
		// },
		projectShippingSend: {},
		eventSubmitShippingSend: function(orderid) {
			var _this = this;

			layer.open({
				type: 1,
				shadeClose: true,
				shade: false,
				title: '确认发货',
				area: ['500px', '300px'], //自定义文本域宽高
				//content: $('#agentRegionOption') ,
				content: '<div id="shippingSen"></div>',
				btn: ["确定", "取消"],
				success: function() {
					//如果工程已存在，不再重复创建
					if(!_this.projectShippingSend[orderid]) {
						_this.projectShippingSend[orderid] = av('orderDetails::shippingSen').new();
					}
					_this.projectShippingSend[orderid].clone({
						expressType: _this.expressType,
					}).render('reload');

				},
				yes: function(index) {
					var form_input = {};
					//订单id  物流状态  物流单号  快递类型
					form_input.shop_order_id=_this.shop_order_id;
					form_input.shop_order_shipping_state = 2;
					form_input.shop_order_shipping_no = _this.projectShippingSend[orderid].data.shop_order_shipping_no;
					form_input.shop_order_shipping_id= _this.projectShippingSend[orderid].data.shop_order_express_type;
					console.log(form_input);
					try {
						if(form_input.shop_order_shipping_id==null || form_input.shop_order_shipping_id==-1){throw '请选择快递类型!';}
						if(form_input.shop_order_shipping_no==null||form_input.shop_order_shipping_no==''){throw '快递单号不能为空!';}
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
						request: ["SHOPADMINORDERSHIPPING", [form_input]],
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
		//确定收货
		eventSubmitShippingTake:function(){
			var _this=this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			layer.msg('你确认收货么？', {time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
					layer.close(index);
					//提交数据
					_this.submit({
						method:"submit",
						request:["SHOPADMINORDERSHIPPING", [{
										shop_order_id:_this.shop_order_id, 
										shop_order_shipping_state:1
									}]],
						error:function(){
							 _this.submitLock = false;
						},
						success:function(){
							 _this.submitLock = false;
							//刷新页面
							//av().compiler("reload").render().run();
							av().run();
						}
					});
				}
				,end:function(){//弹框销毁时取消锁
					_this.submitLock = false;
				}
			});
		},
		eventSubmitGoodsArrival:function(){
			var _this=this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			layer.msg('你确定要更改订单状态为已到货么？', {time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
					layer.close(index);
					//提交数据
					_this.submit({
						method:"submit",
						request:["SHOPADMINORDERCONFIRMGOODS", [{
										id:_this.shop_order_id, 
										
									}]],
						error:function(){
							 _this.submitLock = false;
						},
						success:function(){
							 _this.submitLock = false;
							//刷新页面
							//av().compiler("reload").render().run();
							av().run();
						}
					});
				}
				,end:function(){//弹框销毁时取消锁
					_this.submitLock = false;
				}
			});
		},
		//取消订单close_state
		eventSubmitCloseState:function(){
		var _this=this;
		if( _this.submitLock ){
			return false;
		}else{
			_this.submitLock = true;
		}
		layer.msg('你要取消订单么？', {time: 0 //不自动关闭
			,btn: ['确定', '取消']
			,yes: function(index){
				layer.close(index);
				//提交数据
				_this.submit({
					method:"submit",
					request:["SHOPADMINORDERSTATE", [{
									shop_order_id:_this.shop_order_id, 
									shop_order_state:0
								}]],
					error:function(){
						 _this.submitLock = false;
					},
					success:function(){
						 _this.submitLock = false;
						//刷新页面
						//av().compiler("reload").render().run();
						av().run();
					}
				});
			}
			,end:function(){//弹框销毁时取消锁
				_this.submitLock = false;
			}
		});
	},
		//确定订单confirm_state
		eventSubmitConfirmState:function(){
			var _this=this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			layer.msg('你要确定订单状态么？', {time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
					layer.close(index);
					//提交数据
					_this.submit({
						method:"submit",
						request:["SHOPADMINORDERSTATE", [{
										shop_order_id:_this.shop_order_id, 
										shop_order_state:1
									}]],
						error:function(){
							 _this.submitLock = false;
						},
						success:function(){
							 _this.submitLock = false;
							//刷新页面
							//av().compiler("reload").render().run();
							av().run();
						}
					});
				}
				,end:function(){//弹框销毁时取消锁
					_this.submitLock = false;
				}
			});
		},
		//还原订单restore
		eventSubmitRestore:function(){
			var _this=this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			layer.msg('你要将该订单从回收中还原么？', {time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
					layer.close(index);
					//提交数据
					_this.submit({
						method:"submit",
						request:["SHOPADMINORDERTRASHRESTORE", [{shop_order_id:_this.shop_order_id}]],
						error:function(){
							 _this.submitLock = false;
						},
						success:function(){
							 _this.submitLock = false;
							//刷新页面
							//av().compiler("reload").render().run();
							av().run();
						}
					});
				}
				,end:function(){//弹框销毁时取消锁
					_this.submitLock = false;
				}
			});
		},
		//回收订单trash
		eventSubmitTrash:function(){
			var _this=this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			layer.msg('你要回收订单么？', {time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
					layer.close(index);
					//提交数据
					_this.submit({
						method:"submit",
						request:["SHOPADMINORDERTRASH", [{shop_order_id:_this.shop_order_id}]],
						error:function(){
							 _this.submitLock = false;
						},
						success:function(){
							 _this.submitLock = false;
							//刷新页面
							//av().compiler("reload").render().run();
							av().run();
						}
					});
				}
				,end:function(){//弹框销毁时取消锁
					_this.submitLock = false;
				}
			});
		},
		/*
	 优利 选择区域
	 * */
	projectArea: {},
	eventArea: function(shop_order_id) {
			var _this = this;
			console.log('shop_order_id',shop_order_id)
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
					console.log('_this.areaList',_this.areaList)
					av('orderDetails::checkArea')
				},
				yes: function(index) {
					var form_input = {};
					//订单id  物流状态  物流单号  快递类型
					form_input.region_id= _this.projectArea[shop_order_id].data.shop_order_area;
					form_input.shop_order_id=shop_order_id;
					console.log(form_input);
					try {
						if(form_input.region_id==null || form_input.region_id==-1){throw '请选择区域!';}
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
		}

	},
	
	

});