av({
	id:'project-zrhzfw-orderDetails',//工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include : ["src/common/content.js"],//获取js文件
	extend : ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
	'export' : {
	template: "src/project/zrhzfw/orderDetails.html",},
	'import' : function(e){
			this.template(e.template);//绑定模版
		},
	
	main: function(){
		this.data.shop_order_id = (function(){try{ return av.router().anchor.query.id;}catch(e){return '';}}());
		if( !this.data.shop_order_id ){
			return av.router(av.router().url, '#/zrhzfw-orderList/').request();
		}
		this.data.request.data = ['SHOPADMINORDERDETAILS', [{shop_order_id:this.data.shop_order_id}]];
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
		
		projectShippingSend: {},
		
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
		}

	},
	
	

});