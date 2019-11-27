WangAho({
	
	
	id:"application/type",
	
	
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
		
		//分页
		if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
			config.page = _http.anchor.query.page;
		}
			
		var request = {
				type_module_option:["APPLICATIONADMINTYPEMODULEOPTION"],
				type_option:["APPLICATIONADMINTYPEOPTION", [{sort:["sort_asc"]}]],
				list:["APPLICATIONADMINTYPELIST", [config]]
			};
		if( config.search.type_parent_id ){
			request.type_parent_get = ["APPLICATIONADMINTYPEGET", [{type_id : config.search.type_parent_id}]];
		}
		
		
		WangAho("index").data({
			request : request, 
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
				WangAho("index").view(WangAho().template("page/application/type.html", "#content"), data, {
					
					"module-name" : function(module){
						if( _project.data.response.type_module_option ){
							var type_module_option = _project.data.response.type_module_option;
							for(var i in type_module_option){
								if(module == i){
									return type_module_option[i];
								}
							}
						}else{
							return "未知";
						}
					}
					
				});
				
				_project.event();
				_project.search_event();
				
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
		_project.keyup();
		
		//调用 Chosen
		$('select[name="type_module"],select[name="type_parent_id"]').chosen({
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
			  	content: template( WangAho().template("page/application/type.html", "#search"), function(fn){
					return fn({search:search, response:_project.data.response});
					})
			});
			
			$('input[search]').first().focus();//失去焦点
			_project.search_event();
		});
		
		$('[name="search-submit"]').unbind("click").click(function(){
			var _http = http();
			if( !_http.anchor.query ) _http.anchor.query = {};
			var search = {};
			search.type_id = $.trim($('[name="type_id"]').val());
			search.type_name = $.trim($('[name="type_name"]').val());
			search.type_label = $.trim($('[name="type_label"]').val());
			search.type_module = $.trim($('[name="type_module"]').val());
			search.type_parent_id = $.trim($('[name="type_parent_id"]').val());
			
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
	

	
	event : function(){
		//回车事件
		this.keyup();
		//查看图片
		WangAho("index").image_look_event();
		
		var _project = WangAho(this.id);
		$('[action-button]').unbind("click").click(function(){
			var ids = WangAho("index").action_table_checked();
			var attr = $(this).attr("action-button");
			
			//添加
			if("add" == attr){
				var parent_id = $(this).attr("data-parent-id");
				var parent_data = null;
				if( parent_id && 
					_project.data && 
					_project.data.response && 
					_project.data.response.list && 
					_project.data.response.list.data ){
					var list = _project.data.response.list.data;
					for(var i in list){
						if( list[i].type_id == parent_id ){
							parent_data = list[i];
							break;
						}
					}
					
					if( !parent_data && 
						_project.data.response.type_parent_get &&
						parent_id == _project.data.response.type_parent_get.type_id){
						parent_data = _project.data.response.type_parent_get;
					}
				}
				
				_project.add(parent_data);
				return true;
			}
			//编辑
			if("edit" == attr){
				var id = $(this).attr("data-id");
				var edit_data = null;
				if( id && 
					_project.data && 
					_project.data.response && 
					_project.data.response.list && 
					_project.data.response.list.data ){
					var list = _project.data.response.list.data;
					for(var i in list){
						if( list[i].type_id == id ){
							edit_data = list[i];
							break;
						}
					}
					
					if( !edit_data && 
						_project.data.response.type_parent_get &&
						id == _project.data.response.type_parent_get.type_id){
						edit_data = _project.data.response.type_parent_get;
					}
				}
				
				if(!edit_data){
					layer.msg("编辑数据异常", {icon: 5, time: 2000});
					return false;
				}
				_project.edit(edit_data);
				return true;
			}
			
			//排序
			if("sort" == attr){
				_project.sort();
				return true;
			}
			
			
			if(ids.length < 1){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			//显示
			if("state_show" == attr){
				layer.msg('你确定要显示么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消']
					,yes: function(index){
					   	layer.close(index);
					   	_project.state_show(ids);
					}
				});
				return true;
			}
			
			//隐藏
			if("state_hide" == attr){
				layer.msg('你确定要隐藏么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消']
					,yes: function(index){
					   	layer.close(index);
					   	_project.state_hide(ids);
					}
				});
				return true;
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
				return true;
			}
			
		});
		
		//选择上传图片
		$('[name="image-select"]').unbind("click").click(function(){
			new eonfox().trigger_click($('[name="image-files"]').get(0));
		});
		
		//调用 Chosen
		$('select[name="type_module"],select[name="type_parent_id"]').chosen({
			width: '100%',
			//placeholder_text_single: '-', //默认值
			earch_contains:true, 
			no_results_text: "没有匹配结果",
			case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
        	//group_search: false //选项组是否可搜。此处搜索不可搜
		});
		
	},
	
	
	image_upload_mime_limit : ['image/jpeg','image/pjpeg','image/png', 'image/x-png', 'image/gif', 'image/bmp'],
	
	add_logo_image : null,
	add : function(parent_data){
		//console.log(parent_data);
		
		var _project = WangAho(this.id);
		layer.closeAll();
		//页面层
		layer.open({
			title : "<span class=\"glyphicon glyphicon-plus\"></span> 添加分类",
		  	type: 1,
		  	shadeClose: false,
		  	area: 'auto', //宽高
		  	maxWidth: $(window).width() > 1200? 1200 : $(window).width()-50,
		  	maxHeight: $(window).height()-50,
		  	content: template( WangAho().template("page/application/type.html", "#add"), function(fn){
				return fn({response : _project.data.response, parent_data : parent_data});
				})
		});
		
		$('input[name="type_name"]').focus();//失去焦点
		_project.event();
		
		if(_project.add_logo_image && _project.add_logo_image.src){
			$('[name="type_logo_image_id"]').attr("src", _project.add_logo_image.src);
			$('[name="type_logo_image_id"]').show();
		}else{
			_project.add_logo_image = null;
			$('[name="type_logo_image_id"]').attr("src", "");
			$('[name="type_logo_image_id"]').hide();
		}
		
		//图片发生改变时执行
		$('[name="image-files"]').unbind("change").change(function(){
			if( $('[name="image-files"]')[0].files.length ){
				_project.add_logo_image = $('[name="image-files"]')[0].files[0];
				//判断图片类型是否合法
				var legal = false;
				for(var l in _project.image_upload_mime_limit){
					if(_project.image_upload_mime_limit[l] == _project.add_logo_image.type){
						legal = true;
						break;
					}
				}
				
				if(!legal){
					layer.msg("“"+_project.add_logo_image.name+"” 文件格式不合法，只能上传png、jpg、gif、bmp图片文件", {icon: 5, time: 3000});
					_project.add_logo_image = null;
					false;
				}
				
				_project.add_logo_image.src = new eonfox().file_url( _project.add_logo_image );
				$('[name="type_logo_image_id"]').attr("src", _project.add_logo_image.src);
				$('[name="type_logo_image_id"]').show();
			}
		});
		//选择上传图片
		$('[name="image-clear"]').unbind("click").click(function(){
			_project.add_logo_image = null;
			$('[name="type_logo_image_id"]').attr("src", "");
			$('[name="type_logo_image_id"]').hide();
		});
		
		
		$('[name="add-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var form_data = {};
			form_input.type_name = $.trim($('[name="type_name"]').val());
			form_input.type_info = $.trim($('[name="type_info"]').val());
			form_input.type_parent_id = $.trim($('[name="type_parent_id"]').val());
			form_input.type_module = $.trim($('[name="type_module"]').val());
			form_input.type_label = $.trim($('[name="type_label"]').val());
			form_input.type_comment = $.trim($('[name="type_comment"]').val());
			form_input.type_json = $.trim($('[name="type_json"]').val());
			
			form_input.type_sort = $.trim($('[name="type_sort"]').val());
			form_input.type_state = $('[name="type_state"]').is(':checked')? 0 : 1;
			
			try {
				if(form_input.type_name == '') throw "分类名称不能为空";
				if(form_input.type_module == '') throw "请选择分类模块";
				
				if(form_input.type_json == "") delete form_input.type_json;
				if(form_input.type_sort == "") delete form_input.type_sort;
				if(form_input.type_parent_id == "") delete form_input.type_parent_id;
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			if( _project.add_logo_image ){
				form_data = {file: _project.add_logo_image};
			}
			//提交数据
			eonfox().submit({
				request : JSON.stringify({
					s : ["APPLICATIONADMINTYPEADD", [form_input]],
					}),
				data : form_data,	
				progress : function(loaded, total, percent){
					//console.log(loaded, total, percent);
					if(percent == 100){
		        		//layer.msg('上传成功', {icon: 1, time: 1000});
		        	}else{
		        		layer.msg( Math.floor(percent)+"%" );
		        	}
				},
				callback : function(r){
					layer.closeAll('dialog');//关闭加载
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
						layer.closeAll();//关闭
						_project.add_logo_image = null;
						//刷新页面
						WangAho("index").scroll_constant(function(){
							_project.main();
						});
					}, 1000);
				}
			});
			
			
		});
		
		
	},
	
	
	edit_logo_image : {},
	//编辑
	edit : function(edit_data){
		var _project = WangAho(this.id);
		layer.closeAll();
		var _type_id = edit_data.type_id;
		
		//页面层
		layer.open({
			title : "<span class=\"glyphicon glyphicon-plus\"></span> 编辑分类",
		  	type: 1,
		  	shadeClose: false,
		  	area: 'auto', //宽高
		  	maxWidth: $(window).width() > 1200? 1200 : $(window).width()-50,
		  	maxHeight: $(window).height()-50,
		  	content: template( WangAho().template("page/application/type.html", "#edit"), function(fn){
				return fn({response : _project.data.response, edit_data : edit_data});
				})
		});
		
		$('input[name="type_name"]').focus();//失去焦点
		_project.event();
		
		//初始化上传图片
		if(_project.edit_logo_image && _project.edit_logo_image[_type_id] && _project.edit_logo_image[_type_id].src){
			$('[name="type_logo_image_id"]').attr("src", _project.edit_logo_image[_type_id].src);
			$('[name="type_logo_image_id"]').show();
		}else{
			_project.edit_logo_image[_type_id] = null;
			$('[name="type_logo_image_id"]').attr("src", "");
			$('[name="type_logo_image_id"]').hide();
		}
		
		//图片发生改变时执行
		$('[name="image-files"]').unbind("change").change(function(){
			if( $('[name="image-files"]')[0].files.length ){
				_project.edit_logo_image[_type_id] = $('[name="image-files"]')[0].files[0];
				//判断图片类型是否合法
				var legal = false;
				for(var l in _project.image_upload_mime_limit){
					if(_project.image_upload_mime_limit[l] == _project.edit_logo_image[_type_id].type){
						legal = true;
						break;
					}
				}
				
				if(!legal){
					layer.msg("“"+_project.edit_logo_image[_type_id].name+"” 文件格式不合法，只能上传png、jpg、gif、bmp图片文件", {icon: 5, time: 3000});
					_project.add_logo_image = null;
					false;
				}
				
				_project.edit_logo_image[_type_id].src = new eonfox().file_url( _project.edit_logo_image[_type_id] );
				$('[name="type_logo_image_id"]').attr("src", _project.edit_logo_image[_type_id].src);
				$('[name="type_logo_image_id"]').show();
			}
		});
		//选择上传图片
		$('[name="image-clear"]').unbind("click").click(function(){
			_project.edit_logo_image[_type_id] = null;
			$('[name="type_logo_image_id"]').attr("src", "");
			$('[name="type_logo_image_id"]').hide();
		});
		
		
		$('[name="edit-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var form_data = {};
			form_input.type_id = $.trim($('[name="type_id"]').val());
			form_input.type_name = $.trim($('[name="type_name"]').val());
			form_input.type_info = $.trim($('[name="type_info"]').val());
			form_input.type_parent_id = $.trim($('[name="type_parent_id"]').val());
			form_input.type_module = $.trim($('[name="type_module"]').val());
			form_input.type_label = $.trim($('[name="type_label"]').val());
			form_input.type_comment = $.trim($('[name="type_comment"]').val());
			form_input.type_json = $.trim($('[name="type_json"]').val());
			
			form_input.type_sort = $.trim($('[name="type_sort"]').val());
			form_input.type_state = $('[name="type_state"]').is(':checked')? 0 : 1;
			
			try {
				if(form_input.type_id == '') throw "分类ID异常";
				if(form_input.type_name == '') throw "分类名称不能为空";
				if(form_input.type_module == '') throw "请选择分类模块";
				
				if(form_input.type_json == "") delete form_input.type_json;
				if(form_input.type_sort == "") delete form_input.type_sort;
				if(form_input.type_parent_id == "") delete form_input.type_parent_id;
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			if( _project.edit_logo_image[_type_id] ){
				form_data = {file: _project.edit_logo_image[_type_id]};
			}
			
			//提交数据
			eonfox().submit({
				request : JSON.stringify({
					s : ["APPLICATIONADMINTYPEEDIT", [form_input]],
					}),
				data : form_data,	
				progress : function(loaded, total, percent){
					//console.log(loaded, total, percent);
					if(percent == 100){
		        		//layer.msg('上传成功', {icon: 1, time: 1000});
		        	}else{
		        		layer.msg( Math.floor(percent)+"%" );
		        	}
				},
				callback : function(r){
					layer.closeAll('dialog');//关闭加载
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
						layer.closeAll();//关闭
						_project.edit_logo_image[_type_id] = null;
						//刷新页面
						WangAho("index").scroll_constant(function(){
							_project.main();
						});
					}, 1000);
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
		request_array.push(["APPLICATIONADMINTYPEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["APPLICATIONADMINTYPEEDIT", [{type_id:obj[i].id, type_sort:obj[i].value}]]);
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
	
	
	
	removeremove : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["APPLICATIONADMINTYPEREMOVE", [{type_id:ids[i]}]]);
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
		
		
	},
	
	
	
	state_show : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["APPLICATIONADMINTYPEEDIT", [{type_id:ids[i], type_state:1}]]);
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
		
	},
	
	
	
	state_hide : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["APPLICATIONADMINTYPEEDIT", [{type_id:ids[i], type_state:0}]]);
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




