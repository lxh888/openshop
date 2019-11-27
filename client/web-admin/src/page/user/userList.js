av({
	id: 'page-user-userList',//工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include: ["src/common/content.js", "src/page/user/userList/info.js"],//获取js文件
	extend: ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
	'export': {
		template: "src/page/user/userList.html",
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

		//全部商品状态
		this.data.routerAnchorQuery('shop_goods_state', function (data) {
			_this.data.shop_goods_state = data;
			config.search.shop_goods_state = data;//状态
		}, function () {
			_this.data.shop_goods_state = undefined;
		});
		//全部限时状态
		this.data.routerAnchorQuery('state', function (data) {
			_this.data.state = data;
			config.search.state = data;//状态
		}, function () {
			_this.data.state = undefined;
		});


		this.data.request.list = ['USERADMINLIST', [config]];
		if (config.search.user_parent_id) {
			this.data.request.user_parent = ["USERADMINGET", [{ user_id: config.search.user_parent_id }]];
		}else{
			this.data.request.user_parent = undefined;
			this.data.user_parent = undefined;
		}
		this.data.request.application_config = ["APPLICATIONCONFIG"];
		//检查应用是否是购票小程序
		if(!this.data.applicationCheckZhongrunhuizhan()){
			this.data.search=[
				'user_id',
				'user_phone',
				'user_nickname',
				'user_parent_id',
				'user_parent_phone',
				'user_parent_nickname'
			]
		}
		else{
			this.data.search=[
				'user_id',
				'user_phone',
				'user_nickname'
			]
		}
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
	
		list: {
			data: [],
			applicationConfig: {},
		},
		eventUnban: function () {
			var ids = this.checkboxData('data-id');
			if (!ids || !ids.length) {
				return false;
			}
			var request_array = [];
			request_array.push(["USERADMINEDITCHECK"]); //第一个是判断是否有编辑权限
			for (var i = 0; i < ids.length; i++) {
				request_array.push(["USERADMINEDIT", [{
					user_id: ids[i],
					user_state: 1
				}]]);
			}
			console.log(request_array);
			//提交数据
			this.submit({
				method: "edit",
				request: request_array,
				success: function (data) {
					//刷新页面
					av().run();
				}
			});
		},
		eventBan: function () {
			var ids = this.checkboxData('data-id');
			if (!ids || !ids.length) {
				return false;
			}
			var request_array = [];
			request_array.push(["USERADMINEDITCHECK"]); //第一个是判断是否有编辑权限
			for (var i = 0; i < ids.length; i++) {
				request_array.push(["USERADMINEDIT", [{
					user_id: ids[i],
					user_state: 0
				}]]);
			}
			console.log(request_array);
			//提交数据
			this.submit({
				method: "edit",
				request: request_array,
				success: function (data) {
					//刷新页面
					av().run();
				}
			});
		},
		//设置该用户所在区域的区域代理为邀请人
		eventSetInvite: function (ele, e, userid) {
			var _this = this;

			var form_input = {};
			form_input.user_id = _this.user.user_id;

			layer.msg('你确定要设置该用户所在区域的区域代理为邀请人？', {
				time: 0, //不自动关闭
				btn: ['确定', '取消'],
				end: function () {
				},
				yes: function (index) {
					layer.close(index);

					//提交数据
					_this.submit({
						method: "submit",
						request: ["USERADMINADDPARENTUSERID", [form_input]],
						error: function () {
						},
						success: function () {
							//刷新页面
							av().render("refresh").run();
						}
					});
				}
			});
		},
		
		//查看其他信息
		eventShowInfo: function (ele, e, user_id) {
			//循环属性列表获取当前编辑属性的数据
			var user = null;
			var isBreak = false; //当找到当前编辑对象立即跳出循环
			if (this.list.data && typeof this.list.data == 'object') {
				for (var i in this.list.data) {
					if (this.list.data[i].user_id == user_id) {
						user = this.list.data[i];
						isBreak = true;
					}
					if (isBreak) break;
				}
			}
			if (!user) {
				layer.msg('用户数据异常', {
					icon: 5,
					time: 2000
				});
				return false;
			}

			var AVproject = av('page-user-userList::info');
			//拷贝 将数据复制到新模版
			AVproject.clone({
				user: user, //当前user的数据
				qiniu_domain: this.applicationConfig.qiniu_domain,
			});

			layer.closeAll();
			var selector = 'eventSpuEdit' + Date.parse(new Date());
			//页面层
			var layerid = layer.open({
				title: "<span class=\"glyphicon glyphicon-edit\"></span> 其他信息",
				type: 1,
				area: [500 + "px", 250 + 'px'], //宽高
				content: '<div id="' + selector + '"></div>',
				end: function () {
				},
				success: function () {
					//渲染这个插件
					AVproject.compiler("reload").selector('#' + selector).render("refresh");
				}
			});
		}

	}
});
