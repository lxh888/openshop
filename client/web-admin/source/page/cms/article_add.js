WangAho({
	
	
	id:"cms/article_add",
	
	
	
	main : function(){
		var _project = WangAho(this.id);
		WangAho("index").data({
			success: function(data){
				if( !data ){
					return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view( WangAho().template("page/cms/article_add.html", "#content"), data);
				
				//调用 Chosen
				$('select[name="cms_article_state"]').chosen({
					width: '100%',
					//placeholder_text_single: '-', //默认值
					earch_contains:true, 
					no_results_text: "没有匹配结果",
					case_sensitive_search: false, //搜索大小写敏感。此处设为不敏感
		        	group_search: true //选项组是否可搜。此处搜索不可搜
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
	
	
	submit : function(){
		//按回车键时提交
		this.keyup();
		
		$('[name="submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.cms_article_name = $.trim($('[name="cms_article_name"]').val());
			form_input.cms_article_info = $.trim($('[name="cms_article_info"]').val());
			form_input.cms_article_sort = $.trim($('[name="cms_article_sort"]').val());
			form_input.cms_article_state = $.trim($('[name="cms_article_state"]').val());
			form_input.cms_article_source = $.trim($('[name="cms_article_source"]').val());
			form_input.cms_article_keywords = $.trim($('[name="cms_article_keywords"]').val());
			form_input.cms_article_description = $.trim($('[name="cms_article_description"]').val());
			try {
				if(form_input.cms_article_name == '') throw "文章名称不能为空";
				if(form_input.cms_article_sort == ""){
					delete form_input.cms_article_sort;
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
				request:["CMSADMINARTICLEADD", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//跳转到编辑页面
					http("#/cms/article_edit/?id="+data).request();
				}
			});
			
			
		});
		
	}
	
	
	
	
	
	
	
	
});