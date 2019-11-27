WangAho({
	
	
	id:"admin/admin_list",

	
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
		
		//分页
		if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
			config.page = _http.anchor.query.page;
		}
			
		if( (function(){try{ return http().anchor.query.parent_id;}catch(e){return false;}}()) ){
			config.search.parent_id = http().anchor.query.parent_id;
		}else{
			config.search.type = "parent";
			//config.page = "all";
		}
			
		WangAho("index").data({
			request : {
				list:["ADMINLIST", [config]]
				}, 
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
				WangAho("index").view(WangAho().template("page/admin/admin_list.html", "#content"), data);
				_project.event();
				
			}
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
		
		
		
	},
	
	
	
	
	sort : function(){
		var obj = WangAho("index").action_table("sort");
		if( !obj || !obj.length ){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		request_array.push(["ADMINEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["ADMINEDIT", [{admin_id:obj[i].id, admin_sort:obj[i].value}]]);
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




