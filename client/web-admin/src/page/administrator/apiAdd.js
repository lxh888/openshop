av({
	
	id: 'page-administrator-apiAdd',
	include : [
		"src/common/content.js", 
		/*"include/library/ueditor1_4_3_3-utf8/ueditor.config.js",
		"include/library/ueditor1_4_3_3-utf8/ueditor.all.min.js",
		"include/library/ueditor1_4_3_3-utf8/lang/zh-cn/zh-cn.js"*/
		/*{file:"include/library/ueditor1_4_3_3-utf8/ueditor.config.js", selector:"head", callback:function(ele){
			console.log('2222222', this, ele)
			ele.appendChild(this.element);
		}}*/
		/*{file:"include/library/ueditor1_4_3_3-utf8/ueditor.config.js", selector:"head"},
		{file:"include/library/ueditor1_4_3_3-utf8/ueditor.all.min.js", selector:"head"},
		{file:"include/library/ueditor1_4_3_3-utf8/lang/zh-cn/zh-cn.js", selector:"head"}*/
	],
    extend : ["common-content"],
	'export' : {template : "src/page/administrator/apiAdd.html"},
	'import' : function(e){
        this.template(e.template);
    },
	main: function(){
		//console.log("开始加载：", this);
		this.data.request.moduleOption = ["ADMINISTRATORMODULEOPTION",[{sort:["sort_asc","update_time_asc"]}]];
	},
	event:{
		
		ready: function(){
			console.log('ready this.data.formInputInit');
			this.data.formInputInit();
		},
		
		error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},
		renderEnd: function(){
			//调用 Chosen  先更新
			$('select[name="module_id"]').chosen("destroy");
			$('select[name="module_id"]').chosen({
				//width: '100%',
				//placeholder_text_single: '-', //默认值
				earch_contains:true, 
				no_results_text: "没有匹配结果",
				case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
	        	//group_search: false //选项组是否可搜。此处搜索不可搜
			});
			
			if( this.data.controller == 2 && this.data.UEditor ){
				var _this = this;
				_this.data.UEditor.ready(function(){
					_this.data.UEditor.setContent(_this.data.formInput.api_explain);
				});
			}
			
		}
		
	},
	data:{
		request: {},
		controller: 0,
		UEditor : false,//编辑器是否初始化
		explainEditor : function(){
			if( this.UEditor ){
				return false;
			}
			UEDITOR_HOME_URL = av.router("include/library/ueditor1_4_3_3-utf8/").href;
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
			
			var _this = this;
			var ueditor_config = av.include({
				file:"include/library/ueditor1_4_3_3-utf8/ueditor.config.js"
			});
			var ueditor = av.include({
				file:"include/library/ueditor1_4_3_3-utf8/ueditor.all.min.js"
			});
			var ueditor_lang = av.include({
				file:"include/library/ueditor1_4_3_3-utf8/lang/zh-cn/zh-cn.js"
			});
			
			//如果已经加载了，那么删除
			if( ueditor_config.exists() && ueditor.exists() && ueditor_lang.exists() ){
				ueditor_config.remove();
				ueditor.remove();
				ueditor_lang.remove();
			}
			
			ueditor_config.run();
			ueditor_config.load(function(){
				ueditor.run();
				ueditor.load(function(){
					ueditor_lang.run();
					ueditor_lang.load(function(){
						_this.UEditor = UE.getEditor("api_explain_editor", config);
					});
				});
			});
			
		},
		
		//初始化值
		formInputInit: function(){
			
			if( this.UEditor ){
				this.UEditor = null;
			}
			
			this.formInput = {
				api_id:'',
				api_name:'',
				api_info:'',
				module_id:'',
				api_sort:'',
				administrator:0,
				api_admin:0,
				api_state:1,
				api_program:'',
				api_version:[],
				api_request_args:'',
				api_response_args:'',
				api_explain:'',
			};
			
			this.controller = 0;
			this.apiVersionGather = {};
		},
		
		formInput: {},
		
		//更新输入值
		updateFormInput: function(){
			if( this.controller == 0 ){
				this.formInput.api_id = $.trim($('[name="api_id"]').val());
				this.formInput.api_name = $.trim($('[name="api_name"]').val());
				this.formInput.api_info = $.trim($('[name="api_info"]').val());
				this.formInput.module_id = $.trim($('[name="module_id"]').val());
				this.formInput.api_sort = $.trim($('[name="api_sort"]').val());
				this.formInput.api_state = $('[name="api_state"]').is(':checked')? 0 : 1;
				this.formInput.administrator = $('[name="administrator"]').is(':checked')? 1 : 0;
				this.formInput.api_admin = $('[name="api_admin"]').is(':checked')? 1 : 0;
			}else
			if( this.controller == 1 ){
				this.formInput.api_program = $.trim($('[name="api_program"]').val());
				var api_version = [];
				for(var i in this.apiVersionGather){
					api_version.push(this.apiVersionGather[i]);
				}
				this.formInput.api_version = api_version;
				
			}else
			if( this.controller == 2 ){
				this.formInput.api_request_args = $.trim($('[name="api_request_args"]').val());
				this.formInput.api_response_args = $.trim($('[name="api_response_args"]').val());
				//获取内容
				if( this.UEditor ){
					var _this = this;
					this.UEditor.ready(function (){
						//console.log('UEditor.ready', _this.UEditor.getContent());
						_this.formInput.api_explain = _this.UEditor.getContent();
					});
				}
				
			}
		},
		eventController:function(ele, e, i){
			this.updateFormInput();
			this.controller = i;
			if(this.controller == 2){
				this.explainEditor();
			}
			
		},
		
		//版本ID集合
		apiVersionGather: {},
		eventApiVersionGatherAdd: function(ele, e){
			while(true){
				var timestamp = new Date().getTime();
				var apiVersionKey = timestamp + Math.ceil(Math.random()*1000000);
				if(typeof this.apiVersionGather[apiVersionKey] != 'undefined'){
					continue;
				}
				
				this.apiVersionGather[apiVersionKey] = {
					api_version_id:'',
					api_version_program:'',
					api_version_state:1,
				};
				break;
			}
		},
		inputApiVersionId:function(ele, e, apiVersionKey){
			if(typeof this.apiVersionGather[apiVersionKey] != 'undefined'){
				this.apiVersionGather[apiVersionKey].api_version_id = $(ele).val();
			}
		},
		inputApiVersionProgram:function(ele, e, apiVersionKey){
			if(typeof this.apiVersionGather[apiVersionKey] != 'undefined'){
				this.apiVersionGather[apiVersionKey].api_version_program = $(ele).val();
			}
		},
		inputApiVersionState:function(ele, e, apiVersionKey){
			if(typeof this.apiVersionGather[apiVersionKey] != 'undefined'){
				this.apiVersionGather[apiVersionKey].api_version_state = $(ele).is(':checked')? 0 : 1;
			}
			console.log(this.apiVersionGather);
		},
		
		
		eventApiVersionGatherRemove:function(ele, e, apiVersionKey){
			if(typeof this.apiVersionGather[apiVersionKey] != 'undefined'){
				delete this.apiVersionGather[apiVersionKey];
			}
		},
		
		
		//按回车键时提交
		keyupFunction: function(){
			this.eventSubmit();
		},
		submitLock:false,
		eventSubmit: function(){
			this.updateFormInput();
			var _this = this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			
			try {
				if(_this.formInput.api_id == '') throw "请输入接口ID";
				if(_this.formInput.api_name == '') throw "接口名称不能为空";
				if(_this.formInput.api_sort == ''){
					delete _this.formInput.api_sort;
				}
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			
			//提交数据
			this.submit({
				method:"submit",
				request:["ADMINISTRATORADMINAPIADD", [_this.formInput]],
				error:function(){
					_this.submitLock = false;
				},
				success:function(){
					_this.submitLock = false;
					var api_id = _this.formInput.api_id;
					//刷新页面
					_this.formInputInit();
					//重新编译
					av().compiler("reload");
					//跳转到编辑页面
					av.router(av.router().url, '#/administrator-apiEdit/?id='+api_id).request();
					//av().compiler("reload").run();
				}
			});
			
		}
		
		
		
		
	}
	
	
});
