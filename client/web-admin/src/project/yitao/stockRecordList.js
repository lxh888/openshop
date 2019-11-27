av({
	id: 'project-yitao-stockRecordList',//工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include: ["src/common/content.js"],//获取js文件
	extend: ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
	'export': {
		template: "src/project/yitao/stockRecordList.html",
	},//引入模版，可以同时引入多个
	'import': function (e) {
		// this.data.templateTest = e.template2;//绑定模版
		this.template(e.template);//绑定模版
	},

	main: function () {

		var _this = this;
		this.data.data='';
		this.data.shop_goods_id = (function(){try{ return av.router().anchor.query.id;}catch(e){return '';}}());
		this.data.user_id = (function(){try{ return av.router().anchor.query.user_id;}catch(e){return '';}}());
		
		var config = { search: {} };
		if(this.data.shop_goods_id){
			config.goods_id=this.data.shop_goods_id;
		}
		else if(this.data.user_id){
			config.user_id=this.data.user_id;
		}
		
		config.search.goods_id=this.data.shop_goods_id;
		//搜索
		this.data.routerAnchorQuery('search', function (data) {
			data = av.decodeURL(data);
			config.search = JSON.parse(data);
		});

		//分页
		this.data.routerAnchorQuery('page', function (data) {
			config.page = data;
		});

		if(this.data.shop_goods_id){
			this.data.request.data = ['SHOPADMINPGOODSGET', [{shop_goods_id:this.data.shop_goods_id}]];
		}
		else{
			console.log('day',	this.data.data);
			this.data.data=null;
		}
			this.data.request.list = ['SHOPADMINGOODSSTOCKLOGLIST', [config]];

	
	},
	event: {
		loadEnd:function(){
			if(this.data.user_id){
				this.data.data=null;
			}
		},
		error: function (error) {
			console.log('error 跳转', error);
			//return av.router(av.router().url, '#/').request();
		},

	},
	//数据对象
	data: {
		request: {},
		search: [
		],
		list: {
			data: [],
		},
		data:''
		
	}
});