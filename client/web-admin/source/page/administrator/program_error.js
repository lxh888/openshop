WangAho({
	
	
	id:"administrator/program_error",

	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	
	main : function(){
		var _project = WangAho(this.id);
		
		WangAho("index").data({
			request : {list:["ADMINISTRATORADMINPROGRAMERRORDATA"]}, 
			success : function(data){
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.list.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.list.error, {icon: 5, time: 2000});
				}
				
				WangAho("index").view(WangAho().template("page/administrator/program_error.html", "#content"), data);
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
		
		$('.open-details').click(function(){
			var title = $(this).attr("data-name");
			var id = $(this).attr("data-id");
			var href = http(http().href, '#/administrator/program_error_details/?filename='+id).href;
			layer.open({
		      type: 2,
		      title: title,
		      shadeClose: true,
		      shade: false,
		      maxmin: true, //开启最大化最小化按钮
		      area: ['893px', '600px'],
		      content: href
		    });
		});
		
	},
	
	
	remove : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		var request_array = [];
		for(var i in ids){
			request_array.push(["ADMINISTRATORADMINPROGRAMERRORREMOVE", [{file_name:ids[i]}]]);
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




