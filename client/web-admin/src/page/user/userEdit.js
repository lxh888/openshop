av({

	id: "page-user-userEdit",
	include: ["src/common/content.js"],
	extend: ["common-content"],
	'export': { template: "src/page/user/userEdit.html" },
	'import': function (e) {
		this.template(e.template);
	},
	main: function () {
		//从路由里获取用户id
		var userid = (function () { try { return av.router().anchor.query.id; } catch (e) { return ''; } }());
		console.log(userid);
		if (!userid) {
			return av.router(av.router().url, '#/user-userList/').request();
		}
		//请求全部配置信息
		this.data.request.userinfo = ["USERADMINGET", [{ user_id: userid }]];
		// {application_config:["APPLICATIONCONFIG"],get:["USERADMINGET", [{user_id:user_id}]]};
		this.data.request.user_phone_list = ["USERADMINPHONELIST", [{ user_id: userid }]];//手机
		this.data.request.user_parent_get = ["USERADMINPARENTGET", [{ user_id: userid }]];//推荐人


	},
	event: {
		error: function (error) {
			console.log('error 跳转', error);
			//return av.router(av.router().url, '#/user-userList').request();
		},
		loadEnd: function () {

		}
	},
	data: {

		request: {},
		requestHideError: ["user_parent_get"],
		data: null,
		appCreditsConfig: {},
		//选中的数据的id
		checkedIndex: 'user',
		//按回车键时提交
		keyupFunction: function () {
			if (this.checkedIndex) {
				this.eventSubmit(null, null, this.checkedIndex);
			}

		},

		userLogoUploadFileMimeLimit: ['.jpg', '.png', '.gif', '.jpeg'],//判断图片合法性
		userLogoUploadFile: { file: null, url: '', uploadPercent: '' },//上传的用户头像资源
		//当选择类型时 参数1节点对象，参数2事件对象，参数3自定义 此处为分类对象的id
		eventSelectUserInfonType: function (ele, e, tabbarid) {
			this.checkedIndex = tabbarid;
		},
		submitLock: false,
		eventUpLoad: function () {
			av.triggerClick($('[name="userLogo-files"]')[0]);
		},

		eventFileChange: function (ele, e) {
			var files = ele.files;
			if (files[0]) {
				//判断图片类型是否合法
				//获取前缀
				var suffix = files[0].name.replace(files[0].name.replace(/\.[\w]{1,}$/, ""), "");
				var legal = false;
				for (var l in this.userLogoUploadFileMimeLimit) {
					if (this.userLogoUploadFileMimeLimit[l] == suffix) {
						legal = true;
						break;
					}
				}

				if( !legal ){
				 layer.msg("“"+files[0].name+"” 图片格式不合法，只能上传'.jpg','.png','.gif','.jpeg'图片", {icon: 5, time: 3000});
				 return false;
				}
				this.userLogoUploadFile.file = files[0];
				//获取图片地址
				this.userLogoUploadFile.url = av.getfileURL(files[0])
			}

		
		},
		eventUpLoadSubmit: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}
			var form_input = {};
			form_input.user_id = _this.userinfo.user_id;
			//如果没有上传对象
			if (!this.userLogoUploadFile.file) {
				layer.msg("未选择任何图片！", { icon: 5, time: 3000 });
				return _this.submitLock = false;
			}

			var requestAPIObject = new requestAPI();
			requestAPIObject.submit({
				request: {
					s: ["USERADMINLOGOQINIUUPLOAD", [form_input]],
				},
				data: { file: this.userLogoUploadFile.file },
				timeout: 0,//不限制时间
				progress: function (loaded, total, percent) {
					console.log(loaded, total, percent);
					_this.userLogoUploadFile.uploadPercent = percent + "%";
					if (percent == 100) {
						_this.userLogoUploadFile.upload = true;
						_this.userLogoUploadFile.file = null;
						_this.userLogoUploadFile.uploadPercent = '';
						layer.msg('上传成功', { icon: 1, time: 1000 });

					}
				},

				callback: function (r) {
					layer.closeAll('loading');//关闭加载
					try {
						if (!r) {
							throw "未知错误";
						}
						if ((function () { try { return r.data.s.errno; } catch (e) { return false; } }())) {
							throw r.data.s.error;
						}
					} catch (e) {
						layer.msg(e, { icon: 5, time: 3000 });
						_this.submitLock = false;
					}

					_this.userLogoUploadFile.upload = true;
					_this.submitLock = false;
					av().compiler("reload").render().run();
				}
			});
		},


		//回车提交
		eventSubmit: function (ele, e, checkedIndex) {
			if (this['submit_' + checkedIndex]) {
				this['submit_' + checkedIndex]();
			}
		},
		//修改用户信息
		submit_user: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}
			var form_input = {};
			form_input.user_id = _this.userinfo.user_id;
			form_input.user_nickname = $.trim($('[name="user_nickname"]').val());
			form_input.user_compellation = $.trim($('[name="user_compellation"]').val());
			form_input.user_state = $('[name="user_state"]').is(':checked') ? 0 : 1;
			form_input.user_sex = $.trim($('[name="user_sex"]:checked').val());
			//console.log(form_input);
			try {
				if (form_input.user_id == '') throw "用户ID不能为空！";
			}
			catch (err) {
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			//提交数据
			this.submit({
				method: "submit",
				request: ["USERADMINEDIT", [form_input]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().compiler("reload").render().run();
				}
			});
		},
		//重置密码
		submit_user_password: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}
			var form_input = {};
			form_input.user_id = _this.userinfo.user_id;
			form_input.user_password = $.trim($('[name="user_password"]').val());
			form_input.user_confirm_password = $.trim($('[name="user_confirm_password"]').val());
			//console.log(form_input);
			try {
				if (form_input.user_id == '') throw "用户ID不能为空！";
				if (form_input.user_password == '') throw "登录密码不能为空！";
				if (form_input.user_confirm_password == '') throw "确认密码不能为空！";
				if (form_input.user_confirm_password != form_input.user_password) throw "两次输入的密码不同！";
			}
			catch (err) {
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			//提交数据
			this.submit({
				method: "submit",
				request: ["USERADMINPASSWORDEDIT", [form_input]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().compiler("reload").render().run();
				}
			});
		},
		//添加手机号
		submit_user_phone: function (ele, e) {
			var _this = this;

			var form_input = {};
			form_input.user_id = _this.userinfo.user_id;
			var form = $('.user_phone_add_form');
			form_input.user_phone_id = $.trim(form.find('[name="user_phone_id"]').val());
			form_input.user_phone_sort = $.trim(form.find('[name="user_phone_sort"]').val());
			form_input.user_phone_type = form.find('[name="user_phone_type"]').is(':checked') ? 1 : 0;
			try {
				if (form_input.user_id == '') throw "用户ID不能为空";
				if (form_input.user_phone_id == '') throw "手机号码不能为空";
				if (form_input.user_phone_sort == '') {
					delete form_input.user_phone_sort;
				}
			}
			catch (err) {
				layer.msg(err, { icon: 5, time: 2000 });
			}

			//提交数据
			_this.submit({
				method: "submit",
				request: ["USERADMINPHONEADD", [form_input]],
				error: function () {
				},
				success: function () {
					//刷新页面
					av().compiler("reload").render().run();
				}
			});

		},
		//编辑手机号
		submit_edit_phone: function (ele, e, iphone_id) {
			var _this = this;

			var form_input = {};
			form_input.user_id = _this.userinfo.user_id;
			form_input.user_phone_id = iphone_id;
			var form = $('.user_phone_form' + form_input.user_phone_id);
			form_input.user_phone_sort = $.trim(form.find('[name="user_phone_sort"]').val());
			form_input.user_phone_type = form.find('[name="user_phone_type"]').is(':checked') ? 1 : 0;
			try {
				if (form_input.user_id == '') throw "用户ID获取失败";
				if (form_input.user_phone_id == '') throw "手机号码获取失败";
			}
			catch (err) {
				layer.msg(err, { icon: 5, time: 2000 });
			}

			//提交数据
			_this.submit({
				method: "submit",
				request: ["USERADMINPHONEEDIT", [form_input]],
				error: function () {
				},
				success: function () {
					//刷新页面
					av().render("refresh").run();
				}
			});


		},
		//删除手机号
		submit_remoive_phone: function (ele, e, iphone_id) {
			var _this = this;

			var form_input = {};
			form_input.user_id = _this.userinfo.user_id;
			form_input.user_phone_id = iphone_id;

			try {
				if (form_input.user_id == '') throw "用户ID获取失败";
				if (form_input.user_phone_id == '') throw "手机号码获取失败";
			}
			catch (err) {
				layer.msg(err, { icon: 5, time: 2000 });
			}
			layer.msg('你确定要删除 “' + form_input.user_phone_id + '” 手机号码么？', {
				time: 0, //不自动关闭
				btn: ['确定', '取消'],
				end: function () {
				},
				yes: function (index) {
					layer.close(index);

					//提交数据
					_this.submit({
						method: "submit",
						request: ["USERADMINPHONEREMOVE", [form_input]],
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
		//更新推荐人
		submit_user_parent: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}

			var form_input = {};
			form_input.user_id = _this.userinfo.user_id;
			form_input.user_parent = $.trim($('[name="user_parent"]').val());
			
			try {
				if (form_input.user_id == '') throw "用户ID获取失败";
				if (form_input.user_parent == '') throw "需要输入推荐人ID或者推荐人登录手机号";
			}
			catch (err) {
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			//提交数据
			this.submit({
				method: "submit",
				request: ["USERADMINEDIT", [form_input]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().run("refresh");
				}
			});
		},
		//删除推荐人
		submit_remoive_tuijian: function () {
			var _this = this;
			var form_input = {};
			form_input.user_id = _this.userinfo.user_id;
			form_input.user_parent_id = "";

			try {
				if (form_input.user_id == '') throw "用户ID获取失败";
			}
			catch (err) {
				layer.msg(err, { icon: 5, time: 2000 });
			}
			layer.msg('你确定要删除该用户的推荐人吗？', {
				time: 0, //不自动关闭
				btn: ['确定', '取消'],
				end: function () {
				},
				yes: function (index) {
					layer.close(index);

					//提交数据
					_this.submit({
						method: "submit",
						request: ["USERADMINEDIT", [form_input]],
						error: function () {
						},
						success: function () {
							//刷新页面
							av().run("refresh");
						}
					});
				}
			});

		},
		//补发优惠券
		submit_supplyAgain: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}

			var form_input = {};
			form_input.user_id = _this.userinfo.user_id;
			
			try {
				if (form_input.user_id == '') throw "用户ID获取失败";
			}
			catch (err) {
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			//提交数据
			this.submit({
				method: "submit",
				request: ["USERADMINREPLACEMENTCOUPON", [form_input]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().run("refresh");
				}
			});
		},

	}
})

