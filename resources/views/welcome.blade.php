<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content" ng-app="myApp" ng-controller="myCtrl" ng-init="list()">
                <div class="title m-b-md">
                    Laravel
                </div>
                

                <br>
                Name: <input type="text" name="name" ng-model="name"><br>
                Description: <input type="text" name="description" ng-model="description"><br>
                <button type="button" ng-click="save()">save</button>
                <br>
                <table border="1">
                <tr ng-repeat="task in tasks">
                    <td ng-bind="task.name">
                    <td ng-bind="task.description">
                        
                    </td>
                </tr>
                </div>
                </table>
            </div>
            

        </div>


        


        <script type="text/javascript">
            var app = angular.module('myApp',[]);
            //controller
            app.controller('myCtrl', function($scope,$http){
                $scope.save = function(){
                    console.log($scope.name); //view consol results
                    console.log($scope.description); //view consol results

                    $http({

                        url : "{{url('/api/insert')}} ",
                        method : "POST",
                        data : {
                            "name" : $scope.name,
                            "description" : $scope.description
                        }

                        }).then(function(response){
                            alert('success');
                            $scope.list();
                        },function(response){
                            alert('failed');
                        });

                    
                }

                $scope.list = function(){
                        $http.get("{{url('/api/show')}}")
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
        </script>
    </body>
</html>
