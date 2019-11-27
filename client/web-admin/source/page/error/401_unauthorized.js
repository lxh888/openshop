/*未经授权*/
WangAho({
	
	id : "error/401_unauthorized",
	
	
	
	main : function(){
		
		//判断是否已经登录
		var user = false;
		var request_data = JSON.stringify({
			s:["USERSELF"]
			});
		eonfox().submit({
			request : request_data,
			async : false,
			callback:function(r){
				if( !r ){
					return;
				}
				if( (function(){try{ return r.data.s.data;}catch(e){return false;}}()) ){
					user = r.data.s.data;
				}
		}});
		//JSON
		
		template( WangAho().template("page/error/401_unauthorized.html", "#content"), function(fn){
			WangAho().view( fn({user:user}) );
		});
		//用户退出
		WangAho("index").log_out();
	},
	
	
	
	
	
	
});
