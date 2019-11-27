WangAho({
	
	
	id:"administrator/api_add",
	
	main : function(){
		var _project = WangAho(this.id);
		WangAho("index").data({
			request : {
				module_option:["ADMINISTRATORMODULEOPTION",[{sort:["sort_asc","update_time_asc"]}]]
				},
			success : function(data){
				if( !data ){
					return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				console.log(data)
				
				WangAho("index").view(WangAho().template("page/administrator/api_add.html", "#content"), data);
				//调用 Chosen
				$('select[name="module_id"]').chosen({
					width: '100%',
					//placeholder_text_single: '-', //默认值
					earch_contains:true, 
					no_results_text: "没有匹配结果",
					case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
		        	//group_search: false //选项组是否可搜。此处搜索不可搜
				});
				
				_project.event();
				_project.explain_editor();
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
	
	
	
	UEditor : false,//编辑器是否初始化
	explain_editor : function(){
		UEDITOR_HOME_URL = http("include/library/ueditor1_4_3_3-utf8/").href;
		var config = {
			//服务器统一请求接口路径
			serverUrl : "",
			//允许的最大字符数
			maximumWords : 1000000,
			//皮肤
			themePath : "include/library/ueditor1_4_3_3-utf8/themes/",
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
			_project.UEditor = UE.getEditor("api_explain_editor", config);
		});
	},
	
	
	
	/**
	 * 提交
	 */
	event : function(){
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
			form_input.api_id = $.trim($('[name="api_id"]').val());
			form_input.api_program = $.trim($('[name="api_program"]').val());
			
			form_input.api_name = $.trim($('[name="api_name"]').val());
			form_input.api_info = $.trim($('[name="api_info"]').val());
			form_input.module_id = $.trim($('[name="module_id"]').val());
			form_input.api_sort = $.trim($('[name="api_sort"]').val());
			form_input.api_state = $('[name="api_state"]').is(':checked')? 0 : 1;
			
			form_input.administrator = $('[name="administrator"]').is(':checked')? 1 : 0;
			form_input.api_admin = $('[name="api_admin"]').is(':checked')? 1 : 0;
			form_input.api_request_args = $.trim($('[name="api_request_args"]').val());
			form_input.api_response_args = $.trim($('[name="api_response_args"]').val());
			//获取内容
			form_input.api_explain = _project.UEditor.getContent();
			
			try {
				if(form_input.api_id == '') throw "接口ID不能为空";
				if(form_input.api_name == '') throw "接口名称不能为空";
				if(form_input.api_sort == ""){
					delete form_input.api_sort;
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
				request:["ADMINISTRATORADMINAPIADD", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					WangAho().rerun();
				}
			});
			
			
		});
		
	},
	
	
	
	
	
	
	
	
});


