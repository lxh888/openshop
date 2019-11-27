WangAho({
	
	
	id:"house/product_list",
	
	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	data : null,
	
	main : function(){
		var _project = WangAho(this.id);
		var config = {search:{}};
		var _http = http();
		//排序
		if( (function(){try{ return _http.anchor.query.sort;}catch(e){return false;}}()) ){
			config.sort = [_http.anchor.query.sort];
		}else{
			if(!_http.anchor.query){
				_http.anchor.query = {};
			}
			WangAho().history_remove();//删除本页的记录
			_http.anchor.query.sort = "update_time_desc";
			_http.anchor.query.state = 2;
			http(_http).request();
			return false;
		}
		
		var search = (function(){try{ return _http.anchor.query.search;}catch(e){return false;}}());
		if( search ){
			search = _http.decode(search);
			config.search = JSON.parse(search);
		}
		
		if( (function(){try{ return _http.anchor.query.state;}catch(e){return false;}}()) ){
			config.search.state = _http.anchor.query.state;
		}
		
		//分页
		if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
			config.page = _http.anchor.query.page;
		}
			
		WangAho("index").data({
			request : {
				list:["HOUSEADMINPRODUCTLIST", [config]]
				}, 
			success : function(data){
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.list.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.list.error, {icon: 5, time: 2000});
				}
				
				_project.data = data;
				//获得配置数据
				WangAho("index").view(WangAho().template("page/house/product_list.html", "#content"), data, {
					
					"anchor-query-state" : function(s){
						var h = http();
						if(!h.anchor.query) h.anchor.query = {};
						if(typeof s == 'undefined'){
							if(typeof h.anchor.query.state != 'undefined') delete h.anchor.query.state;
						}else{
							h.anchor.query.state = s;
						}
						
						if(typeof h.anchor.query.page != 'undefined') delete h.anchor.query.page;
						return http(h).href;
					}
					
				
				});
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
				
				$('[name="search-submit"]').first().trigger("click");
				$('[name="edit-submit"]').first().trigger("click");
			}
		});
	},
	
	
			
	search_event : function(){
		var _project = WangAho(this.id);
		_project.keyup();
		//筛选
		$(".search").unbind("click").click(function(){
			layer.closeAll();
			var _http = http();
			var search = (function(){try{ return _http.anchor.query.search;}catch(e){return false;}}());
			if( search ){
				search = _http.decode(search);
				search = JSON.parse(search);
			}
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-filter\"></span> 筛选",
			  	type: 1,
			  	shadeClose: true,
			  	area: 'auto', //宽高
			  	maxWidth: $(window).width() > 1200? 1200 : $(window).width()-50,
			  	maxHeight: $(window).height()-50,
			  	content: template( WangAho().template("page/house/product_list.html", "#search"), function(fn){
					return fn({search:search});
					})
			});
			
			$('input[search]').first().focus();//失去焦点
			_project.search_event();
		});
		
		$('[name="search-submit"]').unbind("click").click(function(){
			var _http = http();
			if( !_http.anchor.query ) _http.anchor.query = {};
			
			var search = {};
			search.house_product_id = $.trim($('[name="house_product_id"]').val());
			search.house_product_name = $.trim($('[name="house_product_name"]').val());
			search.user_id = $.trim($('[name="user_id"]').val());
			search.user_nickname = $.trim($('[name="user_nickname"]').val());
			search.user_phone = $.trim($('[name="user_phone"]').val());
			
			for(var i in search){
				if(search[i] == ""){
					delete search[i];
				}
			}
			
			search = JSON.stringify(search);
			if(search == "{}"){
				if( _http.anchor.query.search ) delete _http.anchor.query.search;
			}else{
				_http.anchor.query.search = _http.encode(search);
			}
			
			http(_http).request();
			layer.closeAll();
		});
		
		//清理筛选
		$('[name="search-clear"]').unbind("click").click(function(){
			var _http = http();
			if( _http.anchor.query && _http.anchor.query.search ){
				delete _http.anchor.query.search;
			}
			
			http(_http).request();
			layer.closeAll();
		});
		
	},
	

	
	
	event : function(){
		
		var _project = WangAho(this.id);
		_project.search_event();
		
		$('[action-button]').unbind("click").click(function(){
			var ids = WangAho("index").action_table_checked();
			var attr = $(this).attr("action-button");
			
			//排序
			if("sort" == attr){
				_project.sort();
				return true;
			}
			
			if(ids.length < 1){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			//审核成功
			if("state_yes" == attr){
				layer.msg('你确定要审核成功么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消']
					,yes: function(index){
					    layer.close(index);
					    _project.state_yes(ids);
					}
				});
			}
			
			
			//审核失败
			if("state_not" == attr){
				layer.msg('你确定要审核失败么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消']
					,yes: function(index){
					    layer.close(index);
					    _project.state_not(ids);
					}
				});
			}
			
			//回收站
			if("trash" == attr){
				layer.msg('你确定要将数据丢进回收站么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消'],yes: function(index){
												    layer.close(index);
												    _project.trash(ids);
												  }
				});
			}
			
		});
		
		
		$('.open-details').unbind('click').click(function(){
			var title = $(this).attr("data-name");
			var id = $(this).attr("data-id");
			var href = http(http().href, '#//house/product_details/?id='+id).href;
			layer.open({
		      type: 2,
		      title: title+"“"+id+"”",
		      shadeClose: true,
		      shade: false,
		      maxmin: true, //开启最大化最小化按钮
		      area: ['893px', '600px'],
		      content: href
		    });
		});
		
		
		
		$('.open-edit').unbind('click').click(function(){
			var product_id = $(this).attr('data-id');
			if( !product_id ){
				layer.msg("产品ID不存在", {icon: 5, time: 3000});
				return false;
			}
			
			var product_data = null;
			if( _project.data.response.list.data ){
				var response_list_data = _project.data.response.list.data;
				for(var i in response_list_data){
					if(response_list_data[i].house_product_id == product_id){
						product_data = response_list_data[i];
						break;
					}
				}
			}
			
			layer.closeAll();
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-edit\"></span> 编辑",
			  	type: 1,
			  	shadeClose: false,
			  	area: 'auto', //宽高
			  	maxWidth: $(window).width() > 1200? 1200 : $(window).width()-50,
			  	maxHeight: $(window).height()-50,
			  	content: template( WangAho().template("page/house/product_list.html", "#edit"), function(fn){
					return fn({product:product_data});
					})
			});
			
			$('input').eq(1).focus();//失去焦点
			_project.event();
		});
		
		
		_project.edit();
		
	},
	
	
	
	
	edit : function(){
		var _project = WangAho(this.id);
		$('[name="edit-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.house_product_id = $.trim($('[name="house_product_id"]').val());
			form_input.wechat_group_id = $.trim($('[name="wechat_group_id"]').val());
			
			try {
				if(form_input.house_product_id == '') throw "产品ID异常";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["HOUSEADMINPRODUCTEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					layer.closeAll();
					$btn.removeClass('disabled');
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
			
		});
		
	},
	
	
	
	
	
	
	state_yes : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		var request_array = [];
		for( var i in ids ){
			request_array.push(["HOUSEADMINPRODUCTSTATE", [{house_product_id:ids[i], house_product_state:1}]]);
		}
		
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
	
	
	state_not : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		var request_array = [];
		for( var i in ids ){
			request_array.push(["HOUSEADMINPRODUCTSTATE", [{house_product_id:ids[i], house_product_state:0}]]);
		}
		
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
	
	
	
	
	//回收站
	trash : function(ids){
		
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["HOUSEADMINPRODUCTTRASH", [{house_product_id:ids[i]}]]);
		}
		
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
		
		
	}
	
	
	
	
	
	
	
	
	
	
});




