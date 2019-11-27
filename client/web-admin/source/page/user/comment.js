WangAho({
	
	
	id:"user/comment",
	
	
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
		
		//分页
		if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
			config.page = _http.anchor.query.page;
		}
		
		if( (function(){try{ return _http.anchor.query.state;}catch(e){return false;}}()) ){
			config.search.state = _http.anchor.query.state;
		}
		
		var request = {
				comment_module_option:["USERADMINCOMMENTMODULEOPTION"],
				list:["USERADMINCOMMENTLIST", [config]]
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
				_project.data = data;
				WangAho("index").view(WangAho().template("page/user/comment.html", "#content"), data, {
					
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
					
					"module-name" : function(module){
						if( _project.data.response.comment_module_option ){
							var comment_module_option = _project.data.response.comment_module_option;
							for(var i in comment_module_option){
								if(module == i){
									return comment_module_option[i];
								}
							}
						}else{
							return "未知";
						}
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
		$('select[name="user_comment_module"]').chosen({
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
			  	content: template( WangAho().template("page/user/comment.html", "#search"), function(fn){
					return fn({search:search, response:_project.data.response});
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
			search.user_nickname = $.trim($('[name="user_nickname"]').val());
			search.user_phone = $.trim($('[name="user_phone"]').val());
			search.type_module = $.trim($('[name="type_module"]').val());
			search.user_comment_id = $.trim($('[name="user_comment_id"]').val());
			search.user_comment_root_id = $.trim($('[name="user_comment_root_id"]').val());
			search.user_comment_parent_id = $.trim($('[name="user_comment_parent_id"]').val());
			
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
			
			if(ids.length < 1){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			
			//审核通过
			if("state_succeed" == attr){
				layer.msg('你确定要审核通过么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消']
					,yes: function(index){
					   	layer.close(index);
					   	_project.state_succeed(ids);
					}
				});
				return true;
			}
			
			//审核失败
			if("state_defeated" == attr){
				layer.msg('你确定要审核失败么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消']
					,yes: function(index){
					   	layer.close(index);
					   	_project.state_defeated(ids);
					}
				});
				return true;
			}
			
		});
		
		//调用 Chosen
		$('select[name="user_comment_module"]').chosen({
			width: '100%',
			//placeholder_text_single: '-', //默认值
			earch_contains:true, 
			no_results_text: "没有匹配结果",
			case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
        	//group_search: false //选项组是否可搜。此处搜索不可搜
		});
		
	},
	
	

	state_succeed : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		var request_array = [];
		for(var i in ids){
			request_array.push(["USERADMINCOMMENTEDIT", [{user_comment_id:ids[i], user_comment_state:1}]]);
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
	
	
	state_defeated : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		var request_array = [];
		for(var i in ids){
			request_array.push(["USERADMINCOMMENTEDIT", [{user_comment_id:ids[i], user_comment_state:0}]]);
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


	
	
	
	
	
});




