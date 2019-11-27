av({
	id:'page-user-userList::info',//工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include : ["src/common/content.js"],//获取js文件
	extend : ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
	'export' : {
	template: "src/page/user/userList/info.html",},
	'import' : function(e){
			this.template(e.template);//绑定模版
		},
	
	main: function(){
	var _this = this;
	},
	event: {
			error : function(error){
		console.log('error 跳转', error);
		return av.router(av.router().url, '#/').request();
	},

	},
	//数据对象
	data:{
		user:null,
		qiniu_domain:''
	}
});
