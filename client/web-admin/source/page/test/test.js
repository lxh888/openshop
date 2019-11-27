WangAho({
	
	
	id:"test/test",
	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	
	main : function(){
		var config = {
			ss_order_name:"测试君",
			ss_order_product:[
				{ss_product_id:"6f9a67c29ce1f2ccdc6e21154579109373", ss_product_attr_id:[]},
				{ss_product_id:"ee318be51e692d1346ca83154590451657", ss_product_attr_id:["066a57276153115532296315460051327132","09056dada62c62650bb65515460046893341","cc390a4a0a2d57b93c42a715460050849968","080edeed3d2f0f1d9e3f6915460046529628"]},
				{ss_product_id:"5eff205f0201d0ee42fe40154579220993", ss_product_attr_id:["111","222",{a:2},"333"]}
			]
			
		};
		
		
		
		
		var _project = WangAho(this.id);
		WangAho("index").data({
			request : {
				list:["SOFTSTOREUSERSELFORDERADD", [config]]
				},
			success: function(data){
				console.log(data);
				
				if( !data ){
					//return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/test/test.html", "#content"), data);
				
				_project.submit();
			}
		});
		
	},
	
	
	submit : function(){
		
		
		
		
	}
	
	
	
	
	
	
});