WangAho({
	
	id : "index",
	
	
	scrollTop : 0,
	
	/**
	 * 滚动高度不变的执行
	 * 
	 * 扩展性的话可以参考：（可以传一个顶部高度）
	 * scroll_constant : function(scrollTop, fn);
	 * 
	 * @param {Object} fn
	 */
	scroll_constant : function(fn){
		this.scrollTop = $('[index="content"] .scroll').scrollTop();
		//console.log(this.scrollTop);
		if(fn && typeof fn == "function"){
			fn();
		}
	},
	
	
	view : function(content_template, data, helper){
		//console.log($('[index="header"]').length, $('[index="navigation"]').length, $('[page="content"]').length);
		
		if( !$('[index="header"]').length || 
		!$('[index="navigation"]').length ||
		!$('[page="content"]').length ){
			this.loading(content_template, data, helper);
			this.event();
			$('[index="navigation"] .subcatalog-hide').css("display","none");
			$('[index="header"] .header-button-list').first().trigger("click");//初始化导航
			
		}else{
			template( WangAho().template("public/index.html", "#header"), function(fn){
				$('[index="header"]').replaceWith( fn(data) );//替换内容
				});
				
			template( content_template, function(fn){
				if(helper){
						this.config.helper = helper;
					}
				
				//分页helper
				for(var i in WangAho("index").page_helper){
					this.config.helper[i] = WangAho("index").page_helper[i];
				}
			
				//console.log(this.config);
				$('[page="content"]').html( fn(data) );//替换内容
				});
				
			this.event();
			//更新导航
			$('[index="navigation"] .subcatalog-hide').css("display","none");
			$('[index="navigation"] .list-group-item').removeClass("active selected");//先删除全部的
			var $_subcatalog = $('[index="navigation"] .subcatalog-'+WangAho().project+'-'+WangAho().page);
			$_subcatalog.addClass("active");
			$_subcatalog.css("display","block");
			$_subcatalog.show();
		}
		
		//判断导航状态
		this.navigation_state();
		
		//分页
		$('.paging .first,.paging .previous,.paging .next,.paging .end').unbind('click').click(function(){
			if( $(this).hasClass("disabled") ){
				return false;
			}
			var page = $(this).attr("data-page");
			var _http = http();
			if(!_http.anchor.query){
				_http.anchor.query = {};
			}
			_http.anchor.query.page = page;
			http(_http).request();
		});
		
		//个人设置
		this.admin_user_config(data);
		
		//查看图片
		this.image_look_event();
		
		//查看信息
		this.tips_look_event();
		
		//注册选中状态
		this.checkbox("checkbox", "checkbox-all");
		this.paging_submit();
		
		this.action_sort_event();
		this.set_content_breadcrumb();
		//设置  $('[index="content"] .wrap') 最大宽度
		this.set_content_width();
		//用户退出
		this.log_out();
		
		//console.log(this.scrollTop);
		var scrollTop = this.scrollTop;
		//高度
		$('[index="content"] .scroll').scrollTop(scrollTop);
		//图片页面可能会出现问题，要在图片加载完成后重置高度
		$("img").each(function(){
			this.onload = function() {
	            //高度
				$('[index="content"] .scroll').scrollTop(scrollTop);
	        }
		});
		
		
		
	},
	
	
	admin_user_config_data : null,
	
	//用户个人设置
	admin_user_config : function(data){
		var _project = WangAho(this.id);
		
		if(data){
			_project.admin_user_config_data = data;
		}
		//筛选
		$(".admin_user_config").unbind("click").click(function(){
			layer.closeAll();
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-wrench\"></span> 个人设置",
			  	type: 1,
			  	shadeClose: true,
			  	area: 'auto', //宽高
			  	maxWidth: $(window).width() > 1200? 1200 : $(window).width()-50,
			  	maxHeight: $(window).height()-50,
			  	content: template( WangAho().template("public/index.html", "#admin_user_config"), function(fn){
					return fn(_project.admin_user_config_data);
					})
			});
			
			$('input[search]').first().focus();//失去焦点
			_project.admin_user_config();
		});
		
		
		$('[name="submit-admin_user_config"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.page_size = $.trim($('[name="page_size"]').val());
			
			try {
				
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["ADMINUSERSELFCONFIG", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					$btn.removeClass('disabled');
					//刷新页面
					WangAho("index").scroll_constant(function(){
						WangAho().rerun();
					});
				}
			});
			
			
		});
		
		
	},
	
	
	
	//分页
	page_helper : {
		"is_first_page" : function(n){
			if(n == 1){
				return true;
			}else{
				return false;
			}
		},
		"is_end_page" : function(j, k){
			j = parseInt(j);
			k = parseInt(k);
			if(j >= k){
				return true;
			}else{
				return false;
			}
		}
	},
	
	
	//跳转页面
	paging_submit : function(){
		$(".paging--submit").unbind("click").click(function(){
			var value = $(".paging--value").val();
			var router = av.router();
			router.anchor.query.page = value;
			av.router(router).request();
		});
	},
	
	
	//tips 查看事件
	tips_look_event : function(){
		$(".tips").unbind("click").click(function(){
			var _this = $(this);
			
			//捕获页
			layer.open({
			  type: 1,
			  shadeClose: true,
			  area: [($(document).width()<1000? $(document).width():1000)+"px",'auto'], //宽高
			  title: _this.attr("data-title"), //不显示标题
			  content: '<div style="padding: 20px 10px 10px;"><pre style="white-space: pre-wrap; word-wrap: break-word;">'+_this.attr("data")+"</pre></div>", //捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响
			});
		});
	},
	
	
	//图片查看事件
	image_look_event : function(){
		$('.image-look').unbind("click").click(function(){
			var width = $(this).attr("data-width");
			var height = $(this).attr("data-height");
			var src = $(this).attr("data-src");
			if( !src ){
				src = $(this).attr("src");
			}
			
			var layer_config = {
			  type: 1,
			  title: false,
			  closeBtn: 0,
			  area: 'auto',
			  maxWidth: $(window).width() > 1200? 1200 : $(window).width()-50,
			  maxHeight: $(window).height()-50,
			  skin: 'layui-layer-nobg', //没有背景色
			  closeBtn:1,
			  shadeClose: true,
			  content: "<div><img src=\""+src+"\" style=\"width: 100%;\"></div>"
			};
			
			if(width && height){
				var new_width = width;
				var new_height = height;
				if( width > 1200 ){
					new_width = 1200;
					//等比例缩放宽度
					if(width > new_width){
						var p = (width / (width - new_width));
						new_height = (new_height - new_height / p);
					}
				}
				
				if( new_height > ($(window).height()-50) ){
					var height = new_height;
					new_height = ($(window).height()-50);
					var p = (height / (height - new_height));
					new_width = (new_width - new_width / p);
				}
				
				layer_config.area = [new_width + "px", new_height + "px"];
			}
			
			//页面层
			layer.open(layer_config);
		});
	},
	
	
	/**
	 * 注册 checkbox
	 * 
	 * @param {Object} son_checkbox_name			子checkbox
	 * @param {Object} checkbox_all_name			全部checkbox
	 */
	checkbox : function(son_checkbox_name, checkbox_all_name){
		var son_checkbox = '[action-table="'+son_checkbox_name+'"]';
		var all_checkbox = '[action-table="'+checkbox_all_name+'"]';
		
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
	
	
	loading : function(content_template, data, helper){
		template( WangAho().template("public/index.html", "#index"), function(fn){
			this.helper("import header", function(){
				return template( WangAho().template("public/index.html", "#header"), function(fn){
					return fn(data);
				});
			});
			this.helper("import navigation", function(){
				return template( WangAho().template("public/index.html", "#navigation"), function(fn){
					
					//根据权限码显示导航
					this.helper("authority check", function(authority_id){
						if( !data.response || !data.response.admin || 
							typeof data.response.admin.admin_authority_id != "object" ||
						data.response.admin.admin_authority_id.constructor != Array ||
						data.response.admin.admin_authority_id.length < 1){
							return false;
						}
						
						var authority = data.response.admin.admin_authority_id;
						var is_authority = false;
						for(var i in authority){
							if(authority[i] == authority_id){
								is_authority = true;
							}
						}
						
						return is_authority;
					});
					
					return fn(data);
				});
			});
			this.helper("import content", function(){
				return template( content_template, function(fn){
					if(helper){
						this.config.helper = helper;
					}
					
					//分页
					this.helper("is_first_page", function(n){
						if(n == 1){
							return true;
						}else{
							return false;
						}
					});
					this.helper("is_end_page", function(j, k){
						j = parseInt(j);
						k = parseInt(k);
						if(j >= k){
							return true;
						}else{
							return false;
						}
					});
					//console.log(this.config);
					
					return fn(data);
				});
			});
			WangAho().view(fn(data));
		});
		
		
	},
	
	
	
	
	
	event : function(){
		// 如果<a>定义了disabled 需要这句来阻止打开页面
		$('a.disabled').unbind("click").click(function(event){
			event.preventDefault();  
			//console.log("点击了");
		});
		
		//上一步和下一步
		$('.header-button-path-last,.header-button-path-next').unbind("click").click(function(){
			if( $(this).hasClass("header-button-path-last") ){
				WangAho().history_path_previous();
			}else{
				WangAho().history_path_next();
			}
		});
		
		//上一步和下一步
		$('.header-button-last,.header-button-next').unbind("click").click(function(){
			if( $(this).hasClass("header-button-last") ){
				WangAho().history_previous();
			}else{
				WangAho().history_next();
			}
		});
		
		
		//子栏目显示与隐藏
		$('[index="navigation"] .catalog').unbind("click").click(function(){
			var $_subcatalog = $($(this).attr("subcatalog"));
			var $_catalog_state = $(this).find(".state");
			if( $_subcatalog.is(":visible") ){
				$_catalog_state.removeClass($_catalog_state.attr("data-show-icon")).addClass($_catalog_state.attr("data-hide-icon"));
				$_subcatalog.hide();
			}else{
				$_catalog_state.removeClass($_catalog_state.attr("data-hide-icon")).addClass($_catalog_state.attr("data-show-icon"));
				$_subcatalog.show();
			}
		});
		
		
		//初始化导航
		$('[index="header"] .header-button-list').unbind("click").click(function(){
			$('[index="navigation"] .list-group-item').removeClass("active selected");//先删除全部的
			$('[index="navigation"] .subcatalog').hide();//全部隐藏
			$('[index="navigation"] .catalog .state').removeClass($('[index="navigation"] .catalog .state').attr("data-show-icon"));
			$('[index="navigation"] .catalog .state').addClass($('[index="navigation"] .catalog .state').attr("data-hide-icon"));
			$('[index="navigation"] .subcatalog-hide').css("display","none");
			
			var $_catalog = $('[index="navigation"] .catalog-'+WangAho().project);
			var $_catalog_state = $('[index="navigation"] .catalog-'+WangAho().project+' .state');
			var $_subcatalog = $('[index="navigation"] .subcatalog-'+WangAho().project+'-'+WangAho().page);
			
			//显示子目录
			$_catalog_state.removeClass($_catalog_state.attr("data-hide-icon")).addClass($_catalog_state.attr("data-show-icon"));
			//$_catalog.addClass("active");
			$_subcatalog.addClass("active");
			$_subcatalog.show().css("display","block");
			var subcatalog = $_catalog.attr("subcatalog");
			$(subcatalog).show();
		});
		
		
		
		
		//全屏
		$('[index="header"] .header-button-fullscreen').unbind("click").click(function(){
			var elem = document;
			var elem_body = document.body;
			if( $(document.body).hasClass("fullscreen") ){
				
				//console.log("退出全屏");
				//退出全屏
			    if (elem.webkitCancelFullScreen) {
			        elem.webkitCancelFullScreen();
			    } else if (elem.mozCancelFullScreen) {
			        elem.mozCancelFullScreen();
			    } else if (elem.cancelFullScreen) {
			        elem.cancelFullScreen();
			    } else if (elem.exitFullscreen) {
			        elem.exitFullscreen();
			    } else {
			    	layer.msg("浏览器不支持全屏API或已被禁用", {icon: 5, time: 2000});
			    	return;
			    }
			    
			    $(document.body).removeClass("fullscreen");
			    
			}else{
				//console.log("全屏");
				//全屏
			    if (elem_body.webkitRequestFullScreen) {
			        elem_body.webkitRequestFullScreen();
			    } else if (elem_body.mozRequestFullScreen) {
			        elem_body.mozRequestFullScreen();
			    } else if (elem_body.requestFullScreen) {
			        elem_body.requestFullscreen();
			    } else {
			    	layer.msg("浏览器不支持全屏API或已被禁用", {icon: 5, time: 2000});
			    }
			    
				$(document.body).addClass("fullscreen");
			}
			
		});
		
		//刷新当前页面
		$('[index="header"] .header-button-refresh').unbind("click").click(function(){
			//var _http = http();
			//_http.reload(true);
			//layer.msg('刷新中', {time:500,icon: 16,shade: 0.01});
			WangAho("index").scroll_constant(function(){
				WangAho().rerun();
			});
		});
		//置顶
		$('[index="header"] .header-button-up').unbind("click").click(function(){
			$('[index="content"] .scroll').scrollTop(0);
		});
		//最底下
		$('[index="header"] .header-button-down').unbind("click").click(function(){
			var h = $(window).height()? $(window).height() : $(document).height();
			$('[index="content"] .scroll').scrollTop(h);
		});
		
		//显示和隐藏 左侧导航栏
		$('[index="header"] .header-button-navigation').unbind("click").click(function(){
			if( $('[index="navigation"] .body').is(":visible") ){
				$('[index="navigation"] .body').hide();
				$('[index="content"] .body').css({"left":"0", "width":"100%"});
				$(this).find(".glyphicon").removeClass($(this).attr("data-show-icon")).addClass($(this).attr("data-hide-icon"));
			}else{
				$('[index="navigation"] .body').show();
				$('[index="content"] .body').css({
					"left":"250px",
					"width":"-moz-calc(100% - 250px)",
					"width":"-webkit-calc(100% - 250px)",
					"width":"calc(100% - 250px)"
					});
				$(this).find(".glyphicon").removeClass($(this).attr("data-hide-icon")).addClass($(this).attr("data-show-icon"));	
			}
			
			WangAho("index").set_content_width();
		});
		
		
		//页面大小加载时
		$(window).unbind("resize").resize(function(){
			WangAho("index").set_content_width();
		});
		
	
	},
	
	
	
	
	/**
	 * 检测导航栏是否隐藏
	 */
	navigation_state : function(){
		var $_header_nav = $('[index="header"] .header-button-navigation');
		var $_glyphicon = $('[index="header"] .header-button-navigation').find(".glyphicon");
		if( $('[index="navigation"] .body').is(":visible") ){
			$_glyphicon.removeClass($_header_nav.attr("data-hide-icon")).addClass($_header_nav.attr("data-show-icon"));
		}else{
			$_glyphicon.removeClass($_header_nav.attr("data-show-icon")).addClass($_header_nav.attr("data-hide-icon"));
			}
	},
	
	
	
	
	
	/**
	 * 更新 breadcrumb
	 */
	set_content_breadcrumb : function(){
		var $_catalog = $('[index="navigation"] .catalog-'+WangAho().project);
		var $_subcatalog = $('[index="navigation"] .subcatalog-'+WangAho().project+'-'+WangAho().page);
		
		var project = $_catalog.html();
		var page = $_subcatalog.html();
		if(project && page){
			$('[page="path"] .project').html(project);
			$('[page="path"] .page').html(page);
			$('[page="path"] .state').remove();
			$('[page="path"]').show();
		}else{
			$('[page="path"]').hide();
		}
		
	},
	
	
	/**
	 * 设置内容的宽度
	 * 设置  $('[index="content"] .wrap') 最大宽度
	 */
	set_content_width : function(){
		var w = $(window).width()? $(window).width() : $(document).width();
		if( $('[index="navigation"] .body').is(":visible") ){
			$('[index="content"] .wrap').css("max-width", (w - $('[index="navigation"] .body').width()) + "px");
		}else{
			$('[index="content"] .wrap').css("max-width", w + "px");
		}
	},
	
	
	
	
	//用户退出
	log_out : function(){
		$(".user_log_out").unbind("click").click(function(){
			//提交数据
			eonfox().submit({
				request : JSON.stringify({
					s:["USERLOGOUT"],
					}),
				async : false,
				callback:function(r){
					layer.msg("退出成功", {icon: 1, time: 2000});
					setTimeout(function(){
						//返回用户中心
						var _http = http();
						if(!_http.anchor.path){
							_http.anchor.path = new Array();
						}
						_http.anchor.path[0] = 'user';
						_http.anchor.path[1] = 'log_in';
						http(_http).request();
					}, 2000);
				},
			});
			
		});
		
	},
	
	
	
	
	/**
	 * 选中
	 */
	action_table_checked : function(c){
		if(!c || typeof c != 'string'){
			c = "checkbox";
		}
		var ids = new Array();
		$('[action-table="'+c+'"]').each(function(){
			if( $(this).is(':checked') ){
				ids.push( $(this).attr("data-id") );
				}
		});
		
		return ids;
	},
	
	
	/**
	 * 获取修改键值对
	 */
	action_table : function(v){
		var obj = new Array();
		$('[action-table="'+v+'"]').each(function(){
			obj.push( { id:$(this).attr("data-id"), value:$(this).val() } );
		});
		return obj;
	},
	
	
	/**
	 * 排序
	 */
	action_sort_event : function(){
		$('[action-sort]').unbind("click").click(function(){
			var sort = $(this).attr("action-sort");
			var _http = http();
			if(!_http.anchor.query){
				_http.anchor.query = {};
			}
			_http.anchor.query.sort = sort;
			//console.log("排序：",http(_http))
			http(_http).request();
		});
		
	},
	
	
	
	/**
	 * 公用的获取数据。必要的是获取用户数据、管理员数据
	 * config = {
	 * 		request : 接口与参数，如：{s:["SESSION",[from_data]], s2:["SESSION",[from_data2]]}
	 * 		error : function(){},错误时执行的函数。如果返回true 则表示终止
	 * 		success ：function(){},操作成功后执行的函数
	 * }
	 * 
	 * @param {Object} config		配置信息
	 */
	data : function(config){
		if(!config.error || typeof config.error != "function"){
			config.error = function(){};
		}
		if(!config.success || typeof config.success != "function"){
			config.success = function(){};
		}
		if(!config.request || typeof config.request != "object"){
			config.request = {};
		}
		
		if(!config.request.application) config.request.application = ["APPLICATION"];
		if(!config.request.application_config) config.request.application_config = ["APPLICATIONCONFIG"];
		if(!config.request.management) config.request.management = ["ADMINISTRATORMANAGEMENTSELF",[{sort:["sort_asc"]}]];
		if(!config.request.user) config.request.user = ["USERSELF"];
		if(!config.request.admin) config.request.admin = ["ADMINSELF"];
		
		layer.closeAll('loading');//关闭加载
		//加载层-风格3
		layer.load(2);
		
		eonfox().submit({
			request : JSON.stringify(config.request),
			async : true,
			callback:function(r){
				layer.closeAll('loading');//关闭加载
				
				var data = {
					response : {},
					responseAll : r,
				};
				
				try {
					
					if( !r ){
						throw "未知错误";
					}
					if( (function(){try{ return r.errno;}catch(e){return false;}}()) ){
						throw r.error;
				        return false;
					}
					
					for(var i in config.request){
						if( (function(){try{ return r.data[i].data, true;}catch(e){return false;}}()) ){
							data.response[i] = r.data[i].data;
						}
					}
					
					if( (function(){try{ return r.data.user.errno;}catch(e){return false;}}()) ){
						throw r.data.user.error;
				        return false;
					}
					if( (function(){try{ return r.data.admin.errno;}catch(e){return false;}}()) ){
						throw r.data.admin.error;
				        return false;
					}
					
				}
				catch(err) {
					
					if( config.error(err) ){
						return false;
					}
			        var layer_msg_id = layer.msg(err, {icon: 5, time: 2000});
			        if( !data.response.user ){
						//layer.msg('亲，您还没有登录', {icon: 5, time: 2000});
						setTimeout(function(){
							layer.close(layer_msg_id);
							//返回用户中心
							var _http = http();
							if(!_http.anchor.path){
								_http.anchor.path = new Array();
							}
							_http.anchor.path[0] = 'user';
							_http.anchor.path[1] = 'log_in';
							http(_http).request();
						}, 2000);
						return false;
					}
			        
			        //判断是否为管理员
					if( !data.response.admin ){
						//if(!admin_error) admin_error = "没有权限"; 
						//layer.msg(admin_error, {icon: 5, time: 2000});
						//JSON
						setTimeout(function(){
							layer.close(layer_msg_id);
							//返回用户中心
							var _http = http();
							if(!_http.anchor.path){
								_http.anchor.path = new Array();
							}
							_http.anchor.path[0] = 'error';
							_http.anchor.path[1] = '401_unauthorized';
							http(_http).request();
						}, 2000);
						return false;
					}
			        
			        return false;
			    }
				
				
				//设置Title
				if(data.response.application && typeof data.response.application.application_name != "undefined"){
					WangAho().title(data.response.application.application_name, "/include/image/favicon.ico");
				}
				
				console.log(data);
				config.success(data);//回调数据
				
		}});
		//JSON
		
	},
	
	
	
	/**
	 * 公用的提交
	 * config = {
	 * 		method ： "submit" submit|remove|edit     edit 如sort排序，第一个参数[0]表示编辑权限检测
	 * 		request : 接口与参数，如：["SESSION",[from_data]]
	 * 		error : function(){},错误时执行的函数
	 * 		success ：function(){},操作成功后执行的函数
	 * }
	 * 
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
		
		layer.closeAll('loading');//关闭加载
		//加载层-风格3
		layer.load(2);
		
		if(config.method == "edit"){
			//config.request 这个时候是一个索引数组。第一个参数[0]表示编辑权限检测
			//提交数据
			eonfox().submit({
				request : JSON.stringify(config.request),
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
						layer.msg("操作成功!", {icon: 1, time: 1000});
						setTimeout(function(){
							//及时执行成功函数
							config.success();
						}, 1000);
					}else{
						layer.msg("没有数据需要更新!", {icon: 7, time: 1000});
					}
						
					return false;
			}});
			
		}else
		if(config.method == "remove"){
			//config.request 这个时候是一个索引数组
			//config.success(have_a_successful); 返回一个是否有一次成功
			
			//提交数据
			eonfox().submit({
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
			eonfox().submit({
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
					
					layer.msg("操作成功!", {icon: 1, time: 1000});
					setTimeout(function(){
						config.success(r.data.s.data);
					}, 1000);	
					return false;
			}});	
			
			
		}
		
		
	},
	
	
	
	
	
	
	
	
	
	
	
	
});