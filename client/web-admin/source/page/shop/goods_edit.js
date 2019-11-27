WangAho({
	
	
	id : "shop/goods_edit",
	
	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	
	data : null,//数据
	
	
	shop_goods_id : "",//产品ID
	
	
	main : function(){
		var _project = WangAho(this.id);
		var _http = http();
		var action = (function(){try{ return _http.anchor.query.action;}catch(e){return false;}}());
		if( !action ){
			action = "basics";
		}
		_project.shop_goods_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if(!_project.shop_goods_id){
			http("#/shop/goods_list").request();
			return false;
		}
		
		//数据请求
		var request = {get:["SHOPADMINPGOODSGET", [{shop_goods_id:_project.shop_goods_id}]]};
		if( "type" === action ){
			request.type_module_shop_goods_option = ["APPLICATIONADMINTYPEOPTION",[{sort:["sort_asc"],module:"shop_goods_type"}]];
		}
		//产品属性
		if( "spu_sku" === action ){
			request.shop_goods_spu_option = ["SHOPADMINGOODSSPUOPTION",[{sort:["sort_asc","insert_time_asc"], search:{shop_goods_id:_project.shop_goods_id}}]];
			var tabs = (function(){try{ return _http.anchor.query.tabs;}catch(e){return false;}}());
			if( "spu" !== tabs ){
				var config = {search:{shop_goods_id:_project.shop_goods_id}};
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
				
				request.list = ["SHOPADMINGOODSSKULIST",[config]];
			}
		}
		
		
		if( "image" === action ){
			var config = {search:{shop_goods_id: _project.shop_goods_id}};
			
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
			request.list = ["SHOPADMINGOODSIMAGELIST",[config]];
			request.application_config = ["APPLICATIONCONFIG"];
		}
		
		
		if( "file" === action ){
			var config = {search:{shop_goods_id: _project.shop_goods_id}};
			
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
			request.list = ["SHOPADMINGOODSFILELIST",[config]];
			request.application_config = ["APPLICATIONCONFIG"];
		}
		
		
		
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
				if( !(function(){try{ return data.response.get.shop_goods_id;}catch(e){return false;}}()) ){
					setTimeout(function(){
						
						if( WangAho().history_previous_exist() ){
							WangAho().history_previous();//存在上一页则返回上一页
						}else{
							WangAho().history_remove();//删除本页的记录
							http("#/shop/goods_list").request();//返回列表页
						}
						
					}, 2000);
					return false;
				}
				
				
				//获取子页面参数
				_project.data.action = action;
				//获得配置数据
				_project.data.config = WangAho().data("config.json");
				
				WangAho("index").view(WangAho().template("page/shop/goods_edit.html", "#content"), _project.data, {
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
						var html = template( WangAho().template("page/shop/goods_edit/"+_project.data.action+".html", "#content"), function(fn){
							this.config.helper = WangAho("index").page_helper;
							
							this.helper("goods_type in_array", function(type_id, shop_goods_type){
								if(typeof shop_goods_type != 'object' || !shop_goods_type.length){
									return false;
								}
								
								var exist = false;
								for(var i in shop_goods_type){
									if( shop_goods_type[i].type_id == type_id){
										exist = true;
										break;
									}
								}
								return exist;
							});
							
							this.helper("nav-tabs-href", function(tabs){
								var _h = http();
								if(!_h.anchor.query){
									_h.anchor.query = {};
								}
								if( !tabs){
									delete _h.anchor.query.tabs;
								}else{
									_h.anchor.query.tabs = tabs;
								}
								
								if( _h.anchor.query.page ){
									delete _h.anchor.query.page;//删除分页
								}
								
								return http(_h).href;
							});
							
							
							return fn(_project.data);
						});
						if(!html){
							return template( WangAho().template("page/shop/goods_edit.html", "#warning"), function(fn){
								return fn(_project.data);
							});
						}else{
							return html;
						}
						
					}
					
					
					
				});
				
				if( "basics" === _project.data.action ){
					//调用 Chosen
					$('select[name="shop_goods_state"], select[name="shop_goods_stock_mode"], select[name="shop_goods_property"], select[name="shop_goods_index"]').chosen({
						width: '100%',
						//placeholder_text_single: '-', //默认值
						earch_contains: true,
						no_results_text: "没有匹配结果",
						case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
			        	//group_search: false //选项组是否可搜。此处搜索不可搜
					});
					
					_project.goods_basics_event();
				}else
				if( "details" === _project.data.action ){
					_project.goods_details();
				}else
				if( "type" === _project.data.action ){
					_project.goods_type_event();
				}else
				if( "spu_sku" === _project.data.action ){
					_project.goods_spu_event();
					_project.goods_sku_event();
				}else
				if( "image" === _project.data.action ){
					_project.goods_image_event();
				}else
				if( "file" === _project.data.action ){
					_project.goods_file_event();
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
				
				$('[name="goods-spu-add-submit"]').first().trigger("click");
				$('[name="goods-spu-edit-submit"]').first().trigger("click");
				
				$('[name="goods-sku-add-submit"]').first().trigger("click");
				$('[name="goods-sku-edit-submit"]').first().trigger("click");
				 
		        $('[name="goods-type-submit"]').first().trigger("click");
		        $('[name="goods-basics-submit"]').first().trigger("click");
		        $('[name="goods-details-submit"]').first().trigger("click");
		        $('[name="goods-image-submit"]').first().trigger("click");
		        $('[name="goods-file-submit"]').first().trigger("click");
			}
		});
	},
	
	
	

	goods_basics_event : function(){
		var _project = WangAho(this.id);
		//回车
		this.keyup();
		
		$('[name="goods-basics-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.shop_goods_id = _project.shop_goods_id;
			form_input.shop_goods_sn = $.trim($('[name="shop_goods_sn"]').val());
			form_input.shop_goods_name = $.trim($('[name="shop_goods_name"]').val());
			form_input.shop_goods_property = $.trim($('[name="shop_goods_property"]').val());
			form_input.shop_goods_index = $.trim($('[name="shop_goods_index"]').val());
			form_input.shop_goods_info = $.trim($('[name="shop_goods_info"]').val());
			form_input.shop_goods_sort = $.trim($('[name="shop_goods_sort"]').val());
			form_input.shop_goods_stock_mode = $.trim($('[name="shop_goods_stock_mode"]').val());
			form_input.shop_goods_keywords = $.trim($('[name="shop_goods_keywords"]').val());
			form_input.shop_goods_description = $.trim($('[name="shop_goods_description"]').val());
			form_input.shop_goods_state = $.trim($('[name="shop_goods_state"]').val());
			form_input.shop_goods_admin_note = $.trim($('[name="shop_goods_admin_note"]').val());
			
			try {
				if(form_input.shop_goods_name == '') throw "产品名称不能为空";
				if(form_input.shop_goods_sort == ""){
					delete form_input.shop_goods_sort;
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
				request:["SHOPADMINGOODSEDIT", [form_input]],
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
	goods_details : function(){
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
			_project.UEditor = UE.getEditor("shop_goods_details_editor", config);
		});
		this.goods_details_event();
		
	},
	goods_details_event : function(){
		var _project = WangAho(this.id);
		
		//回车
		this.keyup();
		
		$('[name="goods-details-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var _http = http();
			var shop_goods_id = _project.shop_goods_id;
			//获取内容
			var shop_goods_details = _project.UEditor.getContent();
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["SHOPADMINGOODSEDIT", [{shop_goods_id : shop_goods_id, shop_goods_details : shop_goods_details}]],
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
	
	
	
		
	
	//产品的分类 事件
	goods_type_event : function(){
		var _project = WangAho(this.id);
		
		//回车
		this.keyup();
		
		$('[name="goods-type-checkbox"]').unbind("background").bind("background", function(){
			$('[name="goods-type-checkbox"]').each(function(){
				$(this).parent().removeClass("checkbox-checked");
			});
			
			$('[name="goods-type-checkbox"]:checked').each(function(){
				$(this).parent().addClass("checkbox-checked");
			});
		});
		$('[name="goods-type-checkbox"]').first().trigger("background");
		$('[name="goods-type-checkbox"]').unbind("click").click(function(){
			$('[name="goods-type-checkbox"]').first().trigger("background");
		});
		
		
		$('[name="goods-type-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var _http = http();
			var shop_goods_id = _project.shop_goods_id;
			var type_id = [];
			$('[name="goods-type-checkbox"]:checked').each(function(){
				type_id.push($(this).val());
			});
			
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["SHOPADMINGOODSTYPEEDIT", [{shop_goods_id:shop_goods_id, type_id:type_id}]],
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
	
	
	
	
	
	goods_spu_remove:function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["SHOPADMINGOODSSPUREMOVE", [{shop_goods_spu_id:ids[i]}]]);
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
	
	
	goods_spu_sort:function(){
		var obj = WangAho("index").action_table("spu-sort");
		if( !obj || !obj.length ){
			return false;
		}
		
		var _project = WangAho(this.id);
		var request_array = [];
		request_array.push(["SHOPADMINGOODSSPUEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["SHOPADMINGOODSSPUEDIT", [{shop_goods_spu_id:obj[i].id, shop_goods_spu_sort:obj[i].value}]]);
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
	
	
	goods_spu_event : function(){
		var _project = WangAho(this.id);
		//回车
		this.keyup();
		//注册 checkbox
		WangAho("index").checkbox("checkbox-spu", "checkbox-spu-all");
		
		$('[action-button-spu]').unbind("click").click(function(){
			var ids = WangAho("index").action_table_checked("checkbox-spu");
			var attr = $(this).attr("action-button-spu");
			
			//排序
			if("sort" == attr){
				_project.goods_spu_sort();
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
												    _project.goods_spu_remove(ids);
												  }
				});
			}
			
		});
		
		
		//创建弹窗
		$('[name="goods-spu-add-input"]').unbind("click").click(function(event, shop_goods_spu_parent_id){
			layer.closeAll();
			
			//获取父级
			if(!shop_goods_spu_parent_id){
				var shop_goods_spu_parent_id = $(this).attr("data-parent");
			}
			if( shop_goods_spu_parent_id ){
				_project.data.shop_goods_spu_parent_id = shop_goods_spu_parent_id;
			}else{
				_project.data.shop_goods_spu_parent_id = "";
			}
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-plus\"></span> 添加属性",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/shop/goods_edit/spu_sku.html", "#spu-add"), function(fn){
							return fn(_project.data);
							})
			});
			
			//调用 Chosen
			$('select[name="shop_goods_spu_parent_id"]').chosen({
				width: '100%',
				//placeholder_text_single: '-', //默认值
				earch_contains: true,
				no_results_text: "没有匹配结果",
				case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
	        	//group_search: false //选项组是否可搜。此处搜索不可搜
			});
			
			
			$('input[name="shop_goods_spu_name"]').first().focus();//失去焦点
			_project.goods_spu_event();
		});
		
			
		$('[name="goods-spu-add-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.shop_goods_id = _project.shop_goods_id;
			form_input.shop_goods_spu_name = $.trim($('[name="shop_goods_spu_name"]').val());
			form_input.shop_goods_spu_sort = $.trim($('[name="shop_goods_spu_sort"]').val());
			form_input.shop_goods_spu_info = $.trim($('[name="shop_goods_spu_info"]').val());
			form_input.shop_goods_spu_parent_id = $.trim($('[name="shop_goods_spu_parent_id"]').val());
			form_input.shop_goods_spu_required = $('[name="shop_goods_spu_required"]').is(':checked')? 1 : 0;
			
			try {
				if(form_input.shop_goods_spu_name == '') throw "属性名称不能为空";
				if(form_input.shop_goods_spu_sort == ""){
					delete form_input.shop_goods_spu_sort;
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
				request:["SHOPADMINGOODSSPUADD", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					$btn.removeClass('disabled');
					$('[name="goods-spu-add-input"]').first().trigger("click", [form_input.shop_goods_spu_parent_id]);
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
		});
		
		
		$('[name="goods-spu-edit-input"]').unbind("click").click(function(){
			layer.closeAll();
			
			//获取父级
			var shop_goods_spu_parent_id = $(this).attr("data-parent");
			//获取ID
			var shop_goods_spu_id = $(this).attr("data-id");
			_project.data.goods_spu_data = null;
			if(shop_goods_spu_id && _project.data.response.shop_goods_spu_option.length){
				//是顶级
				if( !shop_goods_spu_parent_id ){
					for(var i in _project.data.response.shop_goods_spu_option){
						if(_project.data.response.shop_goods_spu_option[i].shop_goods_spu_id == shop_goods_spu_id){
							_project.data.goods_spu_data = _project.data.response.shop_goods_spu_option[i];
						}
					}
				}else{
					//不是顶级
					//先获取父级
					var goods_spu_parent_data = null;
					for(var i in _project.data.response.shop_goods_spu_option){
						if(_project.data.response.shop_goods_spu_option[i].shop_goods_spu_id == shop_goods_spu_parent_id){
							goods_spu_parent_data = _project.data.response.shop_goods_spu_option[i];
						}
					}
					
					//再获取当前数据
					if( goods_spu_parent_data.son ){
						for(var i in goods_spu_parent_data.son){
							if(goods_spu_parent_data.son[i].shop_goods_spu_id == shop_goods_spu_id){
								_project.data.goods_spu_data = goods_spu_parent_data.son[i];
							}
						}
					}
				}
			}
			
			//判断是否有编辑的数据
			if(!_project.data.goods_spu_data){
				layer.msg("没有可编辑的数据，请刷新页面重试！", {icon: 5, time: 2000});
				return false;
			}
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-edit\"></span> 编辑属性",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/shop/goods_edit/spu_sku.html", "#spu-edit"), function(fn){
							return fn(_project.data);
							})
			});
			
			//调用 Chosen
			$('select[name="shop_goods_spu_parent_id"]').chosen({
				width: '100%',
				//placeholder_text_single: '-', //默认值
				earch_contains: true,
				no_results_text: "没有匹配结果",
				case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
	        	//group_search: false //选项组是否可搜。此处搜索不可搜
			});
			
			
			$('input[name="shop_goods_spu_name"]').first().focus();//失去焦点
			_project.goods_spu_event();
			
		});
		
	
		
		$('[name="goods-spu-edit-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.shop_goods_id = _project.shop_goods_id;
			form_input.shop_goods_spu_id = $.trim($('[name="shop_goods_spu_id"]').val());
			form_input.shop_goods_spu_name = $.trim($('[name="shop_goods_spu_name"]').val());
			form_input.shop_goods_spu_sort = $.trim($('[name="shop_goods_spu_sort"]').val());
			form_input.shop_goods_spu_info = $.trim($('[name="shop_goods_spu_info"]').val());
			form_input.shop_goods_spu_parent_id = $.trim($('[name="shop_goods_spu_parent_id"]').val());
			form_input.shop_goods_spu_required = $('[name="shop_goods_spu_required"]').is(':checked')? 1 : 0;
			
			try {
				if(form_input.shop_goods_spu_name == '') throw "属性名称不能为空";
				if(form_input.shop_goods_spu_sort == ""){
					delete form_input.shop_goods_spu_sort;
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
				request:["SHOPADMINGOODSSPUEDIT", [form_input]],
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
	
	
	
	
	
	goods_sku_remove:function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["SHOPADMINGOODSSKUREMOVE", [{shop_goods_sku_id:ids[i]}]]);
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
		
	
	
	
	
	goods_sku_event : function(){
		var _project = WangAho(this.id);
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		//回车
		this.keyup();
		
		$('[action-button-sku]').unbind("click").click(function(){
			var ids = WangAho("index").action_table_checked();
			var attr = $(this).attr("action-button-sku");
			
			if(ids.length < 1){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			//删除
			if("remove" == attr){
				layer.msg('你确定要删除么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消'],yes: function(index){
												    layer.close(index);
												    _project.goods_sku_remove(ids);
												  }
				});
			}
			
		});
		
		
		$('[name="shop_goods_spu_id"]').unbind("background").bind("background", function(){
			$('[name="shop_goods_spu_id"]').each(function(){
				$(this).parent().removeClass("checkbox-checked");
			});
			
			$('[name="shop_goods_spu_id"]:checked').each(function(){
				$(this).parent().addClass("checkbox-checked");
			});
		});
		$('[name="shop_goods_spu_id"]').first().trigger("background");
		$('[name="shop_goods_spu_id"]').unbind("click").click(function(){
			$('[name="shop_goods_spu_id"]').first().trigger("background");
		});
		
		
		$('[name="goods-sku-add-input"]').unbind("click").click(function(){
			layer.closeAll();
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-plus\"></span> 添加规格",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/shop/goods_edit/spu_sku.html", "#sku-add"), function(fn){
							return fn(_project.data);
							}),
			});
			
			$('input[name="shop_goods_sku_stock"]').first().focus();//获得焦点
			_project.goods_sku_event();
		});
		
			
		$('[name="goods-sku-add-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			if( !_application_config_credit ){
				layer.msg("积分配置异常", {icon: 5, time: 2000});
				return false;
			}
			
			var form_input = {};
			var _http = http();
			var shop_goods_property = _project.data.response.get.shop_goods_property;
			form_input.shop_goods_id = _project.shop_goods_id;
			form_input.shop_goods_sku_price = $.trim($('[name="shop_goods_sku_price"]').val());
			form_input.shop_goods_sku_market_price = $.trim($('[name="shop_goods_sku_market_price"]').val());
			form_input.shop_goods_sku_stock = $.trim($('[name="shop_goods_sku_stock"]').val());
			form_input.shop_goods_sku_info = $.trim($('[name="shop_goods_sku_info"]').val());
			
			//获取 库存售价的属性 数组
			form_input.shop_goods_spu_id = [];
			$('[name="shop_goods_spu_id"]:checked').each(function(){
				form_input.shop_goods_spu_id.push( $(this).val() );
			});
			
			//console.log('_application_config_credit', _application_config_credit);
			
			try {
				if( !form_input.shop_goods_spu_id.length ) throw "规格的属性不能为空";
				
				//普通商品
				if( shop_goods_property == 0){
					var scale = 100;//单位
					var precision = 2;//精度
				}else{
					var scale = _application_config_credit.scale;//单位
					var precision = _application_config_credit.precision;//精度
				}
				
				var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
				//市场价格
				if(form_input.shop_goods_sku_market_price == ""){
					delete form_input.shop_goods_sku_market_price;
				}else{
					if( money_format.test(form_input.shop_goods_sku_market_price) ){
						form_input.shop_goods_sku_market_price = ((parseFloat(form_input.shop_goods_sku_market_price).toFixed(precision))*scale).toFixed(0);
					}else{
						if( parseInt(precision) ){
							throw "市场价输入有误，格式必须是大于0的整数或者"+precision+"位小数";
						}else{
							throw "市场价输入有误，格式必须是大于0的整数";
						}
					}
				}
				
				//价格
				if(form_input.shop_goods_sku_price == ""){
					delete form_input.shop_goods_sku_price;
				}else{
					if( money_format.test(form_input.shop_goods_sku_price) ){
						form_input.shop_goods_sku_price = ((parseFloat(form_input.shop_goods_sku_price).toFixed(precision))*scale).toFixed(0);
					}else{
						if( parseInt(precision) ){
							throw "售卖单价输入有误，格式必须是大于0的整数或者"+precision+"位小数";
						}else{
							throw "售卖单价输入有误，格式必须是大于0的整数";
						}
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
				request:["SHOPADMINGOODSSKUADD", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					$('[name="goods-sku-add-input"]').first().trigger("click");
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
		});
	
		
		
		$('[name="goods-sku-edit-input"]').unbind("click").click(function(){
			if( !_application_config_credit ){
				layer.msg("积分配置异常", {icon: 5, time: 2000});
				return false;
			}
			
			var shop_goods_sku_id = $(this).attr("data-id");
			
			_project.data.goods_sku_data = null;
			if( shop_goods_sku_id && _project.data.response.list.data.length ){
				for(var i in _project.data.response.list.data){
					if(_project.data.response.list.data[i].shop_goods_sku_id == shop_goods_sku_id){
						_project.data.goods_sku_data = _project.data.response.list.data[i];
					}
				}
			}
			
			//判断是否有编辑的数据
			if(!_project.data.goods_sku_data){
				layer.msg("没有可编辑的数据，请刷新页面重试！", {icon: 5, time: 2000});
				return false;
			}
			
			layer.closeAll();
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-edit\"></span> 编辑规格",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/shop/goods_edit/spu_sku.html", "#sku-edit"), function(fn){
			  				this.helper("goods_spu_id-checkbox-match", function(goods_spu_id, goods_spu_ids){
			  					var reg_exp = new RegExp("\,"+goods_spu_id+"\,","g");
			  					var m = goods_spu_ids.match(reg_exp);
			  					
			  					if(m){
			  						return true;
			  					}else{
			  						return false;
			  						}
			  					
			  				});
			  				
			  				_project.data.application_config_credit = _application_config_credit;
							return fn(_project.data);
							})
			});
			
			$('input[name="shop_goods_sku_stock"]').first().focus();//获得焦点
			_project.goods_sku_event();
		});
	
		
			
		$('[name="goods-sku-edit-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			if( !_application_config_credit ){
				layer.msg("积分配置异常", {icon: 5, time: 2000});
				return false;
			}
			
			var form_input = {};
			var _http = http();
			var shop_goods_property = _project.data.response.get.shop_goods_property;
			form_input.shop_goods_sku_id = $.trim($('[name="shop_goods_sku_id"]').val());
			form_input.shop_goods_id = _project.shop_goods_id;
			form_input.shop_goods_sku_stock = $.trim($('[name="shop_goods_sku_stock"]').val());
			form_input.shop_goods_sku_info = $.trim($('[name="shop_goods_sku_info"]').val());
			form_input.shop_goods_sku_price = $.trim($('[name="shop_goods_sku_price"]').val());
			form_input.shop_goods_sku_market_price = $.trim($('[name="shop_goods_sku_market_price"]').val());
			
			//获取 库存售价的属性 数组
			form_input.shop_goods_spu_id = [];
			$('[name="shop_goods_spu_id"]:checked').each(function(){
				form_input.shop_goods_spu_id.push( $(this).val() );
			});
			
			try {
				if( !form_input.shop_goods_spu_id.length ) throw "规格的属性不能为空";
				
				//普通商品
				if( shop_goods_property == 0){
					var scale = 100;//单位
					var precision = 2;//精度
				}else{
					var scale = _application_config_credit.scale;//单位
					var precision = _application_config_credit.precision;//精度
				}
				
				var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
				//市场价格
				if(form_input.shop_goods_sku_market_price == ""){
					delete form_input.shop_goods_sku_market_price;
				}else{
					if( money_format.test(form_input.shop_goods_sku_market_price) ){
						form_input.shop_goods_sku_market_price = ((parseFloat(form_input.shop_goods_sku_market_price).toFixed(precision))*scale).toFixed(0);
					}else{
						if( parseInt(precision) ){
							throw "市场价输入有误，格式必须是大于0的整数或者"+precision+"位小数";
						}else{
							throw "市场价输入有误，格式必须是大于0的整数";
						}
					}
				}
				
				//价格
				if(form_input.shop_goods_sku_price == ""){
					delete form_input.shop_goods_sku_price;
				}else{
					if( money_format.test(form_input.shop_goods_sku_price) ){
						form_input.shop_goods_sku_price = ((parseFloat(form_input.shop_goods_sku_price).toFixed(precision))*scale).toFixed(0);
					}else{
						if( parseInt(precision) ){
							throw "售卖单价输入有误，格式必须是大于0的整数或者"+precision+"位小数";
						}else{
							throw "售卖单价输入有误，格式必须是大于0的整数";
						}
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
				request:["shopADMINgoodsSKUEDIT", [form_input]],
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
	
	
	
	image_upload_list : {},
	image_upload_mime_limit : ['image/jpeg','image/pjpeg','image/png', 'image/x-png', 'image/gif', 'image/bmp'],
	//是否需要上传
	image_upload_button : false,
	goods_image_template : function(){
		var _project = WangAho(this.id);
		
		var _shop_goods_id = this.shop_goods_id;
		_project.image_upload_button = false;//是否需要上传
		var list = [];
		if(typeof _project.image_upload_list[_shop_goods_id] == "object"){
			for(var i = 0; i < _project.image_upload_list[_shop_goods_id].length; i ++){
				list[i] = {};
				list[i].src = new eonfox().file_url(_project.image_upload_list[_shop_goods_id][i]);
				list[i].name = _project.image_upload_list[_shop_goods_id][i].name;
				list[i].size = _project.image_upload_list[_shop_goods_id][i].size;
				list[i].type = _project.image_upload_list[_shop_goods_id][i].type;
				list[i].title = _project.image_upload_list[_shop_goods_id][i].title;
				list[i].upload = _project.image_upload_list[_shop_goods_id][i].upload? true : false;
				list[i].error = _project.image_upload_list[_shop_goods_id][i].error? true : false;
				if(!list[i].upload && !list[i].error){
					_project.image_upload_button = true;//有需要上传的
				}
			}
		}
		
		var html = template( WangAho().template("page/shop/goods_edit/image.html", "#content-image"), function(fn){
			return fn({list:list, goods_id:_shop_goods_id});
			});
			
		$('[name="goods-image-upload-list"]').html(html);
		if( !_project.image_upload_button ){
			//没有需要上传的
			$('[name="goods-image-submit"]').addClass("disabled");
		}else{
			$('[name="goods-image-submit"]').removeClass("disabled");
		}
		_project.goods_image_event();//更新事件
	},
	
	
	//排序
	goods_image_edit_sort : function(){
		var obj = WangAho("index").action_table("sort");
		if( !obj || !obj.length ){
			return false;
		}
		
		var request_array = [];
		request_array.push(["SHOPADMINGOODSIMAGEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["SHOPADMINGOODSIMAGEEDIT", [{shop_goods_image_id:obj[i].id, image_sort:obj[i].value}]]);
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
	
	//修改名称
	goods_image_edit_name : function(){
		var obj = WangAho("index").action_table("name");
		if( !obj || !obj.length ){
			return false;
		}
		
		var request_array = [];
		request_array.push(["SHOPADMINGOODSIMAGEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["SHOPADMINGOODSIMAGEEDIT", [{shop_goods_image_id:obj[i].id, image_name:obj[i].value}]]);
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
	goods_image_remove : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["SHOPADMINGOODSIMAGEQINIUREMOVE", [{shop_goods_image_id:ids[i]}]]);
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
	
	
	
	goods_image_set_main : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		var request_array = [];
		request_array.push(["SHOPADMINGOODSIMAGEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in ids){
			request_array.push(["SHOPADMINGOODSIMAGEMAINEDIT", [{shop_goods_image_id:ids[i], shop_goods_image_main:1}]]);
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
	
	goods_image_cancel_main : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		var request_array = [];
		request_array.push(["SHOPADMINGOODSIMAGEEDITCHECK"]);
		for(var i in ids){
			request_array.push(["SHOPADMINGOODSIMAGEMAINEDIT", [{shop_goods_image_id:ids[i], shop_goods_image_main:0}]]);
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
	
	
	//上传文件事件
	goods_image_event : function(){
		var _project = WangAho(this.id);
		var _shop_goods_id = this.shop_goods_id;
		
		//查看图片
		WangAho("index").image_look_event();
		//回车
		this.keyup();
		
		$('[action-button]').unbind("click").click(function(){
			var ids = WangAho("index").action_table_checked();
			var attr = $(this).attr("action-button");
			
			//排序
			if("sort" == attr){
				_project.goods_image_edit_sort();
				return true;
			}
			//修改名称
			if("name" == attr){
				_project.goods_image_edit_name();
				return true;
			}
			
			if(ids.length < 1){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			//设为主图
			if("set-main" == attr){
				_project.goods_image_set_main(ids);
				return true;
			}
			
			//取消主图
			if("cancel-main" == attr){
				_project.goods_image_cancel_main(ids);
				return true;
			}
			
			//删除
			if("remove" == attr){
				layer.msg('你确定要删除么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消'],yes: function(index){
												    layer.close(index);
												    _project.goods_image_remove(ids);
												  }
				});
			}
			
		});
		
		
		//打开
		$('[name="goods-image-add-input"]').unbind("click").click(function(){
			layer.closeAll();
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-plus\"></span> 添加图片",
			  	type: 1,
			  	offset: '0',
			  	area: [$(window).width()+"px", '100%'], //宽高
			  	content: template( WangAho().template("page/shop/goods_edit/image.html", "#add"), function(fn){
							return fn(_project.data);
							})
			});
			
			$('[name="goods-image-files"]').first().focus();//失去焦点
			_project.goods_image_template();//显示模板
			
		});
		
		//开始上传图片
		$('[name="goods-image-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			_project.goods_image_upload();
		});
		
		
		//选择上传图片
		$('[name="goods-image-select"]').unbind("click").click(function(){
			new eonfox().trigger_click($('[name="goods-image-files"]').get(0));
		});
		
		
		//图片发生改变时执行
		$('[name="goods-image-files"]').unbind("change").change(function() {
			var files = $('[name="goods-image-files"]')[0].files;
			for(var i = 0; i < files.length; i++){
				var exist = false;
				if(typeof _project.image_upload_list[_shop_goods_id] == "object" &&
				_project.image_upload_list[_shop_goods_id].length > 0){
					for(var n = 0; n < _project.image_upload_list[_shop_goods_id].length; n ++){
						if(_project.image_upload_list[_shop_goods_id][n].lastModified == files[i].lastModified &&
						_project.image_upload_list[_shop_goods_id][n].name == files[i].name &&
						_project.image_upload_list[_shop_goods_id][n].size == files[i].size &&
						_project.image_upload_list[_shop_goods_id][n].type == files[i].type ){
							exist = true;//该文件存在
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
					
					if(typeof _project.image_upload_list[_shop_goods_id] != "object"){
						_project.image_upload_list[_shop_goods_id] = [];
					}
					
					_project.image_upload_list[_shop_goods_id].push(files[i]);
				}
			}
			
			_project.goods_image_template();//显示模板
		});
		
		
		//修改图片名称
		$('[name="goods-image-upload-list"] .goods-image-name-input').unbind("input propertychange").bind("input propertychange", function(event){
			var id = parseInt($(this).attr("data-id"));
			var goods_id = $(this).attr("data-goods-id");
			if(typeof _project.image_upload_list[goods_id] == 'object' && 
			typeof _project.image_upload_list[goods_id][id] == 'object'){
				_project.image_upload_list[goods_id][id].title = $(this).val();
			}
		});
		
		//清理已经上传的图片
		$('[name="goods-image-clear"]').unbind("click").click(function(){
			if(typeof _project.image_upload_list[_shop_goods_id] != "object" || 
			_project.image_upload_list[_shop_goods_id].length < 1){
				return false;
			}
			
			//这里不能使用splice方法 
			var image_upload_list = [];
			for(var i = 0; i < _project.image_upload_list[_shop_goods_id].length; i ++){
				if( !_project.image_upload_list[_shop_goods_id][i].upload ){
					image_upload_list.push(_project.image_upload_list[_shop_goods_id][i]);
				}
			}
			_project.image_upload_list[_shop_goods_id] = image_upload_list;
			
			_project.goods_image_template();//显示模板
		});
		
		
		//删除
		$('[name="goods-image-delete"]').unbind("click").click(function(){
			if($(this).hasClass("disabled")){
				return false;
			}
			
			var id = parseInt($(this).attr("data-id"));
			var goods_id = $(this).attr("data-goods-id");
			if(typeof _project.image_upload_list[goods_id] != "object" ||
			_project.image_upload_list[goods_id].length < 1){
				return false;
			}
			
			//删除这个标识的文件
			_project.image_upload_list[goods_id].splice(id, 1);
			
			/*var image_upload_list = [];
			for(var i = 0; i < _project.image_upload_list[goods_id].length; i ++){
				if(id != i){
					image_upload_list.push(_project.image_upload_list[goods_id][i]);
				}
			}
			//更新索引
			_project.image_upload_list[goods_id] = image_upload_list;*/
			
			_project.goods_image_template();//显示模板
		});
		
		
		
		
	},
	
	
	//图片上传
	goods_image_upload : function(goods_id){
		var _project = WangAho(this.id);
		var _shop_goods_id = goods_id? goods_id : this.shop_goods_id;
		
		if(typeof _project.image_upload_list[_shop_goods_id] != "object" || 
		_project.image_upload_list[_shop_goods_id].length < 1){
			layer.msg("没有上传的图片", {icon: 5, time: 2000});
			return false;
		}
		
		var _http = http();
		var file_upload_obj;
		var file_upload_id;
		for(var i in _project.image_upload_list[_shop_goods_id]){
			//没有上传并且没有错误
			if(!_project.image_upload_list[_shop_goods_id][i].upload && !_project.image_upload_list[_shop_goods_id][i].error){
				file_upload_obj = _project.image_upload_list[_shop_goods_id][i];
				file_upload_id = i;
				break;
			}
		}
		//如果没有上传对象，则刷新页面
		if(!file_upload_obj){
			console.log("全部上传完成");
			_project.goods_image_template();//显示模板
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
			shop_goods_id : _shop_goods_id,
			image_format : file_upload_obj.name.substring(file_upload_obj.name.lastIndexOf(".")+1, file_upload_obj.name.length),
			image_name : file_upload_obj.title,
			image_type : file_upload_obj.type,
			image_size : file_upload_obj.size
		};
		eonfox().submit({
			request : JSON.stringify({
				qiniu : ["SHOPADMINGOODSIMAGEQINIUUPTOKEN", [auth_config]],
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
					
					_project.image_upload_list[_shop_goods_id][file_upload_id].error = true;
					_project.goods_image_upload(_shop_goods_id);//继续上传
					return;
				}
				
				auth = (function(){try{ return r.data.qiniu.data;}catch(e){return false;}}());
				
			}
		});
		
		if( !auth ){
			_project.goods_image_template();//显示模板
			return;
		}
		
		//已经上传了
	    _project.image_upload_list[_shop_goods_id][file_upload_id].upload = true;
		
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
		var observable = qiniu.upload(file_upload_obj, auth.image_id, auth.qiniu_uptoken, putExtra, config);
		// 上传开始
		var subscription = observable.subscribe({
	    	//接收上传进度信息
	    	next:function(res){
	        	//console.log("observer.next", res);
	        	var $_progress = $('[name="goods-image-progress"][data-id="'+file_upload_id+'"][data-goods-id="'+_shop_goods_id+'"]');
	        	if(res.total.percent == 100){
	        		$('[name="goods-image-delete"][data-id="'+file_upload_id+'"][data-goods-id="'+_shop_goods_id+'"]').removeClass("disabled");
	        		$_progress.removeClass("progress-bar-warning progress-bar-striped active").addClass("progress-bar-success");
	        		$_progress.html('<span class="glyphicon glyphicon-ok"></span> 上传完成');
	        	}else{
	        		$('[name="goods-image-delete"][data-id="'+file_upload_id+'"][data-goods-id="'+_shop_goods_id+'"]').addClass("disabled");
	        		$_progress.removeClass("progress-bar-success").addClass("progress-bar-warning progress-bar-striped active");
	        		$_progress.attr("aria-valuenow", Math.floor(res.total.percent));
	        		$_progress.css("width", Math.floor(res.total.percent)+"%");
	        		$_progress.html(Math.floor(res.total.percent)+"%");
	        	}
	        	
	    	},
	    	//上传错误后触发
	    	error:function(err){
	    		layer.msg(err.message, {icon: 5, time: 3000});
	    		_project.image_upload_list[_shop_goods_id][file_upload_id].error = true;
	    		_project.goods_image_upload(_shop_goods_id);//继续上传
	    		return;
	    		//console.log("observer.error", err);
	    	}, 
	    	//接收上传完成后的后端返回信息
	    	complete:function(res){
	    		//更改删除按钮
				$('[name="goods-image-delete"][data-id="'+file_upload_id+'"][data-goods-id="'+_shop_goods_id+'"]').html('<span class="glyphicon glyphicon-repeat"></span> 清理');
	    		$('[name="goods-image-delete"][data-id="'+file_upload_id+'"][data-goods-id="'+_shop_goods_id+'"]').removeClass("disabled");
	    		
	        	//console.log("observer.complete", res);
	        	//这里上传成功，将信息发送给后台，将图片状态设为1
	        	var form_input = {
	        		shop_goods_image_id : auth.shop_goods_image_id,
	        		image_id : auth.image_id,
	        		image_format : res.format,
	        		image_width : res.width,
	        		image_height : res.height,
	        		image_hash : res.hash,
	        		image_path : res.bucket
	        	};
				//提交数据
				eonfox().submit({
					request : JSON.stringify({
						state:["SHOPADMINGOODSIMAGEQINIUSTATE", [form_input]],
						}),
					recursion: true,
					callback : function(r){
						if( !r ){
							layer.msg("更新上传状态失败，未知错误", {icon: 5, time: 3000});
							return;
						}
						if( (function(){try{ return r.data.state.errno;}catch(e){return false;}}()) ){
							layer.msg(r.data.state.error, {icon: 5, time: 3000});
							return;
						}
						
						_project.goods_image_upload(_shop_goods_id);//继续上传
						return;
						}
				});
	      	}
	    }); 
		
		
		
	},
	
	
	
	
		
	file_upload_list : {},
	//是否需要上传
	file_upload_button : false,
	goods_file_template : function(){
		var _project = WangAho(this.id);
		var _shop_goods_id = this.shop_goods_id;
		_project.file_upload_button = false;//是否需要上传
		var list = [];
		if(typeof _project.file_upload_list[_shop_goods_id] == "object"){
			for(var i = 0; i < _project.file_upload_list[_shop_goods_id].length; i ++){
				list[i] = {};
				list[i].name = _project.file_upload_list[_shop_goods_id][i].name;
				list[i].size = _project.file_upload_list[_shop_goods_id][i].size;
				list[i].type = _project.file_upload_list[_shop_goods_id][i].type;
				list[i].title = _project.file_upload_list[_shop_goods_id][i].title;
				list[i].upload = _project.file_upload_list[_shop_goods_id][i].upload? true : false;
				list[i].error = _project.file_upload_list[_shop_goods_id][i].error? true : false;
				if(!list[i].upload && !list[i].error){
					_project.file_upload_button = true;//有需要上传的
				}
			}
		}
		
		var html = template( WangAho().template("page/shop/goods_edit/file.html", "#content-file"), function(fn){
			return fn({list:list, goods_id:_shop_goods_id});
			});
			
		$('[name="goods-file-upload-list"]').html(html);
		if( !_project.file_upload_button ){
			//没有需要上传的
			$('[name="goods-file-submit"]').addClass("disabled");
		}else{
			$('[name="goods-file-submit"]').removeClass("disabled");
		}
		_project.goods_file_event();//更新事件
	},
	

	
	//排序
	goods_file_edit_sort : function(){
		var obj = WangAho("index").action_table("sort");
		if( !obj || !obj.length ){
			return false;
		}
		
		var request_array = [];
		request_array.push(["SHOPADMINGOODSFILEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["SHOPADMINGOODSFILEEDIT", [{shop_goods_file_id:obj[i].id, file_sort:obj[i].value}]]);
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
	goods_file_edit_name : function(){
		var obj = WangAho("index").action_table("name");
		if( !obj || !obj.length ){
			return false;
		}
		
		var request_array = [];
		request_array.push(["SHOPADMINGOODSFILEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["SHOPADMINGOODSFILEEDIT", [{shop_goods_file_id:obj[i].id, file_name:obj[i].value}]]);
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
	goods_file_remove : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["SHOPADMINGOODSFILEQINIUREMOVE", [{shop_goods_file_id:ids[i]}]]);
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
	goods_file_event : function(){
		var _project = WangAho(this.id);
		var _shop_goods_id = this.shop_goods_id;
		//回车
		this.keyup();
		$('[action-button]').unbind("click").click(function(){
			var ids = WangAho("index").action_table_checked();
			var attr = $(this).attr("action-button");
			
			//排序
			if("sort" == attr){
				_project.goods_file_edit_sort();
				return true;
			}
			//修改名称
			if("name" == attr){
				_project.goods_file_edit_name();
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
												    _project.goods_file_remove(ids);
												  }
				});
			}
			
		});
		
		
		//打开
		$('[name="goods-file-add-input"]').unbind("click").click(function(){
			layer.closeAll();
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-plus\"></span> 添加附件",
			  	type: 1,
			  	offset: '0',
			  	area: [$(window).width()+"px", '100%'], //宽高
			  	content: template( WangAho().template("page/shop/goods_edit/file.html", "#add"), function(fn){
							return fn(_project.data);
							})
			});
			
			$('[name="goods-file-files"]').first().focus();//失去焦点
			_project.goods_file_template();//显示模板
			
		});
		
		//开始上传图片
		$('[name="goods-file-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			_project.goods_file_upload();
		});
		
		
		//选择上传图片
		$('[name="goods-file-select"]').unbind("click").click(function(){
			new eonfox().trigger_click($('[name="goods-file-files"]').get(0));
		});
		
		
		//图片发生改变时执行
		$('[name="goods-file-files"]').unbind("change").change(function() {
			var files = $('[name="goods-file-files"]')[0].files;
			for(var i = 0; i < files.length; i++){
				var exist = false;
				if(typeof _project.file_upload_list[_shop_goods_id] == "object" &&
				_project.file_upload_list[_shop_goods_id].length > 0){
					for(var n = 0; n < _project.file_upload_list[_shop_goods_id].length; n ++){
						if(_project.file_upload_list[_shop_goods_id][n].lastModified == files[i].lastModified &&
						_project.file_upload_list[_shop_goods_id][n].name == files[i].name &&
						_project.file_upload_list[_shop_goods_id][n].size == files[i].size &&
						_project.file_upload_list[_shop_goods_id][n].type == files[i].type ){
							exist = true;//该文件存在
							break;
						}
					}
				}
				
				if(!exist){
					//去掉后缀名称
					files[i].title = files[i].name.replace(/\.[\w]{1,}$/, ""); 
					
					if(typeof _project.file_upload_list[_shop_goods_id] != "object"){
						_project.file_upload_list[_shop_goods_id] = [];
					}
					
					_project.file_upload_list[_shop_goods_id].push(files[i]);
				}
			}
			
			_project.goods_file_template();//显示模板
		});
		
		
		
		//修改图片名称
		$('[name="goods-file-upload-list"] .goods-file-name-input').unbind("input propertychange").bind("input propertychange", function(event){
			var id = parseInt($(this).attr("data-id"));
			var goods_id = $(this).attr("data-goods-id");
			if(typeof _project.file_upload_list[goods_id] == 'object' && 
			typeof _project.file_upload_list[goods_id][id] == 'object'){
				_project.file_upload_list[goods_id][id].title = $(this).val();
			}
		});
		
		//清理已经上传的图片
		$('[name="goods-file-clear"]').unbind("click").click(function(){
			if(typeof _project.file_upload_list[_shop_goods_id] != "object" || 
			_project.file_upload_list[_shop_goods_id].length < 1){
				return false;
			}
			
			//这里不能使用splice方法 
			var file_upload_list = [];
			for(var i = 0; i < _project.file_upload_list[_shop_goods_id].length; i ++){
				if( !_project.file_upload_list[_shop_goods_id][i].upload ){
					file_upload_list.push(_project.file_upload_list[_shop_goods_id][i]);
				}
			}
			_project.file_upload_list[_shop_goods_id] = file_upload_list;
			
			_project.goods_file_template();//显示模板
		});
		//删除
		$('[name="goods-file-delete"]').unbind("click").click(function(){
			if($(this).hasClass("disabled")){
				return false;
			}
			
			var id = parseInt($(this).attr("data-id"));
			var goods_id = $(this).attr("data-goods-id");
			if(typeof _project.file_upload_list[goods_id] != "object" ||
			_project.file_upload_list[goods_id].length < 1){
				return false;
			}
			
			//删除这个标识的文件
			_project.file_upload_list[goods_id].splice(id, 1);
			
			/*var file_upload_list = [];
			for(var i = 0; i < _project.file_upload_list[goods_id].length; i ++){
				if(id != i){
					file_upload_list.push(_project.file_upload_list[goods_id][i]);
				}
			}
			//更新索引
			_project.file_upload_list[goods_id] = file_upload_list;*/
			
			_project.goods_file_template();//显示模板
		});
		
	},
	
	//文件上传
	goods_file_upload : function(goods_id){
		var _project = WangAho(this.id);
		var _shop_goods_id = goods_id? goods_id : this.shop_goods_id;
		
		if(typeof _project.file_upload_list[_shop_goods_id] != "object" || 
		_project.file_upload_list[_shop_goods_id].length < 1){
			layer.msg("没有上传的文件", {icon: 5, time: 2000});
			return false;
		}
		
		var _http = http();
		var file_upload_obj;
		var file_upload_id;
		for(var i in _project.file_upload_list[_shop_goods_id]){
			//没有上传并且没有错误
			if(!_project.file_upload_list[_shop_goods_id][i].upload && !_project.file_upload_list[_shop_goods_id][i].error){
				file_upload_obj = _project.file_upload_list[_shop_goods_id][i];
				file_upload_id = i;
				break;
			}
		}
		//如果没有上传对象，则刷新页面
		if(!file_upload_obj){
			console.log("全部上传完成");
			_project.goods_file_template();//显示模板
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
			shop_goods_id : _shop_goods_id,
			file_format : file_upload_obj.name.substring(file_upload_obj.name.lastIndexOf(".")+1, file_upload_obj.name.length),
			file_name : file_upload_obj.title,
			file_type : file_upload_obj.type,
			file_size : file_upload_obj.size
		};
		eonfox().submit({
			request : JSON.stringify({
				qiniu : ["SHOPADMINGOODSFILEQINIUUPTOKEN", [auth_config]],
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
					
					_project.file_upload_list[_shop_goods_id][file_upload_id].error = true;
					_project.goods_file_upload(_shop_goods_id);//继续上传
					return;
				}
				
				auth = (function(){try{ return r.data.qiniu.data;}catch(e){return false;}}());
				
			}
		});
		
		if( !auth ){
			_project.goods_file_template();//显示模板
			return;
		}
		
		//开始上传
	    _project.file_upload_list[_shop_goods_id][file_upload_id].upload = true;
		
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
		var observable = qiniu.upload(file_upload_obj, auth.file_id, auth.qiniu_uptoken, putExtra, config);
		// 上传开始
		var subscription = observable.subscribe({
	    	//接收上传进度信息
	    	next:function(res){
	        	//console.log("observer.next", res);
	        	var $_progress = $('[name="goods-file-progress"][data-id="'+file_upload_id+'"][data-goods-id="'+_shop_goods_id+'"]');
	        	if(res.total.percent == 100){
	        		$('[name="goods-file-delete"][data-id="'+file_upload_id+'"][data-goods-id="'+_shop_goods_id+'"]').removeClass("disabled");
	        		$_progress.removeClass("progress-bar-warning progress-bar-striped active").addClass("progress-bar-success");
	        		$_progress.html('<span class="glyphicon glyphicon-ok"></span> 上传完成');
	        	}else{
	        		$('[name="goods-file-delete"][data-id="'+file_upload_id+'"][data-goods-id="'+_shop_goods_id+'"]').addClass("disabled");
	        		$_progress.removeClass("progress-bar-success").addClass("progress-bar-warning progress-bar-striped active");
	        		$_progress.attr("aria-valuenow", Math.floor(res.total.percent));
	        		$_progress.css("width", Math.floor(res.total.percent)+"%");
	        		$_progress.html(Math.floor(res.total.percent)+"%");
	        	}
	        	
	    	},
	    	//上传错误后触发
	    	error:function(err){
	    		layer.msg(err.message, {icon: 5, time: 3000});
	    		_project.file_upload_list[_shop_goods_id][file_upload_id].error = true;
	    		_project.goods_file_upload(_shop_goods_id);//继续上传
	    		return;
	    		//console.log("observer.error", err);
	    	}, 
	    	//接收上传完成后的后端返回信息
	    	complete:function(res){
	    		//更改删除按钮
				$('[name="goods-file-delete"][data-id="'+file_upload_id+'"][data-goods-id="'+_shop_goods_id+'"]').html('<span class="glyphicon glyphicon-repeat"></span> 清理');
	    		$('[name="goods-file-delete"][data-id="'+file_upload_id+'"][data-goods-id="'+_shop_goods_id+'"]').removeClass("disabled");
	    		
	        	//console.log("observer.complete", res);
	        	//这里上传成功，将信息发送给后台，将图片状态设为1
	        	var form_input = {
	        		shop_goods_file_id: auth.shop_goods_file_id,
	        		file_id : res.key,
	        		file_hash : res.hash,
	        		file_size : res.size,
	        		file_type : res.type
	        	};
				//提交数据
				eonfox().submit({
					request : JSON.stringify({
						state:["SHOPADMINGOODSFILEQINIUSTATE", [form_input]],
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
						
						_project.goods_file_upload(_shop_goods_id);//继续上传
						return;
						}
				});
	      	}
	    }); 
		
		
		
	},
	
	
		
	
	
	
	
	
	
	
	
	
});