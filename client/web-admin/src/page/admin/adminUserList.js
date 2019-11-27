av({
	id: 'page-admin-adminUserList',//工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include: ["src/common/content.js"],//获取js文件
	extend: ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
	'export': {
		template: "src/page/admin/adminUserList.html",
	},
	'import': function (e) {
		this.template(e.template);//绑定模版
	},

	main: function () {
		var _this = this;
		var config = { search: {} };

		//搜索
		this.data.routerAnchorQuery('search', function (data) {
			data = av.decodeURL(data);
			config.search = JSON.parse(data);
		});

		//排序
		this.data.routerAnchorQuery('sort', function (data) {
			config.sort = [data];
		});

		//分页
		this.data.routerAnchorQuery('page', function (data) {
			config.page = data;
		});

		//文章状态
		this.data.routerAnchorQuery('state', function (data) {
			_this.data.state = data;
			config.search.state = data;//状态
		}, function () {
			_this.data.state = undefined;
		});

		//回收文章列表
		this.data.request.list = ['ADMINUSERLIST', [config]];
		this.data.request.application_config = ["APPLICATIONCONFIG"];
		if (config.search.admin_id) {
			this.data.request.admin_get = ["ADMINGET", [{ admin_id: config.search.admin_id }]];
		}
		//console.log(typeof this.data.request.admin_get);
	},
	event: {
		error: function (error) {
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},

	},
	//数据对象
	data: {
		request: {},
		search: [
			'user_id',
			'user_phone',
			'user_nickname',
			'admin_id',
			'admin_name',
		],
		list: {
			data: [],
		},


		//删除
		eventRemove: function () {
			var _this = this;
			var ids = this.checkboxData('data-id');
			if (!ids || !ids.length) {
				return false;
			}
			var request_array = [];
			for (var i in ids) {
				request_array.push(["ADMINUSERREMOVE", [{ user_id: ids[i]}]]);
			}
			layer.msg('你确定要删除么？(' + ids.length + '条数据)', {
				time: 0 //不自动关闭
				, btn: ['确定', '取消'], yes: function (index) {
					layer.close(index);
					_this.submit({
						method: "list",
						request: request_array,
						success: function (data) {
							if (data) {
								//刷新页面
								av().compiler("reload").render().run();
							}

						}
					});
				}
			});

		},



		//查看简介
		eventShowInfo: function (infotext) {
			layer.open({
				title: '简介',
				content: infotext
			});  
		}



	}
});
