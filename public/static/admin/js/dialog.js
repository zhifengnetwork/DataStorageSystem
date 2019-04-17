var dialog = {
    // 错误弹出层  
	error : function(msg){
		layer.open({  
			content : msg,  
        	icon : 2,  
        	title : '错误提示',
        });
	},
	// 成功弹出层  
    success : function(msg,url) {  
        layer.open({  
            content : msg,  
            icon : 1,  
            yes : function(){  
                location.href=url;  
            },  
        });  
    },  
  
    // 确认弹出层  
    confirm : function(msg, url) {  
        layer.open({  
            content : msg,  
            icon : 3,  
            btn : ['是','否'],  
            yes : function(){  
                location.href=url;  
            },  
        });  
    },  
  
    // 无跳转确认框  
    toconfirm : function(msg) {  
        layer.open({  
            content : msg,  
            icon : 7,  
            btn : ['确定'],  
        });  
    },  
}