av({

  id:'userList::agentRegionOption',
  selector:'#agentRegionOption',
  include : ["src/common/content.js"],
  extend : ["common-content"],
  'export' : {template : "src/page/agent/userList/agentRegionOption.html"},
  'import' : function(e){
        this.template(e.template);
  },
  data:{
    yitaoShenheAgentid:null,
    agentRegionOption:null,
    eventChangeSelect:function(){
      var _this=this;
      _this.yitaoShenheAgentid=$.trim($('[name="agentRegionOption"]').val());
    }
  }
  
});