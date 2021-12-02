
app.controller('PriceListController' , ($scope ,Loader , $http , $timeout) =>{
    
    $scope.file = null;
    
    $scope.fileChanged = (el)=>{
        
        $scope.file = el.files[0];
        
    };
    
    $scope.upload = () => {
        
        if($scope.file == null){
            return pnotify('Error' , 'Valid file is required.' , 'error');
        }
        
        Loader.start();
        
        var formData = new FormData();
        formData.append('file' , $scope.file);
        
        $http.post( $scope.url , formData ,  { transformRequest : angular.identity , headers: { 'content-type': undefined} }  )
                .catch((error ) => {
                    
                    pnotify('Error', getErrorAsString(error.data), 'error');
                    Loader.stop();
                    
                }).then((response) => {

                    Loader.stop();
                    pnotify('Success', response.data.message, 'success');

                    $timeout(() => {
                        window.location = response.data.url;
                    }, 2000);

                });
        
        
    };
    
});