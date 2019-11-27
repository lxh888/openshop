WangAho({
	
	
	id:"shop/order_details",
	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	data : {},
	shop_order_id : "",
	
	main : function(){
		var config = {search:{}};
		var _project = WangAho(this.id);
		var _http = http();
		_project.shop_order_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if(!_project.shop_order_id){
			http("#/shop/order_list").request();
			return false;
		}
		
		config.shop_order_id = _project.shop_order_id;
		
		WangAho("index").data({
			request : {
				get:["SHOPADMINORDERDETAILS", [config]]
				},
			success: function(data){
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.get.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.get.error, {icon: 5, time: 2000});
				}
				
				//判断数据是否存在
				if( !(function(){try{ return data.response.get.shop_order_id;}catch(e){return false;}}()) ){
					setTimeout(function(){
						if( WangAho().history_previous_exist() ){
							WangAho().history_previous();//存在上一页则返回上一页
						}else{
							WangAho().history_remove();//删除本页的记录
							http("#/shop/order_list").request();//返回列表页
						}
						
					}, 2000);
					return false;
				}
				
				_project.data = data;
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/shop/order_details.html", "#content"), data);
				
				_project.event();
			}
		});
		
	},
	
	
	
	keyup : function(){
		//按回车键时提交
		$(document).unbind("keyup").on('keyup', function(e){
			if(e.keyCode === 13){
				if( $("textarea").is(":focus") ){  
			        return false;
			    }
				
		       	//$('[name="contact_notes-submit"]').first().trigger("click");
			}
		});
	},
	
	
	event : function(){
		var _project = WangAho(this.id);
		_project.keyup();
		
		$('.shipping_send').unbind("click").click(function(){
			layer.prompt({
				title: '确认发货，请输入运单号', 
				formType: 2, 
				area: ['500px', '200px'] //自定义文本域宽高
			}, function(text, index){
			    	layer.close(index);
			    	_project.shipping_send(text);
			    	
			});
		});
		
		$('.shipping_take').unbind("click").click(function(){
			layer.msg('你确认收货么？', {time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
				    layer.close(index);
				    _project.shipping_take();
				}
			});
		});
		
		$('.close_state').unbind("click").click(function(){
			layer.msg('你要取消订单么？', {time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
				    layer.close(index);
				    _project.close_state();
				}
			});
		});
		
		$('.confirm_state').unbind("click").click(function(){
			layer.msg('你要确定订单状态么？', {time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
				    layer.close(index);
				    _project.confirm_state();
				}
			});
		});
		
		$('.trash').unbind("click").click(function(){
			layer.msg('你要回收订单么？', {time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
				    layer.close(index);
				    _project.trash();
				}
			});
		});
		
		$('.restore').unbind("click").click(function(){
			layer.msg('你要将该订单从回收中还原么？', {time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
				    layer.close(index);
				    _project.restore();
				}
			});
		});
		
	},
	
	
	//还原订单
	restore : function(){
		var _project = WangAho(this.id);
		//提交数据
		WangAho("index").submit({
			method:"submit",
			request:["SHOPADMINORDERTRASHRESTORE", [{shop_order_id:_project.shop_order_id}]],
			error:function(){
				$btn.removeClass('disabled');
			},
			success:function(data){
				//刷新页面
				WangAho("index").scroll_constant(function(){
					_project.main();
				});
			}
			
		});
		
	},
	
	
	//回收订单
	trash : function(){
		var _project = WangAho(this.id);
		//提交数据
		WangAho("index").submit({
			method:"submit",
			request:["SHOPADMINORDERTRASH", [{shop_order_id:_project.shop_order_id}]],
			error:function(){
				$btn.removeClass('disabled');
			},
			success:function(data){
				//刷新页面
				WangAho("index").scroll_constant(function(){
					_project.main();
				});
			}
			
		});
		
	},
	
	
	//确定订单
	confirm_state : function(){
		var _project = WangAho(this.id);
		
		//提交数据
		WangAho("index").submit({
			method:"submit",
			request:["SHOPADMINORDERSTATE", [{
				shop_order_id:_project.shop_order_id, 
				shop_order_state:1
			}]],
			error:function(){
				$btn.removeClass('disabled');
			},
			success:function(data){
				//刷新页面
				WangAho("index").scroll_constant(function(){
					_project.main();
				});
			}
			
		});
		
	},
	
	
	//关闭订单
	close_state : function(){
		var _project = WangAho(this.id);
		
		//提交数据
		WangAho("index").submit({
			method:"submit",
			request:["SHOPADMINORDERSTATE", [{
				shop_order_id:_project.shop_order_id, 
				shop_order_state:0
			}]],
			error:function(){
				$btn.removeClass('disabled');
			},
			success:function(data){
				//刷新页面
				WangAho("index").scroll_constant(function(){
					_project.main();
				});
			}
			
		});
		
	},
	
	
	
	//确定发货
	shipping_send : function(shop_order_shipping_no){
		var _project = WangAho(this.id);
		
		//提交数据
		WangAho("index").submit({
			method:"submit",
			request:["SHOPADMINORDERSHIPPING", [{
				shop_order_id:_project.shop_order_id, 
				shop_order_shipping_state:2, 
				shop_order_shipping_no:shop_order_shipping_no
			}]],
			error:function(){
				$btn.removeClass('disabled');
			},
			success:function(data){
				//刷新页面
				WangAho("index").scroll_constant(function(){
					_project.main();
				});
			}
		});
		
	},
	
	
	//确定收货
	shipping_take : function(){
		var _project = WangAho(this.id);
		
		//提交数据
		WangAho("index").submit({
			method:"submit",
			request:["SHOPADMINORDERSHIPPING", [{
				shop_order_id:_project.shop_order_id, 
				shop_order_shipping_state:1
			}]],
			error:function(){
				$btn.removeClass('disabled');
			},
			success:function(data){
				//刷新页面
				WangAho("index").scroll_constant(function(){
					_project.main();
				});
			}
			
		});
		
		
	},
	
	
	
});