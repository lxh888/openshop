av({
	
	id: 'common-content',
	selector: '[page="content"]',
    include : ["src/module/frame/frame.js", "src/module/search/search.js", "src/common/application.js"],
    extend : ["common-application"],
	event:{
		
		//当项目准备好的时候（初始化完成的时候），this 即 system.frameworkProjectPublic[projectID] 值
		ready:function(){
			console.log('ready', this.id);
		},
		//用户进入该页面时，this 即 system.frameworkProjectPublic[projectID] 值
		show:function(){
			console.log('show', this.id);
		},
		//用户离开该页面时，this 即 system.frameworkProjectPublic[projectID] 值
		hide:function(){
			console.log('hide', this.id);
		},
		
		//当锚点路由更新时 
        routerChange : function(){
            av().run('     REload ');
        },
        
		//当渲染的完成之后
		renderEnd: function(){
			//注册选中状态
			this.data.checkboxRegister("checkbox", "checkbox-all");
		},
		
		//页面开始加载时
		loadStart: function(){
			this.state('run stop');//暂停
			var RAPI = new requestAPI();
        	//console.log(this.data, this.data.request);
        	if(!this.data.request) this.data.request = {};
        	if(!this.data.request.application) this.data.request.application = ["APPLICATION"];
			if(!this.data.request.applicationConfig) this.data.request.applicationConfig = ["APPLICATIONCONFIG"];
			if(!this.data.request.management) this.data.request.management = ["ADMINISTRATORMANAGEMENTSELF",[{sort:["sort_asc"]}]];
			if(!this.data.request.user) this.data.request.user = ["USERSELF"];
			if(!this.data.request.admin) this.data.request.admin = ["ADMINSELF"];
			
			var _this = this;
			RAPI.submit({
        		request: this.data.request,
        		async : true,
        		callback: function(data){
        			var bool = _this.data.loadStartGetData(_this.data.request, data);
        			if( !bool ){
        				return _this.state('run off');//关闭
        			}
        			
        			//回调
        			if( typeof _this.data.callback == 'function'){
        				_this.data.callback(data);
        			}
        			
        			
        			//设置Title
					if(_this.data.application && typeof _this.data.application.application_name != "undefined"){
						av.setTitle(_this.data.application.application_name, "/include/image/favicon.ico");
					}
					
					av('module-frame').data.management 	= _this.data.management;
					av('module-frame').data.user 		= _this.data.user;
					av('module-frame').data.admin 		= _this.data.admin;
					av('module-frame').data.application = _this.data.application;
					av('module-frame').render();//先把框架渲染了
					//更新侧栏状态
					av('module-frame').data.initCheckedParentSonId();
					
					
					av('module-search').data.showList 		= _this.data.search;
					av('module-search').render("refresh");
					
					
					
					//按回车键时提交
					if( typeof _this.data.keyupFunction == 'function' ){
						av('common-event').data.keyupFunctions['common-content'] = function(){
							_this.data.keyupFunction();
						};
					}else{
						av('common-event').data.keyupFunctions['common-content'] = function(){};
					}
					
					//开始
					_this.state('run on');
					
					/*setTimeout(function(){
						_this.start();//开始
						console.log('测试页面开始加载时 暂停渲染')
					}, 5000);*/
					
        		},
        		error: function(e){
        			//信息框-例4
            		layer.msg('数据加载出错！请联系管理员['+e+']', {icon: 5, time:-1});
        		}
        	});
        	
        	
        },
		
	},
	
	data: {
		request:{},
		requestHideError:[],
		//?sort=register_time_desc
		
		//先顺序再倒序
		eventSort: function(ele, e, asc, desc){
			var router = av.router();
			if( !router.anchor.query ){
				router.anchor.query = {};
			}
			
			if( router.anchor.query.sort && router.anchor.query.sort == asc){
				router.anchor.query.sort = desc;
			}else{
				router.anchor.query.sort = asc;
			}
			av.router(router).request();
		},
		existSort: function(asc, desc){
			var router = av.router();
			if( !router.anchor.query ){
				return false;
			}
			
			if( !router.anchor.query.sort ){
				return false;
			}
			
			if( router.anchor.query.sort != asc &&  router.anchor.query.sort != desc){
				return false;
			}
			
			return true;
		},
		checkSort: function(sort){
			var router = av.router();
			if( !router.anchor.query ){
				return false;
			}
			
			if( !router.anchor.query.sort ){
				return false;
			}
			
			if( router.anchor.query.sort != sort ){
				return false;
			}
			
			return true;
		},
		
		
		eventUp:function(){
			console.log('eventUp');
		},
		//加载开始获取数据
		loadStartGetData : function(request, r){
			//console.log('数据需要处理', r, loadFunction);
			try {
					
				if( !r ) throw "未知错误";
				if( (function(){try{ return r.errno;}catch(e){return false;}}()) ){
					throw r.error;
				}
				
				for(var i in request){
					if( (function(){try{ return r.data[i].data, true;}catch(e){return false;}}()) ){
						this[i] = r.data[i].data;
					}
				}
				
				if( (function(){try{ return r.data.user.errno;}catch(e){return false;}}()) ){
					throw r.data.user.error;
				}
				if( (function(){try{ return r.data.admin.errno;}catch(e){return false;}}()) ){
					throw r.data.admin.error;
				}
				
				for(var i in request){
					if(this.requestHideError !== null && typeof this.requestHideError == 'object'){
						var isExist = false;
						for(var n in this.requestHideError){
							if(i == this.requestHideError[n]){
								isExist = true;
								break;
							}
						}
						if(isExist){
							continue;
						}
					}
					if( (function(){try{ return r.data[i].errno;}catch(e){return false;}}()) ){
						throw r.data[i].error;
					}
				}
				
			} catch(err) {
				
				var layer_msg_id = layer.msg(err, {icon: 5, time: 2000});
				if( !this.user ){
					//layer.msg('亲，您还没有登录', {icon: 5, time: 2000});
					setTimeout(function(){
						layer.close(layer_msg_id);
						//返回登录页面
						av.router(av.router().url, '#/userLogin').request();
					}, 2000);
					return false;
				}else
				//判断是否为管理员
				if( !this.admin ){
					//if(!admin_error) admin_error = "没有权限"; 
					//layer.msg(admin_error, {icon: 5, time: 2000});
					//JSON
					setTimeout(function(){
						layer.close(layer_msg_id);
						//返回权限受限页面
						av.router(av.router().url, '#/401Unauthorized').request();
					}, 2000);
					return false;
				}else{
					
					setTimeout(function(){
						layer.close(layer_msg_id);
					}, 2000);
					
					av().error(err);
					return false;
				}
				
			}
			
			return true;
		},
		
		
		user: null,//用户数据
		management: null,//管理菜单数据
		application: null,//应用数据
		applicationConfig: null,//应用配置
		
		//检测是否需要筛选
		checkRouterAnchorQuerySearch : function(){
			return av.router().anchor.query && av.router().anchor.query.search;
		},
		
		
		//tr 双击 选中 或 取消选中 checkbox
		eventTrDblclick : function(ele){
			//console.log('双击事件触发');
			var id = ele.getAttribute('data-id');
			var checkboxName = ele.getAttribute('data-checkbox');
			var $checkbox = '[checkbox="'+checkboxName+'"][data-id="'+id+'"]';
			//console.log(1,$checkbox);
			if( $($checkbox)[0] ){
				av.triggerClick($($checkbox)[0]);
			}
		},
		
		
		/**
		 * 获取输入对象
		 * 
		 * 节点中必须存在  data-id 属性
		 * 
		 * @param {Object} attr
		 */
		inputData : function(attrName){
			var ids = new Array();
			$('[input="'+attrName+'"]').each(function(){
				ids.push( { id:$(this).attr("data-id"), value:$(this).val() } );
			});
			
			return ids;
		},
		
		
		/**
		 * 获取属性值
		 * 
		 * @param {Object} attr
		 */
		checkboxData : function(attrName, checkboxName){
			if(!attrName || typeof attrName != 'string') attrName = "data-id";
			if(!checkboxName || typeof checkboxName != 'string') checkboxName = "checkbox";
			
			var ids = new Array();
			$('[checkbox="'+checkboxName+'"]').each(function(){
				if( $(this).is(':checked') ){
					ids.push( $(this).attr(attrName) );
				}
			});
			
			return ids;
		},
		
		
		/**
		 * 注册 checkbox
		 * 
		 * @param {Object} son_checkbox_name			子checkbox
		 * @param {Object} checkbox_all_name			全部checkbox
		 */
		checkboxRegister : function(son_checkbox_name, checkbox_all_name){
			var son_checkbox = '[checkbox="'+son_checkbox_name+'"]';
			var all_checkbox = '[checkbox="'+checkbox_all_name+'"]';
			
			//初始化
			if( $(son_checkbox).length ){
				var is_checked = 0;
				$(son_checkbox).each(function(){
					if( $(this).is(':checked') ){
						$(this).parent().addClass("checkbox-checked");
						is_checked ++;
					}else{
						$(this).parent().removeClass("checkbox-checked");
					}
				});
				
				if(is_checked == $(son_checkbox).length){
					$(all_checkbox).prop("checked",true);
					$(all_checkbox).attr("checked", "checked");
					
					$(all_checkbox).parent().addClass("checkbox-checked");
					}else{
						$(all_checkbox).prop("checked",false);
						$(all_checkbox).removeAttr("checked");
						
						$(all_checkbox).parent().removeClass("checkbox-checked");
						}
			}
			
			//全部选中
			$(all_checkbox).unbind("click").click(function(){
				if( $(this).is(':checked') ){
					$(son_checkbox).prop("checked",true);
					$(son_checkbox).attr("checked", "checked");
					
					$(son_checkbox).parent().addClass("checkbox-checked");
					$(this).parent().addClass("checkbox-checked");
					}else{
						$(son_checkbox).prop("checked",false);
						$(son_checkbox).removeAttr("checked");
						
						$(son_checkbox).parent().removeClass("checkbox-checked");
						$(this).parent().removeClass("checkbox-checked");
						}
			});
			
			//当前行数据选中
			$(son_checkbox).unbind("click").click(function(){
				var is_checked = 0;
				$(son_checkbox).each(function(){
					if( $(this).is(':checked') ){
						$(this).parent().addClass("checkbox-checked");
						is_checked ++;
					}else{
						$(this).parent().removeClass("checkbox-checked");
					}
				});
				
				if(is_checked == $(son_checkbox).length){
					$(all_checkbox).prop("checked",true);
					$(all_checkbox).attr("checked", "checked");
					
					$(all_checkbox).parent().addClass("checkbox-checked");
					}else{
						$(all_checkbox).prop("checked",false);
						$(all_checkbox).removeAttr("checked");
						
						$(all_checkbox).parent().removeClass("checkbox-checked");
						}
			});
			
			/*$('[name="admin_authority_id-checkbox"]').unbind("background").bind("background", function(){
				$('[name="admin_authority_id-checkbox"]').each(function(){
					$(this).parent().removeClass("checkbox-checked");
				});
				
				$('[name="admin_authority_id-checkbox"]:checked').each(function(){
					$(this).parent().addClass("checkbox-checked");
				});
			});
			$('[name="admin_authority_id-checkbox"]').first().trigger("background");
			$('[name="admin_authority_id-checkbox"]').unbind("click").click(function(){
				$('[name="admin_authority_id-checkbox"]').first().trigger("background");
			});*/
			
		},
	
	
	
		actionRemoveIds : function(ids, callback){
			if( ids.length < 1 ){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			if( typeof callback != 'function'){
				callback = function(){};
			}
			
			layer.msg('你确定要删除么？('+ids.length+'条数据)', 
				{time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
					layer.close(index);
				    callback(ids);
				}
			});
			
		},
		
		actionStateIds : function(ids, callback){
			if( ids.length < 1 ){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			if( typeof callback != 'function'){
				callback = function(){};
			}
			
			layer.msg('你确定要更新状态么？('+ids.length+'条数据)', 
				{time: 0 //不自动关闭
				,btn: ['确定', '取消']
				,yes: function(index){
					layer.close(index);
				    callback(ids);
				}
			});
		},
	
	
		actionPromptIds : function(ids, title, callback){
			if( ids.length < 1 ){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			if( !title ) title = ''; 
			layer.prompt({
				title: '('+ids.length+'条数据)'+title, 
				formType: 2,
				area: ['500px', '200px'] //自定义文本域宽高
			}, function(text, index){
			    layer.close(index);
			    callback(text, ids);
			});
			
		},
	
	
			
		/**
		 * 公用的提交
		 * config = {
		 * 		method ： "submit" submit|list|edit     edit 如sort排序，第一个参数[0]表示编辑权限检测
		 * 		request : 接口与参数，如：["SESSION",[from_data]]
		 * 		error : function(){},错误时执行的函数
		 * 		success ：function(){},操作成功后执行的函数
		 * }
		 * 
		 * @param {Object} config
		 */
		submit : function(config){
			if(!config.error || typeof config.error != "function"){
				config.error = function(){};
			}
			if(!config.success || typeof config.success != "function"){
				config.success = function(){};
			}
			if(!config.request || typeof config.request != "object"){
				config.request = [];
			}
			if(!config.method || typeof config.method != "string"){
				config.method = "submit";
			}
			
			//默认弹出成功消息
			if(typeof config.alert == 'undefined'){
				config.alert = true;
			}else{
				config.alert = config.alert? true : false;
			}
			
			
			layer.closeAll('loading');//关闭加载
			//加载层-风格3
			layer.load(2);
			
			if(config.method == "edit"){
				//config.request 这个时候是一个索引数组。第一个参数[0]表示编辑权限检测
				//提交数据
				new requestAPI().submit({
					request : config.request,
					callback : function(r){
						layer.closeAll('loading');//关闭加载
						
						var is_succeed = false;//是否存在成功项
						try {
							if( !r ){
								throw "未知错误";
							}
							if( (function(){try{ return r.errno;}catch(e){return false;}}()) ){
								throw r.error;
						        return false;
							}
							if( (function(){try{ return r.data[0].errno;}catch(e){return false;}}()) ){
								throw r.data[0].error;
						        return false;
							}
							
							//判断是否存在成功项
							for(var i = 1;i < r.data.length;i++){
								if(typeof r.data[i].errno == "number" && r.data[i].errno == 0){
									is_succeed = true;
								}
							}
							
						}
						catch(err) {
					        layer.msg(err, {icon: 5, time: 2000});
					        config.error(err);
					        return false;
					    }
						
						if(is_succeed){
							
							if( config.alert ){
								layer.msg("操作成功!", {icon: 1, time: 1000});
								setTimeout(function(){
									//及时执行成功函数
									config.success();
								}, 1000);
							}else{
								//及时执行成功函数
								config.success();
							}
							
						}else{
							layer.msg("没有数据需要更新!", {icon: 7, time: 1000});
						}
							
						return false;
				}});
				
			}else
			if(config.method == "list"){
				//config.request 这个时候是一个索引数组
				//config.success(have_a_successful); 返回一个是否有一次成功
				
				//提交数据
				new requestAPI().submit({
					request : JSON.stringify(config.request),
					callback : function(r){
						layer.closeAll('loading');//关闭加载
						try {
							if( !r ){
								throw "未知错误";
							}
							if( (function(){try{ return r.errno;}catch(e){return false;}}()) ){
								throw r.error;
						        return false;
							}
							
						}
						catch(err) {
					        layer.msg(err, {icon: 5, time: 2000});
					        config.error(err);
					        return false;
					    }
						//console.log(r);
						var have_a_successful = false;//是否要刷新
						var html = "<div style=\"padding: 10px;\"><hr/>";
						for(var i in r.data){
							if( (function(){try{ return r.data[i].errno;}catch(e){return false;}}()) ){
								html += "第"+(parseInt(i)+1)+"条数据： <span class=\"label label-danger\">操作失败</span><br><span class=\"text-danger\">"+r.data[i].error+"</span>";
							}else{
								html += "第"+(parseInt(i)+1)+"条数据： <span class=\"label label-success\">操作成功</span>";
								have_a_successful = true;//有一次成功就要刷新
							}
							html += "<hr/>";
						}
						html += "</div>";
						
						layer.open({
						  type: 1,
						  shade: [0.5, '#000'],
						  shadeClose: true,
						  title: false, //不显示标题
						  content: html, 
						  end: function(){
						  	config.success(have_a_successful);
						  }
						});
				}});
				
			}else{
				//submit
				//提交数据
				new requestAPI().submit({
					request : JSON.stringify({
						s:config.request,
						}),
					callback : function(r){
						layer.closeAll('loading');//关闭加载
						
						try {	
							if( !r ){
								throw "未知错误";
							}
							//console.log(r);
							if( (function(){try{ return r.errno;}catch(e){return false;}}()) ){
								throw r.error;
							}
							if( (function(){try{ return r.data.s.errno;}catch(e){return false;}}()) ){
								throw r.data.s.error;
							}
							if( !(function(){try{ return r.data.s.data, true;}catch(e){return false;}}()) ){
								throw "未知响应数据";
							}
							
						} catch(err) {
					        layer.msg(err, {icon: 5, time: 2000});
					        config.error(err);
					        return false;
					    }
						
						if( config.alert ){
							layer.msg("操作成功!", {icon: 1, time: 1000});
							setTimeout(function(){
								config.success(r.data.s.data);
							}, 1000);
							
						}else{
							config.success(r.data.s.data);
						}
							
						return false;
				}});	
				
				
			}
			
			
		},
	
		/**
		 * 是否是开始页面
		 * 
		 * @param {Object} j
		 * @param {Object} k
		 */
		isFirstPage: function(n){
			if(n == 1){
				return true;
			}else{
				return false;
			}
		},
		
		/**
		 * 是否是结束页面
		 * 
		 * @param {Object} j
		 * @param {Object} k
		 */
		isEndPage: function(j, k){
			j = parseInt(j);
			k = parseInt(k);
			if(j >= k){
				return true;
			}else{
				return false;
			}
		},
	
	
		/**
		 * 跳转页面
		 * 
		 * @param {Object} ele
		 */
		eventPageRequest: function(ele){
			if( $(ele).hasClass("disabled") ){
				return false;
			}
			var page = $(ele).attr("data-page");
			var router = av.router();
			if(!router.anchor.query){
				router.anchor.query = {};
			}
			router.anchor.query.page = page;
			av.router(router).request();
		},
		/**
		 * 输入跳转页数
		 * 
		 * @param {Object} ele
		 */
		eventPageRequestSubmit: function(ele){
			var page = $('[input-page="requestPageRequestValue"]').val();
			var router = av.router();
			if(!router.anchor.query){
				router.anchor.query = {};
			}
			router.anchor.query.page = page;
			av.router(router).request();
		},
		/**
		 * 查看消息
		 * 
		 * @param {Object} ele
		 */
		eventShowMessage: function(ele, e, title, message){
			
			var content = message;
			var title = title;
			
			if(!content){
				layer.msg('信息为空', {icon: 5, time:2000});
				return false;
			}
			
			//捕获页
			layer.open({
			  type: 1,
			  shadeClose: true,
			  area: [($(document).width()<1000? $(document).width():1000)+"px",'auto'], //宽高
			  title: title, //不显示标题
			  content: '<div style="padding: 20px 10px 10px;"><pre style="white-space: pre-wrap; word-wrap: break-word;">'+av.encodeHTML(content)+"</pre></div>", //捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响
			});
			
		},
		
		
		eventQuery: function(ele, e, k, v){
			//console.log(k, v);
			var tempRouter = av.router();
			if( !tempRouter.anchor.query ){
				tempRouter.anchor.query = {};
			}
			
			if( v === false ){
				if(typeof tempRouter.anchor.query[k] != 'undefined') delete tempRouter.anchor.query[k];
			}else{
				tempRouter.anchor.query[k] = v;
			}
			
			av.router(tempRouter).request();
		},
		
		
		searchShow: false,
		eventSearch: function(ele, e){
			if( this.searchShow ){
				return false;
			}else{
				this.searchShow = true;
			}
			var _this = this;
			
			av().ready(function(){
				var AVmoduleSearch = av('module-search');
				//按回车键时提交
	    		av('common-event').data.keyupFunctions['module-search'] = function(){
	    			AVmoduleSearch.data.submit();
	    		};
	    		
		    	var common_content_keyupFunctions = av('common-event').data.keyupFunctions['common-content'];
				av('common-event').data.keyupFunctions['common-content'] = function(){};
				
				var selector = 'module-search-'+Date.parse(new Date());
				
				layer.closeAll();
				//页面层
				layer.open({
					title : "<span class=\"glyphicon glyphicon-filter\"></span> 筛选",
				  	type: 1,
				  	shadeClose: true,
				  	//area: 'auto', //宽高
				  	area: [($(window).width()>1200? 1200:$(window).width())+'px', ($(window).height()-50)+'px'], //宽高
				  	//maxWidth: $(window).width() > 1200? 1200 : $(window).width()-50,
				  	//maxHeight: $(window).height()-50,
				  	content: '<div id="'+selector+'"></div>',
				  	end: function(){
				  		_this.searchShow = false;
				  		delete av('common-event').data.keyupFunctions['module-search'];//退出的时候删除事件，防止公共事件冲突
				  		av('common-event').data.keyupFunctions['common-content'] = common_content_keyupFunctions;
				  	},
				  	success: function(){
				  		//渲染这个插件
				  		AVmoduleSearch.submitDisabled = false;//关闭
						AVmoduleSearch.selector('#'+selector).render("refresh");
				  		$('[input-focus="module-search"]').focus();//失去焦点
				  	}
				});
				
			});
			
			
		},
		
		
		/**
		 * 获取路由上的值
		 * 
		 * @param {Object} k
		 * @param {Object} fn
		 */
		routerAnchorQuery:function(k, fn, errFn){
			if( typeof fn != 'function' ){
				fn = function(){};
			}
			if( typeof errFn != 'function' ){
				errFn = function(){};
			}
			
			if( (function(){try{ return av.router().anchor.query[k];}catch(e){return false;}}()) ){
				fn(av.router().anchor.query[k]);
			}else{
				errFn(undefined);
			}
			
		},
		
		
		//获取应用的积分配置
		applicationCreditConfig:function(){
			var _this = this;
			return (function(){try{ return _this.applicationConfig.credit;}catch(e){return false;}}());
		},
		
		
		
		
		
		
	}
	
	
});
