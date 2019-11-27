av({
	
	id: 'book',
    //include : ["src/common/content.js"],
    //extend : ["common-content"],
    selector:'view',
    'export' : {template : "markdown/book.html"},
    'import' : function(e){
        this.template(e.template);
    },
    main:function(){
    	this.data.bookName = (function(){try{ return av.router().anchor.path[0];}catch(e){return false;}}());
    },
    event:{
    	
		//当锚点路由更新时
        routerChange : function(){
            var _this = this;
            var bookName = (function(){try{ return av.router().anchor.path[0];}catch(e){return false;}}());
            if(_this.data.bookName != bookName){
            	av().run();//书籍发生改变，刷新页面
            	return true;
            }
            
            _this.data.getContent();
        },
        
        
		//页面开始加载时
		loadStart: function(){
			this.state('run stop');//暂停
			
        	var request = {
        		user:["USERSELF"],
        		user_phone:["USERPHONESELFVERIFY"],
        		admin:["ADMINSELF"],
        		config:["APPLICATIONCONFIG"],
        		bookList:['ADMINISTRATORMARKDOWNBOOK']
        	};
			
			var _this = this;
			new requestAPI().submit({
        		request: request,
        		async : true,
        		callback: function(r){
					try {
							
						if( !r ) throw "未知错误";
						if( (function(){try{ return r.errno;}catch(e){return false;}}()) ){
							throw r.error;
						}
						
						for(var i in request){
							if( (function(){try{ return r.data[i].data, true;}catch(e){return false;}}()) ){
								_this.data[i] = r.data[i].data;
							}
							
							if( (function(){try{ return r.data[i].errno;}catch(e){return false;}}()) ){
								throw r.data[i].error;
							}
							
						}
						
						//获取默认手机号
						if( _this.data.user ){
							if( (function(){try{ return r.data.user_phone.data[0].user_phone_id;}catch(e){return false;}}()) ){
								_this.data.user.user_phone = r.data.user_phone.data[0].user_phone_id
							}
						}
						
					} catch(err) {
						var layer_msg_id = layer.msg(err, {icon: 5, time: 3000});
						setTimeout(function(){
							layer.close(layer_msg_id);
						}, 2000);
						av().error(err);
						return _this.state('run off');//关闭
					}
        			
					//判断是否是管理员
					if( !_this.data.user ){
						_this.state('run off');//终止运行
						var layer_msg_id = layer.msg('亲，您还没有登录', {icon: 5, time: 2000});
						setTimeout(function(){
							layer.close(layer_msg_id);
							//存在上一页则跳转
							if( av.framework().history.pageBackExist() ){
								av.framework().history.routerBack();
							}else{
								//返回登录页面
								av.router(av.router().url, 'default.html#/userLogin').request();
							}
							
						}, 2000);
						return false;
					}else
					//判断是否为管理员
					if( !_this.data.admin ){
						_this.state('run off');//终止运行
						var layer_msg_id = layer.msg('亲，您没有权限', {icon: 5, time: 2000});
						//JSON
						setTimeout(function(){
							layer.close(layer_msg_id);
							//存在上一页则跳转
							if( av.framework().history.pageBackExist() ){
								av.framework().history.routerBack();
							}else{
								//返回首页面
								av.router(av.router().url, 'default.html#/').request();
							}
						}, 2000);
						return false;
					}
					
					
					//获取书籍信息
					if( typeof _this.data.bookList[_this.data.bookName] == 'undefined' ){
						_this.state('run off');//终止运行
						var layer_msg_id = layer.msg('书籍不存在', {icon: 5, time: -1});
						return false;
					}else{
						_this.data.book = null;
					}
					
					_this.data.book = _this.data.bookList[_this.data.bookName];
					//设置Title
					if(_this.data.book.title && typeof _this.data.book.title != "undefined"){
						av.setTitle(_this.data.book.title, "include/image/favicon.ico");
					}
					
					//开始
					_this.state('run on');
					
        		},
        		error: function(e){
        			//信息框-例4
            		layer.msg('数据加载出错！请联系管理员['+e+']', {icon: 5, time:-1});
        		}
        	});
        	
        	
        },
		
    	
    	loadEnd:function(){
    		//console.log();
    		var _this = this;
	    	layui.use('tree', function() {
				var tree = layui.tree;
				//渲染
				var inst1 = tree.render({
					elem: '#tree', //绑定元素
					data: _this.data.book.catalog,//数据
					click: function(obj){
						console.log( obj.data.title );
				      	var data = obj.data;  //获取当前点击的节点数据
				      	//layer.msg('状态：'+ obj.state + '<br>节点数据：' + JSON.stringify(data));
				      	console.log('状态：'+ obj.state + '<br>节点数据：' ,data);
				      	if( obj.data.path ){
				      		var router = av.router();
				      		if( !router.anchor.query ){
				      			router.anchor.query = {}
				      		}
				      		router.anchor.query.path = obj.data.path;
				      		av.router(router).request();
				      	}
				    }
				});
			});
    		
    		_this.data.getContent();
    		
    		//宽度拖动。鼠标按钮被按下
    		$('#left .drag').unbind('mousedown').on('mousedown', function(){
    			//这里可以先设储存默认的宽度，在不存在的情况下
    			//鼠标在移动时
				document.onmousemove = function(e){
					var e = e || window.event;
					
					//获取最大宽度。最大不能超过 屏幕的宽度 - 左侧宽度
					var winWidth = $(window).width()? $(window).width() : $(document).width();
					
					//获得点击右键的 x 坐标，宽度
					var leftWidth = e.clientX;
					var rightWidth = winWidth - leftWidth;
					
					console.log(leftWidth, rightWidth);
					
					$('#left .drag').css('background-color', '#999');
					$('#left .body').width(leftWidth+'px');
					$('#left .scroll').width((leftWidth+20)+'px');
					$('#left .wrap').width(leftWidth+'px');
					
					$('#right').css('left', leftWidth+'px').css('width', '-moz-calc(100% - '+leftWidth+'px)').css('width', '-webkit-calc(100% - '+leftWidth+'px)').css('width', 'calc(100% - '+leftWidth+'px)');
					
					//某个鼠标按键被松开
					document.onmouseup = function(){
						$('#left .drag').css('background-color', '');
						
						_this.data.leftBodyStyle.width = leftWidth+'px';
						_this.data.leftScrollStyle.width = (leftWidth+20)+'px';
						_this.data.leftWrapStyle.width = leftWidth+'px';
						_this.data.rightStyle.left = leftWidth+'px';
						_this.data.rightStyle.width = ['-moz-calc(100% - '+leftWidth+'px)', '-webkit-calc(100% - '+leftWidth+'px)', 'calc(100% - '+leftWidth+'px)'];
						
						document.onmousemove = null;
						document.onmouseup = null;
					}
				}
    			
    		});
    		//mouseup 事件会在鼠标按键被松开时发生
			$('#left .drag').unbind('mouseup').on('mouseup', function(e){
				$(this).css('background-color', 'transparent');
				document.onmousemove = null;
				document.onmouseup = null;
			});
    		
    		
    	}
    	
    	
    },
	data:{
		leftBodyStyle:{
			width:'250px'
			},
		leftScrollStyle:{
			width:'270px'
		},
		leftWrapStyle:{
			width:'250px'
		},
		rightStyle:{
			left:'250px',
			width:['-moz-calc(100% - 250px)', '-webkit-calc(100% - 250px)', 'calc(100% - 250px)']
		},
		
		content:'',//内容
		bookName:'',
		book:null,//当前书籍
		page:null,
		getContent:function(){
			var _this = this;
			layer.closeAll();
			layer.load();
            var path = (function(){try{ return av.router().anchor.query.path;}catch(e){return false;}}());
            if( !path ){
            	return false;
            }
            
			new requestAPI().submit({
        		request: {content:["ADMINISTRATORMARKDOWNCONTENT", [{book:_this.bookName, path:path}]]},
        		callback: function(r){
        			layer.closeAll('loading');
        			try{
        				
        				if( (function(){try{ return r.data.content.errno;}catch(e){return false;}}()) ){
							throw r.data.content.error;
						}
        				
        				if( (function(){try{ return r.data.content.data, true;}catch(e){return false;}}()) ){
        					_this.page = _this.book.bound[path];
        					
	        				if(r.data.content.data == ''){
	        					$('#content').html('');
	        					//_this.content = '';
	        					return false;
	        				}
	        				var html = marked(r.data.content.data);
	        				var jqueryContent = $('#content');
	        				//var jqueryContent = $('<div></div>');
	        				jqueryContent.html(html);
	        				jqueryContent.find('table').addClass('layui-table');
	        				jqueryContent.find('blockquote').addClass('layui-elem-quote');
	        				jqueryContent.find('ul,ol').addClass('layui-elem-quote layui-quote-nm');
	        				//.addClass("line-numbers").css("white-space", "pre-wrap")
	        				jqueryContent.find('pre').addClass('language-php');
	        				jqueryContent.find('pre').find('code').addClass('prism language-php');
	        				jqueryContent.find('code:not(.prism)').addClass('layui-badge-rim');
	        				
	        				jqueryContent.find('code.prism').each(function(){
				    			Prism.highlightElement(this, false);
				    		});
	        				//jqueryHtml.find('code').addClass('language-c++');
	        				/*jqueryHtml.find('pre').each(function(){
	        					console.log(this);
				    			Prism.highlightElement(this, true);
				    		});*/
	        				//console.log(html);
							//_this.content = jqueryContent.html();
							
							//图片查看
							$('#content').find('img').click(function(){
								_this.showImage(this);
							});
							
						}
        				
        			}catch(e){
        				layer.open({
						  type: 1,
						  shade: false,
						  title: false, //不显示标题
						  content: '<div style="padding: 20px;color: red;">'+e+'</div>',
						});
						return false;
        			}
        			
        			
        		},
        		error: function(e){
            		layer.msg('数据加载出错！请联系管理员['+e+']', {icon: 5, time:-1});
        		}
        	});
		},
		
		showImage:function(ele){
			var height = ele.naturalHeight;
			var width = ele.naturalWidth;
			var src = $(ele).attr('src');
			
			if(!src || !height || !width){
				return false;
			}
			
			var layer_config = {
				type: 1,
				title: false,
				closeBtn: 0,
				area: 'auto',
				maxWidth: $(window).width() > 1200 ? 1200 : $(window).width() - 50,
				maxHeight: $(window).height() - 50,
				skin: 'layui-layer-nobg', //没有背景色
				closeBtn: 1,
				shadeClose: true,
				content: "<div><img src=\"" + src + "\" style=\"width: 100%;\"></div>"
			};
			
			if( width && height ){
				var new_width = width;
				var new_height = height;
				if(width > 1920) {
					new_width = 1920;
					//等比例缩放宽度
					if(width > new_width) {
						var p = (width / (width - new_width));
						new_height = (new_height - new_height / p);
					}
				}
			
				if(new_height > ($(window).height() - 50)) {
					var height = new_height;
					new_height = ($(window).height() - 50);
					var p = (height / (height - new_height));
					new_width = (new_width - new_width / p);
				}
			
				layer_config.area = [new_width + "px", new_height + "px"];
			}
			
			//页面层
			layer.open(layer_config);
		}
		
	}
	
	
	
});
