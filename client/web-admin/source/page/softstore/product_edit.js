WangAho({
	
	
	id : "softstore/product_edit",
	
	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	
	data : null,//数据
	
	
	ss_product_id : "",//产品ID
	
	
	main : function(){
		var _project = WangAho(this.id);
		
		var _http = http();
		var action = (function(){try{ return _http.anchor.query.action;}catch(e){return false;}}());
		if( !action ){
			action = "basics";
		}
		_project.ss_product_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if(!_project.ss_product_id){
			http("#/softstore/product_list").request();
			return false;
		}
		
		//数据请求
		var request = {get:["SOFTSTOREADMINPRODUCTGET", [{ss_product_id:_project.ss_product_id}]]};
		
		if( "image" === action ){
			var config = {search:{}};
			
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
			
			//分页
			if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
				config.page = _http.anchor.query.page;
			}
			//产品id
			config.search.ss_product_id = _project.ss_product_id;
			request.list = ["SOFTSTOREADMINPRODUCTIMAGELIST",[config]];
		}
		
		if( "type" === action ){
			request.softstore_type_option = ["SOFTSTOREADMINTYPEOPTION",[{sort:["sort_asc"]}]];
		}
		
		
		//产品属性
		if( "attr" === action ){
			request.softstore_product_attr_option = ["SOFTSTOREADMINPRODUCTATTRIBUTEOPTION",[{sort:["sort_asc","insert_time_asc"], search:{product_id:_project.ss_product_id}}]];
		}
		
		
		if( "file" === action ){
			var config = {search:{}};
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
			//分页
			if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
				config.page = _http.anchor.query.page;
			}
			//产品id
			config.search.ss_product_id = _project.ss_product_id;
			request.list = ["SOFTSTOREADMINPRODUCTFILELIST",[config]];
		}
		
		var _project = WangAho(this.id);
		WangAho("index").data({
			request : request,
			success : function(data){
				_project.data = data;
				if( !_project.data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.get.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.get.error, {icon: 5, time: 2000});
				}
				
				//判断数据是否存在
				if( !(function(){try{ return data.response.get.ss_product_id;}catch(e){return false;}}()) ){
					setTimeout(function(){
						
						if( WangAho().history_previous_exist() ){
							WangAho().history_previous();//存在上一页则返回上一页
						}else{
							WangAho().history_remove();//删除本页的记录
							http("#/softstore/product_list").request();//返回列表页
						}
						
					}, 2000);
					return false;
				}
				
				
				//获取子页面参数
				_project.data.action = action;
				//获得配置数据
				_project.data.config = WangAho().data("config.json");
				
				WangAho("index").view(WangAho().template("page/softstore/product_edit.html", "#content"), _project.data, {
					"action-query-href" : function(action){
						var _h = http();
						if(!_h.anchor.query){
							_h.anchor.query = {};
						}
						if( !action){
							delete _h.anchor.query.action;
						}else{
							_h.anchor.query.action = action;
						}
						
						if( _h.anchor.query.page ){
							delete _h.anchor.query.page;//删除分页
						}
						
						return http(_h).href;
					},
					"import-action-template" : function(){
						var html = template( WangAho().template("page/softstore/product_edit/"+_project.data.action+".html", "#content"), function(fn){
							this.config.helper = WangAho("index").page_helper;
							
							this.helper("product_type in_array", function(ss_type_id, ss_product_type){
								if(typeof ss_product_type != 'object' || !ss_product_type.length){
									return false;
								}
								
								var exist = false;
								for(var i in ss_product_type){
									if( ss_product_type[i].ss_type_id == ss_type_id){
										exist = true;
										break;
									}
								}
								return exist;
							});
							
							return fn(_project.data);
						});
						if(!html){
							return template( WangAho().template("page/softstore/product_edit.html", "#warning"), function(fn){
								return fn(_project.data);
							});
						}else{
							return html;
						}
						
					}
					
					
				});
				
				
				if( "basics" === _project.data.action ){
					//调用 Chosen
					$('select[name="ss_product_state"]').chosen({
						width: '100%',
						//placeholder_text_single: '-', //默认值
						earch_contains: true,
						no_results_text: "没有匹配结果",
						case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
			        	//group_search: false //选项组是否可搜。此处搜索不可搜
					});
					
					_project.product_basics_event();
				}else
				if( "details" === _project.data.action ){
					_project.product_details();
				}else
				if( "type" === _project.data.action ){
					_project.product_type_event();
				}else
				//产品属性
				if( "attr" === _project.data.action ){
					_project.product_attr_event();
				}else
				if( "file" === _project.data.action ){
					_project.product_file_event();
				}else
				if( "image" === _project.data.action ){
					_project.product_image_event();
					$('[name="product-image-files"]').first().trigger("template");//更新模板
				}
				
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
				
		        $('[name="product-attr-add-submit"]').first().trigger("click");
		        $('[name="product-attr-edit-submit"]').first().trigger("click");
		        $('[name="product-attr-parent-add-submit"]').first().trigger("click");
		       	$('[name="product-attr-parent-edit-submit"]').first().trigger("click");
		        $('[name="product-type-submit"]').first().trigger("click");
		        $('[name="product-basics-submit"]').first().trigger("click");
		        $('[name="product-details-submit"]').first().trigger("click");
		        $('[name="product-image-submit"]').first().trigger("click");
		        $('[name="product-file-submit"]').first().trigger("click");
			}
		});
	},
	
	
	
	
	//删除
	product_attr_remove : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["SOFTSTOREADMINPRODUCTATTRIBUTEREMOVE", [{ss_product_attr_id:ids[i]}]]);
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
	
	
	
	//排序
	product_attr_sort : function(){
		var obj = WangAho("index").action_table("sort");
		if( !obj || !obj.length ){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		request_array.push(["SOFTSTOREADMINPRODUCTATTRIBUTEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["SOFTSTOREADMINPRODUCTATTRIBUTEEDIT", [{ss_product_attr_id:obj[i].id, ss_product_attr_sort:obj[i].value}]]);
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
	
	
	//产品的属性 事件 
	product_attr_event : function(){
		var _project = WangAho(this.id);
		
		//回车
		this.keyup();
		
		//注册 checkbox
		WangAho("index").checkbox("checkbox-attr-parent", "checkbox-attr-parent-all");
		
		$('[action-button]').unbind("click").click(function(){
			var attr = $(this).attr("action-button");
			
			//排序
			if("sort" == attr){
				_project.product_attr_sort();
				return true;
			}
			
			var ids = WangAho("index").action_table_checked("checkbox");
			var parent_ids = WangAho("index").action_table_checked("checkbox-attr-parent");
			for(var i in parent_ids){
				ids.push(parent_ids[i]);
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
												    _project.product_attr_remove(ids);
												  }
				});
			}
			
		});
		
		//添加主题
		$('[name="product-attr-parent-add-input"]').unbind("click").click(function(){
			layer.closeAll();
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-plus\"></span> 添加主题",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/softstore/product_edit/attr.html", "#attr-parent-add"), function(fn){
							return fn(_project.data);
							})
			});
			
			$('input[name="ss_product_attr_name"]').first().focus();//获取焦点
			_project.product_attr_event();
			
		});
		
		//编辑主题
		$('[name="product-attr-parent-edit-input"]').unbind("click").click(function(){
			var ss_product_attr_id = $(this).attr("data-id");
			
			_project.data.product_attr_data = null;
			if( ss_product_attr_id && _project.data.response.softstore_product_attr_option.length ){
				for(var i in _project.data.response.softstore_product_attr_option){
					if(_project.data.response.softstore_product_attr_option[i].ss_product_attr_id == ss_product_attr_id){
						_project.data.product_attr_data = _project.data.response.softstore_product_attr_option[i];
					}
				}
			}
			
			//判断是否有编辑的数据
			if(!_project.data.product_attr_data){
				layer.msg("没有可编辑的数据，请刷新页面重试！", {icon: 5, time: 2000});
				return false;
			}
			
			layer.closeAll();
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-edit\"></span> 编辑主题",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/softstore/product_edit/attr.html", "#attr-parent-edit"), function(fn){
							return fn(_project.data);
							})
			});
			
			$('input[name="ss_product_attr_name"]').first().focus();//获取焦点
			_project.product_attr_event();
		});
	
		
		//提交新增主题
		$('[name="product-attr-parent-add-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.ss_product_id = _project.ss_product_id;
			form_input.ss_product_attr_name = $.trim($('[name="ss_product_attr_name"]').val());
			form_input.ss_product_attr_sort = $.trim($('[name="ss_product_attr_sort"]').val());
			form_input.ss_product_attr_info = $.trim($('[name="ss_product_attr_info"]').val());
			
			try {
				if(form_input.ss_product_attr_name == '') throw "主题名称不能为空";
				if(form_input.ss_product_attr_sort == ""){
					delete form_input.ss_product_attr_sort;
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
				request:["SOFTSTOREADMINPRODUCTATTRIBUTEADD", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					$btn.removeClass('disabled');
					$('[name="product-attr-parent-add-input"]').first().trigger("click");
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
		});
		
		//提交编辑主题		
		$('[name="product-attr-parent-edit-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.ss_product_attr_id = $.trim($('[name="ss_product_attr_id"]').val());
			form_input.ss_product_attr_name = $.trim($('[name="ss_product_attr_name"]').val());
			form_input.ss_product_attr_sort = $.trim($('[name="ss_product_attr_sort"]').val());
			form_input.ss_product_attr_info = $.trim($('[name="ss_product_attr_info"]').val());
			
			try {
				if(form_input.ss_product_attr_name == '') throw "属性名称不能为空";
				if(form_input.ss_product_attr_sort == ""){
					delete form_input.ss_product_attr_sort;
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
				request:["SOFTSTOREADMINPRODUCTATTRIBUTEEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					$btn.removeClass('disabled');
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
			
		});
		

		
		$('[name="product-attr-add-input"]').unbind("click").click(function(event, ss_product_attr_parent_id){
			layer.closeAll();
			
			//获取父级
			if(!ss_product_attr_parent_id){
				var ss_product_attr_parent_id = $(this).attr("data-parent");
			}
			
			if( ss_product_attr_parent_id ){
				_project.data.ss_product_attr_parent_id = ss_product_attr_parent_id;
			}else{
				_project.data.ss_product_attr_parent_id = "";
			}
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-plus\"></span> 添加属性选项",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/softstore/product_edit/attr.html", "#attr-add"), function(fn){
							return fn(_project.data);
							})
			});
			
			//调用 Chosen
			$('select[name="ss_product_attr_parent_id"]').chosen({
				width: '100%',
				//placeholder_text_single: '-', //默认值
				earch_contains: true,
				no_results_text: "没有匹配结果",
				case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
	        	//group_search: false //选项组是否可搜。此处搜索不可搜
			});
			
			
			$('input[name="ss_product_attr_name"]').first().focus();//失去焦点
			_project.product_attr_event();
			
		});
		
		
		$('[name="product-attr-edit-input"]').unbind("click").click(function(){
			layer.closeAll();
			
			//获取父级
			var ss_product_attr_parent_id = $(this).attr("data-parent");
			//获取ID
			var ss_product_attr_id = $(this).attr("data-id");
			_project.data.product_attr_data = null;
			if(ss_product_attr_id && _project.data.response.softstore_product_attr_option.length){
				//是顶级
				if( !ss_product_attr_parent_id ){
					for(var i in _project.data.response.softstore_product_attr_option){
						if(_project.data.response.softstore_product_attr_option[i].ss_product_attr_id == ss_product_attr_id){
							_project.data.product_attr_data = _project.data.response.softstore_product_attr_option[i];
						}
					}
				}else{
					//不是顶级
					//先获取父级
					var product_attr_parent_data = null;
					for(var i in _project.data.response.softstore_product_attr_option){
						if(_project.data.response.softstore_product_attr_option[i].ss_product_attr_id == ss_product_attr_parent_id){
							product_attr_parent_data = _project.data.response.softstore_product_attr_option[i];
						}
					}
					
					//再获取当前数据
					if( product_attr_parent_data.son ){
						for(var i in product_attr_parent_data.son){
							if(product_attr_parent_data.son[i].ss_product_attr_id == ss_product_attr_id){
								_project.data.product_attr_data = product_attr_parent_data.son[i];
							}
						}
					}
				}
			}
			
			//判断是否有编辑的数据
			if(!_project.data.product_attr_data){
				layer.msg("没有可编辑的数据，请刷新页面重试！", {icon: 5, time: 2000});
				return false;
			}
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-edit\"></span> 编辑属性选项",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/softstore/product_edit/attr.html", "#attr-edit"), function(fn){
							return fn(_project.data);
							})
			});
			
			//调用 Chosen
			$('select[name="ss_product_attr_parent_id"]').chosen({
				width: '100%',
				//placeholder_text_single: '-', //默认值
				earch_contains: true,
				no_results_text: "没有匹配结果",
				case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
	        	//group_search: false //选项组是否可搜。此处搜索不可搜
			});
			
			
			$('input[name="ss_product_attr_name"]').first().focus();//失去焦点
			_project.product_attr_event();
			
		});
		
		
		//添加属性选项
		$('[name="product-attr-add-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.ss_product_id = _project.ss_product_id;
			form_input.ss_product_attr_name = $.trim($('[name="ss_product_attr_name"]').val());
			form_input.ss_product_attr_sort = $.trim($('[name="ss_product_attr_sort"]').val());
			form_input.ss_product_attr_info = $.trim($('[name="ss_product_attr_info"]').val());
			form_input.ss_product_attr_parent_id = $.trim($('[name="ss_product_attr_parent_id"]').val());
			form_input.ss_product_attr_required = $('[name="ss_product_attr_required"]').is(':checked')? 1 : 0;
			form_input.ss_product_attr_stock = $.trim($('[name="ss_product_attr_stock"]').val());
			form_input.ss_product_attr_money = $.trim($('[name="ss_product_attr_money"]').val());
			
			try {
				if(form_input.ss_product_attr_name == '') throw "属性名称不能为空";
				if(form_input.ss_product_attr_sort == ""){
					delete form_input.ss_product_attr_sort;
				}
				
				//价格
				if(form_input.ss_product_attr_money == ""){
					delete form_input.ss_product_attr_money;
				}else{
					var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
					if( money_format.test(form_input.ss_product_attr_money) ){
						form_input.ss_product_attr_money = parseInt((parseFloat(form_input.ss_product_attr_money).toFixed(2))*100);//元转为分
					}else{
						throw "售卖单价输入有误，格式必须是整数或者是最多两位小数";
					}
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
				request:["SOFTSTOREADMINPRODUCTATTRIBUTEADD", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					$btn.removeClass('disabled');
					$('[name="product-attr-add-input"]').first().trigger("click", [form_input.ss_product_attr_parent_id]);
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
		});
		
		
		//编辑属性选项
		$('[name="product-attr-edit-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.ss_product_attr_id = $.trim($('[name="ss_product_attr_id"]').val());
			form_input.ss_product_attr_name = $.trim($('[name="ss_product_attr_name"]').val());
			form_input.ss_product_attr_sort = $.trim($('[name="ss_product_attr_sort"]').val());
			form_input.ss_product_attr_info = $.trim($('[name="ss_product_attr_info"]').val());
			form_input.ss_product_attr_parent_id = $.trim($('[name="ss_product_attr_parent_id"]').val());
			form_input.ss_product_attr_required = $('[name="ss_product_attr_required"]').is(':checked')? 1 : 0;
			form_input.ss_product_attr_stock = $.trim($('[name="ss_product_attr_stock"]').val());
			form_input.ss_product_attr_money = $.trim($('[name="ss_product_attr_money"]').val());
			
			try {
				if(form_input.ss_product_attr_name == '') throw "属性名称不能为空";
				if(form_input.ss_product_attr_sort == ""){
					delete form_input.ss_product_attr_sort;
				}
				
				//价格
				if(form_input.ss_product_attr_money == ""){
					delete form_input.ss_product_attr_money;
				}else{
					var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
					if( money_format.test(form_input.ss_product_attr_money) ){
						form_input.ss_product_attr_money = parseInt((parseFloat(form_input.ss_product_attr_money).toFixed(2))*100);//元转为分
					}else{
						throw "售卖单价输入有误，格式必须是整数或者是最多两位小数";
					}
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
				request:["SOFTSTOREADMINPRODUCTATTRIBUTEEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					$btn.removeClass('disabled');
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
			
		});
		
		
		
	},
	
	
	
	//产品的分类 事件
	product_type_event : function(){
		var _project = WangAho(this.id);
		
		//回车
		this.keyup();
		
		$('[name="product-type-checkbox"]').unbind("background").bind("background", function(){
			$('[name="product-type-checkbox"]').each(function(){
				$(this).parent().removeClass("checkbox-checked");
			});
			
			$('[name="product-type-checkbox"]:checked').each(function(){
				$(this).parent().addClass("checkbox-checked");
			});
		});
		$('[name="product-type-checkbox"]').first().trigger("background");
		$('[name="product-type-checkbox"]').unbind("click").click(function(){
			$('[name="product-type-checkbox"]').first().trigger("background");
		});
		
		
		$('[name="product-type-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var _http = http();
			var ss_product_id = _project.ss_product_id;
			var ss_type_id = [];
			$('[name="product-type-checkbox"]:checked').each(function(){
				ss_type_id.push($(this).val());
			});
			
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["SOFTSTOREADMINPRODUCTTYPEEDIT", [{ss_product_id:ss_product_id, ss_type_id:ss_type_id}]],
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
	
	
	
	
	
	
	//产品的基础信息 事件
	product_basics_event : function(){
		var _project = WangAho(this.id);
		
		//回车
		this.keyup();
		
		$('[name="product-basics-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.ss_product_id = _project.ss_product_id;
			form_input.ss_product_name = $.trim($('[name="ss_product_name"]').val());
			form_input.ss_product_info = $.trim($('[name="ss_product_info"]').val());
			form_input.ss_product_source = $.trim($('[name="ss_product_source"]').val());
			form_input.ss_product_sort = $.trim($('[name="ss_product_sort"]').val());
			form_input.ss_product_state = $.trim($('[name="ss_product_state"]').val());
			
			try {
				if(form_input.ss_product_name == '') throw "产品名称不能为空";
				if(form_input.ss_product_sort == ""){
					delete form_input.ss_product_sort;
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
				request:["SOFTSTOREADMINPRODUCTEDIT", [form_input]],
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
	
	
	
	
	
	
	
	UEditor : false,//编辑器是否初始化
	
	product_details : function(){
		
		UEDITOR_HOME_URL = http("include/library/ueditor1_4_3_3-utf8/").href;
		
		var config = {
			//服务器统一请求接口路径
			serverUrl : "",
			//允许的最大字符数
			maximumWords : 1000000,
			//皮肤
			themePath : "/include/library/ueditor1_4_3_3-utf8/themes/",
			//如果sourceEditor是codemirror需要配置这项，codeMirror js加载的路径
			codeMirrorJsUrl : "include/library/ueditor1_4_3_3-utf8/third-party/codemirror/codemirror.js",
			//如果sourceEditor是codemirror需要配置这项，codeMirror css加载的路径
			codeMirrorCssUrl : "include/library/ueditor1_4_3_3-utf8/third-party/codemirror/codemirror.css",
			//customDomain : true,
			//定制工具栏图标
			toolbars:[
				//源代码
				//撤销//重做
				//加粗//斜体//下划线//字符边框//删除线//上标//下标//引用 
				//查询替换//清除格式//格式刷 //自动排版//纯文本粘贴模式//全选//清空文档
				//字体颜色//背景色//有序列表//无序列表//段前距//段后距//行间距  //自定义标题//段落格式//字体//字号  //从左向右输入//从右向左输入//首行缩进//居左对齐//居右对齐//居中对齐//两端对齐 //字母大写//字母小写  //超链接//取消链接//锚点  //默认//左浮动//右浮动//居中//单图上传//多图上传  //附件//视频//音乐//表情//涂鸦  //Baidu地图//插入Iframe//背景//分页//分隔线//日期//时间//特殊字符//编辑提示
				//插入表格//删除表格//"表格前插入行"//前插入行//删除行//前插入列//删除列//合并多个单元格//右合并单元格//下合并单元格//拆分成行//拆分成列//完全拆分单元格//表格属性//单元格属性//插入标题//删除表格标题
				// 图表//代码语言//打印//预览//全屏
				[ 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'blockquote', '|', 'searchreplace', 'removeformat', 'formatmatch', 'autotypeset', 'pasteplain', 'selectall', 'cleardoc', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'rowspacingtop','rowspacingbottom', 'lineheight', '|','customstyle', 'paragraph', 'fontfamily', 'fontsize' , '|', 'directionalityltr', 'directionalityrtl', 'indent', '|', 'justifyleft','justifyright','justifycenter', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|', 'link','unlink', 'anchor', '|', 'imagenone','imageleft', 'imageright', 'imagecenter', '|', 'simpleupload', 'insertimage', 'attachment', 'insertvideo', 'music','emotion', 'scrawl', 'map','insertframe', 'background','pagebreak','horizontal','date','time',  'spechars', '|', 'edittip', 'inserttable','deletetable',  'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittorows',  'splittocols', 'splittocells','edittable', 'edittd', 'inserttitle', 'deletecaption', '|', 'charts','insertcode','print', '|','preview','fullscreen']
			]
//				        'snapscreen', //截图
//				        'gmap', //Google地图
//						'help', //帮助
// 						'wordimage', //图片转存
//				        'template', //模板
//				        'webapp', //百度应用
//				        'drafts', // 从草稿箱加载
 
		};
		
		
		//加载js文件，css文件
		/*
		 * <link href="include/library/ueditor1_4_3_3-utf8/third-party/SyntaxHighlighter/styles/shCore.css" rel="stylesheet" type="text/css" />
		 * <link href="include/library/ueditor1_4_3_3-utf8/third-party/SyntaxHighlighter/styles/shThemeMidnight.css" rel="stylesheet" type="text/css" />
		 * <script type="text/javascript" src="include/library/ueditor1_4_3_3-utf8/third-party/SyntaxHighlighter/shCore.js"></script>
		 * 
		 * <script type="text/javascript" charset="utf-8" src="include/library/ueditor1_4_3_3-utf8/ueditor.config.js"></script>
		 * <script type="text/javascript" charset="utf-8" src="include/library/ueditor1_4_3_3-utf8/ueditor.all.min.js"> </script>
		 * <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
		 * <script type="text/javascript" charset="utf-8" src="include/library/ueditor1_4_3_3-utf8/lang/zh-cn/zh-cn.js"></script>
		 */
		//加载层-风格3
		layer.load(2);
		var _project = WangAho(this.id);
		var ueditor_config_js = include('include/library/ueditor1_4_3_3-utf8/ueditor.config.js');
		var ueditor_all_js = include('include/library/ueditor1_4_3_3-utf8/ueditor.all.min.js');
		var ueditor_lang_js = include('include/library/ueditor1_4_3_3-utf8/lang/zh-cn/zh-cn.js');
		$('[name="UEditor-javascript"]').append(ueditor_config_js.element);
		$('[name="UEditor-javascript"]').append(ueditor_all_js.element);
		$('[name="UEditor-javascript"]').append(ueditor_lang_js.element);
		ueditor_lang_js.ready(function(){
			layer.closeAll('loading');//关闭加载
			_project.UEditor = UE.getEditor("ss_product_details_editor", config);
		});
		
		//实例化编辑器
		//建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
		/*if( this.UEditor ){
			UE.delEditor("ss_product_details_editor");
			this.UEditor = UE.getEditor("ss_product_details_editor", config);
			//this.UEditor.render('ss_product_details_editor');
		}else{
			this.UEditor = UE.getEditor("ss_product_details_editor", config);
		}*/
		this.product_details_event();
	},
	
	
	product_details_event : function(){
		var _project = WangAho(this.id);
		
		//回车
		this.keyup();
		
		$('[name="product-details-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var _http = http();
			var ss_product_id = _project.ss_product_id;
			//获取内容
			var ss_product_details = _project.UEditor.getContent();
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["SOFTSTOREADMINPRODUCTEDIT", [{ss_product_id : ss_product_id, ss_product_details : ss_product_details}]],
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
	
	
	
	
	image_upload_list : [],
	
	image_upload_mime_limit : ['image/jpeg','image/pjpeg','image/png', 'image/x-png', 'image/gif', 'image/bmp'],
	
	product_image_event : function(){
		var _project = WangAho(this.id);
		
		//查看图片
		WangAho("index").image_look_event();
		//回车
		this.keyup();
		
		$('[action-button]').unbind("click").click(function(){
			var ids = WangAho("index").action_table_checked();
			var attr = $(this).attr("action-button");
			
			//排序
			if("sort" == attr){
				_project.product_image_edit_sort();
				return true;
			}
			//修改名称
			if("name" == attr){
				_project.product_image_edit_name();
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
												    _project.product_image_remove(ids);
												  }
				});
			}
			
		});
		
		
		//设为主图
		$('[name="product-image-edit-main"]').unbind("click").click(function(){
			if($(this).hasClass("disabled")){
				return false;
			}
			
			var ss_product_image_id = $(this).attr("data-id");
			
			//提交数据
			eonfox().submit({
				request : JSON.stringify({edit:["SOFTSTOREADMINPRODUCTIMAGEEDITMAIN",[{ss_product_image_id:ss_product_image_id, ss_product_image_main:1}]]}),
				callback : function(r){
					try {
						if( !r ){
							throw "未知错误";
						}
						if( (function(){try{ return r.errno;}catch(e){return false;}}()) ){
							throw r.error;
					        return false;
						}
						if( (function(){try{ return r.data.edit.errno;}catch(e){return false;}}()) ){
							throw r.data.edit.error;
					        return false;
						}
					}
					catch(err) {
				        layer.msg(err, {icon: 5, time: 2000});
				        return false;
				    }
					
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
					
			}});
			
		});
		
		
		
		//选择上传图片
		$('[name="product-image-select"]').unbind("click").click(function(){
			new eonfox().trigger_click($('[name="product-image-files"]').get(0));
		});
		
		
		//图片发生改变时执行
		$('[name="product-image-files"]').unbind("change").change(function() {
			var files = $('[name="product-image-files"]')[0].files;
			for(var i = 0; i < files.length; i++){
				var exist = false;
				if(_project.image_upload_list.length > 0){
					for(var n = 0; n < _project.image_upload_list.length; n ++){
						if(_project.image_upload_list[n].lastModified == files[i].lastModified &&
						_project.image_upload_list[n].name == files[i].name &&
						_project.image_upload_list[n].size == files[i].size &&
						_project.image_upload_list[n].type == files[i].type ){
							exist = true;//该图片存在
							break;
						}
					}
				}
				
				//判断图片类型是否合法
				var legal = false;
				for(var l in _project.image_upload_mime_limit){
					if(_project.image_upload_mime_limit[l] == files[i].type){
						legal = true;
						break;
					}
				}
				
				if(!legal){
					layer.msg("“"+files[i].name+"” 文件格式不合法，只能上传png、jpg、gif图片文件", {icon: 5, time: 3000});
					continue;
				}
				
				if(!exist){
					//去掉后缀名称
					files[i].title = files[i].name.replace(/\.[\w]{1,}$/, ""); 
					_project.image_upload_list.push(files[i]);
				}
			}
			
			$('[name="product-image-files"]').first().trigger("template");
		});
		
		
		//显示
		$('[name="product-image-files"]').unbind("template").on("template", function(){
			var is_upload = false;//是否需要上传
			var list = [];
			for(var i = 0; i < _project.image_upload_list.length; i ++){
				list[i] = {};
				list[i].src = new eonfox().file_url(_project.image_upload_list[i]);
				list[i].name = _project.image_upload_list[i].name;
				list[i].size = _project.image_upload_list[i].size;
				list[i].type = _project.image_upload_list[i].type;
				list[i].title = _project.image_upload_list[i].title;
				list[i].upload = _project.image_upload_list[i].upload? true : false;
				if(!list[i].upload){
					is_upload = true;//有需要上传的
				}
			}
			var html = template( WangAho().template("page/softstore/product_edit/image.html", "#content-image"), function(fn){
				return fn({list:list});
				});
			$('[name="product-image-upload-list"]').html(html);
			
			if( !is_upload ){
				//没有需要上传的
				$('[name="product-image-submit"]').addClass("disabled");
			}else{
				$('[name="product-image-submit"]').removeClass("disabled");
			}
			
			//修改图片名称
			$('[name="product-image-upload-list"] .product-image-name-input').unbind("input propertychange").bind("input propertychange", function(event){
				var id = parseInt($(this).attr("data-id"));
				_project.image_upload_list[id].title = $(this).val();
				//_project.product_image_event();//更新事件
			});
			_project.product_image_event();//更新事件
		});
		
		
		//清理已经上传的图片
		$('[name="product-image-clear"]').unbind("click").click(function(){
			if(_project.image_upload_list.length < 1){
				return false;
			}
			
			var image_upload_list = [];
			for(var i = 0; i < _project.image_upload_list.length; i ++){
				if(!_project.image_upload_list[i].upload){
					image_upload_list.push(_project.image_upload_list[i]);
				}
			}
			
			_project.image_upload_list = image_upload_list;
			$('[name="product-image-files"]').first().trigger("template");
		});
		
		//删除
		$('[name="product-image-delete"]').unbind("click").click(function(){
			if($(this).hasClass("disabled")){
				return false;
			}
			
			var id = parseInt($(this).attr("data-id"));
			if(_project.image_upload_list.length < 1){
				return false;
			}
			
			var image_upload_list = [];
			for(var i = 0; i < _project.image_upload_list.length; i ++){
				if(id != i){
					image_upload_list.push(_project.image_upload_list[i]);
				}
			}
			
			_project.image_upload_list = image_upload_list;
			$('[name="product-image-files"]').first().trigger("template");
		});
		
		
		//开始上传图片
		$('[name="product-image-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			_project.product_image_upload();
		});
		
	},
	
	
	//上传图片
	product_image_upload : function(){
		var _project = WangAho(this.id);
		if(_project.image_upload_list.length < 1){
				layer.msg("没有上传的文件", {icon: 5, time: 2000});
				return false;
			}
			
			var _http = http();
			var image_upload_obj;
			var image_upload_id;
			for(var i in _project.image_upload_list){
				if(!_project.image_upload_list[i].upload){
					image_upload_obj = _project.image_upload_list[i];
					image_upload_id = i;
					break;
				}
			}
			//如果没有上传对象，则刷新页面
			if(!image_upload_obj){
				console.log("全部上传完成");
				WangAho("index").scroll_constant(function(){
					_project.main();
				});
				return;
			}
			
			//获取七牛云uptoken
			var auth = null;
			//加载层-风格3
			layer.load(2);
			auth_config = {
				ss_product_id : _project.ss_product_id,
				file_name : image_upload_obj.title,
				file_type : image_upload_obj.type,
				file_size : image_upload_obj.size
			};
			eonfox().submit({
				request : JSON.stringify({
					qiniu : ["SOFTSTOREADMINPRODUCTIMAGEQINIUUPTOKEN", [auth_config]],
					}),
				async:false,
				callback : function(r){
					layer.closeAll('loading');//关闭加载
					if( !r ){
						layer.msg("获取uptoken失败，未知错误", {icon: 5, time: 3000});
						return;
					}
					if( (function(){try{ return r.data.qiniu.errno;}catch(e){return false;}}()) ){
						layer.msg(r.data.qiniu.error, {icon: 5, time: 3000});
						return;
					}
					
					auth = (function(){try{ return r.data.qiniu.data;}catch(e){return false;}}());
					
				}
			});
			
			if(!auth){
				$('[name="product-image-files"]').first().trigger("template");
				return;
			}
			
			
			$('[name="product-image-delete"][data-id="'+image_upload_id+'"]').addClass("disabled");
			
			var putExtra = {
				//文件原文件名
		    	fname: "",
		    	//用来放置自定义变量
		    	params: {},
		    	//用来限制上传文件类型
		    	mimeType: _project.image_upload_mime_limit
		    };
			var config = {};
			//文件资源名
			var observable = qiniu.upload(image_upload_obj, auth.key, auth.qiniu_uptoken, putExtra, config);
			// 上传开始
			var subscription = observable.subscribe({
		    	//接收上传进度信息
		    	next:function(res){
		        	//console.log("observer.next", res);
		        	var $_progress = $('[name="product-image-progress"][data-id="'+image_upload_id+'"]');
		        	if(res.total.percent == 100){
		        		$_progress.removeClass("progress-bar-warning progress-bar-striped active").addClass("progress-bar-success");
		        		$_progress.html('<span class="glyphicon glyphicon-ok"></span> 上传完成');
		        	}else{
		        		$_progress.removeClass("progress-bar-success").addClass("progress-bar-warning progress-bar-striped active");
		        		$_progress.attr("aria-valuenow", Math.floor(res.total.percent));
	        			$_progress.css("width", Math.floor(res.total.percent)+"%");
	        			$_progress.html(Math.floor(res.total.percent)+"%");
		        	}
		        	
		    	},
		    	//上传错误后触发
		    	error:function(err){
		    		layer.msg(err.message, {icon: 5, time: 3000});
		    		//console.log("observer.error", err);
		    	}, 
		    	//接收上传完成后的后端返回信息
		    	complete:function(res){
		    		//更改删除按钮
		    		$('[name="product-image-delete"][data-id="'+image_upload_id+'"]').removeClass("disabled");
					$('[name="product-image-delete"][data-id="'+image_upload_id+'"]').html('<span class="glyphicon glyphicon-repeat"></span> 清理');
		    		
		    		//已经上传了
		    		_project.image_upload_list[image_upload_id].upload = true;
		        	//console.log("observer.complete", res);
		        	//这里上传成功，将信息发送给后台，将图片状态设为1
		        	var form_input = {
		        		ss_product_image_hash : res.hash,
		        		ss_product_image_id : res.key,
		        		ss_product_image_format : res.format,
		        		ss_product_image_width : res.width,
		        		ss_product_image_height : res.height,
		        		ss_product_image_path : res.bucket
		        	};
					//提交数据
					eonfox().submit({
						request : JSON.stringify({
							state:["SOFTSTOREADMINPRODUCTIMAGEQINIUSTATE", [form_input]],
							}),
						recursion: true,
						callback : function(r){
							//console.log("状态已修改", r);
							if( !r ){
								layer.msg("获取uptoken失败，未知错误", {icon: 5, time: 3000});
								return;
							}
							if( (function(){try{ return r.data.state.errno;}catch(e){return false;}}()) ){
								layer.msg(r.data.state.error, {icon: 5, time: 3000});
								return;
							}
							
							_project.product_image_upload();
							return;
							}
					});
		      	}
		    }); 
	},
	
	
	
	
	//修改名称
	product_image_edit_name : function(){
		var obj = WangAho("index").action_table("name");
		if( !obj || !obj.length ){
			return false;
		}
		
		var request_array = [];
		request_array.push(["SOFTSTOREADMINPRODUCTIMAGEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["SOFTSTOREADMINPRODUCTIMAGEEDIT", [{ss_product_image_id:obj[i].id, ss_product_image_name:obj[i].value}]]);
		}
		
		var _project = WangAho(this.id);
		
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
	
	//排序
	product_image_edit_sort : function(){
		var obj = WangAho("index").action_table("sort");
		if( !obj || !obj.length ){
			return false;
		}
		
		var request_array = [];
		request_array.push(["SOFTSTOREADMINPRODUCTIMAGEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["SOFTSTOREADMINPRODUCTIMAGEEDIT", [{ss_product_image_id:obj[i].id, ss_product_image_sort:obj[i].value}]]);
		}
		
		var _project = WangAho(this.id);
		
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
	
	//删除
	product_image_remove :function(ids){
		if(!ids || !ids.length){
			return false;
		}
		var request_array = [];
		for(var i in ids){
			request_array.push(["SOFTSTOREADMINPRODUCTIMAGEQINIUREMOVE", [{ss_product_image_id:ids[i]}]]);
		}
		
		var _project = WangAho(this.id);
		
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
	
	
	
	file_upload_list : {},
	//是否需要上传
	file_upload_button : false,
	product_file_template : function(){
		var _project = WangAho(this.id);
		var _product_id = this.ss_product_id;
		_project.file_upload_button = false;//是否需要上传
		var list = [];
		if(typeof _project.file_upload_list[_product_id] == "object"){
			for(var i = 0; i < _project.file_upload_list[_product_id].length; i ++){
				list[i] = {};
				list[i].name = _project.file_upload_list[_product_id][i].name;
				list[i].size = _project.file_upload_list[_product_id][i].size;
				list[i].type = _project.file_upload_list[_product_id][i].type;
				list[i].title = _project.file_upload_list[_product_id][i].title;
				list[i].upload = _project.file_upload_list[_product_id][i].upload? true : false;
				list[i].error = _project.file_upload_list[_product_id][i].error? true : false;
				if(!list[i].upload && !list[i].error){
					_project.file_upload_button = true;//有需要上传的
				}
			}
		}
		
		var html = template( WangAho().template("page/softstore/product_edit/file.html", "#content-file"), function(fn){
			return fn({list:list, product_id:_product_id});
			});
			
		$('[name="product-file-upload-list"]').html(html);
		if( !_project.file_upload_button ){
			//没有需要上传的
			$('[name="product-file-submit"]').addClass("disabled");
		}else{
			$('[name="product-file-submit"]').removeClass("disabled");
		}
		_project.product_file_event();//更新事件
	},
	
	
	//排序
	product_file_edit_sort : function(){
		var obj = WangAho("index").action_table("sort");
		if( !obj || !obj.length ){
			return false;
		}
		
		var request_array = [];
		request_array.push(["SOFTSTOREADMINPRODUCTFILEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["SOFTSTOREADMINPRODUCTFILEEDIT", [{ss_product_file_id:obj[i].id, ss_product_file_sort:obj[i].value}]]);
		}
		
		var _project = WangAho(this.id);
		
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
	
	//修改文件名称
	product_file_edit_name : function(){
		var obj = WangAho("index").action_table("name");
		if( !obj || !obj.length ){
			return false;
		}
		
		var request_array = [];
		request_array.push(["SOFTSTOREADMINPRODUCTFILEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["SOFTSTOREADMINPRODUCTFILEEDIT", [{ss_product_file_id:obj[i].id, ss_product_file_name:obj[i].value}]]);
		}
		
		var _project = WangAho(this.id);
		
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
	
	//删除文件
	product_file_remove : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["SOFTSTOREADMINPRODUCTFILEQINIUREMOVE", [{ss_product_file_id:ids[i]}]]);
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
	
	//上传文件事件
	product_file_event : function(){
		var _project = WangAho(this.id);
		var _product_id = this.ss_product_id;
		
		//回车
		this.keyup();
		
		$('[action-button]').unbind("click").click(function(){
			var ids = WangAho("index").action_table_checked();
			var attr = $(this).attr("action-button");
			
			//排序
			if("sort" == attr){
				_project.product_file_edit_sort();
				return true;
			}
			//修改名称
			if("name" == attr){
				_project.product_file_edit_name();
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
												    _project.product_file_remove(ids);
												  }
				});
			}
			
		});
		
		
		//打开
		$('[name="product-file-add-input"]').unbind("click").click(function(){
			layer.closeAll();
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-plus\"></span> 添加附件",
			  	type: 1,
			  	offset: '0',
			  	area: [$(window).width()+"px", '100%'], //宽高
			  	content: template( WangAho().template("page/softstore/product_edit/file.html", "#add"), function(fn){
							return fn(_project.data);
							})
			});
			
			$('[name="product-file-files"]').first().focus();//失去焦点
			_project.product_file_template();//显示模板
			
		});
		
		//开始上传图片
		$('[name="product-file-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			_project.product_file_upload();
		});
		
		
		//选择上传图片
		$('[name="product-file-select"]').unbind("click").click(function(){
			new eonfox().trigger_click($('[name="product-file-files"]').get(0));
		});
		
		
		//图片发生改变时执行
		$('[name="product-file-files"]').unbind("change").change(function() {
			var files = $('[name="product-file-files"]')[0].files;
			for(var i = 0; i < files.length; i++){
				var exist = false;
				if(typeof _project.file_upload_list[_product_id] == "object" &&
				_project.file_upload_list[_product_id].length > 0){
					for(var n = 0; n < _project.file_upload_list[_product_id].length; n ++){
						if(_project.file_upload_list[_product_id][n].lastModified == files[i].lastModified &&
						_project.file_upload_list[_product_id][n].name == files[i].name &&
						_project.file_upload_list[_product_id][n].size == files[i].size &&
						_project.file_upload_list[_product_id][n].type == files[i].type ){
							exist = true;//该文件存在
							break;
						}
					}
				}
				
				if(!exist){
					//去掉后缀名称
					files[i].title = files[i].name.replace(/\.[\w]{1,}$/, ""); 
					
					if(typeof _project.file_upload_list[_product_id] != "object"){
						_project.file_upload_list[_product_id] = [];
					}
					
					_project.file_upload_list[_product_id].push(files[i]);
				}
			}
			
			_project.product_file_template();//显示模板
		});
		
		
		
		//修改图片名称
		$('[name="product-file-upload-list"] .product-file-name-input').unbind("input propertychange").bind("input propertychange", function(event){
			var id = parseInt($(this).attr("data-id"));
			var product_id = $(this).attr("data-product-id");
			if(typeof _project.file_upload_list[product_id] == 'object' && 
			typeof _project.file_upload_list[product_id][id] == 'object'){
				_project.file_upload_list[product_id][id].title = $(this).val();
			}
		});
		
		//清理已经上传的图片
		$('[name="product-file-clear"]').unbind("click").click(function(){
			if(typeof _project.file_upload_list[_product_id] != "object" || 
			_project.file_upload_list[_product_id].length < 1){
				return false;
			}
			
			//这里不能使用splice方法 
			var file_upload_list = [];
			for(var i = 0; i < _project.file_upload_list[_product_id].length; i ++){
				if( !_project.file_upload_list[_product_id][i].upload ){
					file_upload_list.push(_project.file_upload_list[_product_id][i]);
				}
			}
			_project.file_upload_list[_product_id] = file_upload_list;
			
			_project.product_file_template();//显示模板
		});
		//删除
		$('[name="product-file-delete"]').unbind("click").click(function(){
			if($(this).hasClass("disabled")){
				return false;
			}
			
			var id = parseInt($(this).attr("data-id"));
			var product_id = $(this).attr("data-product-id");
			if(typeof _project.file_upload_list[product_id] != "object" ||
			_project.file_upload_list[product_id].length < 1){
				return false;
			}
			
			//删除这个标识的文件
			_project.file_upload_list[product_id].splice(id, 1);
			
			/*var file_upload_list = [];
			for(var i = 0; i < _project.file_upload_list[product_id].length; i ++){
				if(id != i){
					file_upload_list.push(_project.file_upload_list[product_id][i]);
				}
			}
			//更新索引
			_project.file_upload_list[product_id] = file_upload_list;*/
			
			_project.product_file_template();//显示模板
		});
		
		
		
		
	},
	
	//文件上传
	product_file_upload : function(product_id){
		var _project = WangAho(this.id);
		var _product_id = product_id? product_id : this.ss_product_id;
		
		if(typeof _project.file_upload_list[_product_id] != "object" || 
		_project.file_upload_list[_product_id].length < 1){
			layer.msg("没有上传的文件", {icon: 5, time: 2000});
			return false;
		}
		
		var _http = http();
		var file_upload_obj;
		var file_upload_id;
		for(var i in _project.file_upload_list[_product_id]){
			//没有上传并且没有错误
			if(!_project.file_upload_list[_product_id][i].upload && !_project.file_upload_list[_product_id][i].error){
				file_upload_obj = _project.file_upload_list[_product_id][i];
				file_upload_id = i;
				break;
			}
		}
		//如果没有上传对象，则刷新页面
		if(!file_upload_obj){
			console.log("全部上传完成");
			_project.product_file_template();//显示模板
			WangAho("index").scroll_constant(function(){
				_project.main();
			});
			return;
		}
		
		
		//获取七牛云uptoken
		var auth = null;
		//加载层-风格3
		layer.load(2);
		auth_config = {
			ss_product_id : _product_id,
			file_format : file_upload_obj.name.substring(file_upload_obj.name.lastIndexOf(".")+1, file_upload_obj.name.length),
			file_name : file_upload_obj.title,
			file_type : file_upload_obj.type,
			file_size : file_upload_obj.size
		};
		eonfox().submit({
			request : JSON.stringify({
				qiniu : ["SOFTSTOREADMINPRODUCTFILEQINIUUPTOKEN", [auth_config]],
				}),
			async:false,
			callback : function(r){
				layer.closeAll('loading');//关闭加载
				if( !r ){
					layer.msg("获取uptoken失败，未知错误", {icon: 5, time: 3000});
					return;
				}
				if( (function(){try{ return r.data.qiniu.errno;}catch(e){return false;}}()) ){
					layer.msg(r.data.qiniu.error, {icon: 5, time: 3000});
					
					_project.file_upload_list[_product_id][file_upload_id].error = true;
					_project.product_file_upload(_product_id);//继续上传
					return;
				}
				
				auth = (function(){try{ return r.data.qiniu.data;}catch(e){return false;}}());
				
			}
		});
		
		if( !auth ){
			_project.product_file_template();//显示模板
			return;
		}
		
		
		$('[name="product-file-delete"][data-id="'+file_upload_id+'"][data-product-id="'+_product_id+'"]').addClass("disabled");
		
		var putExtra = {
			//文件原文件名
	    	fname: "",
	    	//用来放置自定义变量
	    	params: {},
	    	//用来限制上传文件类型
	    	mimeType: null
	    };
		var config = {};
		//文件资源名
		var observable = qiniu.upload(file_upload_obj, auth.key, auth.qiniu_uptoken, putExtra, config);
		// 上传开始
		var subscription = observable.subscribe({
	    	//接收上传进度信息
	    	next:function(res){
	        	//console.log("observer.next", res);
	        	var $_progress = $('[name="product-file-progress"][data-id="'+file_upload_id+'"][data-product-id="'+_product_id+'"]');
	        	if(res.total.percent == 100){
	        		$_progress.removeClass("progress-bar-warning progress-bar-striped active").addClass("progress-bar-success");
	        		$_progress.html('<span class="glyphicon glyphicon-ok"></span> 上传完成');
	        	}else{
	        		
	        		$_progress.removeClass("progress-bar-success").addClass("progress-bar-warning progress-bar-striped active");
	        		$_progress.attr("aria-valuenow", Math.floor(res.total.percent));
	        		$_progress.css("width", Math.floor(res.total.percent)+"%");
	        		$_progress.html(Math.floor(res.total.percent)+"%");
	        	}
	        	
	    	},
	    	//上传错误后触发
	    	error:function(err){
	    		layer.msg(err.message, {icon: 5, time: 3000});
	    		_project.file_upload_list[_product_id][file_upload_id].error = true;
	    		_project.product_file_upload(_product_id);//继续上传
	    		return;
	    		//console.log("observer.error", err);
	    	}, 
	    	//接收上传完成后的后端返回信息
	    	complete:function(res){
	    		//更改删除按钮
				$('[name="product-file-delete"][data-id="'+file_upload_id+'"][data-product-id="'+_product_id+'"]').html('<span class="glyphicon glyphicon-repeat"></span> 清理');
	    		$('[name="product-file-delete"][data-id="'+file_upload_id+'"][data-product-id="'+_product_id+'"]').removeClass("disabled");
	    		
	    		//已经上传了
	    		_project.file_upload_list[_product_id][file_upload_id].upload = true;
	        	//console.log("observer.complete", res);
	        	//这里上传成功，将信息发送给后台，将图片状态设为1
	        	var form_input = {
	        		ss_product_file_id : res.key,
	        		ss_product_file_hash : res.hash,
	        		ss_product_file_size : res.size,
	        		ss_product_file_type : res.type
	        	};
				//提交数据
				eonfox().submit({
					request : JSON.stringify({
						state:["SOFTSTOREADMINPRODUCTFILEQINIUSTATE", [form_input]],
						}),
					recursion: true,
					callback : function(r){
						//console.log("状态已修改", r);
						if( !r ){
							layer.msg("获取uptoken失败，未知错误", {icon: 5, time: 3000});
							return;
						}
						if( (function(){try{ return r.data.state.errno;}catch(e){return false;}}()) ){
							layer.msg(r.data.state.error, {icon: 5, time: 3000});
							return;
						}
						
						_project.product_file_upload(_product_id);//继续上传
						return;
						}
				});
	      	}
	    }); 
		
		
		
	},
	
	
	
	
	
	
	
	
});