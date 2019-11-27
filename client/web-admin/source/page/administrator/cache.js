WangAho({
	
	
	id:"administrator/cache",

	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	
	main : function(){
		var _project = WangAho(this.id);
		
		WangAho("index").data({
			request : {}, 
			success : function(data){
				if( !data ){
					return false;
				}
				
				//判断权限
				/*if( (function(){try{ return data.responseAll.data.list.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.list.error, {icon: 5, time: 2000});
				}
				*/
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/administrator/cache.html", "#content"), data);
				_project.event();
				
			}
		});
		
	},
	
	
	
	
	event : function(){
		var _project = WangAho(this.id);
		$('[action-button]').unbind("click").click(function(){
			var ids = WangAho("index").action_table_checked();
			var attr = $(this).attr("action-button");
			
			//清理全部缓存
			if("clear_all" == attr){
				layer.msg('你确定要清理全部缓存么？', {time: 0 //不自动关闭
					,btn: ['确定', '取消']
					,yes: function(index){
					    layer.close(index);
					    _project.clear_all();
					  }
				});
				return false;
			}
			
			
			if(ids.length < 1){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			/*if("remove" == attr){
				layer.msg('你确定要删除么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消'],yes: function(index){
												    layer.close(index);
												    _project.remove(ids);
												  }
				});
			}*/
			
		});
		
		
		
	},
	
	
	
	
	
	clear_all : function(){
		var _project = WangAho(this.id);
		
		var request_array = [["ADMINISTRATORADMINCACHECLEAR"]];
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




