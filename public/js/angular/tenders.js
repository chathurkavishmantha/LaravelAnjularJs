
app.controller('TenderController', ($scope, $http, Loader, $uibModal) => {



    $scope.init = () => {

        $('.date').datepicker({
            format: 'dd-mm-yyyy',
            forceParse: false,
            todayHighlight: true,
            todayBtn: true,
            'language': 'en'
        });

    };

    /**
     * 
     * @param {type} tender_id
     * @returns {undefined}
     */
    $scope.openTenderItems = (tender_id) => {

        var modalInstance = $uibModal.open(
                {
                    size: 'lg',
                    controller: 'TenderItemController',
                    templateUrl: 'tender-items.html',
                    resolve: {TenderID: () => {
                            return tender_id;
                        }}
                }
        );

        modalInstance.result.then(() => {
        }).catch(() => {
        });

    };




});


app.controller('TenderItemController', ($scope, $http, $uibModalInstance, TenderID, Loader) => {

    $scope.itemFile = null;


    $scope.cancel = () => {
        $uibModalInstance.dismiss();
    };

    $scope.init = (searchURL) => {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        Loader.start();

        var url = document.baseURI + '/tenders/tender-items/get/' + TenderID;

        $http.get(url).then((response) => {

            Loader.stop();

            $scope.data = response.data.data;
            $scope.tenderNo = response.data.tenderNo;

			

        }).catch((error) => {
            pnotify('Error', getErrorAsString(error.data), 'error');
            Loader.stop();
        });
		
		if(typeof($('#itemListSearch').data('select2')) == 'undefined'){

            $('#itemListSearch').select2({
                allowClear: true,
                minimumInputLength: 3,
                placeholder: 'Start Typing',
                delay: 250,
                ajax: {
                    'url': searchURL,
                    dataType: 'json',
                    method: 'POST',
                    processResults: (data) => {
                        ///     console.log(data);

                        return {results: data};
                    },
                }
            }).on('change' , function(e){ $scope.obj.code =  e.target.value ;});

			}



    };

    $scope.ok = () => {
        $uibModalInstance.close();
    };

    $scope.fileChanged = (el) => {

        $scope.itemFile = el.files[0];

    };

    $scope.upload = () => {

        if (!$scope.itemFile) {
            return pnotify('Error', 'File is required.', 'error');
        }

        var formData = new FormData();

        formData.append('tenderItemsFile', $scope.itemFile);
        formData.append('operation', $scope.form.operation);
        formData.append('tenderID', TenderID);

        Loader.start();

        var url = $scope.url + '/upload-file';

        $http.post(url, formData, {headers: {'content-type': undefined}, transformRequest: angular.identity})
                .then((response) => {

                    Loader.stop();
                    pnotify('Success', response.data.message, 'success');
					$scope.init();

                })
                .catch((error) => {
                    pnotify('Error', getErrorAsString(error.data), 'error');
                    Loader.stop();
                });

    };

    $scope.addNew = () => {


        if (!$scope.obj.code)
            return;

        var url = $scope.url + '/tender-items/add';

        Loader.start();

        $http.post(url, {code: $scope.obj.code, tenderID: TenderID})
                .then((response) => {
                    $scope.data = {};
                    Loader.stop();
                    pnotify('Success', response.data.message, 'success');
                    $scope.obj.code = null;
                    $scope.init();
                })
                .catch((error) => {
                    pnotify('Error', getErrorAsString(error.data), 'error');
                    Loader.stop();
                });

    };


    $scope.remove = (item) => {

        swal(
                {
                    'title': 'Delete Item',
                    'text': 'Do you want to delete this item?',
                    'showConfirmButton': true,
                    'showCancelButton': true,
                    'confirmButtonText': 'Delete'
                }

        ).then((result) => {

            //  console.log(result);

            if (result === true) {

                var url = $scope.url + '/tender-items/remove';

                Loader.start();

                $http.post(url, {itemID: item.id, tenderID: TenderID})
                        .then((response) => {
                            $scope.data = {};
                            Loader.stop();
                            pnotify('Success', response.data.message, 'success');

                            $scope.init();
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


    $scope.init();

});

app.controller('FormController', ($scope, $http, Loader, $timeout) => {

    $scope.tenderFile = null;
    $scope.tenderItemsFile = null;
    $scope.data = {
        dateAdded: moment().format('DD-MM-YYYY'),
        dateClosing: moment().add(1, 'month').format('DD-MM-YYYY hh:mm a')
    };


    $scope.init = (tender) => {

        Loader.start();

        $http.get($scope.url + '/' + tender + '?json')
                .then((response) => {

                    Loader.stop();
                    $scope.data = response.data.data;

                    $scope.data.dateAdded = moment($scope.data.dateAdded, 'YYYY-MM-DD').format('DD-MM-YYYY');
                    $scope.data.dateClosing = moment($scope.data.dateClosing, 'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY hh:mm a');

                })
                .catch((error) => {
                    pnotify('Error', getErrorAsString(error.data), 'error');
                    Loader.stop();
                });

    };

    $scope.save = () => {

        $scope.submitted = true;

        $('.date,.time').trigger('change');

        if ($scope.dataForm.$invalid) {
            pnotify('Error', 'There is something wrong with your input.', 'error');
            return;
        }


        if ($scope.tenderFile == null && !$scope.data.id) {
            pnotify('Error', 'Tender file must be uploaded.', 'error');
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

        if ($scope.tenderFile)
            formData.append('tenderFileX', $scope.tenderFile);

        if ($scope.tenderItemsFile)
            formData.append('tenderItemsFile', $scope.tenderItemsFile);

        Loader.start();

        $http.post(url, formData, {headers: {'content-type': undefined}, transformRequest: angular.identity})
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


    $scope.tenderFileChanged = (el) => {
        $scope.tenderFile = el.files[0];
    };
    $scope.tenderItemsChanged = (el) => {
        $scope.tenderItemsFile = el.files[0];
    };


    $scope.delete = () => {

        swal(
                {
                    'title': 'Delete Tender',
                    'text': 'Do you want to delete this tender?',
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