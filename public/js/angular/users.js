app.controller('FormController', ($scope, $http, Loader, $timeout, $uibModal) => {
    $scope.data = {active: 1};
    $scope.file = null;
    
    $scope.customPermissions = [];

 $scope.calculatePWScore = () => {

        $scope.pwScore = 0;

        var confirm = $scope.data.confirm;

        if (confirm.length >= 6)
            $scope.pwScore += 1;

        if (/[a-z]+/.test(confirm))
            $scope.pwScore += 1;
        if (/[A-Z]+/.test(confirm))
            $scope.pwScore += 1;

        if (/[0-9]+/.test(confirm))
            $scope.pwScore += 1;

    };

    $scope.save = () => {

        $scope.submitted = true;

        if ($scope.dataForm.$invalid) {
            pnotify('error', 'There is something wrong with your input.');
            return;
        }

        var url = $scope.url;

        if ($scope.data.id) {
            url += '/' + $scope.data.id;
            $scope.data._method = 'PUT';
        }

        var formData = new FormData();

        angular.forEach($scope.data, (value, key) => {
            formData.append(key, value);
        });
        
        formData.append('customPermissions' , JSON.stringify( $scope.customPermissions));

        if ($scope.file) {
            formData.append('imageFile', $scope.file);
        }

        Loader.start();


        $http.post(url, formData, {transformRequest: angular.identity, headers: {'content-type': undefined}})
                .then((response) => {
                    Loader.stop();
                    pnotify('Success', response.data.message, 'success');

                    $timeout(() => {
                        window.location = response.data.url;
                    }, 2000);
                })
                .catch((error) => {
                    pnotify('Error', getErrorAsString(error.data), 'error');
                    Loader.stop();
                })


    };

    $scope.imageChanged = (el) => {
        $scope.file = el.files[0];
    };


    $scope.showSpPermissionDialog = () => {

        var modalInstance = $uibModal.open(
                {size: 'lg', 'controller': 'SpPermissionController',
                    templateUrl: 'permissions.html',
                    resolve: {url: () => {
                            return $scope.url;
                        }
                    }
                }
        );

        modalInstance.result.then((data) => {
            
            $scope.customPermissions.push(data);
            
        }).catch(() => {
        });

    };

    $scope.removePermission = (perm)=>{
        perm.deleted = true;
    };

    $scope.init = (user) => {

        var url = $scope.url + '/' + user;

        Loader.start();

        $http.get(url)
                .then((response) => {
                    Loader.stop();
                    $scope.data = response.data.data;
                    $scope.customPermissions = $scope.data.custom_permissions || [];
                    $scope.data.custom_permissions = null;
                })
                .catch((error) => {
                    Loader.stop();
                    pnotify('Error', getErrorAsString(error.data), 'error');
                });

    };
    
    
    $scope.delete = () => {

        swal(
                {
                    'title': 'Delete User',
                    'text': 'Do you want to delete this User',
                    'showConfirmButton': true,
                    'showCancelButton': true,
                    'confirmButtonText': 'Delete'
                }

        ).then((result) => {

          //  console.log(result);

            if (result === true) {
                
                var url = $scope.url + '/' + $scope.data.id;

                Loader.start();

                $http.delete(url)
                        .then((response) => {
                            $scope.data = {};
                            Loader.stop();
                            pnotify('Success', response.data.message, 'success');

                            $timeout(() => {
                                window.location = response.data.url;
                            }, 2000);

                        })
                        .catch((error) => {
                            pnotify('Error', getErrorAsString(error.data), 'error');
                            Loader.stop();
                        });


            }

        })
                .catch(() => {
                });

    };

});


app.controller('SpPermissionController', ($scope,$http, $uibModalInstance, url,Loader) => {

    $scope.cancel = () => {
        $uibModalInstance.dismiss();
    };
    
    $scope.ok = () =>{
        
        if($scope.subForm.$invalid){
            pnotify('Error', 'There is something wrong with your input','error');
            return;
        }
        
        var permissionLabel = $('#module_id').find(':selected').text() + ':' +
                $('#permission_id').find(':selected').text() + ' ' + $('#enabled').find(':selected').text();
        
        $scope.data.permissionLabel = permissionLabel;
        
        $uibModalInstance.close($scope.data);
        
    };


    $scope.init = () => {

        $('#subForm select').select2({});

    }
    
    $scope.getPermissions = () =>{
        
        if(!$scope.data.module_id){
            $scope.permissions = [];
            return;
        }
        
        Loader.start();
        
        $http.post(url + '/getPermissions/' + $scope.data.module_id)
                .then((response) => {
                    Loader.stop();
                    $scope.permissions = response.data.data;
                })
                .catch((error) => {
                    Loader.stop();
                    pnotify('Error', getErrorAsString(error.data), 'error');
                });
        
    };


});