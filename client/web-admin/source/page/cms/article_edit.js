WangAho({
	
	
	id : "cms/article_edit",
	
	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	
	data : null,//数据
	cms_article_id : "",//文章ID
	main : function(){
		var _project = WangAho(this.id);
		var _http = http();
		var action = (function(){try{ return _http.anchor.query.action;}catch(e){return false;}}());
		if( !action ){
			action = "basics";
		}
		
		_project.cms_article_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if( !_project.cms_article_id ){
			http("#/cms/article_list").request();
			return false;
		}
		
		//数据请求
		var request = {get:["CMSADMINARTICLEGET", [{cms_article_id:_project.cms_article_id}]]};
		if( "type" === action ){
			request.cms_article_type_option = ["APPLICATIONADMINTYPEOPTION",[{sort:["sort_asc"],module:"cms_article_type"}]];
		}
		
		
		if( "image" === action ){
			var config = {search:{cms_article_id: _project.cms_article_id}};
			
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
			request.list = ["CMSADMINARTICLEIMAGELIST",[config]];
		}
		
		
		WangAho("index").data({
			request : request, 
			success : function(data){
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.get.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.get.error, {icon: 5, time: 2000});
				}
				
				//判断数据是否存在
				if( !(function(){try{ return data.response.get.cms_article_id;}catch(e){return false;}}()) ){
					setTimeout(function(){
						
						if( WangAho().history_previous_exist() ){
							WangAho().history_previous();//存在上一页则返回上一页
						}else{
							WangAho().history_remove();//删除本页的记录
							http("#/cms/article_list").request();//返回列表页
						}
						
					}, 2000);
					return false;
				}
				
				//数据
				_project.data = data;
				//获取子页面参数
				_project.data.action = action;
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/cms/article_edit.html", "#content"), data, {
					
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
						
						var html = template( WangAho().template("page/cms/article_edit/"+_project.data.action+".html", "#content"), function(fn){
							this.config.helper = WangAho("index").page_helper;
							
							this.helper("type in_array", function(type_id, type){
								if(typeof type != 'object' || !type.length){
									return false;
								}
								
								var exist = false;
								for(var i in type){
									if( type[i].type_id == type_id){
										exist = true;
										break;
									}
								}
								return exist;
							});
							
							return fn(_project.data);
						});
						
						if(!html){
							return template( WangAho().template("page/cms/article_edit.html", "#warning"), function(fn){
								return fn(_project.data);
							});
						}else{
							return html;
						}
						
					}
					
					
				});
				
				
				if( "basics" === _project.data.action ){
					//调用 Chosen
					$('select[name="cms_article_state"]').chosen({
						width: '100%',
						//placeholder_text_single: '-', //默认值
						earch_contains: true,
						no_results_text: "没有匹配结果",
						case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
			        	//group_search: false //选项组是否可搜。此处搜索不可搜
					});
					_project.event_basics();
				}else
				if( "content" === _project.data.action ){
					_project.editor_content();
					_project.event_content();
				}else
				if( "type" === _project.data.action ){
					_project.event_type();
				}else
				if( "image" === _project.data.action ){
					_project.article_image_event();
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
			    
			    $('[name="submit-basics"]').first().trigger("click");
		        $('[name="submit-content"]').first().trigger("click");
		        $('[name="submit-type"]').first().trigger("click");
		        $('[name="article-image-submit"]').first().trigger("click");
		        
			}
		});
	},
	
	
	UEditor : false,//编辑器是否初始化
	editor_content : function(){
		var _project = WangAho(this.id);
		//按回车键时提交
		this.keyup();
		
		
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
			_project.UEditor = UE.getEditor("cms_article_content_editor", config);
		});
		this.event_content();
		
	},
	
	
	
	
	event_content : function(){
		var _project = WangAho(this.id);
		//按回车键时提交
		this.keyup();
		$('[name="submit-content"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			
			form_input.cms_article_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.cms_article_content = _project.UEditor.getContent();//获取内容
			
			try {
				if(form_input.cms_article_id == '') throw "文章ID异常";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["CMSADMINARTICLEEDIT", [form_input]],
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
	
	
	
	event_basics : function(){
		var _project = WangAho(this.id);
		//按回车键时提交
		this.keyup();
		$('[name="submit-basics"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.cms_article_id = _project.cms_article_id;
			form_input.cms_article_name = $.trim($('[name="cms_article_name"]').val());
			form_input.cms_article_info = $.trim($('[name="cms_article_info"]').val());
			form_input.cms_article_sort = $.trim($('[name="cms_article_sort"]').val());
			form_input.cms_article_state = $.trim($('[name="cms_article_state"]').val());
			form_input.cms_article_source = $.trim($('[name="cms_article_source"]').val());
			form_input.cms_article_keywords = $.trim($('[name="cms_article_keywords"]').val());
			form_input.cms_article_description = $.trim($('[name="cms_article_description"]').val());
			
			try {
				if(form_input.cms_article_id == '') throw "文章ID异常";
				if(form_input.cms_article_name == '') throw "文章名称不能为空";
				if(form_input.cms_article_sort == "") {
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
				request:["CMSADMINARTICLEEDIT", [form_input]],
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
	
	
	
	event_type : function(){
		var _project = WangAho(this.id);
		//按回车键时提交
		this.keyup();
		
		$('[name="type-checkbox"]').unbind("background").bind("background", function(){
			$('[name="type-checkbox"]').each(function(){
				$(this).parent().removeClass("checkbox-checked");
			});
			
			$('[name="type-checkbox"]:checked').each(function(){
				$(this).parent().addClass("checkbox-checked");
			});
		});
		$('[name="type-checkbox"]').first().trigger("background");
		$('[name="type-checkbox"]').unbind("click").click(function(){
			$('[name="type-checkbox"]').first().trigger("background");
		});
		
		$('[name="submit-type"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.cms_article_id = _project.cms_article_id;
			form_input.type_id = [];
			$('[name="type-checkbox"]:checked').each(function(){
				form_input.type_id.push($(this).val());
			});
			
			try {
				if(form_input.cms_article_id == '') throw "文章ID异常";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["CMSADMINARTICLETYPEEDIT", [form_input]],
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
	
	
	
	
		
	
	image_upload_list : {},
	image_upload_mime_limit : ['image/jpeg','image/pjpeg','image/png', 'image/x-png', 'image/gif', 'image/bmp'],
	//是否需要上传
	image_upload_button : false,
	article_image_template : function(){
		var _project = WangAho(this.id);
		
		var _cms_article_id = this.cms_article_id;
		_project.image_upload_button = false;//是否需要上传
		var list = [];
		if(typeof _project.image_upload_list[_cms_article_id] == "object"){
			for(var i = 0; i < _project.image_upload_list[_cms_article_id].length; i ++){
				list[i] = {};
				list[i].src = new eonfox().file_url(_project.image_upload_list[_cms_article_id][i]);
				list[i].name = _project.image_upload_list[_cms_article_id][i].name;
				list[i].size = _project.image_upload_list[_cms_article_id][i].size;
				list[i].type = _project.image_upload_list[_cms_article_id][i].type;
				list[i].title = _project.image_upload_list[_cms_article_id][i].title;
				list[i].upload = _project.image_upload_list[_cms_article_id][i].upload? true : false;
				list[i].error = _project.image_upload_list[_cms_article_id][i].error? true : false;
				if(!list[i].upload && !list[i].error){
					_project.image_upload_button = true;//有需要上传的
				}
			}
		}
		
		var html = template( WangAho().template("page/cms/article_edit/image.html", "#content-image"), function(fn){
			return fn({list:list, article_id:_cms_article_id});
			});
			
		$('[name="article-image-upload-list"]').html(html);
		if( !_project.image_upload_button ){
			//没有需要上传的
			$('[name="article-image-submit"]').addClass("disabled");
		}else{
			$('[name="article-image-submit"]').removeClass("disabled");
		}
		_project.article_image_event();//更新事件
	},
	
	
	//排序
	article_image_edit_sort : function(){
		var obj = WangAho("index").action_table("sort");
		if( !obj || !obj.length ){
			return false;
		}
		
		var request_array = [];
		request_array.push(["CMSADMINARTICLEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["CMSADMINARTICLEIMAGEEDIT", [{cms_article_image_id:obj[i].id, image_sort:obj[i].value}]]);
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
	article_image_edit_name : function(){
		var obj = WangAho("index").action_table("name");
		if( !obj || !obj.length ){
			return false;
		}
		
		var request_array = [];
		request_array.push(["CMSADMINARTICLEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["CMSADMINARTICLEIMAGEEDIT", [{cms_article_image_id:obj[i].id, image_name:obj[i].value}]]);
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
	article_image_remove : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["CMSADMINARTICLEIMAGEQINIUREMOVE", [{cms_article_image_id:ids[i]}]]);
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
	
	
	
	article_image_set_main : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		var request_array = [];
		request_array.push(["CMSADMINARTICLEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in ids){
			request_array.push(["CMSADMINARTICLEIMAGEEDITMAIN", [{cms_article_image_id:ids[i], cms_article_image_main:1}]]);
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
	
	article_image_cancel_main : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		var request_array = [];
		request_array.push(["CMSADMINARTICLEEDITCHECK"]);
		for(var i in ids){
			request_array.push(["CMSADMINARTICLEIMAGEEDITMAIN", [{cms_article_image_id:ids[i], cms_article_image_main:0}]]);
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
	article_image_event : function(){
		var _project = WangAho(this.id);
		var _cms_article_id = this.cms_article_id;
		
		//查看图片
		WangAho("index").image_look_event();
		//回车
		this.keyup();
		
		$('[action-button]').unbind("click").click(function(){
			var ids = WangAho("index").action_table_checked();
			var attr = $(this).attr("action-button");
			
			//排序
			if("sort" == attr){
				_project.article_image_edit_sort();
				return true;
			}
			//修改名称
			if("name" == attr){
				_project.article_image_edit_name();
				return true;
			}
			
			if(ids.length < 1){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			//设为主图
			if("set-main" == attr){
				_project.article_image_set_main(ids);
				return true;
			}
			
			//取消主图
			if("cancel-main" == attr){
				_project.article_image_cancel_main(ids);
				return true;
			}
			
			//删除
			if("remove" == attr){
				layer.msg('你确定要删除么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消'],yes: function(index){
												    layer.close(index);
												    _project.article_image_remove(ids);
												  }
				});
			}
			
		});
		
		
		//打开
		$('[name="article-image-add-input"]').unbind("click").click(function(){
			layer.closeAll();
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-plus\"></span> 添加图片",
			  	type: 1,
			  	offset: '0',
			  	area: [$(window).width()+"px", '100%'], //宽高
			  	content: template( WangAho().template("page/cms/article_edit/image.html", "#add"), function(fn){
							return fn(_project.data);
							})
			});
			
			$('[name="article-image-files"]').first().focus();//失去焦点
			_project.article_image_template();//显示模板
			
		});
		
		//开始上传图片
		$('[name="article-image-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			_project.article_image_upload();
		});
		
		
		//选择上传图片
		$('[name="article-image-select"]').unbind("click").click(function(){
			new eonfox().trigger_click($('[name="article-image-files"]').get(0));
		});
		
		
		//图片发生改变时执行
		$('[name="article-image-files"]').unbind("change").change(function() {
			var files = $('[name="article-image-files"]')[0].files;
			for(var i = 0; i < files.length; i++){
				var exist = false;
				if(typeof _project.image_upload_list[_cms_article_id] == "object" &&
				_project.image_upload_list[_cms_article_id].length > 0){
					for(var n = 0; n < _project.image_upload_list[_cms_article_id].length; n ++){
						if(_project.image_upload_list[_cms_article_id][n].lastModified == files[i].lastModified &&
						_project.image_upload_list[_cms_article_id][n].name == files[i].name &&
						_project.image_upload_list[_cms_article_id][n].size == files[i].size &&
						_project.image_upload_list[_cms_article_id][n].type == files[i].type ){
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
					
					if(typeof _project.image_upload_list[_cms_article_id] != "object"){
						_project.image_upload_list[_cms_article_id] = [];
					}
					
					_project.image_upload_list[_cms_article_id].push(files[i]);
				}
			}
			
			_project.article_image_template();//显示模板
		});
		
		
		//修改图片名称
		$('[name="article-image-upload-list"] .article-image-name-input').unbind("input propertychange").bind("input propertychange", function(event){
			var id = parseInt($(this).attr("data-id"));
			var article_id = $(this).attr("data-article-id");
			if(typeof _project.image_upload_list[article_id] == 'object' && 
			typeof _project.image_upload_list[article_id][id] == 'object'){
				_project.image_upload_list[article_id][id].title = $(this).val();
			}
		});
		
		//清理已经上传的图片
		$('[name="article-image-clear"]').unbind("click").click(function(){
			if(typeof _project.image_upload_list[_cms_article_id] != "object" || 
			_project.image_upload_list[_cms_article_id].length < 1){
				return false;
			}
			
			//这里不能使用splice方法 
			var image_upload_list = [];
			for(var i = 0; i < _project.image_upload_list[_cms_article_id].length; i ++){
				if( !_project.image_upload_list[_cms_article_id][i].upload ){
					image_upload_list.push(_project.image_upload_list[_cms_article_id][i]);
				}
			}
			_project.image_upload_list[_cms_article_id] = image_upload_list;
			
			_project.article_image_template();//显示模板
		});
		
		
		//删除
		$('[name="article-image-delete"]').unbind("click").click(function(){
			if($(this).hasClass("disabled")){
				return false;
			}
			
			var id = parseInt($(this).attr("data-id"));
			var article_id = $(this).attr("data-article-id");
			if(typeof _project.image_upload_list[article_id] != "object" ||
			_project.image_upload_list[article_id].length < 1){
				return false;
			}
			
			//删除这个标识的文件
			_project.image_upload_list[article_id].splice(id, 1);
			
			/*var image_upload_list = [];
			for(var i = 0; i < _project.image_upload_list[article_id].length; i ++){
				if(id != i){
					image_upload_list.push(_project.image_upload_list[article_id][i]);
				}
			}
			//更新索引
			_project.image_upload_list[article_id] = image_upload_list;*/
			
			_project.article_image_template();//显示模板
		});
		
		
		
		
	},
	
	
	//图片上传
	article_image_upload : function(article_id){
		var _project = WangAho(this.id);
		var _cms_article_id = article_id? article_id : this.cms_article_id;
		
		if(typeof _project.image_upload_list[_cms_article_id] != "object" || 
		_project.image_upload_list[_cms_article_id].length < 1){
			layer.msg("没有上传的图片", {icon: 5, time: 2000});
			return false;
		}
		
		var _http = http();
		var file_upload_obj;
		var file_upload_id;
		for(var i in _project.image_upload_list[_cms_article_id]){
			//没有上传并且没有错误
			if(!_project.image_upload_list[_cms_article_id][i].upload && !_project.image_upload_list[_cms_article_id][i].error){
				file_upload_obj = _project.image_upload_list[_cms_article_id][i];
				file_upload_id = i;
				break;
			}
		}
		//如果没有上传对象，则刷新页面
		if(!file_upload_obj){
			console.log("全部上传完成");
			_project.article_image_template();//显示模板
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
			cms_article_id : _cms_article_id,
			image_format : file_upload_obj.name.substring(file_upload_obj.name.lastIndexOf(".")+1, file_upload_obj.name.length),
			image_name : file_upload_obj.title,
			image_type : file_upload_obj.type,
			image_size : file_upload_obj.size
		};
		eonfox().submit({
			request : JSON.stringify({
				qiniu : ["CMSADMINARTICLEIMAGEQINIUUPTOKEN", [auth_config]],
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
					
					_project.image_upload_list[_cms_article_id][file_upload_id].error = true;
					_project.article_image_upload(_cms_article_id);//继续上传
					return;
				}
				
				auth = (function(){try{ return r.data.qiniu.data;}catch(e){return false;}}());
				
			}
		});
		
		if( !auth ){
			_project.article_image_template();//显示模板
			return;
		}
		
		//已经上传了
	    _project.image_upload_list[_cms_article_id][file_upload_id].upload = true;
		
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
	        	var $_progress = $('[name="article-image-progress"][data-id="'+file_upload_id+'"][data-article-id="'+_cms_article_id+'"]');
	        	if(res.total.percent == 100){
	        		$('[name="article-image-delete"][data-id="'+file_upload_id+'"][data-article-id="'+_cms_article_id+'"]').removeClass("disabled");
	        		$_progress.removeClass("progress-bar-warning progress-bar-striped active").addClass("progress-bar-success");
	        		$_progress.html('<span class="glyphicon glyphicon-ok"></span> 上传完成');
	        	}else{
	        		$('[name="article-image-delete"][data-id="'+file_upload_id+'"][data-article-id="'+_cms_article_id+'"]').addClass("disabled");
	        		$_progress.removeClass("progress-bar-success").addClass("progress-bar-warning progress-bar-striped active");
	        		$_progress.attr("aria-valuenow", Math.floor(res.total.percent));
	        		$_progress.css("width", Math.floor(res.total.percent)+"%");
	        		$_progress.html(Math.floor(res.total.percent)+"%");
	        	}
	        	
	    	},
	    	//上传错误后触发
	    	error:function(err){
	    		layer.msg(err.message, {icon: 5, time: 3000});
	    		_project.image_upload_list[_cms_article_id][file_upload_id].error = true;
	    		_project.article_image_upload(_cms_article_id);//继续上传
	    		return;
	    		//console.log("observer.error", err);
	    	}, 
	    	//接收上传完成后的后端返回信息
	    	complete:function(res){
	    		//更改删除按钮
				$('[name="article-image-delete"][data-id="'+file_upload_id+'"][data-article-id="'+_cms_article_id+'"]').html('<span class="glyphicon glyphicon-repeat"></span> 清理');
	    		$('[name="article-image-delete"][data-id="'+file_upload_id+'"][data-article-id="'+_cms_article_id+'"]').removeClass("disabled");
	    		
	        	//console.log("observer.complete", res);
	        	//这里上传成功，将信息发送给后台，将图片状态设为1
	        	var form_input = {
	        		cms_article_image_id : auth.cms_article_image_id,
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
						state:["CMSADMINARTICLEIMAGEQINIUSTATE", [form_input]],
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
						
						_project.article_image_upload(_cms_article_id);//继续上传
						return;
						}
				});
	      	}
	    }); 
		
		
		
	},
	
	
	
	
	
	
	
	
	
	
	
	
	
});