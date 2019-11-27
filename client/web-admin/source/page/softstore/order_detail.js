WangAho({
	
	
	id:"softstore/order_detail",
	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	data : {},
	ss_order_id : "",
	
	main : function(){
		var config = {search:{}};
		var _project = WangAho(this.id);
		var _http = http();
		_project.ss_order_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if(!_project.ss_order_id){
			http("#/softstore/order_list").request();
			return false;
		}
		
		config.ss_order_id = _project.ss_order_id;
		
		WangAho("index").data({
			request : {
				get:["SOFTSTOREADMINORDERDETAIL", [config]]
				},
			success: function(data){
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.get.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.get.error, {icon: 5, time: 2000});
				}
				
				//判断数据是否存在
				if( !(function(){try{ return data.response.get.ss_order_id;}catch(e){return false;}}()) ){
					setTimeout(function(){
						if( WangAho().history_previous_exist() ){
							WangAho().history_previous();//存在上一页则返回上一页
						}else{
							WangAho().history_remove();//删除本页的记录
							http("#/softstore/order_list").request();//返回列表页
						}
						
					}, 2000);
					return false;
				}
				
				_project.data = data;
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/softstore/order_detail.html", "#content"), data);
				
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
				
		       	$('[name="contact_notes-submit"]').first().trigger("click");
			}
		});
	},
	
	
	event : function(){
		var _project = WangAho(this.id);
		
		_project.keyup();
		//确定联系
		$('[action-button="determine_contact"]').unbind("click").click(function(){
			layer.closeAll();
			
			if( !(function(){try{ return _project.data.response.get.ss_order_id, true;}catch(e){return false;}}()) ){
				layer.msg("订单ID不合法", {icon: 5, time: 2000});
				return false;
			}
			
			var title = "";
			if( parseInt(_project.data.response.get.ss_order_contact_state) == 0 ){
				title = "<span class=\"glyphicon glyphicon-phone\"></span> 确定联系";
			}else{
				title = "<span class=\"glyphicon glyphicon-phone\"></span> 编辑联系备注";
			}
			
			//页面层
			layer.open({
				title : title,
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/softstore/order_detail.html", "#contact_notes"), function(fn){
							return fn(_project.data.response.get);
							})
			});
			
			$('input[name="ss_order_id"]').first().focus();//获取焦点
			_project.event();//事件
		});
		
		
		$('[name="contact_notes-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.ss_order_id = $.trim($('[name="ss_order_id"]').val());
			form_input.ss_order_contact_notes = $.trim($('[name="ss_order_contact_notes"]').val());
			form_input.ss_order_contact_state = 1;
			
			try {
				if(form_input.ss_order_id == '') throw "订单ID不能为空";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["SOFTSTOREADMINORDERCONTACT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					layer.closeAll();
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
			
		});
		
	}
	
	
	
	
	
	
});