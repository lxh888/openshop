av({
	
	
	id:'module-frame',
	//渲染到页面上的位置,可以是ele或者是筛选标识
    selector:'view',
		include : ["src/common/event.js", "src/module/setting/setting.js", "src/common/application.js"],
		extend : ["common-application"],
	//导出模板、其他数据文件内容。可以是对象，也可以是数组
    'export' : {
    	templateIndex : "src/module/frame/index.html",
    	templateHeader : "src/module/frame/header.html",
    	templateNavigation : "src/module/frame/navigation.html",
    	},
	//输入 export 导出的变量数据。e是数组还是对象是根据export而定。
    'import' : function(e){
    	this.data.templateHeader = e.templateHeader;
    	this.data.templateNavigation = e.templateNavigation;
        this.template(e.templateIndex);
    },
	event:{
		
		//开始渲染
		/*renderStart: function(){
			//判断是否已经初始化
			if( !this.data.initCheckedParentSonIdFirst ){
				this.data.initCheckedParentSonIdFirst = true;
				this.data.initCheckedParentSonId();
			}
		},*/
		
		renderEnd: function(){
			//console.log('开始渲染');
			av('module-setting').render("refresh");
		},
	},
	data : {
		templateHeader: '',
		templateNavigation: '',
		admin:{},
		//用户数据
		user:{},
		management:null,//管理菜单数据
		application:{},
		
		//initCheckedParentSonIdFirst:false,//记录是否已经第一次完成初始化
		//初始化侧栏的选中状态
		initCheckedParentSonId : function(){
			var tempRouter = av.router();
			if( !tempRouter.path || !tempRouter.path.length ){
				tempRouter.path = new Array();
			}
			
			if( tempRouter.path.length > 0 && 
				tempRouter.path[tempRouter.path.length - 1] != 'index.html' && 
				tempRouter.path[tempRouter.path.length - 1] != 'default.html'){
				tempRouter.path.push('index.html');	
			}
			
			var url  = av.router().url
			var link = av.router().anchor.link;
			var currentPagePath = av.router(url, '#'+link).href;
		
			this.navigation.checkedParentData = null;
			this.navigation.checkedSonData = null;
			this.navigation.checkedParentId = '';
			this.navigation.checkedSonId = '';
			
			for(var i in this.management){
				if( !this.management[i].son || !this.management[i].son.length ){
					continue;
				}
				
				for(var s in this.management[i].son){
					if( this.management[i].son[s].management_href == ''){
						continue;
					}
					
					var tempRouter = av.router(av.router(tempRouter).url, this.management[i].son[s].management_href);
					//var tempHref = av.router(av.router(tempRouter).url, this.management[i].son[s].management_href).href;
					tempRouter.anchor.query = undefined;
					tempRouter = av.router(tempRouter);//更新设置
					//tempRouter.setting();
					//console.log(currentPagePath == tempRouter.href, currentPagePath, tempRouter.href);
					//console.log('initCheckedParentSonId', currentPagePath, tempHref);
					if( currentPagePath == tempRouter.href ){
						//console.log('initCheckedParentSonId OK', currentPagePath, tempHref);
						
						this.navigation.showParentIds[this.management[i].management_id] = true;
						
						this.navigation.checkedParentId = this.management[i].management_id;
						this.navigation.checkedSonId = this.management[i].son[s].management_id;
						
						this.navigation.checkedParentData = this.management[i];//对应选中的父级 数据
						this.navigation.checkedSonData = this.management[i].son[s];//选中的子级 数据
						break;
					}
					
				}
				
			}
			
			
		},
		
		content : {
			wrapStyle:{},
			bodyStyle:{},
		},
		
		
		
		header : {
			
			eventUserLogout : function(){
				av('common-event').data.userLogout();
			},
			
			//设置
			eventSetting: function(){
				var _this = this;
			
				av().ready(function(){
					//console.log( $('[module="search"]')[0].childNodes.length );
					var AVmoduleSetting = av('module-setting');
					AVmoduleSetting.data.admin = av().data.admin;
					//console.log('数据：', av().data.admin);
					
					//按回车键时提交
		    		av('common-event').data.keyupFunctions['module-setting'] = function(){
		    			AVmoduleSetting.data.submit();
		    		};
		    		
		    		var common_content_keyupFunctions = av('common-event').data.keyupFunctions['common-content'];
					av('common-event').data.keyupFunctions['common-content'] = function(){};
					
					
					var selector = 'module-setting-'+Date.parse(new Date());
					layer.closeAll();
					//页面层
					layer.open({
						title : "<span class=\"glyphicon glyphicon-wrench\"></span> 个人设置",
					  	type: 1,
					  	shadeClose: true,
					  	//area: 'auto', //宽高
					  	area: [($(window).width()>1200? 1200:$(window).width())+'px', ($(window).height()-50)+'px'], //宽高
					  	//maxWidth: $(window).width() > 1200? 1200 : $(window).width()-50,
					  	//maxHeight: $(window).height()-50,
					  	content: '<div id="'+selector+'"></div>',
					  	end: function(){
					  		delete av('common-event').data.keyupFunctions['module-setting'];//退出的时候删除事件，防止公共事件冲突
					  		av('common-event').data.keyupFunctions['common-content'] = common_content_keyupFunctions;
					  	},
					  	success: function(){
					  		//渲染这个插件
					  		AVmoduleSetting.submitDisabled = false;//关闭
							AVmoduleSetting.selector('#'+selector).render("refresh");
					  		$('[input-focus="module-setting"]').focus();//失去焦点
					  	}
					});
					
				});
				
				
			},
			
			
			eventRefresh: function(){
				av().run({refresh:true});
			},
			
			//上一个路由锚点
			eventRouterBack: function(){
				av.framework().history.routerBack();
			},
			//下一个路由锚点
			eventRouterForward: function(){
				av.framework().history.routerForward();
			},
			eventPageBack: function(){
				av.framework().history.pageBack();
			},
			eventPageForward: function(){
				av.framework().history.pageForward();
			},
			
			fullscreen: false,
			eventFullscreen : function(){
				if( this.header.fullscreen ){
					//退出全屏
				    if (document.webkitCancelFullScreen) {
				        document.webkitCancelFullScreen();
				    } else if (document.mozCancelFullScreen) {
				        document.mozCancelFullScreen();
				    } else if (document.cancelFullScreen) {
				        document.cancelFullScreen();
				    } else if (document.exitFullscreen) {
				        document.exitFullscreen();
				    } else {
				    	layer.msg("浏览器不支持全屏API或已被禁用", {icon: 5, time: 2000});
				    	return;
				    }
				    
				    this.header.fullscreen = false;
				}else{
					//全屏
					console.log('开启全屏');
				    if (document.body.webkitRequestFullScreen) {
				        document.body.webkitRequestFullScreen();
				    } else if (document.body.mozRequestFullScreen) {
				        document.body.mozRequestFullScreen();
				    } else if (document.body.requestFullScreen) {
				        document.body.requestFullscreen();
				    } else {
				    	layer.msg("浏览器不支持全屏API或已被禁用", {icon: 5, time: 2000});
				    }
				    
					this.header.fullscreen = true;
				}
				
				
			},
			
			//置顶
			eventUp:function(){
				$('[index="content"] .scroll').scrollTop(0);
			},
			//置底
			eventDown:function(){
				var h = $(document).height()? $(document).height() : $(window).height();
				$('[index="content"] .scroll').scrollTop(h);
			},
			
			//头部 点击 对侧栏的隐藏/显示
			classNavigationVisibility : {
				'glyphicon glyphicon-indent-left':false, 
				'glyphicon glyphicon-indent-right':true
			},
			
			//头部 点击 对侧栏的隐藏/显示
			eventNavigationVisibility : function(ele){
				this.navigation.visibility = !this.navigation.visibility;
				this.header.classNavigationVisibility['glyphicon glyphicon-indent-left'] = !this.navigation.visibility;
				this.header.classNavigationVisibility['glyphicon glyphicon-indent-right'] = this.navigation.visibility;
				
				if( this.navigation.visibility ){
					//如果侧栏是显示状态
					this.content.wrapStyle = {"max-width": ""};
					this.content.bodyStyle = {
						"left":"",
						"width":"",
						//"width":["-moz-calc(100% - 250px)", "-webkit-calc(100% - 250px)", "calc(100% - 250px)"],
					};
				}else{
					var w = $(window).width()? $(window).width() : $(document).width();
					this.content.wrapStyle = {
						"max-width": w + "px"
					};
					this.content.bodyStyle = {
						"left":"0", 
						"width":"100%"
					};
				}
				
			},
			
			//对侧栏的整理
			eventNavigationNeaten : function(ele){
				//不存在，直接初始化返回
				this.navigation.showParentIds = {};
				this.navigation.showParentIds[this.navigation.checkedParentId] = true;
			}
			
			
		},
		
		navigation : {
			visibility: true,
			//展开的父级 ID 列表
			showParentIds : {},
			//选中的子级 ID
			checkedSonId : '',
			//对应选中的父级 ID
			checkedParentId : '',
			
			//选中的子级 数据
			checkedSonData : {},
			//对应选中的父级 数据
			checkedParentData : {},
			
			sonUrl : function(href, valuePage, sonValuePage, hide){
				//如果是隐藏的，则不给连接
				if( hide == 1 ){
					return '';
				}
				var tempRouter = av.router();
				if( !tempRouter.path || !tempRouter.path.length ){
					tempRouter.path = new Array();
				}
				
				if( tempRouter.path.length > 0 && 
					tempRouter.path[tempRouter.path.length - 1] != 'index.html' && 
					tempRouter.path[tempRouter.path.length - 1] != 'default.html'){
					tempRouter.path.push('index.html');	
				}
				
				if( !href ){
					return av.router(av.router(tempRouter, 'index.html').url, '#' + valuePage + '/' + sonValuePage).href;	
				}
				
				return av.router(av.router(tempRouter).url, href).href;	
			},
			
			//侧栏的父级点击事件
			eventParent : function(ele, e, parentId){
				console.log('eventParent', parentId, this.navigation.showParentIds[parentId]);
				//存在则删除，不存在则添加
				if( typeof this.navigation.showParentIds[parentId] == 'undefined' ){
					this.navigation.showParentIds[parentId] = true;
				}else{
					delete this.navigation.showParentIds[parentId];
				}
				
			},
			
			
			//侧栏的子级点击事件
			eventSon : function(ele){
				var parentId = ele.getAttribute('management-parent-id');
				var sonId = ele.getAttribute('management-id');
				//存在则删除，不存在则添加
				if( typeof this.navigation.showParentIds[parentId] == 'undefined' ){
					this.navigation.showParentIds[parentId] = true;
				}
				
				this.navigation.checkedParentId = parentId;
				this.navigation.checkedSonId = sonId;
			},
			
			
			
		},
		
	},
	
	
	
});
