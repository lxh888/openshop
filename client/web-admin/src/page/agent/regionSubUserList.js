av({

	id: 'page-agent-regionSubUserList',
	include: ["src/common/content.js"],
	extend: ["common-content"],
	'export': { template: "src/page/agent/regionSubUserList.html" },
	'import': function (e) {
		this.template(e.template);
	},
	main: function () {
		this.data.request.list = ['USERRECOMMENDSELFAREALOWERLEVEL'];
	},
	event: {
		error: function (error) {
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/agent-userList/').request();
		},
	},
	data: {
		request: {},
		list: {
			data: [],
		},
		countMembeNum: function (arr) {
			var i;
			for (i = 0; i < arr.length; i++) {
				if (arr[i].admin_id) {
				}

			}
			return i
		},
		goTop: function (ele,e) {
			$("#table").scrollTop(0);
		},
		goBottom: function (ele,e) {
			//console.log($("#table").context.scrollingElement.scrollHeight);
			//console.log($("#table").context.scrollingElement.clientHeight);
			$("#table").scrollTop($("#table")[0].scrollHeight-$("#table")[0].clientHeight)
		},
	}
});
