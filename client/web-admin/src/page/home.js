av({
	
	id: 'page-home',
    include : ["src/common/content.js"],
    extend : ["common-content"],
    'export' : {template : "src/page/home.html"},
    'import' : function(e){
        this.template(e.template);
    },
	data:{
		request: {},
	}
	
	
});
