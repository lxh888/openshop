WangAho({
	
	
	id:"house/product_details",
	
	
	house_product_id : null,
	
	main : function(){
		var _project = WangAho(this.id);
		var config = {};
		var _http = http();
		_project.house_product_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if( !_project.house_product_id ){
			layer.msg("ID无效", {icon: 5, time: 2000});
			return false;
		}
		
		config.house_product_id = _project.house_product_id;
		
		WangAho("index").data({
			request : {
				get:["HOUSEADMINPRODUCTDETAILS",[config]]
				},
			success : function(data){
				
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.get.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.get.error, {icon: 5, time: 2000});
				}
				
				template( WangAho().template("page/house/product_details.html", "#content"), function(fn){
					WangAho().view( fn(data) );
				});
				
				_project.event();
			}
		});
		
	},
	
	
	event : function(){
		//查看图片
		WangAho("index").image_look_event();
		
		var _project = WangAho(this.id);
		$('.action-repeat').unbind("click").click(function(){
			WangAho("index").scroll_constant(function(){
				_project.main();
			});
		});
		
		//审核成功
		$('.action-state_yes').unbind("click").click(function(){
			layer.msg('你确定要审核成功么？', {time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
				    layer.close(index);
				    _project.state_yes(_project.house_product_id);
				}
			});
		});
		//审核失败
		$('.action-state_not').unbind("click").click(function(){
			layer.msg('你确定要审核失败么？', {time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
				    layer.close(index);
				    _project.state_not(_project.house_product_id);
				}
			});
		});
		
	},
	
	
	
		
	state_yes : function(id){
		if(!id){
			return false;
		}
		
		var _project = WangAho(this.id);
		var request_array = [];
		request_array.push(["HOUSEADMINPRODUCTSTATE", [{house_product_id:id, house_product_state:1}]]);
		
		//提交数据
		WangAho("index").submit({
			method:"remove",
			request:request_array,
			success:function(bool){
				if(bool){
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			}
		});
		
		
	},
	
	
	state_not : function(id){
		if(!id){
			return false;
		}
		
		var _project = WangAho(this.id);
		var request_array = [];
		request_array.push(["HOUSEADMINPRODUCTSTATE", [{house_product_id:id, house_product_state:0}]]);
		
		//提交数据
		WangAho("index").submit({
			method:"remove",
			request:request_array,
			success:function(bool){
				if(bool){
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			}
		});
		
		
	},
	
	
	
	
	
	
	
	
	
	
	
});