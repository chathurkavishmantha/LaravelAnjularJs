angular.module('SPCLoader',[]).provider('Loader' , function(){
    
    this.$get= function(){ 
	
		
        return { start: function () { $("<div id='stock_loader' class='stock-loader'><div></div></div>").appendTo("body");  },
                    stop: function() { $("#stock_loader").remove();  }
        };
    
    };
    
   
    
});