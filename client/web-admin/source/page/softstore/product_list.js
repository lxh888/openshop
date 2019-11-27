WangAho({
	
	
	id : "softstore/product_list",
	
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
			if(!_http.anchor.query){
				_http.anchor.query = {};
			}
			WangAho().history_remove();//删除本页的记录
			_http.anchor.query.sort = "update_time_desc";
			http(_http).request();
			return false;
		}
		
		//状态
		if( (function(){try{ return _http.anchor.query.state;}catch(e){return false;}}()) ){
			config.search.state = _http.anchor.query.state;
		}
		
		//分页
		if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
			config.page = _http.anchor.query.page;
		}
		
		WangAho("index").data({
			request : {
				softstore_type_option:["SOFTSTOREADMINTYPEOPTION", [{type:"son"}]],
				list:["SOFTSTOREADMINPRODUCTLIST", [config]]
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
				WangAho("index").view(WangAho().template("page/softstore/product_list.html", "#content"), data, {
					
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
	
	
	
	
	event : function(){
		
		var _this = WangAho(this.id);
		$('[action-button]').unbind("click").click(function(){
			var ids = WangAho("index").action_table_checked();
			var attr = $(this).attr("action-button");
			
			//排序
			if("sort" == attr){
				_this.sort();
				return true;
			}
			
			
			if(ids.length < 1){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			//发布
			if("state_publish" == attr){
				_this.state_publish(ids);
				return false;
			}
			
			//取消发布
			if("state_cancel_publish" == attr){
				_this.state_cancel_publish(ids);
				return false;
			}
			
			//回收站
			if("trash" == attr){
				layer.msg('你确定要将数据丢进回收站么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消'],yes: function(index){
												    layer.close(index);
												    _this.trash(ids);
												  }
				});
			}
			
		});
		
		
	},
	
	//排序
	sort : function(){
		var obj = WangAho("index").action_table("sort");
		if( !obj || !obj.length ){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		request_array.push(["SOFTSTOREADMINPRODUCTEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["SOFTSTOREADMINPRODUCTEDIT", [{ss_product_id:obj[i].id, ss_product_sort:obj[i].value}]]);
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
	
	
	//发布
	state_publish : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		request_array.push(["SOFTSTOREADMINPRODUCTEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in ids){
			request_array.push(["SOFTSTOREADMINPRODUCTEDIT", [{ss_product_id:ids[i], ss_product_state:1}]]);
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
	
	
	//取消发布
	state_cancel_publish : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		request_array.push(["SOFTSTOREADMINPRODUCTEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in ids){
			request_array.push(["SOFTSTOREADMINPRODUCTEDIT", [{ss_product_id:ids[i], ss_product_state:3}]]);
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
	
	
	trash : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["SOFTSTOREADMINPRODUCTTRASH", [{ss_product_id:ids[i]}]]);
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