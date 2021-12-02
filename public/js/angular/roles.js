
app.controller('FormController', ($scope, $http, Loader, $timeout) => {

    $scope.data = {active : 1};

    $scope.init = (id) => {

        $http.get($scope.url + '/' + id)
                .then((response) => {

                    $scope.data = response.data.data;

                })
                .catch((error) => {
                    pnotify('Error', getErrorAsString(error.data), 'error');
                });

    };

    $scope.save = () => {

        $scope.submitted = true;
        var url = $scope.url;

        if ($scope.dataForm.$invalid) {
            return pnotify('Error', 'There is something wrong with your input.', 'error');
        }


        if ($scope.data.id) {
            url += '/' + $scope.data.id;
            $scope.data._method = 'put';
        }

        Loader.start();

        $http.post(url, $scope.data)
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
                });

    };


    $scope.delete = () => {

        swal(
                {
                    'title': 'Delete Role',
                    'text': 'Do you want to delete this role?',
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