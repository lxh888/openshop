av({
	id: 'page-application-cache',//工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include: ["src/common/content.js"],//获取js文件
	extend: ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
	'export': {
		template: "src/page/application/cache.html",
	},
	'import': function (e) {
		this.template(e.template);//绑定模版
	},

	main: function () {
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
		eventClearAll: function () {
			var _this=this;
			layer.msg('你确定要清理全部缓存么？', {
				time: 0 //不自动关闭
				, btn: ['确定', '取消']
				, yes: function (index) {
					layer.close(index);
					//提交数据
					_this.submit({
						method: "submit",
						request: ["APPLICATIONADMINCACHECLEAR"],
						success: function (data) {
							//刷新页面
							av().run();
						}
					});
				}
			});

		},
		eventUpdateRelationalChainCaching:function(){
			var _this=this;
			layer.msg('你确定要更新关系链缓存么？', {
				time: 0 //不自动关闭
				, btn: ['确定', '取消']
				, yes: function (index) {
					layer.close(index);
					//提交数据
					_this.submit({
						method: "submit",
						request: ["USERADMINRECOMMENDUPDATERECOMMEND"],
						success: function (data) {
							//刷新页面
							av().run();
						}
					});
				}
			});
		},
		//重置用户身份及推荐关系链
		//USERADMINRESETUSER
		eventSubmit:function(){
			var _this=this;
			var userId=$.trim($('[name="user_id"]').val());
			try {
				if(userId=='') throw '请填写要重置的用户id'
			}
			catch (err) {
				//console.log(err);
				layer.msg(err, { icon: 5, time: 2000 });
				return
			}
			layer.msg('你确定要重置用户身份及推荐关系链吗？', {
				time: 0 //不自动关闭
				, btn: ['确定', '取消']
				, yes: function (index) {
					layer.close(index);
					//提交数据
					_this.submit({
						method: "submit",
						request: ["USERADMINRESETUSER", [{ user_id: userId }]],
						error: function () {
						},
						success: function () {
							//刷新页面
							av().render("refresh").run();
						}
					});
				}
			});
			
		}
	}
});
