WangAho({
	
	
	id:"merchant/user_list",
	
	
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
			config.search.merchant_user_state = _http.anchor.query.state;
		}
		
		//分页
		if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
			config.page = _http.anchor.query.page;
		}
			
		var request = {
				application_config:["APPLICATIONCONFIG"],
				list:["MERCHANTADMINUSERLIST", [config]]
			};
		if( config.search.merchant_id ){
			request.merchant_get = ["MERCHANTADMINGET", [{merchant_id : config.search.merchant_id}]];
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
				WangAho("index").view(WangAho().template("page/merchant/user_list.html", "#content"), data, {
					
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
				
				$('[name="add-submit"]').first().trigger("click");
				$('[name="edit-submit"]').first().trigger("click");
				$('[name="search-submit"]').first().trigger("click");
			}
		});
	},
	
	
	search_event : function(){
		var _project = WangAho(this.id);
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
			  	content: template( WangAho().template("page/merchant/user_list.html", "#search"), function(fn){
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
			search.merchant_user_name = $.trim($('[name="merchant_user_name"]').val());
			search.user_id = $.trim($('[name="user_id"]').val());
			search.user_phone = $.trim($('[name="user_phone"]').val());
			search.user_nickname = $.trim($('[name="user_nickname"]').val());
			search.merchant_id = $.trim($('[name="merchant_id"]').val());
			search.merchant_name = $.trim($('[name="merchant_name"]').val());
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
		this.keyup();
		
		//筛选
		this.search_event();
		
		
		$('[action-button]').unbind("click").click(function(){
			var ids = WangAho("index").action_table_checked();
			var attr = $(this).attr("action-button");
			
			if(ids.length < 1){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			//正常
			if("unban" == attr){
				
				layer.msg('你确定要认证成功么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消']
					,yes: function(index){
					  	layer.close(index);
					    _project.unban(ids);
					  	}
				});
				return true;
			}
			//封禁
			if("banned" == attr){
				layer.msg('你确定要认证失败么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消']
					,yes: function(index){
					  	layer.close(index);
					    _project.banned(ids);
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
				return true;
			}
			
		});
		
		
		//调用 Chosen
		$('select[name="merchant_user_state"]').chosen({
			width: '100%',
			//placeholder_text_single: '-', //默认值
			earch_contains: true,
			no_results_text: "没有匹配结果",
			case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
        	//group_search: false //选项组是否可搜。此处搜索不可搜
		});
		
		//添加商家用户
		$(".merchant_user_add").unbind("click").click(function(){
			layer.closeAll();
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-plus\"></span> 添加商家用户",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/merchant/user_list.html", "#add"), function(fn){
							return fn(_project.data);
							})
			});
			
			
			$('input[name="user"]').first().focus();//失去焦点
			_project.event();
			
		});
		
		
		$('[name="add-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.merchant_id = _project.data.response.merchant_get.merchant_id;
			form_input.user = $.trim($('[name="user"]').val());
			form_input.merchant_user_name = $.trim($('[name="merchant_user_name"]').val());
			form_input.merchant_user_info = $.trim($('[name="merchant_user_info"]').val());
			form_input.merchant_user_state = $.trim($('[name="merchant_user_state"]').val());
			
			try {
				if(form_input.merchant_id == '') throw "商家ID异常";
				if(form_input.user == '') throw "用户不能为空，请输入用户ID或者用户手机号";
				if(form_input.merchant_user_name == '') throw "商家用户名称不能为空";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["MERCHANTADMINUSERADD", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					$btn.removeClass('disabled');
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
		});
		
	
		
		$(".merchant_user_edit").unbind("click").click(function(){
			layer.closeAll();
			var merchant_user_id = $(this).attr("data-id");
			var merchant_user_list = (function(){try{ return _project.data.response.list.data;}catch(e){return false;}}());
			if( !merchant_user_list ){
				layer.msg("商家用户数据不存在", {icon: 5, time: 2000});
				return false;
			}
			
			var merchant_user_data = null;
			for(var i in merchant_user_list){
				if( merchant_user_list[i].merchant_user_id == merchant_user_id ){
					merchant_user_data = merchant_user_list[i];
					break;
				}
			}
			
			if( !merchant_user_data ){
				layer.msg("商家用户数据不存在", {icon: 5, time: 2000});
				return false;
			}
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-edit\"></span> 编辑商家用户",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/merchant/user_list.html", "#edit"), function(fn){
					return fn({merchant_user : merchant_user_data});
					})
			});
			
			$('input[name="user"]').first().focus();//失去焦点
			_project.event();
		});
		
		
		$('[name="edit-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.merchant_user_id = $.trim($('[name="merchant_user_id"]').val());
			form_input.merchant_user_name = $.trim($('[name="merchant_user_name"]').val());
			form_input.merchant_user_info = $.trim($('[name="merchant_user_info"]').val());
			form_input.merchant_user_state = $.trim($('[name="merchant_user_state"]').val());
			
			try {
				if(form_input.merchant_user_id == '') throw "商家用户ID不能为空";
				if(form_input.merchant_user_name == '') throw "商家用户名称不能为空";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["MERCHANTADMINUSEREDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					$btn.removeClass('disabled');
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
		});
		
		
		
		
		
		
	},
	
	
	
	//正常
	unban : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["MERCHANTADMINUSEREDIT", [{merchant_user_id:ids[i], merchant_user_state:1}]]);
		}
		
		//提交数据
		WangAho("index").submit({
			method:"remove",
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
			request_array.push(["MERCHANTADMINUSEREDIT", [{merchant_user_id:ids[i], merchant_user_state:0}]]);
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
			request_array.push(["MERCHANTADMINUSERREMOVE", [{merchant_user_id:ids[i]}]]);
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




