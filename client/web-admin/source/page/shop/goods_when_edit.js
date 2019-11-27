WangAho({
	
	
	id:"shop/goods_when_edit",
	
	
	main : function(){
		var _project = WangAho(this.id);
		var config = {};
		var _http = http();
		config.shop_goods_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if(!config.shop_goods_id){
			http("#/shop/goods_when_list").request();
			return false;
		}
		
		WangAho("index").data({
			request : {
				get:["SHOPADMINGOODSWHENGET", [config]]
				},
			success : function(data){
				
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.get.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.get.error, {icon: 5, time: 2000});
				}
				
				//判断数据是否存在
				if( !(function(){try{ return data.response.get.shop_goods_id.length;}catch(e){return false;}}()) ){
					setTimeout(function(){
						
						if( WangAho().history_previous_exist() ){
							WangAho().history_previous();//存在上一页则返回上一页
						}else{
							WangAho().history_remove();//删除本页的记录
							http("#/shop/goods_when_list").request();//返回列表页
						}
						
					}, 2000);
					return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/shop/goods_when_edit.html", "#content"), data);
				
				laydate.render({
				  elem: '#shop_goods_when_time'
				  ,type: 'datetime'
				  ,theme: '#337ab7'
				  ,range: '~'
				}); 
				
				_project.submit();
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
		        $('[name="submit"]').first().trigger("click");
			}
		});
	},
	
	
	/**
	 * 提交
	 */
	submit : function(){
		var _project = WangAho(this.id);
		//按回车键时提交
		this.keyup();
		
		$('[name="submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.shop_goods_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.shop_goods_when_name = $.trim($('[name="shop_goods_when_name"]').val());
			form_input.shop_goods_when_info = $.trim($('[name="shop_goods_when_info"]').val());
			form_input.shop_goods_when_sort = $.trim($('[name="shop_goods_when_sort"]').val());
			
			try {
				
				if( form_input.shop_goods_id == "" ){
					throw "商品ID异常";	
				}
				
				var times = $.trim($('#shop_goods_when_time').val());
				if( times == "" ){
					throw "请选择售卖的时间范围";	
				}
				
				var times_split = times.split('~', 2);
				if( times_split[0] ) times_split[0] = $.trim(times_split[0]);
				if( times_split[1] ) times_split[1] = $.trim(times_split[1]);
				
				var format = /^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/;
				if( times_split[0] && format.test(times_split[0]) ){
					form_input.shop_goods_when_start_time = times_split[0];
				}else{
					throw "开始销售时间不合法";	
				}
				
				if( times_split[1] && format.test(times_split[1]) ){
					form_input.shop_goods_when_end_time = times_split[1];
				}else{
					throw "结束售卖时间不合法";	
				}
				
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["SHOPADMINGOODSWHENEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
			
		});
		
	},
	
	
	
	
	
	
	
	
});