(function(w, d){
	
	var eonfox = function(config){
		if( config ){
			if( config.debug ){
				eonfox.prototype.debug = true;
			}
			if( config.application ){
				eonfox.prototype.application = config.application;
			}
			if( config.api_server_url ){
				eonfox.prototype.api_server_url = config.api_server_url;
			}
			
			if(w.http){
				var _http = w.http();
				if( _http.query ){
					if( _http.query.app ) eonfox.prototype.application = _http.query.app;
					if( _http.query.application ) eonfox.prototype.application = _http.query.application;
					
					if( _http.query.server == "developer" ){
						eonfox.prototype.api_server_url = "http://server.test.eapie.com/";//测试版
					}
					
				}
			}
		}
	};
	eonfox.prototype = {
		constructor : eonfox,
		
		//是否开启调试模式
		debug : false,
		
		
		//接口地址
		api_server_url : 'http://server.test.eapie.com/',
		//文件服务器URL
		file_server_url : 'http://img.eonfox.cc/',
		
		
		//应用ID
		application : "administrator",
		
		
		//会话名称
		session_name : 'Open_Source-Eonfox_API_Engine_Session',
		
		
		/**
		 * 错误检测
		 */
		error : function(){
			if(!w.FormData || !w.FormData.prototype.append){
				return "您正在使用的浏览器版本过低，将不能正常浏览和使用。请升级IE10版本以上，或更换浏览器。";
			}
			
			return null;
		},
		
		
		
		/**
		 * JS动态修改浏览器中的title
		 * 并且兼容微信：
		 * 由于微信浏览器只在页面首次加载时初始化了标题title，之后就没有再监听 window.title的change事件。
		 * 所以这里修改了title后，立即创建一个请求，加载一个空的iframe，由于加载后立即就移除，也不会对页面造成影响，但这样微信浏览器上的title便刷新了。
		 * 
		 * @param {Object} t
		 */
		update_title : function(t){
			if( typeof t != 'string' && 
			typeof t != 'number' ){
				t = '';
			}
			
			if(document.title == t){
				return false;
			}
			
			//设置标题
			document.title = t;
			var body = $('body');
			var iframe = $('<iframe src="https://www.baidu.com/" frameborder="0" hspace="0" vspace="0" scrolling="no" height="0" width="0" style="left:-99999px;position:absolute;"></iframe>');
			iframe.on('load',function() {
			  setTimeout(function() {
			      iframe.off('load').remove();
			  }, 0);
			}).appendTo(body);
			
		},
		
		
		/**
		 * 获取token
		 */
		token : function(){
			var token = localStorage.getItem(this.session_name +":"+ this.application);
			if( token ){
				token = (function(){try{ return jQuery.parseJSON(token);}catch(e){return false;}}());
			}
			return token;
		},
		
		
		
		
		/**
		 * 获取左token
		 */
		left_token : function(){
			eonfox.prototype.submit({async : false});
			var token = localStorage.getItem(this.session_name +":"+ this.application);
			if( token ){
				token = (function(){try{ return jQuery.parseJSON(token);}catch(e){return false;}}());
			}
			if( (function(){try{ return token['session_left_token'];}catch(e){return false;}}()) ){
				return token['session_left_token'];
				}else{
					return '';
				}
		},
		
		
		
		/**
		 * 异步获取左token
		 */
		async_left_token : function(fn){
			if(typeof fn != "function"){
				var token = localStorage.getItem(this.session_name +":"+ this.application);
				if( token ){
					token = (function(){try{ return jQuery.parseJSON(token);}catch(e){return false;}}());
				}
				if( (function(){try{ return token['session_left_token'];}catch(e){return false;}}()) ){
					return token['session_left_token'];
				}else{
					return '';
				}
				
			}else{
				eonfox.prototype.submit({
					callback: function(){
						//从本地缓存中同步获取指定 key 对应的内容。
						var leftToken = "";
						var token = localStorage.getItem(this.session_name +":"+ this.application);
						if( token ){
							token = (function(){try{ return jQuery.parseJSON(token);}catch(e){return false;}}());
						}
						if( (function(){try{ return token['session_left_token'];}catch(e){return false;}}()) ){
							leftToken = token['session_left_token'];
							}
						fn(leftToken);
					}
				});
			}
		},
		
		
		
		/**
		 * 获取 websocketToken
		 */
		websocketToken : function(fn){
			if(typeof fn != "function"){
				var token = localStorage.getItem(this.session_name +":"+ this.application);
				if( token ){
					token = (function(){try{ return jQuery.parseJSON(token);}catch(e){return false;}}());
				}
				if( (function(){try{ return token['session_websocket_token'];}catch(e){return false;}}()) ){
					return token['session_websocket_token'];
				}else{
					return '';
				}
				
			}else{
				eonfox.prototype.submit({
					callback: function(){
						//从本地缓存中同步获取指定 key 对应的内容。
						var websocket_token = "";
						var token = localStorage.getItem(this.session_name +":"+ this.application);
						if( token ){
							token = (function(){try{ return jQuery.parseJSON(token);}catch(e){return false;}}());
						}
						if( (function(){try{ return token['session_websocket_token'];}catch(e){return false;}}()) ){
							websocket_token = token['session_websocket_token'];
							}
						fn(websocket_token);
					}
				});
			}
		},
		
		
		
		
		
		
		
		/**
		 * 提交队列
		 */
		submit_queue : {},
		
		
		
		/**
		 * 提交登记
		 * 
		 * @return	{String}	返回一个登记随机标识
		 */
		submit_register : function(){
			var register_id = false;
			do{
				var rand_id = Math.random();
				rand_id += ""+(new Date()).getTime();
				if(typeof eonfox.prototype.submit_queue[rand_id] == 'undefined'){
					register_id = rand_id;
					}
			}
			while(!register_id);
			
			eonfox.prototype.submit_queue[register_id] = ((new Date()).getTime()/1000);//赋值是 时间戳 （秒）,用于有效时间
			return register_id;
		},
		
		
		
		/**
		 * 提交完成后
		 * 删除登记标识
		 * 如果存在多个登记标识，那么循环继续提交（必须是有效期内的）
		 * 因为并发的情况，所以最后要给一个1秒超时器来判断是否还存在登记标识
		 * 
		 * @param {String} register_id
		 */
		submit_done : function(register_id){
			if(typeof eonfox.prototype.submit_queue[register_id] != 'undefined'){
				delete eonfox.prototype.submit_queue[register_id];
			}
			
			//这里for  in  登记标识。如果还存在有效期内的登记标识，则继续提交
			//......
			
		},
		
		
		
		/**
		 * 判断有效的注册标识有几个
		 * 
		 * @return	{int}	返回一个整数
		 */
		submit_length : function(){
			var s = 30;
			var length = 0;
			for(var i in eonfox.prototype.submit_queue){
				if( (eonfox.prototype.submit_queue[i] + s) < ((new Date()).getTime()/1000) ){
					//已经过了有效期
					delete eonfox.prototype.submit_queue[i];
				}else{
					length ++;
				}
			}
			
			return length;
		},
		
		
		
		
		
		/**
		 * 请求
		 * 暂时只支持 POST
		 * 
		 * {
		 * 	url : this.api_server_url,默认接口地址
		 * 	data : {},
		 *  temp : false,是否关闭token为临时访问 
		 * 	async : 是否异步。(默认: true)
		 *  recursion ： 递归提交。默认false。如果为true，那么再并发异步
		 *  callback : 回调函数。
		 *  progress :function(loaded[已经上传大小情况], total[总], percent[百分比]);
		 * }
		 * 
		 * jquery中各个事件执行顺序如下：
		 * ajaxStart(全局事件)
		 * beforeSend
		 * ajaxSend(全局事件)
		 * success
		 * ajaxSuccess(全局事件)
		 * error
		 * ajaxError (全局事件)
		 * complete
		 * ajaxComplete(全局事件)
		 * ajaxStop(全局事件)
		 * 
		 * [并发排队的新思路]
		 * 如果存在多个提交，那么使用超时器，等待前面的提交完，然后再提交
		 * 等待的队列也是有有效时间以及提交的配置，过了有效时间则自动删除
		 * 请求进来，先判断前面是否存在提交。存在的话，recursion 为false 表示不强制提交。则返回false(并且不注册提交登记)
		 * recursion 为 true，表示需要循环提交，注册提交登记，直至提交完成。
		 * 前面的提交完成，删除登记标识的时候，如果还存在有效时间内的登记标识，则随机抽选第一个为 下一个提交。
		 * 因为把配置也放在了提交队列中，所以可以 直接 eonfox.prototype.submit()
		 * 
		 * @param {Object} config	配置信息
		 */
		submit : function(config){
			if( eonfox.prototype.debug ){
				console.log("submit()传入参数:", config);
			}
			
			//回调函数
			if( !config.callback || config.callback.constructor != Function ){
				config.callback = function(){};
			}
			
			//是否异步
			if(typeof config.async == 'undefined') config.async = true;//默认true
			config.async = config.async? true : false;
			
			if(typeof config.url == 'undefined' || typeof config.url != 'string'){
				config.url = this.api_server_url;
			}
			
			var token = localStorage.getItem(this.session_name +":"+ this.application);
			if( token ){
				token = (function(){try{ return jQuery.parseJSON(token);}catch(e){return false;}}());
			}
			
			var right_data = new FormData();
			var left_data = new FormData();
			
			if( config.request ){
				//如果是对象，则先转换为字符串
				if(typeof config.request == "object"){
					config.request = JSON.stringify(config.request)
				}
				if(typeof config.request == "string"){
					right_data.append("data", config.request);
					left_data.append("data", config.request);
				}
			}
			
			//用户传入的data数据
			if( config.data && typeof config.data == "object" ){
				for(var i in config.data){
					right_data.append(i, config.data[i]);
					left_data.append(i, config.data[i]);
				}
			}
			
			if( eonfox.prototype.debug ){
				console.log("post()：right_data、left_data:", right_data, left_data);
			}
			
			if( !config.temp ){
				if( !(function(){try{ return token['session_right_token'];}catch(e){return false;}}()) ){
					right_data.append("session", "start");
					right_data.append("application", this.application);
				}else{
					right_data.append("token", token['session_right_token']);
					
					left_data.append("token", token['session_left_token']);
					left_data.append("session", "start");
					left_data.append("application", this.application);
				}
			}
			
			
			//获取提交标识
			config.submit_register = eonfox.prototype.submit_register();
			
			//提交请求
			var request = {
				//上传文件必要参数
				processData: false,
				contentType: false,
				
				type: 'POST',
				async: config.async,
				url: config.url,
				complete : function(){
					//当请求完成之后调用这个函数，无论成功或失败。执行时间比success晚
					eonfox.prototype.submit_done(config.submit_register);//完成提交后
				},
				success : function(){},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					if(typeof XMLHttpRequest.responseJSON == 'object'){
						console.warn(textStatus, errorThrown);
						this.success(XMLHttpRequest.responseJSON);
					}
				},
				
			};
			
			if( config.progress && config.progress.constructor == Function ){
				request.xhr = function(){
					myXhr = $.ajaxSettings.xhr();
	                if(myXhr.upload){ // 检查上传属性是否存在
	                	
	                    myXhr.upload.addEventListener('progress', function(e){                            
	                        var loaded = e.loaded;                  		//已经上传大小情况 
	                        var total = e.total;                      		//附件总大小 
	                        var percent = Math.floor(100*loaded/total);     //已经上传的百分比  
	                        config.progress(loaded, total, percent);
	                        
	                        /*
	                        console.log("已经上传了："+percent+"%");                 
	                        */
	                        
	                    }, false); // 用于处理上传的进度
	                    
	                }
	                return myXhr;
				};
			}
		
			
			//右令牌
			var right_token_post = function(right_data, left_data){
				request.data = right_data;
				request.success = function(success_data){
					if(typeof success_data != 'object'){
						success_data = (function(){try{ return jQuery.parseJSON(success_data);}catch(e){return false;}}());
					}
					if(typeof success_data != 'object'){
						console.warn("应用接口响应异常");
						return config.callback(false);
					}
					
					//如果存在请求令牌，直接返回数据
					if( (function(){try{ return success_data['token'];}catch(e){return false;}}()) ){
						//储存令牌
						eonfox.prototype.storage_token(success_data);
						//返回到回调函数
						return config.callback(success_data);
					}else{
						
						/**
						 * 这里可能会收到异步并发的影响
						 * 多次提交异步，而返回结果并不会同步，token的有效性也会受到影响
						 * 所以再右令牌没有返回令牌数据的时候，需要判断队列的随机标识
						 * 如果存在多个标识，那么当 config.recursion 为真时，就强行递归
						 */
						var submit_length = eonfox.prototype.submit_length();
						if( submit_length > 1 ){
							console.warn("应用接口提交队列并发个数：", submit_length);
							if( config.recursion ){
								return eonfox.prototype.submit(config);
							}else{
								return config.callback(success_data);
							}
						}else{
							//否则说明没有这个会话，再进行左令牌查询
							return left_token_post(left_data);
						}
						
					}
				};
				if( eonfox.prototype.debug ){
					console.log("post()：右令牌提交:", request);
				}
				$.ajax(request);
				
			};
			
			
			//左令牌
			var left_token_post = function(left_data){
				request.data = left_data;
				request.success = function(success_data){
						if(typeof success_data != 'object'){
							success_data = (function(){try{ return jQuery.parseJSON(success_data);}catch(e){return false;}}());
						}
						if(typeof success_data != 'object'){
							console.warn("应用接口响应异常");
							return config.callback(false);
						}
						//如果没有报错
						if( (function(){try{ return success_data['token'];}catch(e){return false;}}()) ){
							//储存令牌
							eonfox.prototype.storage_token(success_data);
						}
						
						//返回到回调函数
						return config.callback(success_data);
				};
				
				if( eonfox.prototype.debug ){
					console.log("post()：左令牌提交:", request);
				}
				
				$.ajax(request);
			};
			
			
			return right_token_post(right_data, left_data);
		},
		
		
		
		
		
		
		/**
		 * 储存token
		 * 
		 * @param {Object} data
		 */
		storage_token : function(data){
			if( !data ){
				return false;
			}
			
			var token_data = null;
			var exist_right_token = false;
			var exist_left_token = false;
			
			exist_right_token  = (function(){try{ return data['token']['session_right_token'];}catch(e){return false;}}());
			exist_left_token  = (function(){try{ return data['token']['session_left_token'];}catch(e){return false;}}());
			if(exist_right_token && exist_left_token){
				token_data = data['token'];
			}else{
				//有可能是顶级关联对象
				exist_right_token  = (function(){try{ return data['session_right_token'];}catch(e){return false;}}());
				exist_left_token  = (function(){try{ return data['session_left_token'];}catch(e){return false;}}());
				if(exist_right_token && exist_left_token){
					token_data = data;
				}
			}
			
			if(!token_data){
				return false;
			}
			
			//异步可能存在覆盖的问题，所以对比已存在的token,如果右左有一个相同则比较当前时间，即最大的当前时间是最新的。
			var localStorage_token = localStorage.getItem(this.session_name +":"+ this.application);
			if( localStorage_token ){
				localStorage_token = (function(){try{ return jQuery.parseJSON(localStorage_token);}catch(e){return false;}}());
			}
			if( (function(){try{ return localStorage_token['session_right_token'];}catch(e){return false;}}()) && 
			(function(){try{ return localStorage_token['session_left_token'];}catch(e){return false;}}()) ){
				
				if(localStorage_token['session_right_token'] == token_data['session_right_token'] || 
				localStorage_token['session_left_token'] == token_data['session_left_token'] ){
					if( eonfox.prototype.debug ){
						console.log("需要对比旧token中的当前时间戳,为true则不需要更新token", localStorage_token['session_now_time'], token_data['session_now_time'], parseInt(localStorage_token['session_now_time']) > parseInt(token_data['session_now_time']));
					}
					if( parseInt(localStorage_token['session_now_time']) > parseInt(token_data['session_now_time']) ){
						if( eonfox.prototype.debug ){
							console.log("并发异步，不需要更新token" );
						}
						return false;
					}
					
				}
			}
			
			localStorage.setItem(this.session_name +":"+ this.application, JSON.stringify(token_data));
			return true;
		},
		
		
		
		
		
		/**
		 * 获取文件的路由信息，用于在页面上显示
		 * file_url( $('[type="file"]')[0].files[0] );
		 * 
		 * @param {Object} file
		 */
		file_url : function(file){
			var url = "" ;
			try{
				if (window.createObjectURL != undefined) { // basic
					url = window.createObjectURL(file) ;
				} else if (window.URL != undefined) { // mozilla(firefox)
					url = window.URL.createObjectURL(file) ;
				} else if (window.webkitURL != undefined) { // webkit or chrome
					url = window.webkitURL.createObjectURL(file) ;
				}
			} catch (e){
				url = "" ;
			}
			
			return url ;
		},
		
		
		
		/**
		 * 触发点击
		 * trigger_click( $('[type="file"]').get(0) );
		 * 
		 * @param {Object} obj
		 */
		trigger_click : function (obj){
			//先判断是否是ie
		　　var ie = navigator.appName == "Microsoft Internet Explorer" ? true : false; 
		　　if(ie){
				obj.click(); 
			}else{
				var a=document.createEvent("MouseEvents");//FF的处理 
				a.initEvent("click", true, true);  
				obj.dispatchEvent(a);
				}
			
		},
		
		
				
		/**
		 * JS动态修改浏览器中的title
		 * 并且兼容微信：
		 * 由于微信浏览器只在页面首次加载时初始化了标题title，之后就没有再监听 window.title的change事件。
		 * 所以这里修改了title后，立即创建一个请求，加载一个空的iframe，由于加载后立即就移除，也不会对页面造成影响，但这样微信浏览器上的title便刷新了。
		 * 
		 * @param {Object} t
		 * @param {Object} src	加载一个路由。为空则默认为百度地址
		 */
		title : function(t, src){
			if( typeof t != 'string' && 
			typeof t != 'number' ){
				t = '';
			}
			
			if(document.title == t){
				return false;
			}
			
			if(!src){
				src = "https://www.baidu.com/";
			}
			
			//设置标题
			document.title = t;
			var body = $('body');
			var iframe = $('<iframe src="'+src+'" frameborder="0" hspace="0" vspace="0" scrolling="no" height="0" width="0" style="left:-99999px;position:absolute;"></iframe>');
			iframe.on('load',function() {
			  setTimeout(function() {
			      iframe.off('load').remove();
			  }, 0);
			}).appendTo(body);
			
		},
		
		
		
		
		
		/**
		 * 对Date的扩展，将 Date 转化为指定格式的String * 月(M)、日(d)、12小时(h)、24小时(H)、分(m)、秒(s)、周(E)、季度(q)
		 * 可以用 1-2 个占位符 * 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
	     * (new cao).date("yyyy-MM-dd hh:mm:ss.S")==> 2006-07-02 08:09:04.423      
		 * (new cao).date("yyyy-MM-dd E HH:mm:ss") ==> 2009-03-10 二 20:09:04      
		 * (new cao).date("yyyy-MM-dd EE hh:mm:ss") ==> 2009-03-10 周二 08:09:04      
		 * (new cao).date("yyyy-MM-dd EEE hh:mm:ss") ==> 2009-03-10 星期二 08:09:04      
		 * (new cao).date("yyyy-M-d h:m:s.S") ==> 2006-7-2 8:9:4.18  
		 * 
		 * timestamp 是一个毫秒时间戳
		 */
		date : function(fmt, timestamp){
			if(timestamp){
				var time = new Date(timestamp);
				}else{
					var time = new Date();
					}
			
			var o = {         
		    "M+" : time.getMonth()+1, //月份  ，注意js里的月要加1        
		    "d+" : time.getDate(), //日         
		    "h+" : time.getHours()%12 == 0 ? 12 : time.getHours()%12, //小时         
		    "H+" : time.getHours(), //小时         
		    "m+" : time.getMinutes(), //分         
		    "s+" : time.getSeconds(), //秒         
		    "q+" : Math.floor((time.getMonth()+3)/3), //季度         
		    "S" : time.getMilliseconds() //毫秒         
		    };         
		    var week = {         
		    "0" : "日",         
		    "1" : "一",         
		    "2" : "二",         
		    "3" : "三",         
		    "4" : "四",         
		    "5" : "五",         
		    "6" : "六"        
		    };         
		    if(/(y+)/.test(fmt)){         
		        fmt=fmt.replace(RegExp.$1, (time.getFullYear()+"").substr(4 - RegExp.$1.length));         
		    }         
		    if(/(E+)/.test(fmt)){         
		        fmt=fmt.replace(RegExp.$1, ((RegExp.$1.length>1) ? (RegExp.$1.length>2 ? "星期" : "周") : "")+week[time.getDay()+""]);         
		    }         
		    for(var k in o){         
		        if(new RegExp("("+ k +")").test(fmt)){         
		            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));         
		        }         
		    }         
		    return decodeURI(fmt);
		},
		
		
		
		
		
		
		
		
		
	};
	
	
	
	
	
	w.eonfox = function(config){
		return new eonfox(config);
	};
	
	w.eonfox.v = 'v1';//版本号



})(window, document);		


