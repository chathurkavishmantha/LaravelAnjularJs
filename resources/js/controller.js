angular.module('app')
      .controller('AdminListController', function($scope,$modal ) {
    
        $scope.Confirm = function ( ) {
            alertModalInstance = $modal.open({
              animation: $scope.animationsEnabled,
              templateUrl: 'confirmAlert.html',
              scope: $scope
            });
        $scope.cancelDelete = function () {
            console.log("cancel");
            alertModalInstance.dismiss('cancel');
          };
        $scope.ok = function () {
            console.log("ok");
            alertModalInstance.close(true);
          };
        
        }
    });