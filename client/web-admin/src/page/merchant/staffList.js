av({
  id:'page-merchant-staffList',//工程ID
  include : ["src/common/content.js"],//获取js文件
  extend : ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
  'export' : {
  template: "src/page/merchant/staffList.html",
  },//引入模版，可以同时引入多个
  'import' : function(e){
    // this.data.templateTest = e.template2;//绑定模版
      this.template(e.template);//绑定模版
    },
  
  main: function(){
    
  var _this = this;
  var config = { search:{} };
  
  //搜索
  this.data.routerAnchorQuery('search', function(data){
    data = av.decodeURL(data);
    config.search = JSON.parse(data);
  });
  
  //搜索 审核状态
  this.data.routerAnchorQuery('state', function(data){
    _this.data.state = data;
    config.search.state = data;//状态
  }, function(){
    _this.data.state = undefined;
  });
  
  //分页
  this.data.routerAnchorQuery('page', function(data){
    config.page = data;
  });
  
  
  this.data.request.list= ['MERCHANTADMINCASHIERLIST', [config]];
  this.data.request.application_config = ["APPLICATIONCONFIG"];
  },
  event: {
      error : function(error){
    console.log('error 跳转', error);
    return av.router(av.router().url, '#/').request();
  },

  },
  //数据对象
  data:{
    request: {},
    search:[
      'merchant_cashier_name',
      'user_id',
      'user_nickname',
      'user_phone'
    ],
    list:{
      data : [],
    },
    state:null,
    //删除员工
    eventDeleteStaff(merchant_cashier_id){
      var _this = this;

			var form_input = {};
      form_input.merchant_cashier_id = merchant_cashier_id;

			layer.msg('删除员工不可撤销，你确定要删除员工吗？', {
				time: 0, //不自动关闭
				btn: ['确定', '取消'],
				end: function () {
				},
				yes: function (index) {
					layer.close(index);

					//提交数据
					_this.submit({
						method: "submit",
						request: ["MERCHANTADMINCASHIERREMOVE", [form_input]],
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
    //审核通过
    eventPassApply(merchant_cashier_id){
      var _this = this;

			var form_input = {};
      form_input.merchant_cashier_id = merchant_cashier_id;
      form_input.merchant_cashier_state=1;

			layer.msg('你确定要通过该用户的员工认证申请吗？', {
				time: 0, //不自动关闭
				btn: ['确定', '取消'],
				end: function () {
				},
				yes: function (index) {
					layer.close(index);

					//提交数据
					_this.submit({
						method: "submit",
						request: ["MERCHANTADMINCASHIERSTATE", [form_input]],
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
    //审核不通过
    eventNotPassApply(merchant_cashier_id){
      var _this = this;

			var form_input = {};
      form_input.merchant_cashier_id = merchant_cashier_id;
      form_input.merchant_cashier_state=0;

			layer.msg('你确定要拒绝该用户的员工认证申请吗？', {
				time: 0, //不自动关闭
				btn: ['确定', '取消'],
				end: function () {
				},
				yes: function (index) {
					layer.close(index);

					//提交数据
					_this.submit({
						method: "submit",
						request: ["MERCHANTADMINCASHIERSTATE", [form_input]],
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
  }
});