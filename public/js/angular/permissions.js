
app.controller('PermissionController', ($scope, $http, Loader, $timeout, $uibModal) => {

    $scope.init = () => {

        $http.post($scope.url)
                .then((response) => {

                    $scope.modules = response.data.data;

                })
                .catch((error) => {
                    pnotify('Error', getErrorAsString(error.data), 'error');
                });

    };

    $scope.loadPermissions = (module) => {

        var modalInstance = $uibModal.open(
                {
                    templateUrl: 'permissions.html',
                    size: 'md',
                    'controller': 'PermissionListController',
                    resolve: {'Module': () => {
                            return module
                        }, URL: () => {
                            return $scope.url;
                        }}
                }
        );

        modalInstance.result.then(() => {
            $scope.init();
        }).catch(() => {

        });

    };


});


app.controller('RolePermissionController', ($scope, $http, Loader, $timeout,) => {
    
    $scope.activeDataset = null;

    $scope.init = () => {

        $http.post($scope.url)
                .then((response) => {

                    $scope.data = response.data.data;

                })
                .catch((error) => {
                    pnotify('Error', getErrorAsString(error.data), 'error');
                });

    };
    
    
    $scope.roleChanged =() =>{
        
        if(!$scope.role_id){
            return;
        }
        
        $http.post($scope.url + '/get-role-permission/' + $scope.role_id)
                .then((response) => {

                    $scope.modules = response.data.data;

                })
                .catch((error) => {
                    pnotify('Error', getErrorAsString(error.data), 'error');
                });
    }


    $scope.save = () => {
        
        if(!$scope.role_id){
            return;
        }
        
        Loader.start();
        
        $http.post($scope.url + '/save-role-permission/' + $scope.role_id , {modules : $scope.modules})
                .then((response) => {
                    Loader.stop();
                    pnotify('Success' ,response.data.message , 'success');

                })
                .catch((error) => {
                    Loader.stop();
                    pnotify('Error', getErrorAsString(error.data), 'error');
                });
        
    };



});

app.controller('PermissionListController', ($scope, $http, Loader, $uibModalInstance, URL, Module) => {

    $scope.init = () => {

        $http.post(URL + '/' + Module.id).then((response) => {
            $scope.data = response.data.data;
        }).catch((error) => {
            pnotify('Error', getErrorAsString(error.data), 'error');
        })

    };

    $scope.ok = () => {

        Loader.start();
        $http.post(URL + '/' + Module.id + '/save', {items: $scope.data}).then((response) => {
            pnotify('Success', response.data.message, 'success');
            Loader.stop();
            $uibModalInstance.close();
        }).catch((error) => {
            pnotify('Error', getErrorAsString(error.data), 'error');
            Loader.stop();
        })


    };

    $scope.cancel = () => {
        $uibModalInstance.dismiss();
    };


    $scope.init();

    $scope.enableEdit = (perm) => {
        perm.edit = true;
    };

    $scope.removePerm = (perm) => {
        perm.remove = true;
    };

    $scope.addNewPerm = () => {

        if ($scope.obj.name == '') {
            return;
        }

        $scope.data.push({'name': $scope.obj.name, 'active': 1});

        $scope.obj.name = '';

    };

});