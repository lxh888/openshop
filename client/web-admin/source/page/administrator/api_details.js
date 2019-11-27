WangAho({
	
	
	id:"administrator/api_details",
	
	
	main : function(){
		var _project = WangAho(this.id);
		var config = {};
		var _http = http();
		config.api_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if( !config.api_id ){
			layer.msg("接口ID无效", {icon: 5, time: 2000});
			return false;
		}
		
		WangAho("index").data({
			request : {
				get:["ADMINISTRATORADMINAPIGET",[config]]
				},
			success : function(data){
				
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.get.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.get.error, {icon: 5, time: 2000});
				}
				
				template( WangAho().template("page/administrator/api_details.html", "#content"), function(fn){
					WangAho().view( fn(data) );
				});
				_project.submit();
				}
		});
		
	},
	
	
	
	submit : function(){
		var viewer_css = include('include/library/json-viewer/css/jquery.json-viewer.css');
		var viewer_js = include('include/library/json-viewer/js/jquery.json-viewer.js');
		$('[name="json-viewer-javascript"]').append(viewer_css.element);
		$('[name="json-viewer-javascript"]').append(viewer_js.element);
		viewer_js.ready(function(){
			var options = {
		    	collapsed: false,
		    	withQuotes: true
		    };
		    var api_request_args = $('#json-api_request_args').html();
		    api_request_args = (function(){try{ return jQuery.parseJSON(api_request_args);}catch(e){return false;}}());
		    if(typeof api_request_args == 'object'){
		    	$('#json-api_request_args').jsonViewer(api_request_args, options);
		    }
		    var api_response_args = $('#json-api_response_args').html();
		    api_response_args = (function(){try{ return jQuery.parseJSON(api_response_args);}catch(e){return false;}}());
		    if(typeof api_response_args == 'object'){
		    	$('#json-api_response_args').jsonViewer(api_response_args, options);
		    }
			
		});
	},
	
	
	
	
	
	
	
	
});