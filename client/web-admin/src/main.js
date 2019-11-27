var avStart = av.framework({
    //includeSelector: 'xxxx',
    version: '1.1.4',//版本号
    includeTime: true,//引入文件是否加上时间戳参数
	exportTime: true,//导入文件是否加上时间戳参数
	debug: false,//调试模式
	
    //页面
    page: {
        '':                 			['page-home', 'src/page/home.js'],
        '/':                			["page-home", 'src/page/home.js'],
        '/userLogin':       			['page-userLogin', 'src/page/userLogin.js'],
        '/401Unauthorized':				['page-401Unauthorized', 'src/page/401Unauthorized.js'],

        //应用管理
        '/admin-adminUserEdit':     		['page-admin-adminUserEdit','src/page/admin/adminUserEdit.js'],
        '/admin-adminUserList':     		['page-admin-adminUserList','src/page/admin/adminUserList.js'],

        

        //接口添加
        '/administrator-apiAdd':		['page-administrator-apiAdd', 'src/page/administrator/apiAdd.js'],
        '/administrator-apiEdit':		['page-administrator-apiEdit', 'src/page/administrator/apiEdit.js'],
        '/administrator-apiList':		['page-administrator-apiList', 'src/page/administrator/apiList.js'],
        '/administrator-markdown':		['page-administrator-markdown', 'src/page/administrator/markdown.js'],
        
        //用户系统
        '/user-searchVerifyCode':		['page-user-searchVerifyCode', 'src/page/user/searchVerifyCode.js'],
        '/user-userAdd':				['page-user-userAdd', 'src/page/user/userAdd.js'],
        '/user-userEdit':				['page-user-userEdit', 'src/page/user/userEdit.js'],
        '/user-accountExcel':			['page-user-accountExcel', 'src/page/user/accountExcel.js'],
        '/user-withdrawList':			['page-user-withdrawList', 'src/page/user/withdrawList.js'],
        '/user-userExcel':              ['page-user-userExcel','src/page/user/userExcel.js'],
        '/user-userList':               ['page-user-userList','src/page/user/userList.js'],
        '/user-officerList':            ['page-user-officerList','src/page/user/officerList.js'],
        
        //购物商城
        '/shop-groupGoodsList':				['page-shop-groupGoodsList', 'src/page/shop/groupGoodsList.js'],
        '/shop-groupGoodsAdd':				['page-shop-groupGoodsAdd', 'src/page/shop/groupGoodsAdd.js'],
        '/shop-groupGoodsEdit':				['page-shop-groupGoodsEdit', 'src/page/shop/groupGoodsEdit.js'],
        '/shop-goodsRegionAdd':             ['page-shop-goodsRegionAdd', 'src/page/shop/goodsRegionAdd.js'],
        '/shop-goodsRegionList':            ['page-shop-goodsRegionList', 'src/page/shop/goodsRegionList.js'],
        '/shop-goodsAttr':               	['page-shop-goodsAttr', 'src/page/shop/goodsAttr.js'],
		'/shop-goodsAdd':               	['page-shop-goodsAdd', 'src/page/shop/goodsAdd.js'],
		'/shop-goodsList':               	['page-shop-goodsList', 'src/page/shop/goodsList.js'],
        '/shop-goodsEdit':               	['page-shop-goodsEdit', 'src/page/shop/goodsEdit.js'],
		'/shop-goodsDetailsEdit':           ['page-shop-goodsDetailsEdit', 'src/page/shop/goodsDetailsEdit.js'],
        '/shop-orderList':                  ['page-shop-orderList', 'src/page/shop/orderList.js'],
        '/shop-orderDetails':               ['page-shop-orderDetails', 'src/page/shop/orderDetails.js'],
        '/shop-orderDetailsPrint':               ['page-shop-orderDetailsPrint', 'src/page/shop/orderDetailsPrint.js'],
        '/shop-goodsWhenAdd':               ['page-shop-goodsWhenAdd', 'src/page/shop/goodsWhenAdd.js'],
        '/shop-goodsWhenEdit':              ['page-shop-goodsWhenEdit', 'src/page/shop/goodsWhenEdit.js'],
        '/shop-goodsWhenList':              ['page-shop-goodsWhenList', 'src/page/shop/goodsWhenList.js'],
        '/shop-myRegionOrder':              ['page-shop-myRegionOrder', 'src/page/shop/myRegionOrder.js'],
        '/shop-orderGroupList':              ['page-shop-orderGroupList', 'src/page/shop/orderGroupList.js'],
        
        
        //快递系统
        '/express-riderAdd':			['page-express-riderAdd', 'src/page/express/riderAdd.js'],
        '/express-riderEdit':			['page-express-riderEdit', 'src/page/express/riderEdit.js'],
        '/express-riderList':			['page-express-riderList', 'src/page/express/riderList.js'],
        '/express-orderList':			['page-express-orderList', 'src/page/express/orderList.js'],
        '/express-orderInfo':			['page-express-orderInfo', 'src/page/express/orderInfo.js'],
        
        //代理系统
        '/agent-regionAdd':				['page-agent-regionAdd', 'src/page/agent/regionAdd.js'],
        '/agent-regionList':			['page-agent-regionList', 'src/page/agent/regionList.js'],
        '/agent-regionEdit':			['page-agent-regionEdit', 'src/page/agent/regionEdit.js'],
        '/agent-userAdd':				['page-agent-userAdd', 'src/page/agent/userAdd.js'],
        '/agent-userList':				['page-agent-userList', 'src/page/agent/userList.js'],
        '/agent-userEdit':				['page-agent-userEdit', 'src/page/agent/userEdit.js'],
        '/agent-regionSubUserList':     ['page-agent-regionSubUserList','src/page/agent/regionSubUserList.js'],

        //优惠券
        '/application-couponList':       ['page-application-couponList','src/page/application/couponList.js'],
        '/application-couponEdit':       ['page-application-couponEdit','src/page/application/couponEdit.js'],
        '/application-couponAdd':       ['page-application-couponAdd','src/page/application/couponAdd.js'],
        '/application-config':       ['page-application-config','src/page/application/config.js'],
        '/application-cache':       ['page-application-cache','src/page/application/cache.js'],
        '/application-type':		['page-application-type', 'src/page/application/type.js'],
        
        

        //商家管理
        '/merchant-staffList':     			['page-merchant-staffList','src/page/merchant/staffList.js'],
        //内容管理系统
        '/cms-articleList':     			['page-cms-articleList','src/page/cms/articleList.js'],
        '/cms-articleTrash':     			['page-cms-articleTrash','src/page/cms/articleTrash.js'],
        //中润会展服务
        '/zrhzfw-orderList':                  ['project-zrhzfw-orderList', 'src/project/zrhzfw/orderList.js'],
        '/zrhzfw-orderDetails':               ['project-zrhzfw-orderDetails', 'src/project/zrhzfw/orderDetails.js'],
        //易淘
        '/yitao-stockRecordList':            ['project-yitao-stockRecordList', 'src/project/yitao/stockRecordList.js']
        
    },
	
    //公共事件，不会被局部覆盖，并且先执行公共事件，后执行项目的事件
    event:{
        //页面开始加载时
        pageStart:function(){
            //加载层-默认风格
            layer.load();
        },
        //页面完成加载时
        pageEnd:function(){
            //加载层-默认风格
            layer.closeAll('loading');
        },
       
        //页面加载有错误时
		error:function(message){
            //信息框-例4
            //layer.msg(message, {icon: 5});
            //关闭加载
            layer.closeAll('loading');
            console.log("main error：", message);
        },
        //当页面不存在时
        pageNotFound:function(href){
			layer.open({
				type: 1,
				shade: false,
				title: false, //不显示标题
				area: [($(window).width()>1200? 1200:$(window).width())+'px', ($(window).height()-50)+'px'], //宽高
				content: '<div style="padding:20px;">“'+av.router().href+'”页面不存在！</div>',
				cancel: function(){
					//删除当前浏览记录、返回上一页
					av.framework().history.pageRemove();
					av.framework().history.routerRemove();
					var pageBack = av.framework().history.pageBack();
					console.log('错误返回', pageBack);
				}
			});
        	//layer.msg('“'+av.router().href+'”页面不存在！', {icon: 5, time:-1});
            //console.log("pageNotFound 事件：", href, this);
        },
        
       

    }

}).run();

