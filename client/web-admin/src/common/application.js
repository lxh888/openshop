av({
	
	id: 'common-application',
	data: {
		
		//检查应用是否是易淘
		applicationCheckYitaoshop: function(){
			var objectRequestAPI = new requestAPI();
			if( objectRequestAPI.application() == 'yitaoshop' || objectRequestAPI.application() == 'yitaoshop_test' ){
				return true;
			}else{
				return false;
			}
		},
		
		//检查应用是否是e麦商城
		applicationCheckEmshop: function(){
			var objectRequestAPI = new requestAPI();
			if( objectRequestAPI.application() == 'emshop_test' || objectRequestAPI.application() == 'emshop' ){
				return true;
			}else{
				return false;
			}
		},
		
		//检查应用是否是创联众宜
		applicationCheckChuanglianzhongyi: function(){
			var objectRequestAPI = new requestAPI();
			if( objectRequestAPI.application() == 'chuanglianzhongyi_test' || objectRequestAPI.application() == 'chuanglianzhongyi' ){
				return true;
			}else{
				return false;
			}
		},
		//检查应用是否是中润会展
		applicationCheckZhongrunhuizhan: function(){
			var objectRequestAPI = new requestAPI();
			if( objectRequestAPI.application() == 'zrhzfw_test' || objectRequestAPI.application() == 'zrhzfw' ){
				return true;
			}else{
				return false;
			}
		},
		//检查应用是否是优利小程序 
		applicationCheckYouli: function(){
			var objectRequestAPI = new requestAPI();
			if( objectRequestAPI.application() == 'youli_test' || objectRequestAPI.application() == 'youli' ){
				return true;
			}else{
				return false;
			}
		},
		//检查应用是否是母婴商城 
		applicationCheckMuYing: function(){
			var objectRequestAPI = new requestAPI();
			if( objectRequestAPI.application() == 'muyingshop_test' || objectRequestAPI.application() == 'muyingshop' ){
				return true;
			}else{
				return false;
			}
		},
		//检查应用是否是喜乐淘 
		applicationCheckXiLeTao: function(){
			var objectRequestAPI = new requestAPI();
			if( objectRequestAPI.application() == 'xiletao_test' || objectRequestAPI.application() == 'xiletao' ){
				return true;
			}else{
				return false;
			}
		},
		//检查应用是否是江油快递
		applicationCheckJiangYouKuaiDi: function(){
			var objectRequestAPI = new requestAPI();
			if( objectRequestAPI.application() == 'jiangyoukuaidi_test' || objectRequestAPI.application() == 'jiangyoukuaidi' ){
				return true;
			}else{
				return false;
			}
		},
		//商品类型
		applicationShopGoodsPropertyOption:function(){
			var temp = [
				{id:0,name:'普通商品'},
				{id:1,name:'积分商品'},
				// {id:2,name:'礼包商品'},
			];

			if( this.applicationCheckYitaoshop() ||this.applicationCheckZhongrunhuizhan()){
				temp = [{id:0,name:'普通商品'}];
			}

			return temp;
		},
		//商品类型
		applicationShopGoodsIndexOption:function(){
			var temp = [
				{id:0,name:'无标记'},
				{id:1,name:'会员礼包'},
				// {id:2,name:'礼包商品'},
			];

			if( this.applicationCheckYitaoshop() ){
				temp = [{id:0,name:'非门槛商品'},
				{id:1,name:'门槛商品'}];
			}
			if(this.applicationCheckZhongrunhuizhan()){
				temp = [{id:0,name:'无标记'} ];
			}
			return temp;
		},





		
	}
});
