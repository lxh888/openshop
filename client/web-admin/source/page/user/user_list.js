WangAho({
	
	
	id:"user/user_list",

	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	
	data: null,
	
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
			_http.anchor.query.sort = "register_time_desc";
			http(_http).request();
			return false;
			
		}
		
		var search = (function(){try{ return _http.anchor.query.search;}catch(e){return false;}}());
		if( search ){
			search = _http.decode(search);
			config.search = JSON.parse(search);
		}
		
		//分页
		if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
			config.page = _http.anchor.query.page;
		}
		
		var request = {application_config:["APPLICATIONCONFIG"], list:["USERADMINLIST", [config]]};
		if( config.search.user_parent_id ){
			request.user_parent = ["USERADMINGET", [{user_id:config.search.user_parent_id}]];
		}
		
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
				_project.data = data;
				WangAho("index").view(WangAho().template("page/user/user_list.html", "#content"), data);
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
			  	content: template( WangAho().template("page/user/user_list.html", "#search"), function(fn){
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
			search.user_parent_nickname = $.trim($('[name="user_parent_nickname"]').val());
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
			
			
			//正常
			if("unban" == attr){
				_project.unban(ids);
				return true;
			}
			//封禁
			if("banned" == attr){
				_project.banned(ids);
				return true;
			}
			
			
			if(ids.length < 1){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			//删除
			if("remove" == attr){
				layer.msg('你确定要删除么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消'],yes: function(index){
												    layer.close(index);
												    _project.remove(ids);
												  }
				});
			}
			
		});
		
		
		$('.user_json').unbind("click").click(function(){
			var user_id = $(this).attr('data-id');
			if( !user_id ){
				layer.msg("用户ID异常", {icon: 5, time: 2000});
			}
			
			if( !_project.data || 
				!_project.data.response || 
				!_project.data.response.list || 
				!_project.data.response.list.data ){
				layer.msg("列表数据异常", {icon: 5, time: 2000});
			}
				
			var list_data = _project.data.response.list.data;
			var user_data = null;
			for(var i in list_data){
				if(list_data[i].user_id == user_id){
					user_data = list_data[i];
					break;
				}
			}
			
			if( !user_data ){
				layer.msg("用户数据异常", {icon: 5, time: 2000});
			}
			
			//格式化对象
			if( typeof user_data.user_json == 'string' && user_data.user_json){
				user_data.user_json = JSON.parse(user_data.user_json);
			}
			user_data.qiniu_domain = _project.data.response.application_config.qiniu_domain;
			
			layer.closeAll();
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-filter\"></span> 用户信息",
			  	type: 1,
			  	shadeClose: true,
			  	area: 'auto', //宽高
			  	maxWidth: $(window).width() > 1200? 1200 : $(window).width()-50,
			  	maxHeight: $(window).height()-50,
			  	content: template( WangAho().template("page/user/user_list.html", "#info"), function(fn){
					return fn(user_data);
					})
			});
			
			
			console.log( _project.data.response.list.data );
			_project.event();
		});
		
		
	},
	
	
	
	//正常
	unban : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		request_array.push(["USERADMINEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in ids){
			request_array.push(["USERADMINEDIT", [{user_id:ids[i], user_state:1}]]);
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
	
	
	
	//封禁
	banned : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["USERADMINEDIT", [{user_id:ids[i], user_state:0}]]);
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




