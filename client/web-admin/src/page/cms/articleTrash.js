av({
	id: 'page-cms-articleTrash',//工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include: ["src/common/content.js", "src/page/cms/articleTrash/info.js"],//获取js文件
	extend: ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
	'export': {
		template: "src/page/cms/articleTrash.html",
	},
	'import': function (e) {
		this.template(e.template);//绑定模版
	},

	main: function () {
		var _this = this;
		var config = {};

		//排序
		this.data.routerAnchorQuery('sort', function (data) {
			config.sort = [data];
		});

		//分页
		this.data.routerAnchorQuery('page', function (data) {
			config.page = data;
		});



		//回收文章列表
		this.data.request.list = ['CMSADMINARTICLETRASHLIST', [config]];

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
		},
		eventRestore: function () {
			var _this=this;
			var ids = this.checkboxData('data-id');
			if (!ids || !ids.length) {
				return false;
			}
			var request_array = [];
			//request_array.push(["USERADMINEDITCHECK"]); //第一个是判断是否有编辑权限
			for (var i = 0; i < ids.length; i++) {
				request_array.push(["CMSADMINARTICLETRASHRESTORE", [{
					cms_article_id: ids[i],
				}]]);
			}
			console.log(request_array);
			layer.msg('你确定要还原么？(' + ids.length + '条数据)', {
				time: 0 //不自动关闭
				, btn: ['确定', '取消']
				, yes: function (index) {
					layer.close(index);
					//提交数据
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
		eventShowInfo: function (ele, e, cms_article_id) {
			//循环属性列表获取当前编辑属性的数据
			var user = null;
			var isBreak = false; //当找到当前编辑对象立即跳出循环
			if (this.list.data && typeof this.list.data == 'object') {
				for (var i in this.list.data) {
					if (this.list.data[i].cms_article_id == cms_article_id) {
						cms_article = this.list.data[i];
						isBreak = true;
					}
					if (isBreak) break;
				}
			}
			if (!cms_article) {
				layer.msg('用户数据异常', {
					icon: 5,
					time: 2000
				});
				return false;
			}
			console.log('2312', cms_article);
			var AVproject = av('page-cms-articleTrash::info');
			//拷贝 将数据复制到新模版
			AVproject.clone({
				cms_article: cms_article, //当前数据
			});

			layer.closeAll();
			var selector = 'articleTrashInfo' + Date.parse(new Date());
			//页面层
			var layerid = layer.open({
				title: "<span class=\"glyphicon glyphicon-edit\"></span> 查看商品详细",
				type: 1,
				area: [900 + "px", 250 + 'px'], //宽高
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
