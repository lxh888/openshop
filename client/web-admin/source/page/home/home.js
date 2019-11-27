WangAho({
	
	id : "home/home",
	
	//路由发送变化时
	hashchange : function(){
		console.log('路由发送了变化', http() );
		var tpl = WangAho().template("/home/home.html", "#content");
		console.log(tpl);
	},
	
	
	session : {},
	
	main : function(){
		var _project = WangAho(this.id);
		WangAho("index").data({
			success : function(data){
				if( !data ){
					return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/home/home.html", "#content"), data,{
					"home test" :function(){
						
						
						
						
					}
				});
			}
		});
		
	},
	
	
	
	
	
	
	
});
