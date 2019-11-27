av({
	
	//城市选择器
	id:'module-citypicker',
	selector: '[module="citypicker"]',
	'export' : {
		template 	: 'src/module/citypicker/citypicker.html',
		area 		: 'src/module/citypicker/city-data/area.js', //区
		city 		: 'src/module/citypicker/city-data/city.js', //市
		province 	: 'src/module/citypicker/city-data/province.js' //省
	},
	'import' : function(e){
		this.template(e.template);
		
		this.data.importProvinceList 	= JSON.parse(e.province);
		this.data.importCityList 		= JSON.parse(e.city);
		this.data.importAreaList 		= JSON.parse(e.area);
		//初始化
		this.data.init();
		//console.log( JSON.parse(e.province), JSON.parse(e.city), JSON.parse(e.area) );
    },
	data: {
		pickerValue: [0, 0, 0],
		
		importProvinceList: [],
		importCityList: [],
		importAreaList: [],
		
	    provinceList: [],
	    cityList: [],
	    areaList: [],
		
		provinceLabel:'',
		cityLabel:'',
		areaLabel:'',
		
		provinceValue:'',
		cityValue:'',
		areaValue:'',
		
		provinceSelected:'',
		citySelected:'',
		areaSelected:'',
		
		provinceShow: true,
		cityShow: true,
		areaShow: true,
		
		//默认值 {'四川省','绵阳市','游仙区'}
		defaultLabel : function(data){
			//先找到省份索引
			for(var i in this.importProvinceList){
				//默认第一个 或者 自定义
				if( i == 0 || this.importProvinceList[i].label == data[0]){
					this.pickerValue[0] = i;
					this.provinceSelected = this.importProvinceList[i].value;
					this.provinceLabel 	= this.importProvinceList[i].label; // 选中文本
					this.provinceValue	= this.importProvinceList[i].value; // 选中值
					if( this.importProvinceList[i].label == data[0] ){
						break;
					}
				}
			}
			
			//再找到市份索引
			for(var i in this.importCityList[this.pickerValue[0]]){
				//默认第一个 或者 自定义
				if( i == 0 || this.importCityList[this.pickerValue[0]][i].label == data[1]){
					this.pickerValue[1] = i;
					this.citySelected = this.importCityList[this.pickerValue[0]][i].value;
					this.cityLabel 	= this.importCityList[this.pickerValue[0]][i].label; // 选中文本
					this.cityValue	= this.importCityList[this.pickerValue[0]][i].value; // 选中值
					if( this.importCityList[this.pickerValue[0]][i].label == data[1] ){
						break;
					}
				}
			}
			
			//再找到区份索引
			for(var i in this.importAreaList[this.pickerValue[0]][this.pickerValue[1]]){
				//默认第一个 或者 自定义
				if( i == 0 || this.importAreaList[this.pickerValue[0]][this.pickerValue[1]][i].label == data[2]){
					this.pickerValue[2] = i;
					this.areaSelected = this.importAreaList[this.pickerValue[0]][this.pickerValue[1]][i].value;
					this.areaLabel 	= this.importAreaList[this.pickerValue[0]][this.pickerValue[1]][i].label; // 选中文本
					this.areaValue	= this.importAreaList[this.pickerValue[0]][this.pickerValue[1]][i].value; // 选中值
					if( this.importAreaList[this.pickerValue[0]][this.pickerValue[1]][i].label == data[2] ){
						break;
					}
				}
			}
			
			//然后初始化
			this.init();
		},
		
		//初始化
		init: function(){
			this.provinceList = this.importProvinceList;
			this.cityList = this.importCityList[this.pickerValue[0]];
			this.areaList = this.importAreaList[this.pickerValue[0]][this.pickerValue[1]];
			
			//console.log('init', this.provinceSelected, this.citySelected, this.areaSelected);
		},
		
		eventProvinceChange : function(ele){
			var index 				= ele.selectedIndex;// 选中索引(选取select中option选中的第几个)
			var provinceListIndex 	= ele.options[index].getAttribute('index');
			// 判断select中的某个option是否选中   true为选中   false 为未选中
			if( ele.options[index].selected && this.pickerValue[0] != provinceListIndex ){
				// 第一级发生滚动
	        	this.cityList = this.importCityList[provinceListIndex];
	        	this.areaList = this.importAreaList[provinceListIndex][0];
	        	this.pickerValue[0] = provinceListIndex;
	        	this.provinceSelected = ele.options[index].value;
	        	this.provinceLabel 	= ele.options[index].text; // 选中文本
				this.provinceValue	= ele.options[index].value; // 选中值
				
	        	this.pickerValue[1] = 0;
	        	this.citySelected = this.importCityList[this.pickerValue[0]][0].value;
	        	this.cityLabel 	= this.importCityList[this.pickerValue[0]][0].label; // 选中文本
				this.cityValue	= this.importCityList[this.pickerValue[0]][0].value; // 选中值
				
	        	this.pickerValue[2] = 0;
	        	this.areaSelected = this.importAreaList[this.pickerValue[0]][this.pickerValue[1]][0].value;
				this.areaLabel 	= this.importAreaList[this.pickerValue[0]][this.pickerValue[1]][0].label; // 选中文本
				this.areaValue	= this.importAreaList[this.pickerValue[0]][this.pickerValue[1]][0].value; // 选中值
			}
			
			//console.log('eventProvinceChange', this.provinceSelected, this.citySelected, this.areaSelected);
	    },
	    
	    eventCityChange : function(ele){
	    	var index 				= ele.selectedIndex;// 选中索引(选取select中option选中的第几个)
			var cityListIndex 	= ele.options[index].getAttribute('index');
	    	// 判断select中的某个option是否选中   true为选中   false 为未选中
	    	// 判断select中的某个option是否选中   true为选中   false 为未选中
			if( ele.options[index].selected && this.pickerValue[1] != cityListIndex ){
				// 第一级发生滚动
	        	this.areaList = this.importAreaList[this.pickerValue[0]][cityListIndex];
	        	this.pickerValue[1] = cityListIndex;
	        	this.citySelected = ele.options[index].value;
	        	this.cityLabel 	= ele.options[index].text; // 选中文本
				this.cityValue	= ele.options[index].value; // 选中值
				
	        	this.pickerValue[2] = 0;
	        	this.areaSelected = this.importAreaList[this.pickerValue[0]][this.pickerValue[1]][0].value;
				this.areaLabel 	= this.importAreaList[this.pickerValue[0]][this.pickerValue[1]][0].label; // 选中文本
				this.areaValue	= this.importAreaList[this.pickerValue[0]][this.pickerValue[1]][0].value; // 选中值
			}
			
			//console.log('eventCityChange', this.provinceSelected, this.citySelected, this.areaSelected);
	    },
	    
	    eventAreaChange : function(ele){
	    	var index 			= ele.selectedIndex;// 选中索引(选取select中option选中的第几个)
			var areaListIndex 	= ele.options[index].getAttribute('index');
	    	// 判断select中的某个option是否选中   true为选中   false 为未选中
			if( ele.options[index].selected && this.pickerValue[2] != areaListIndex ){
				// 第一级发生滚动
	        	this.pickerValue[2] = areaListIndex;
	        	//console.log(ele.options[index].text, ele.options[index].value);
	        	this.areaSelected = ele.options[index].value;
				this.areaLabel 	= ele.options[index].text; // 选中文本
				this.areaValue	= ele.options[index].value; // 选中值
			}
			
			//console.log('eventAreaChange', this.provinceSelected, this.citySelected, this.areaSelected);
	    },
	    
		
	    
	    
	}
	
});
