/*页面不存在*/
WangAho({
	
	id : "error/404_not_found",
	
	
	
	main : function(){
		WangAho("index").data({
			success : function(data){
				if( !data ){
					return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(
					WangAho().template("page/error/404_not_found.html", "#content"), data
				);
			}
		});
		
		
	}
	
	
	
	
	
});