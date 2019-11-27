av({
	
	id:'page-shop-goodsDetailsEdit',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/shop/goodsDetailsEdit.html"},
    'import' : function(e){
        this.template(e.template);
    },
	main: function(){
		var shop_goods_id = (function(){try{ return av.router().anchor.query.id;}catch(e){return '';}}());
		if( !shop_goods_id ){
			return av.router(av.router().url, '#/shop-goodsList/').request();
		}
		this.data.request.data = ['SHOPADMINPGOODSGET', [{shop_goods_id:shop_goods_id}]];
	},
	event:{
		
		ready: function(){
			console.log('ready this.data.formInputInit');
			this.data.formInputInit();
		},
		loadEnd: function(){
			//加载完成，说明有数据了，将值赋
			if( this.data.data ){
				this.data.formInput.shop_goods_id = this.data.data.shop_goods_id;
				this.data.formInput.shop_goods_details = this.data.data.shop_goods_details;
			}else{
				this.data.formInputInit();
			}
			
		},
		renderEnd: function(){
			this.data.detailsEditor();
			if( this.data.UEditor ){
				var _this = this;
				_this.data.UEditor.ready(function(){
					_this.data.UEditor.setContent(_this.data.formInput.shop_goods_details);
				});
			}
		}
	},
	data:{
		request: {},
		data: null,
		
		UEditor : false,//编辑器是否初始化
		detailsEditor : function(){
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
						_this.UEditor = UE.getEditor("shop_goods_details", config);
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
				shop_goods_id:'',
				shop_goods_details:'',
			};
		},
		
		formInput: {},
		//更新输入值
		updateFormInput: function(){
			//获取内容
			if( this.UEditor ){
				var _this = this;
				this.UEditor.ready(function (){
					//console.log('UEditor.ready', _this.UEditor.getContent());
					_this.formInput.shop_goods_details = _this.UEditor.getContent();
				});
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
				if(_this.formInput.shop_goods_id == '') throw "商品ID异常";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			
			//提交数据
			this.submit({
				method:"submit",
				request:["SHOPADMINGOODSEDIT", [_this.formInput]],
				error:function(){
					_this.submitLock = false;
				},
				success:function(){
					_this.submitLock = false;
					//刷新页面
					av().run();
				}
			});
			
		}
		
		
		
		
		
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
});