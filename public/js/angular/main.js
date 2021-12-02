
var app = angular.module('SPCApp' , ['SPCLoader','ui.bootstrap']);



function getErrorAsString(errorObj) {

    //var errorObj = {"code": ["The code has already been taken."]};
    var errMsg = errorObj.message || 'An error occured';
    errMsg += '<br><ul>';
    
    var errorText = '';

    try {

        if (typeof (errorObj.errors) != 'undefined') {

            var keyList = Object.keys(errorObj.errors);

            

            keyList.forEach((value) => {

                errorText += '<li>' + errorObj.errors[value][0] + '</li>';

            });
        }
    } catch (ex) {

    }

    return errMsg + errorText + '</ul>';

}


function pnotify(title,text,type){
    new PNotify({'title': title , 'text' : text , 'type' : type , 'delay' : 2000});
}


app.directive('tableRepeat' , ()=>{
    
    return (scope,element,attr)=>{
        
        if(scope.$last){
            scope.initDataTable();
        }
        
    };
    
});