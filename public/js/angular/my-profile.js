app.controller('MyProfileController', ($scope, $http, Loader, $timeout, $uibModal) => {
    $scope.data = {active: 1};
    $scope.file = null;


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



        var formData = new FormData();

        angular.forEach($scope.data, (value, key) => {
            formData.append(key, value);
        });



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


    $scope.init = () => {

        var url = $scope.url + '/get';

        Loader.start();

        $http.get(url)
                .then((response) => {
                    Loader.stop();
                    $scope.data = response.data.data;

                })
                .catch((error) => {
                    Loader.stop();
                    pnotify('Error', getErrorAsString(error.data), 'error');
                });

    };



});

