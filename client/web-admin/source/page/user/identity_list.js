WangAho({
	
	
	id:"user/identity_list",

	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	
	main : function(){
		var _project = WangAho(this.id);
		var config = {search:{}};
		var _http = http();
		
		//排序
		if( (function(){try{ return _http.anchor.query.sort;}catch(e){return false;}}()) ){
			config.sort = [_http.anchor.query.sort];
		}else{
			
			if( !_http.anchor.query ){
				_http.anchor.query = {};
			}
			WangAho().history_remove();//删除本页的记录
			_http.anchor.query.sort = "update_time_desc";
			http(_http).request();
			return false;
			
		}
		
		var search = (function(){try{ return _http.anchor.query.search;}catch(e){return false;}}());
		if( search ){
			search = _http.decode(search);
			config.search = JSON.parse(search);
		}
		//状态
		if( (function(){try{ return _http.anchor.query.state;}catch(e){return false;}}()) ){
			config.search.user_identity_state = _http.anchor.query.state;
		}
		
		//分页
		if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
			config.page = _http.anchor.query.page;
		}
		
		var request = {
			application_config:["APPLICATIONCONFIG"], 
			state_clear:["USERADMINIDENTITYSTATECLEAR"],
			list:["USERADMINIDENTITYLIST", [config]]
			};
		
		WangAho("index").data({
			request : request, 
			success : function(data){
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.list.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.list.error, {icon: 5, time: 2000});
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/user/identity_list.html", "#content"), data, {
					
					"user_identity-expire_time" :function(expire_time){
						if( expire_time < 60 ){
							return expire_time+"秒";
						}else
						if( expire_time >= 60 && expire_time < 3600){
							return (expire_time/60).toFixed(0)+"分";
						}else
						if( expire_time >= 60 && expire_time < 86400){
							return (expire_time/3600).toFixed(0)+"小时";
						}else{
							return (expire_time/86400).toFixed(0)+"天";
						}
						return "未知天数";
					},
					
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
			  	content: template( WangAho().template("page/user/identity_list.html", "#search"), function(fn){
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
			search.user_id = $.trim($('[name="user_id"]').val());
			search.user_phone = $.trim($('[name="user_phone"]').val());
			search.user_nickname = $.trim($('[name="user_nickname"]').val());
			
			search.user_parent_id = $.trim($('[name="user_parent_id"]').val());
			search.user_parent_phone = $.trim($('[name="user_parent_phone"]').val());
			
			search.real_name = $.trim($('[name="real_name"]').val());
			search.card_number = $.trim($('[name="card_number"]').val());
			search.card_address = $.trim($('[name="card_address"]').val());
			
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
			
			if(ids.length < 1){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			if("state_yes" == attr){
				
				layer.msg('你确定要认证成功么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消']
					,yes: function(index){
					    layer.close(index);
					    _project.state_yes(ids);
					}
				});
				return true;
			}
			if("state_not" == attr){
				layer.msg('你确定要认证失败么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消']
					,yes: function(index){
					    layer.close(index);
					    _project.state_not(ids);
					}
				});
				return true;
			}
			
			//删除
			if("remove" == attr){
				layer.msg('你确定要删除么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消']
					,yes: function(index){
					    layer.close(index);
					    _project.remove(ids);
					}
				});
			}
			
		});
		
		
		
	},
	
	
	
	//认证成功
	state_yes : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		request_array.push(["USERADMINIDENTITYEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in ids){
			request_array.push(["USERADMINIDENTITYEDIT", [{user_id:ids[i], user_identity_state:1}]]);
		}
		
		//提交数据
		WangAho("index").submit({
			method:"edit",
			request:request_array,
			success:function(data){
				//刷新页面
				WangAho("index").scroll_constant(function(){
					_project.main();
				});
			}
		});
		
	},
	
	
	
	//认证失败
	state_not : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		request_array.push(["USERADMINIDENTITYEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in ids){
			request_array.push(["USERADMINIDENTITYEDIT", [{user_id:ids[i], user_identity_state:0}]]);
		}
		
		//提交数据
		WangAho("index").submit({
			method:"edit",
			request:request_array,
			success:function(data){
				//刷新页面
				WangAho("index").scroll_constant(function(){
					_project.main();
				});
			}
		});
		
	},
	
	
	
	
	
	
	remove : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["ADMINREMOVE", [{admin_id:ids[i]}]]);
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



