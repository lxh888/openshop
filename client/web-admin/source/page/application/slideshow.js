WangAho({
	
	
	id:"application/slideshow",
	
		
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
			if(!_http.anchor.query){
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
			config.search.slideshow_state = _http.anchor.query.state;
		}
		
		//分页
		if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
			config.page = _http.anchor.query.page;
		}
		
		WangAho("index").data({
			request : {
				slideshow_module_option:["APPLICATIONADMINSLIDESHOWMODULEOPTION"],
				list:["APPLICATIONADMINSLIDESHOWLIST", [config]]
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
				_project.data = data;
				WangAho("index").view(WangAho().template("page/application/slideshow.html", "#content"), data, {
					
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
					},
					"module-name" : function(module){
						if( _project.data.response.slideshow_module_option ){
							var slideshow_module_option = _project.data.response.slideshow_module_option;
							for(var i in slideshow_module_option){
								if(module == i){
									return slideshow_module_option[i];
								}
							}
						}else{
							return "未知";
						}
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
		
		//调用 Chosen
		$('select[name="slideshow_module"]').chosen({
			width: '100%',
			//placeholder_text_single: '-', //默认值
			earch_contains:true, 
			no_results_text: "没有匹配结果",
			case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
        	//group_search: false //选项组是否可搜。此处搜索不可搜
		});
		
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
			  	content: template( WangAho().template("page/application/slideshow.html", "#search"), function(fn){
					return fn({search:search, response:_project.data.response});
					})
			});
			
			$('input').eq(1).focus();//失去焦点
			_project.search_event();
		});
		
		$('[name="search-submit"]').unbind("click").click(function(){
			var _http = http();
			if( !_http.anchor.query ) _http.anchor.query = {};
			var search = {};
			search.slideshow_id = $.trim($('[name="slideshow_id"]').val());
			search.slideshow_name = $.trim($('[name="slideshow_name"]').val());
			search.slideshow_label = $.trim($('[name="slideshow_label"]').val());
			search.slideshow_module = $.trim($('[name="slideshow_module"]').val());
			
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
	

	
	
	
	image_file : null,
	event : function(){
		//查看图片
		WangAho("index").image_look_event();
		var _project = WangAho(this.id);
		this.keyup();
		//筛选
		this.search_event();
		
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
		
		
		
		//选择上传图片
		$('[name="image-select"]').unbind("click").click(function(){
			new eonfox().trigger_click($('[name="image-files"]').get(0));
		});
		
		//图片发生改变时执行
		$('[name="image-files"]').unbind("change").change(function(){
			if( $('[name="image-files"]')[0].files.length ){
				_project.image_file = $('[name="image-files"]')[0].files[0];
				_project.image_file.src = new eonfox().file_url( _project.image_file );
				
				layer.closeAll();
				//页面层
				layer.open({
					title : "<span class=\"glyphicon glyphicon-plus\"></span> 添加轮播图",
				  	type: 1,
				  	shadeClose: false,
				  	area: 'auto', //宽高
				  	maxWidth: $(window).width() > 1200? 1200 : $(window).width()-50,
				  	maxHeight: $(window).height()-50,
				  	content: template( WangAho().template("page/application/slideshow.html", "#add"), function(fn){
						return fn({file:_project.image_file, response:_project.data.response});
						})
				});
				
				$('input').eq(1).focus();//失去焦点
				_project.event();
			}
		});
		
		
		$('[name="add-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.slideshow_name = $.trim($('[name="slideshow_name"]').val());
			form_input.slideshow_info = $.trim($('[name="slideshow_info"]').val());
			form_input.slideshow_module = $.trim($('[name="slideshow_module"]').val());
			form_input.slideshow_label = $.trim($('[name="slideshow_label"]').val());
			form_input.slideshow_comment = $.trim($('[name="slideshow_comment"]').val());
			form_input.slideshow_sort = $.trim($('[name="slideshow_sort"]').val());
			form_input.slideshow_json = $.trim($('[name="slideshow_json"]').val());
			form_input.slideshow_state = $('[name="slideshow_state"]').is(':checked')? 0 : 1;
			
			try {
				if(form_input.slideshow_sort == ""){
					delete form_input.slideshow_sort;
				}
				if( !_project.image_file ) throw "没有需要上传的图片";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			
			//提交数据
			eonfox().submit({
				request : JSON.stringify({
					s : ["APPLICATIONADMINSLIDESHOWADD", [form_input]],
					}),
				data : {file: _project.image_file},	
				progress : function(loaded, total, percent){
					//console.log(loaded, total, percent);
					if(percent == 100){
		        		//layer.msg('上传成功', {icon: 1, time: 1000});
		        	}else{
		        		layer.msg( Math.floor(percent)+"%" );
		        	}
				},
				callback : function(r){
					layer.closeAll();//关闭加载
					if( !r ){
						layer.msg("未知错误", {icon: 5, time: 3000});
						$btn.removeClass('disabled');
						return;
					}
					if( (function(){try{ return r.data.s.errno;}catch(e){return false;}}()) ){
						layer.msg(r.data.s.error, {icon: 5, time: 3000});
						$btn.removeClass('disabled');
						return;
					}
					
					layer.msg("操作成功!", {icon: 1, time: 1000});
					setTimeout(function(){
						//刷新页面
						WangAho("index").scroll_constant(function(){
							_project.main();
						});
					}, 1000);
				}
			});
			
		});
		
		
		
		$(".input_edit_open").unbind("click").click(function(){
			layer.closeAll();
			var slideshow_id = $(this).attr("data-id");
			if( !slideshow_id ){
				layer.msg("轮播图ID不存在", {icon: 5, time: 3000});
				return false;
			}
			
			var slideshow_data = null;
			if( _project.data.response.list.data ){
				var response_list_data = _project.data.response.list.data;
				for(var i in response_list_data){
					if(response_list_data[i].slideshow_id == slideshow_id){
						slideshow_data = response_list_data[i];
						break;
					}
				}
			}
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-edit\"></span> 编辑轮播图",
			  	type: 1,
			  	shadeClose: false,
			  	area: 'auto', //宽高
			  	maxWidth: $(window).width() > 1200? 1200 : $(window).width()-50,
			  	maxHeight: $(window).height()-50,
			  	content: template( WangAho().template("page/application/slideshow.html", "#edit"), function(fn){
					return fn({ qiniu_domain : _project.data.response.application_config.qiniu_domain, slideshow_data : slideshow_data, response:_project.data.response });
					})
			});
			
			$('input').eq(1).focus();//失去焦点
			_project.event();
		});
		
		
		$('[name="edit-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.slideshow_id = $.trim($('[name="slideshow_id"]').val());
			form_input.slideshow_name = $.trim($('[name="slideshow_name"]').val());
			form_input.slideshow_info = $.trim($('[name="slideshow_info"]').val());
			form_input.slideshow_module = $.trim($('[name="slideshow_module"]').val());
			form_input.slideshow_label = $.trim($('[name="slideshow_label"]').val());
			form_input.slideshow_comment = $.trim($('[name="slideshow_comment"]').val());
			form_input.slideshow_json = $.trim($('[name="slideshow_json"]').val());
			form_input.slideshow_sort = $.trim($('[name="slideshow_sort"]').val());
			form_input.slideshow_state = $('[name="slideshow_state"]').is(':checked')? 0 : 1;
			
			try {
				if(form_input.slideshow_id == '') throw "轮播图ID异常";
				if(form_input.slideshow_sort == ""){
					delete form_input.slideshow_sort;
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
				request:["APPLICATIONADMINSLIDESHOWEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					layer.closeAll();
					$btn.removeClass('disabled');
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
			
		});
		
		
		
	},
	
	
	
	
		
	/**
	 * 排序
	 */
	sort : function(){
		var obj = WangAho("index").action_table("sort");
		if( !obj || !obj.length ){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		request_array.push(["APPLICATIONADMINSLIDESHOWEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["APPLICATIONADMINSLIDESHOWEDIT", [{slideshow_id:obj[i].id, slideshow_sort:obj[i].value}]]);
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
			request_array.push(["APPLICATIONADMINSLIDESHOWREMOVE", [{slideshow_id:ids[i]}]]);
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