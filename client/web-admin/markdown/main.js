av.framework({
    //includeSelector: 'xxxx',
    version: '1.1.2',//版本号
    includeTime: true,//引入文件是否加上时间戳参数
	exportTime: true,//导入文件是否加上时间戳参数
	debug: false,//调试模式
	
   
    //公共事件，不会被局部覆盖，并且先执行公共事件，后执行项目的事件
    event:{
        //页面开始加载时
        pageStart:function(){
            layer.load();
        },
        //页面完成加载时
        pageEnd:function(){
            layer.closeAll('loading');
        },
        //页面
       	page:function(href, callback){
       		return callback({id:'book', file:'markdown/book.js'});
       	},
       
        //页面加载有错误时
		error:function(message){
            //信息框-例4
            //layer.msg(message, {icon: 5});
            //关闭加载
            layer.closeAll('loading');
            console.log("main error：", message);
        },
        //当页面不存在时
        pageNotFound:function(href){
			layer.open({
				type: 1,
				shade: false,
				title: false, //不显示标题
				area: [($(window).width()>1200? 1200:$(window).width())+'px', ($(window).height()-50)+'px'], //宽高
				content: '<div style="padding:20px;">“'+av.router().href+'”页面不存在！</div>',
				cancel: function(){
					//删除当前浏览记录、返回上一页
					av.framework().history.pageRemove();
					av.framework().history.routerRemove();
					var pageBack = av.framework().history.pageBack();
					console.log('错误返回', pageBack);
				}
			});
        	//layer.msg('“'+av.router().href+'”页面不存在！', {icon: 5, time:-1});
            //console.log("pageNotFound 事件：", href, this);
        },
        
       

    }

}).run();

