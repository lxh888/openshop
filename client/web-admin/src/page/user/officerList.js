av({

	id: 'page-user-officerList',
	include: ["src/common/content.js"],
	extend: ["common-content"],
	'export': {
		template: "src/page/user/officerList.html"
	},
	'import': function(e) {
		this.template(e.template);
	},
	main: function() {
		var _this = this;
		var config = {
			search: {}
		};

		//搜索
		this.data.routerAnchorQuery('search', function(data) {
			data = av.decodeURL(data);
			config.search = JSON.parse(data);
		});

		//排序
		this.data.routerAnchorQuery('sort', function(data) {
			config.sort = [data];
		});

		//分页
		this.data.routerAnchorQuery('page', function(data) {
			config.page = data;
		});

		//状态
		this.data.routerAnchorQuery('state', function(data) {
			_this.data.state = data;
			config.search.state = data; //状态
		}, function() {
			_this.data.state = undefined;
		});

		this.data.request.list = ['USERADMINOFFICERLIST', [config]];
//		this.data.request.agentRegionOption = ['AGENTADMINREGIONOPTION'];
	},
	event: {

		error: function(error) {
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},

	},
	data: {
		request: {},
		state: undefined,
		list: {
			data: [],
		},
		search: [
			'user_id',
			'user_nickname',
			'user_phone',
			'agent_region_id',
			'agent_region_province',
			'agent_region_city',
			'agent_region_district',
		],

		//删除
		eventRemove: function(ele) {
			var ids = this.checkboxData('data-id');
			var _this = this;
			this.actionRemoveIds(ids, function() {

				var request_array = [];
				for(var i in ids) {
					request_array.push(["USERADMINPFFICERREMOVE", [{
						id: ids[i]
					}]]);
				}

				//提交数据
				_this.submit({

					method: "list",
					request: request_array,
					success: function(bool) {
						if(bool) {
							//刷新页面
							av().compiler("reload").render().run();
						}
					}

				});

			});
		},
		
		/**
		 * 更新状态-审核失败
		 * 
		 * @param {Object} bool
		 */
		eventStateDefeated: function(ele, e) {
			var ids = this.checkboxData('data-id');
			var _this = this;


				var request_array = [];
				for(var i in ids) {
					request_array.push(["USERADMINPFFICEREDIT", [{
						id: ids[i],
						state: 0,
					}]]);
				}

				//提交数据
				_this.submit({

					method: "list",
					request: request_array,
					success: function(bool) {
						if(bool) {
							//刷新页面
							av().compiler("reload").render().run();
						}
					}

				});

		

		},

		/**
		 * 更新状态-审核成功
		 * 
		 * @param {Object} void
		 */
		eventStateSucceed: function(ele, e) {
			var ids = this.checkboxData('data-id');
			var _this = this;

			this.actionStateIds(ids, function() {

				var request_array = [];
				for(var i in ids) {
					request_array.push(["USERADMINPFFICEREDIT", [{
						id: ids[i],
						state: 1
					}]]);
				}

				//提交数据
				_this.submit({

					method: "list",
					request: request_array,
					success: function(bool) {
						if(bool) {
							//刷新页面
							av().compiler("reload").render().run();
						}
					}

				});

			});
		},


	}

});