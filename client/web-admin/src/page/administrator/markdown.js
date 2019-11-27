av({
	
	id: 'page-administrator-markdown',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/administrator/markdown.html"},
    'import' : function(e){
        this.template(e.template);
    },
    main: function(){
		this.data.request.bookList = ['ADMINISTRATORMARKDOWNBOOK'];
	},
	event:{
		
		error: function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},
		
	},
	data:{
		request: {},
		
		
		
		
	}
	
	
	
});
