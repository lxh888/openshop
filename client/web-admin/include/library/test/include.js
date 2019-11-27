(function(w, d){
	
	
	/**
	 * var obj = include()
	 * obj.load(); 加载文件。默认加载在head头之后。obj.load(true)只引入一次。否则false或者默认可以引入多次
	 * obj.exists(); //检查js/css文件是否已经加载
	 * obj.remove(); //检查js/css文件是否已经加载，已加载则删除
	 * obj.url //文件路径
	 * obj.query //参数
	 * obj.type //文件类型
	 * obj.element; //返回 Element
	 * obj.charset //编码
	 */
	function include(url, query, charset){
		var obj = {};
		
		obj.url = "";
		obj.query = "";
		obj.charset = "";
		obj.element = null;//定义生成的元素
		if(arguments.length > 0){
			if( typeof url == 'object' ){
				obj.element = url;
			}else
			if( typeof url == 'string' ){
				obj.url = url;
				obj.query = typeof query == 'string' || typeof query == 'number'? query.toString() : '';
				obj.charset = typeof charset == 'string' ? charset : "";
			}
		}
		
		//判断obj.query前面是否有?符号
		if(obj.query != ''){
			obj.query = obj.query.replace(/(^\s*)|(\s*$)/g,'');//清空两边空白
			obj.query = '?' + obj.query.replace(/^\?{0,}/g,'');//清理?符号
		}
		
		//获取后缀，文件类型
		obj.type = '';
		if(obj.url != ''){
			//获取后缀，css或js
			obj.type = obj.url.split(".").pop();
			//清空两边空白
			obj.type = obj.type.replace(/(^\s*)|(\s*$)/g,'');
			//转为小写
			obj.type = obj.type.toLowerCase();
		}
		
		
		//生成元素
		if( obj.type === 'css' ){
			obj.element = document.createElement("link");  
            obj.element.rel = "stylesheet";  
            obj.element.href = obj.url+obj.query;
            if(obj.charset) obj.element.charset = obj.charset;
		}else
		if( obj.type === 'js' ){
			obj.element = document.createElement("script");  
            obj.element.type = "text/javascript";  
            obj.element.src = obj.url+obj.query;
            if(obj.charset) obj.element.charset = obj.charset;
		}
		
		
		//检查js/css文件是否已经加载
		obj.exists = function(){
			if(this.url == '' || this.type == ''){
				return false;
			}
			
			var is = false;  
			var tags = {"js":"script", "css":"link"};
			var tagn = tags[this.type];
			if( tagn != undefined ){
			    var elts = document.getElementsByTagName(tagn);
		        for(i in elts){  
		            if( (elts[i].href && elts[i].href.toString().indexOf(this.url) !="-1" ) ||   
		                (elts[i].src && elts[i].src.toString().indexOf(this.url) != "-1" ) ){  
		                is = true;
		                break;//停止循环
		            	}  
		        	}  
			    } 
			    
			return is; 
		};
		
		
		//检查js/css文件是否已经加载，已加载则删除。如果存在并删除，则返回true,否则返回false
		obj.remove = function(){
			if(this.url == '' || this.type == ''){
				return false;
			}
			
			var is = false;  
			var tags = {"js":"script", "css":"link"};
			var tagn = tags[this.type];
			if( tagn != undefined ){
			    var elts = document.getElementsByTagName(tagn);
		        for(i in elts){  
		            if( (elts[i].href && elts[i].href.toString().indexOf(this.url) !="-1" ) ||   
		                (elts[i].src && elts[i].src.toString().indexOf(this.url) != "-1" ) ){ 
		                elts[i].parentNode.removeChild(elts[i]);//删除
		                is = true;
		                break;//停止循环
		            	}  
		        	}  
			    } 
			    
			return is; 
		};
		
		
		//加载文件。默认加载在head头之后。obj.load(true)只引入一次。否则false或者默认可以引入多次
		obj.load = function(b){
			if(!this.element || this.url == '' || this.type == ''){
				return false;
			}
			
			//obj.load(true)只引入一次。否则false或者默认可以引入多次
			if( arguments.length > 0 && b ){
				if( this.exists() ){
					return true;
				}
			}
			
			//获得第一个head头
			var head = document.getElementsByTagName('head')[0];
			head.appendChild(this.element);//写入
			return true;
		};
		
		
		
		/**
		 * DOM加载出错的函数
		 * 
		 * @param {Object} fn
		 */
		obj.error = function(fn){
			//判断是否是函数 fn.constructor == Function
			if( !this.element || !fn || (typeof fn != 'function') ){
				return false;
				}
			window.onerror = function(){
				fn();
			}
		};
		
		
		
		/**
		 * 要在DOM就绪时执行的函数。兼容 IE8
		 * 
		 * @param {Object} fn
		 * @return {void} 
		 */
		obj.ready = function(fn){
			
			//判断是否是函数 fn.constructor == Function
			if( !this.element || !fn || (typeof fn != 'function') ){
				return false;
				}
			
			var ready_state = false;
			var ready_fn = function(){
				if(ready_state){
					return false;//只执行一次
				}
				
				//执行函数
				fn();
				
				ready_state = true;
			};
			
			if ( document.readyState === "complete" ) {
				//异步处理它，让脚本有机会延迟准备
        		setTimeout( ready_fn, 1 );

    		} else if ( document.addEventListener ) {
    			// 目前Mozilla、Opera和webkit 525+内核支持DOMContentLoaded事件
        		document.addEventListener( "DOMContentLoaded", ready_fn, false );
       			window.addEventListener( "load", ready_fn, false );
    		}else {
    			//如果IE
            	document.attachEvent( "onreadystatechange", ready_fn );
           		window.attachEvent( "onload", ready_fn );
            	var top = false;
	            try {
	                top = window.frameElement == null && document.documentElement;
	            } catch(e) {}
	            //轮询调用doScroll 方法检测DOM是否加载完毕
	            if ( top && top.doScroll ) {
	                (function doScrollCheck() {
	                    try {
                            top.doScroll("left");
                        } catch(e) {
                            return setTimeout( doScrollCheck, 50 );
                        }
                        ready_fn();
	                })();
	            }
        	}
		
		};
		
		
		
		
		
		
		return obj;
	}
	
	
	
	
	
	w.include = include;
	w.include.v = 'v1';//版本号



})(window, document);	