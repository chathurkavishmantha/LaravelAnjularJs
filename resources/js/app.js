require('./bootstrap');


var app = angular.module('myApp',[]);
            //controller
            app.controller('myCtrl', function($scope,$http){
                $scope.save = function(){
                    console.log($scope.name); //view consol results
                    console.log($scope.description); //view consol results

                    $http({

                        url : "{{URL('api/insert')}}",
                        method : "POST",
                        data : {
                            "name" : $scope.name,
                            "description" : $scope.description
                        }

                        }).then(function(response){
                            // alert('success');
                            $scope.list();
                        },function(response){
                            alert('failed');
                        });

                    
                }

                $scope.list = function(){
                        $http.get("{{ URL('api/show' )}}")
                        .then(function(response){
                            $scope.tasks = response.data;
                        });
                }

                $scope.edit_task = {};
                // initialize update action
                $scope.initEdit = function (index) {
                    $scope.errors = [];
                    $scope.edit_task = $scope.tasks[index];
                    $("#edit_task").modal('show');
                };

                // update the given task
                $scope.updateTask = function () {
                    $http.patch('/task/' + $scope.edit_task.id, {
                        name: $scope.edit_task.name,
                        description: $scope.edit_task.description
                    }).then(function success(e) {
                        $scope.errors = [];
                        $("#edit_task").modal('hide');
                    }, function error(error) {
                        $scope.recordErrors(error);
                    });
                };
            });