av({
	
	id: 'page-401Unauthorized',
	selector:'view',
	include : ["src/common/event.js"],
    extend : ["common-event"],
	'export' : {template : "src/page/401Unauthorized.html"},
    'import' : function(e){
        this.template(e.template);
    },
	event:{
        //页面加载完成时
		loadStart: function(){
			this.state('run stop');//暂停
			var RAPI = new requestAPI();
			var _this = this;
			RAPI.submit({
        		request: {user:["USERSELF"]},
        		async : true,
        		callback: function(r){
        			if( (function(){try{ return r.data.user.data, true;}catch(e){return false;}}()) ){
						_this.data.user = r.data.user.data;
					}
        			
        			_this.state('run on');//开始
        		},
        		error: function(e){
        			//信息框-例4
            		layer.msg('数据加载出错！请联系管理员['+e+']', {icon: 5, time:-1});
        		}
        	});
        },
        
	},
	data:{
		user: null,//用户数据
	}
	
	
});
