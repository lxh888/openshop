WangAho({
	
	
	id:"administrator/program_error_details",
	
	
	main : function(){
		var _project = WangAho(this.id);
		var config = {};
		var _http = http();
		config.file_name = (function(){try{ return _http.anchor.query.filename;}catch(e){return false;}}());
		if( !config.file_name ){
			layer.msg("文件名称无效", {icon: 5, time: 2000});
			return false;
		}
		
		WangAho("index").data({
			request : {
				get:["ADMINISTRATORADMINPROGRAMERRORDETAILS",[config]]
				},
			success : function(data){
				
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.get.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.get.error, {icon: 5, time: 2000});
				}
				
				template( WangAho().template("page/administrator/program_error_details.html", "#content"), function(fn){
					WangAho().view( fn(data) );
				});
				
				_project.event();
			}
		});
		
	},
	
	
	event : function(){
		var _project = WangAho(this.id);
		$('.action-repeat').unbind("click").click(function(){
			WangAho("index").scroll_constant(function(){
				_project.main();
			});
		});
	}
	
	
	
	
});