WangAho({
	
	
	id:"softstore/order_list",
	
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
		}
		//状态
		if( (function(){try{ return _http.anchor.query.contact_state;}catch(e){return false;}}()) ){
			config.search.contact_state = _http.anchor.query.contact_state;
		}
		
		if( !config.sort || !config.search.contact_state ){
			if( !_http.anchor.query ){
				_http.anchor.query = {};
			}
			WangAho().history_remove();//删除本页的记录
			if( !config.sort ){
				_http.anchor.query.sort = "time_desc";
			}else{
				_http.anchor.query.sort = config.sort;
			}
			
			if( !config.search.contact_state ){
				_http.anchor.query.contact_state = "0";
			}else{
				_http.anchor.query.contact_state = config.search.contact_state;
			}
			
			http(_http).request();
			return false;
		}
		
		
		
		//分页
		if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
			config.page = _http.anchor.query.page;
		}
		
		WangAho("index").data({
			request : {
				list:["SOFTSTOREADMINORDERLIST", [config]]
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
				WangAho("index").view(WangAho().template("page/softstore/order_list.html", "#content"), data, {
					"anchor-query-contact_state" : function(s){
						var h = http();
						if(!h.anchor.query) h.anchor.query = {};
						if(typeof s == 'undefined'){
							if(typeof h.anchor.query.contact_state != 'undefined') delete h.anchor.query.contact_state;
						}else{
							h.anchor.query.contact_state = s;
						}
						return http(h).href;
					}
				});
				_project.event();
			}
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
		
		
	},
	
	
	
	//回收站
	trash : function(ids){
		
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["SOFTSTOREADMINORDERTRASH", [{ss_order_id:ids[i]}]]);
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