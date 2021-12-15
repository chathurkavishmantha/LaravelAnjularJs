
<!DOCTYPE html>
<html ng-app="crudApp">
	<head>
		<title>AngularJS PHP CRUD (Create, Read, Update, Delete) using Bootstrap Modal</title>
	
		<link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
		<link rel="stylesheet" href="{{asset('css/datatables.bootstrap.css')}}">
		<link rel="stylesheet" href="{{asset('css/ui-bootstrap-custom-2.5.0-csp.css')}}">

        
		
	</head>
	<body  ng-controller="crudController">

        {{-- defind popup model --}}
        <script type='text/ng-template' id="crudmodal.html"> 

            <div class="modal-header">
                <h3 class="modal-title" id="modal-title">I'm a modal!</h3>
            </div>
            <div class="modal-body" id="modal-body">
                <form>                    
                    <div class="form-group">
                        <label>Enter First Name</label>
                        <input type="text"  ng-model="data.name"   id="fName"  class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Enter Description </label>
                        <input type="text"  ng-model="data.description" id="description" class="form-control" />
                        <input type="hidden"  ng-model="data.id" id="description" class="form-control" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="submit" id="submit"  ng-click="save()" >@{{bNew ? 'Add' : 'Update'}}</button>
                <button class="btn btn-warning" type="button" ng-click="cancel()">Cancel</button>
            </div>

        </script>
		
		<div class="container" ng-init="fetchData()">
			<br />
				<h3 align="center">Crude App</h3>
			<br />
			<div class="alert alert-success alert-dismissible" ng-show="success" >
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				
			</div>
			<div align="right">
				<button type="button" name="add_button" ng-click="addData()" class="btn btn-success">Add</button>
			</div>
			<br />
			<div class="table-responsive" style="overflow-x: unset;">
				<table  datatable="ng" dt-options="vm.dtOptions" class="table table-bordered table-striped">
					<thead>
						<tr>
                            <th>id</th>
							<th>First Name</th>
							<th>Last Name</th>
							
							<th>Edit</th>
							<th>Delete</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="task in tasks">
                            <td >@{{task.id}}</td>
							<td >@{{task.name}}</td>
                            <td >@{{task.description}}</td>
                            
							<td><button type="button" ng-click="edit(task,'edit')" class="btn btn-warning btn-xs">Edit</button></td>
							<td><button type="button" ng-click="deleteData(task)" class="btn btn-danger btn-xs">Delete</button></td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>
        
        <script src="{{asset('js/jquery.min.js')}}"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.js"></script>
		<script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
		<script src="{{asset('js/angular-datatables.min.js')}}"></script>
		<script src="{{asset('js/bootstrap.min.js')}}"></script>
        <script src="{{asset('js/angular-animate.js')}}"></script>
		<script src="{{asset('js/ui-bootstrap-custom-2.5.0.js')}}"></script>
		<script src="{{asset('js/ui-bootstrap-custom-tpls-2.5.0.js')}}"></script>


        <script>

        var app = angular.module('crudApp', ['datatables','ui.bootstrap' , 'ngAnimate']); //defind attribute that imported files in js folder.

        app.controller('crudController', function($scope, $http , $uibModal){ // defind $uibModal attribute that created Main modal's.     

            $scope.success = false;

            $scope.error = false;

            $scope.openModal = function(task = null , bNew = false){ // create a function to access all data that belogs to the specific modal

               var modalInstance =  $uibModal.open( // create a function to access modal.
                    {
                        controller: 'CRUDController',
                        templateUrl : 'crudmodal.html',
                        size : 'md',
                        resolve : 
                            { 
                                'Task' : () =>
                                { 
                                    return task;
                                } ,

                                'New' : () => { return bNew ;}

                            }                        
                    }

                );

                modalInstance.result.then(( result ) => {} ) // catch error if there is any problem with data.
                .catch(() => {});

            };

// -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

            //open addDdata modal view
            $scope.addData = function(){
                $scope.openModal(null ,  true);
            };

// -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


            //fetch customers listing from 
            $scope.fetchData = function(){
               
                var url = 'api/' ;
                $http({
                    method : 'Get',
                    url : url ,
                }).then(function (response) {
                    $scope.tasks = response.data.tasks; // fetch data from database.
                }, function (error) {
                    alert('This is embarassing. An error has occurred. Please check the log for details');
                });
            };            

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            //open model and display selected data from id.
            $scope.edit = function(task,edit){
                $scope.openModal(task);   
            };

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

             //Delete data 
             $scope.deleteData = function(task){
                var url = 'api/delete/' + task.id;
                $http({
                    method : 'delete',
                    url : url ,
                }).then(function (response) {
                    alert('Do you want to delete this record.');
                    window.location.reload();
                }, function (error) {
                    alert('This is embarassing. An error has occurred. Please check the log for details');
                });
            }; 
            
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        }).controller ('CRUDController' , ($http, $scope, $uibModalInstance, Task , New) => {

            $scope.data = Task; // bind data to show model's input fild.
            console.log(Task); // to view what data comming from that New variable?

            $scope.bNew = New;
            console.log(New); // to view what data comming from that New variable?


            $scope.ok = () => {
                $uibModalInstance.close(true);
            };

            $scope.cancel = () => {
                $uibModalInstance.dismiss();
                window.location.reload();
            };

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            // save new data and new edita data
            $scope.save = () => {   

                var newValue = "false";
                var url = 'api/task';
                var method = 'POST';
                var isTrueSet = New.toString();
                console.log(isTrueSet); // test New object returening boolean value is now converting to the string value to isTrueSet varible.
               
                
                if ( newValue == isTrueSet) { // To know the New data returening bootean value is equal to the newValue variable.

                    url += "/" + $scope.data.id;        
                    method = "PUT";
                    console.log(method);

                }
                $http({
                       method: method,
                       animation: false,
                       url: url,
                       data:{ 'name':$scope.data.name, 'description':$scope.data.description}
                    }).then(function(response){
                        console.log('success');
                        window.location.reload();
                    },function(response){
                        alert('failed');
                    }); 
            };       
            

        });

        </script>
	</body>
</html>