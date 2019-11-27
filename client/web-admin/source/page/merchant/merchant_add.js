WangAho({
	
	
	id:"merchant/merchant_add",
	
	
	main : function(){
		var _project = WangAho(this.id);
		
		WangAho("index").data({
			request : {},
			success : function(data){
				
				if( !data ){
					return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/merchant/merchant_add.html", "#content"), data);
				
				//调用 Chosen
				$('select[name="merchant_state"]').chosen({
					width: '100%',
					//placeholder_text_single: '-', //默认值
					earch_contains:true, 
					no_results_text: "没有匹配结果",
					case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
		        	//group_search: false //选项组是否可搜。此处搜索不可搜
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
			form_input.merchant_name = $.trim($('[name="merchant_name"]').val());
			form_input.merchant_info = $.trim($('[name="merchant_info"]').val());
			form_input.merchant_phone = $.trim($('[name="merchant_phone"]').val());
			
			form_input.merchant_province = $.trim($('[name="merchant_province"]').val());
			form_input.merchant_city = $.trim($('[name="merchant_city"]').val());
			form_input.merchant_district = $.trim($('[name="merchant_district"]').val());
			form_input.merchant_address = $.trim($('[name="merchant_address"]').val());
			
			form_input.merchant_longitude = $.trim($('[name="merchant_longitude"]').val());
			form_input.merchant_latitude = $.trim($('[name="merchant_latitude"]').val());
			form_input.merchant_state = $.trim($('[name="merchant_state"]').val());
			
			try {
				if(form_input.merchant_name == '') throw "商家名称不能为空";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["MERCHANTADMINADD", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//跳转到编辑页面
					http("#/merchant/merchant_edit/?id="+data).request();
					//刷新页面
					WangAho().rerun();
				}
			});
			
			
		});
		
	},
	
	
	
	
	
	
	
	
});