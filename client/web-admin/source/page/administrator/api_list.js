WangAho({
	
	
	id:"administrator/api_list",

	
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
			config.search.state = _http.anchor.query.state;
		}
		if( (function(){try{ return _http.anchor.query.administrator;}catch(e){return false;}}()) ){
			config.search.administrator = _http.anchor.query.administrator;
		}
		if( (function(){try{ return _http.anchor.query.admin;}catch(e){return false;}}()) ){
			config.search.admin = _http.anchor.query.admin;
		}
		
		//分页
		if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
			config.page = _http.anchor.query.page;
		}
			
		var request = {
			module_option:["ADMINISTRATORMODULEOPTION",[{sort:["sort_asc","update_time_asc"]}]],
			list:["ADMINISTRATORADMINAPILIST", [config]]
			};
		if( config.search.module_id ){
			request.module_get = ["ADMINISTRATORMODULEGET", [{module_id:config.search.module_id}]];
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
				WangAho("index").view(WangAho().template("page/administrator/api_list.html", "#content"), data, {
					
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
					},
					
					"anchor-query-administrator" : function(s){
						var h = http();
						if(!h.anchor.query) h.anchor.query = {};
						if(typeof s == 'undefined'){
							if(typeof h.anchor.query.administrator != 'undefined') delete h.anchor.query.administrator;
						}else{
							h.anchor.query.administrator = s;
						}
						return http(h).href;
					},
					
					"anchor-query-admin" : function(s){
						var h = http();
						if(!h.anchor.query) h.anchor.query = {};
						if(typeof s == 'undefined'){
							if(typeof h.anchor.query.admin != 'undefined') delete h.anchor.query.admin;
						}else{
							h.anchor.query.admin = s;
						}
						return http(h).href;
					}
					
				});
				_project.event();
				_project.search_event();
				
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
		
		//调用 Chosen
		$('select[name="module_id"]').chosen({
			width: '100%',
			//placeholder_text_single: '-', //默认值
			earch_contains:true, 
			no_results_text: "没有匹配结果",
			case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
        	//group_search: false //选项组是否可搜。此处搜索不可搜
		});
		
		
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
			  	content: template( WangAho().template("page/administrator/api_list.html", "#search"), function(fn){
					return fn({search:search, module_option : _project.data.response.module_option});
					})
			});
			
			$('input[search]').first().focus();//失去焦点
			_project.search_event();
		});
		
		$('[name="search-submit"]').unbind("click").click(function(){
			var _http = http();
			if( !_http.anchor.query ) _http.anchor.query = {};
			var search = {};
			search.api_id = $.trim($('[name="api_id"]').val());
			search.like_api_id = $.trim($('[name="like_api_id"]').val());
			search.api_name = $.trim($('[name="api_name"]').val());
			search.module_id = $.trim($('[name="module_id"]').val());
			
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
		
		$('.open-api-details').click(function(){
			var title = $(this).attr("data-name");
			var id = $(this).attr("data-id");
			var href = http(http().href, '#/administrator/api_details/?id='+id).href;
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
		
		
	},
	
	
	
	/**
	 * 排序
	 */
	sort : function(){
		var obj = WangAho("index").action_table("sort");
		if( !obj || !obj.length ){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		request_array.push(["ADMINISTRATORADMINAPIEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["ADMINISTRATORADMINAPIEDIT", [{primary_key:obj[i].id, api_sort:obj[i].value}]]);
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
			request_array.push(["ADMINISTRATORADMINAPIREMOVE", [{api_id:ids[i]}]]);
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




