av({

	id: 'page-admin-adminUserEdit',
	include: ["src/common/content.js"],
	extend: ["common-content"],
	'export': { template: "src/page/admin/adminUserEdit.html" },
	'import': function (e) {
		this.template(e.template);
	},

	main: function () {
		this.data.admin_user_id = (function () { try { return av.router().anchor.query.id; } catch (e) { return ''; } }());

		var config = {};
		config.user_id = this.data.admin_user_id;
		// if( !this.data.agent_user_id ){
		// 	return av.router(av.router().url, '#/').request();
		// }
		this.data.request.data = ['ADMINUSERGET', [config]];
		this.data.request.admin_option = ["ADMINOPTION", [{ sort: ["sort_asc", "update_time_asc"] }]];
		
	},

	event: {
		error: function (error) {
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/agent-userList/').request();
		},
		loadEnd: function(){
			//加载完成，说明有数据了，将值赋
			if( this.data.data ){
				this.data.selectAdminOption=this.data.data.admin_id;
			}
		},
	},

	data: {
		request: {},
		data: null,
		admin_user_id: '',
		selectAdminOption: '',

		eventSelectAdminOptionChange:function(ele,e){
			this.selectAdminOption=$(ele).val();
			console.log(this.selectAdminOption);
		},

		//按回车键时提交
		keyupFunction: function () {
			this.eventSubmit();
		},

		submitLock: false,
		eventSubmit: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}

			var form_input = {};
			if(_this.applicationCheckEmshop() && _this.selectAdminOption=='area_agent')
			{
				form_input={admin_user_json:{}}
			}
			form_input.user_id = _this.admin_user_id;
			form_input.admin_id = $.trim($('[name="admin_id"]').val());
			form_input.admin_user_info = $.trim($('[name="admin_user_info"]').val());
			form_input.admin_user_sort = $.trim($('[name="admin_user_sort"]').val());
			form_input.admin_user_state = $('[name="admin_user_state"]').is(':checked')? 0 : 1;
			
			if(_this.applicationCheckEmshop() && _this.selectAdminOption=='area_agent')
			{
				form_input.admin_user_json.show_region_order = $('[name="show_region_order"]').is(':checked')? 1 : 0;
			}
			


			try {
				if (form_input.user_id == '') throw "用户ID异常";
			}
			catch (err) {
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}

			//console.log(form_input);
			//提交数据
			this.submit({
				method: "submit",
				request: ["ADMINUSEREDIT", [form_input]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});

		}


	}




});
