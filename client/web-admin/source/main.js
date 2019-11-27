(function(w, d){
	
	
	var main = function(source){
		if(typeof source == "string"){
			if(main.prototype.source[source]){
				return main.prototype.source[source];
			}
		}
		if(typeof source == "object" && source.id){
			main.prototype.source[source.id] = source;
		}
		
		return this;
	};
	
	
	main.prototype = {
		
		constructor : main,
	
		
		//版本号
		version : "0.3",
		
		//源代码
		source : {},
	
	
		//当前源代码键
		id : null,
		
		
		//当前项目
		project : "",
		
		//当前页面
		page : "",
		
		
		//配置信息
		config : {
			debug : false,//是否开启调试模式
			project : "main", //默认项目名称
			page : "main",//默认页面名称
			run_dir : "source/",//默认运行目录
			data_dir : "source/",//默认数据目录
			template_dir : "source/",//默认模板目录
			source_include_selector : "main-source",
			view_include_selector : "main-view",
			error : ["error", "404"],//页面不存在时，执行的 项目名称、页面名称
			history_maximum : 100, //历史记录最多条数
			history_path_maximum : 100 //path路径的历史记录最多条数
		},
		
		
		
		/**
		 * 数据列表
		 */
		data_list : {},
		
		//引入数据
		data : function(u){
			
			if(u){
				u = u.replace(/^[\/\\]/g,"");//去掉前面的目录分隔符
			}
			
			if(u && (typeof this.data_list[u] == 'object' || 
			typeof this.data_list[u] == 'number') ){
				return this.data_list[u];
			}
			
			var query = main.prototype.version;
			if( main.prototype.config.debug ){
				query = Math.random();
			}
			
			var url = main.prototype.config.data_dir+"/"+u+"?v="+query;
			//去掉前面的目录分隔符
			url = url.replace(/^[\/\\]/g,"");
			
			
			var json_data = {};
			$.ajax({
				type: "GET",
				async: false,
				dataType : "json",
				url: url,
				success: function(success_data){
					if(typeof success_data != 'object'){
						success_data = (function(){try{ return jQuery.parseJSON(success_data);}catch(e){return false;}}());
					}
					json_data = success_data;
				}
			});
			
			this.data_list[u] = json_data;
			return this.data_list[u];
		},
		
		
		
		
		/**
		 * 模板列表
		 */
		template_list : {},
		
		
		//引入模板信息
		template : function(u, selector){
			
			if(u){
				u = u.replace(/^[\/\\]/g,"");//去掉前面的目录分隔符
			}
			
			if(u && (typeof this.template_list[u] == 'string' || 
			typeof this.template_list[u] == 'number' ) ){
				if(selector){
					return $("<div>"+this.template_list[u]+"</div>").find(selector).html();
				}else{
					return this.template_list[u];
				}
			}
			
			var query = main.prototype.version;
			if( main.prototype.config.debug ){
				query = Math.random();
			}
			var url = main.prototype.config.template_dir+"/"+u+"?v="+query;
			//去掉前面的目录分隔符
			url = url.replace(/^[\/\\]/g,"");
			
			var html = '';
			$.ajax({
				type: "GET",
				async: false,
				url: url,
				success: function(success_data){
					html = $("<div>"+success_data+"</div>").html();
				}
			});
			
			this.template_list[u] = html;
			if(selector){
				return $("<div>"+this.template_list[u]+"</div>").find(selector).html();
			}else{
				return this.template_list[u];
			}
			
		},
		
		
		
		/**
		 * 显示页面
		 * 
		 * @param {Object} s
		 */
		view : function(s){
			if( typeof s != 'string' && 
			typeof s != 'number' ){
				s = '';
			}
			
			$(main.prototype.config.view_include_selector).html(s);
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
		
			
		
		/**
		 * 字节单位换算函数（bytes，KB）
		 * 
		 * @param {Object} bytes
		 */
		byte_convert : function(bytes) { 
			if( isNaN(bytes) ){
				return '';
   			} 
   			var symbols = ['bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']; 
   			var exp = Math.floor(Math.log(bytes)/Math.log(2)); 
   			if (exp < 1) {
 				exp = 0;
			} 
			var i = Math.floor(exp / 10); 
			bytes = bytes / Math.pow(2, 10 * i); 
			if ( bytes.toString().length > bytes.toFixed(2).toString().length) {
				bytes = bytes.toFixed(2);
    		} 
    		return bytes + ' ' + symbols[i]; 
		},
		
		
		
		
		/**
		 * 刷新运行
		 */
		rerun : function(){
			main.prototype.run(main.prototype.project, main.prototype.page);
		},
		
		
		
		/**
		 * 载入项目页面
		 * 
		 * error 如果为真，表示是错误页面过来的，如果再错误则不再自动执行跳转到错误页面。防止死循环
		 */
		run : function(project, page, error){
			
			//更新 项目和页面
			if( !error ){
				main.prototype.project = project;
				main.prototype.page = page;
			}
			
			var id = project + '/' + page;
			main.prototype.id = id;//当前控制器的key键
			
			var query = main.prototype.version;
			if( main.prototype.config.debug ){
				query = Math.random();
			}
			
			//载入控制器
			var src = main.prototype.config.run_dir+"/"+id+".js";
			//去掉前面的目录分隔符
			src = src.replace(/^[\/\\]/g,"");
			//引入控制器，加载js文件
			var obj = include(src, "v="+query, "utf-8" );
			//obj.load(true);//只引入一次
			if( !obj.exists() ){
				$(main.prototype.config.source_include_selector).append(obj.element);
			}
			
			obj.ready(function(){
				var id = main.prototype.id;
				if(id && 
					main.prototype.source[id] &&
					main.prototype.source[id].main &&
					typeof main.prototype.source[id].main == "function"){
					if( main.prototype.config.debug ){
						console.log("载入项目页面，进入“"+id+"”的main方法");
					}
					
					main.prototype.source[id].main();
				} else {
					//obj.remove();//删除已添加到页面上的
					if( main.prototype.config.debug ){
						console.error( "“"+main.prototype.id+"” 控制器不存在，或者控制器的 main 方法不存在" );
					}
					
					//跳转到页面不存在的地址
					if( !error ){
						main.prototype.run(main.prototype.config.error[0], main.prototype.config.error[1], true);
					}
					
				}
			});
			
		},
		
		
		//只收集path记录。
		history_path_list : {
			previous:[],
			next:[]
		},
		
		
		//判断上一页是否存在
		history_path_previous_exist : function(){
			var length = main.prototype.history_path_list.previous.length;
			//包括当前页面的
			if(length < 2){
				return false;
			}else{
				return true;
			}
		},
		
		//判断下一页是否存在
		history_path_next_exist : function(){
			var length = main.prototype.history_path_list.next.length;
			if(length < 1){
				return false;
			}else{
				return true;
			}
		},
		
		
		//收集页面 
		history_path_gather : function(href){
			href = http(href).href;
			var length = main.prototype.history_path_list.previous.length;
			if( length < 1 ){
				main.prototype.history_path_list.previous.push(href);
			}else{
				var previous = main.prototype.history_path_list.previous[length-1];
				if(previous != href){
					//最多收集的条数,记录超过了，则从最开始去掉
					var history_maximum = main.prototype.config.history_path_maximum;
					//这里不加1是因为包括本页
					if( length > history_maximum){
						//删除第一个元素
						main.prototype.history_path_list.previous.shift();
					}
					main.prototype.history_path_list.previous.push(href);
				}
				
			}
			
			//初始化上一页。如果上一页就是当前页则删除上一页
			var next_length = main.prototype.history_path_list.next.length;
			var next = main.prototype.history_path_list.next[next_length-1];
			if(next == href){
				main.prototype.history_path_list.next.splice(next_length-1, 1);
				/*delete main.prototype.history_path_list.next[next_length-1];
				main.prototype.history_path_list.next.length --;*/
			}
			
		},
		
		
		//删除 当前页的记录
		history_path_remove : function(){
			var length = main.prototype.history_path_list.previous.length;
			if( length < 1 ){
				main.prototype.history_path_list.previous = [];
			}else{
				main.prototype.history_path_list.previous.splice(length-1, 1);
				/*delete main.prototype.history_path_list.previous[length-1];
				main.prototype.history_path_list.previous.length--;*/
			}
		},
		
		
		
		
		//上一页
		history_path_previous : function(){
			var length = main.prototype.history_path_list.previous.length;
			if( length < 2 ){
				return false;
			}
			
			//获得需要跳转的页面
			var _http = http(main.prototype.history_path_list.previous[length-2]);
			
			//添加到下一页
			var next_length = main.prototype.history_path_list.next.length;
			//最多收集的条数,记录超过了，则从最开始去掉一个
			if( (next_length+1) > main.prototype.config.history_path_maximum){
				//删除第一个元素
				main.prototype.history_path_list.next.shift();
			}
			main.prototype.history_path_list.next.push(main.prototype.history_path_list.previous[length-1]);
			
			
			//删除当前页、上一页
			if(length == 2){
				main.prototype.history_path_list.previous = [];//初始化
			}else{
				main.prototype.history_path_list.previous.splice(length-2, 2);
				/*delete main.prototype.history_path_list.previous[length-1];
				delete main.prototype.history_path_list.previous[length-2];
				main.prototype.history_path_list.previous.length = main.prototype.history_path_list.previous.length-2;*/
			}
			
			//跳转
			_http.request();
		},
		
		
		
		
		//下一页  后入先出
		history_path_next : function(){
			var length = main.prototype.history_path_list.next.length;
			if( length < 1 ){
				return false;
			}
			//获得需要跳转的页面
			var _http = http(main.prototype.history_path_list.next[length-1]);
			if(length == 1){
				main.prototype.history_path_list.next = [];//初始化
			}else{
				main.prototype.history_path_list.next.splice(length-1, 1);
				/*delete main.prototype.history_path_list.next[length-1];
				main.prototype.history_path_list.next.length --;*/
			}
			
			//跳转
			_http.request();
		},
		
		
		
		
		
		
		//收集记录。
		history_list : {
			previous:[],
			next:[]
		},
		
		
		
		
		//判断上一页是否存在
		history_previous_exist : function(){
			var length = main.prototype.history_list.previous.length;
			//包括当前页面的
			if(length < 2){
				return false;
			}else{
				return true;
			}
		},
		
		//判断下一页是否存在
		history_next_exist : function(){
			var length = main.prototype.history_list.next.length;
			if(length < 1){
				return false;
			}else{
				return true;
			}
		},
		
		
		//收集页面 
		history_gather : function(href){
			href = http(href).href;
			var length = main.prototype.history_list.previous.length;
			if( length < 1 ){
				main.prototype.history_list.previous.push(href);
			}else{
				var previous = main.prototype.history_list.previous[length-1];
				if(previous != href){
					//最多收集的条数,记录超过了，则从最开始去掉
					var history_maximum = main.prototype.config.history_maximum;
					//这里不加1是因为包括本页
					if( length > history_maximum){
						//删除第一个元素
						main.prototype.history_list.previous.shift();
					}
					main.prototype.history_list.previous.push(href);
				}
				
			}
			
			//初始化上一页。如果上一页就是当前页则删除上一页
			var next_length = main.prototype.history_list.next.length;
			var next = main.prototype.history_list.next[next_length-1];
			if(next == href){
				main.prototype.history_list.next.splice(next_length-1, 1);
				/*delete main.prototype.history_list.next[next_length-1];
				main.prototype.history_list.next.length --;*/
			}
			
		},
		
		
		//删除 当前页的记录
		history_remove : function(){
			var length = main.prototype.history_list.previous.length;
			if( length < 1 ){
				main.prototype.history_list.previous = [];
			}else{
				main.prototype.history_list.previous.splice(length-1, 1);
				/*delete main.prototype.history_list.previous[length-1];
				main.prototype.history_list.previous.length--;*/
			}
		},
		
		
		//上一页
		history_previous : function(){
			var length = main.prototype.history_list.previous.length;
			if( length < 2 ){
				return false;
			}
			
			//console.log(main.prototype.history_list.previous);
			//获得需要跳转的页面
			var _http = http(main.prototype.history_list.previous[length-2]);
			
			//添加到下一页
			var next_length = main.prototype.history_list.next.length;
			//最多收集的条数,记录超过了，则从最开始去掉一个
			if( (next_length+1) > main.prototype.config.history_maximum){
				//删除第一个元素
				main.prototype.history_list.next.shift();
			}
			main.prototype.history_list.next.push(main.prototype.history_list.previous[length-1]);
			
			
			//删除当前页、上一页
			if(length == 2){
				main.prototype.history_list.previous = [];//初始化
			}else{
				main.prototype.history_list.previous.splice(length-2, 2);
				/*delete main.prototype.history_list.previous[length-1];
				delete main.prototype.history_list.previous[length-2];
				main.prototype.history_list.previous.length = main.prototype.history_list.previous.length-2;*/
			}
			
			//console.log(main.prototype.history_list.previous);
			//跳转
			_http.request();
		},
		
		
		
		
		//下一页  后入先出
		history_next : function(){
			var length = main.prototype.history_list.next.length;
			if( length < 1 ){
				return false;
			}
			//获得需要跳转的页面
			var _http = http(main.prototype.history_list.next[length-1]);
			if(length == 1){
				main.prototype.history_list.next = [];//初始化
			}else{
				main.prototype.history_list.next.splice(length-1, 1);
				/*delete main.prototype.history_list.next[length-1];
				main.prototype.history_list.next.length --;*/
			}
			
			//跳转
			_http.request();
		},
		
		
		
		
		
		/**
		 * 程序开始
		 * 
		 * @param {Object} config		配置信息
		 */
		start : function(config){
			
			/**
			 * 监听锚点
			 * 轮询
			 * 只要锚点中的path参数发生改变则初始化
			 */
			if( !w.hashchange ){
				w.hashchange = function(){
					var h = http();
					var anchor_path = (h.anchor && h.anchor.path)? h.anchor.path : '';
					var hash = h.hash;
					//获取记录
					main.prototype.history_gather(h.href);
					main.prototype.history_path_gather(h.href);
					
					setInterval(function(){
						var h_now = http();
						var anchor_path_now = (h_now.anchor && h_now.anchor.path)? h_now.anchor.path : '';
						//如果锚点发生变化，通知当前控制器
						var hash_now = h_now.hash;
						var href_now = h_now.href;
						
						if(JSON.stringify(anchor_path) != JSON.stringify(anchor_path_now)){
							//获取记录
							main.prototype.history_gather(href_now);
							main.prototype.history_path_gather(href_now);
							
							anchor_path = anchor_path_now;//重新赋值
							hash = hash_now;//也要更新一些全部锚点。防止重复执行
							main.prototype.start();//初始化页面
							
							if( main.prototype.config.debug ){
								console.log("path参数发生变化，初始化页面");
							}
							
							}else{
								if(hash_now != hash){
									//获取记录
									main.prototype.history_gather(href_now);
							
									hash = hash_now;
									var id = main.prototype.id;
									if(id && 
										main.prototype.source[id] &&
										main.prototype.source[id].hashchange &&
										typeof main.prototype.source[id].hashchange == "function"){
											
											if( main.prototype.config.debug ){
												console.log("hash参数发生变化，通知“"+id+"”的hashchange方法");
											}
										
										main.prototype.source[id].hashchange();
									}
								}
								
							}
						
					}, 0);
				}
				//并执行
				w.hashchange();
			}
			
			if(config){
				//是否开启调试模式
				main.prototype.config.debug = config.debug? true : false;
				if( config.error && typeof config.error == "object" && config.error.constructor == Array && config.error.length > 1 ){
					main.prototype.config.error = config.error;
				}
				if(config.project && typeof config.project == "string") main.prototype.config.project = config.project;
				if(config.page && typeof config.page == "string") main.prototype.config.page = config.page;
				if(config.run_dir && typeof config.run_dir == "string") main.prototype.config.run_dir = config.run_dir;
				if(config.data_dir && typeof config.data_dir == "string") main.prototype.config.data_dir = config.data_dir;
				if(config.template_dir && typeof config.template_dir == "string") main.prototype.config.template_dir = config.template_dir;
				if(config.source_include_selector && typeof config.source_include_selector == "string") main.prototype.config.source_include_selector = config.source_include_selector;
				if(config.view_include_selector && typeof config.view_include_selector == "string") main.prototype.config.view_include_selector = config.view_include_selector;
				
				if(typeof config.history_maximum == "number") main.prototype.config.history_maximum = config.history_maximum;
				if(typeof config.history_path_maximum == "number") main.prototype.config.history_path_maximum = config.history_path_maximum;
			}
			//去掉后面的目录分隔符、去掉前面的目录分隔符
			main.prototype.config.run_dir = main.prototype.config.run_dir.replace(/[\/\\]$/g,"").replace(/^[\/\\]/g,"");
			main.prototype.config.data_dir = main.prototype.config.data_dir.replace(/[\/\\]$/g,"").replace(/^[\/\\]/g,"");
			main.prototype.config.template_dir = main.prototype.config.template_dir.replace(/[\/\\]$/g,"").replace(/^[\/\\]/g,"");
			
			var _http = http();
			var bool = (function(){try{ return (typeof _http.anchor.path[0] == 'string' && _http.anchor.path[0] !='' && typeof _http.anchor.path[1] == 'string' && _http.anchor.path[1] !='');}catch(e){return false;}}());
			
			if( bool ){
				if( main.prototype.config.debug ){
					console.log("start：开始运行程序，自定义项目“"+_http.anchor.path[0]+"”/默认页面“"+_http.anchor.path[1]+"”");
				}
				
				main.prototype.run(_http.anchor.path[0], _http.anchor.path[1]);
			}else{
				if( main.prototype.config.debug ){
					console.log("start：开始运行程序，默认项目“"+this.config.project+"”/默认页面“"+this.config.page+"”");
				}
				
				//否则就是首页
				main.prototype.run(this.config.project, this.config.page);
			}
			
			
		},
		
		
		
		
	}
	
	
	
	w.WangAho = function(source){
		return new main(source);
	};
	w.WangAho.v = 'v1';//版本号

})(window, document);


