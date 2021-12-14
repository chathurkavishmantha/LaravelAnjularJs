
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
                <button class="btn btn-primary" type="submit" id="submit"  ng-click="save(state, data.id)" >OK</button>
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
							<th>First Name</th>
							<th>Last Name</th>
							<th>id</th>
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

            $scope.openModal = function(task = null){ // create a function to access all data that belogs to the specific modal

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
                                } 

                            }                        
                    }

                );

                modalInstance.result.then(( result ) => {} ) // catch error if there is any problem with data.
                .catch(() => {});

            };

            $scope.addData = function(){
                // $scope.modalTitle = 'Add_Data';
                $scope.submit_button = 'Insert';
                $scope.openModal();
            };

            //fetch customers listing from 
            $scope.fetchData = function(){
                var url = 'api/' ;
                $http({
                    method : 'Get',
                    url : url ,
                }).then(function (response) {
                    $scope.tasks = response.data.tasks;
                    // $scope.data = response.data.tasks;
                    // console.log($scope.data);
                }, function (error) {
                    alert('This is embarassing. An error has occurred. Please check the log for details');
                });
            };            

            $scope.edit = function(task,edit){
                // $scope.tasks.id = task;
                // console.log($scope.tasks.id);
                // var val = $scope.data.task;
                //     console.log(val);

                // $scope.edit = edit;

                // console.log(edit);
                // $scope.item = null;
                $scope.edit = edit;
        
                switch (edit) {
                    case 'edit':
                        $scope.form_title = "Item Edit";
                        // console.log($scope.form_title);

                        break;
                    default:
                        break;
                }


                $scope.openModal(task,edit);
		        
            };




             //fetch customers listing from 
             $scope.deleteData = function(task){
                var url = 'api/delete/' + task.id;
                $http({
                    method : 'delete',
                    url : url ,
                }).then(function (response) {
                    $scope.tasks = response.data.tasks;
                    window.location.reload();

                    // $scope.data = response.data.tasks;
                    // console.log($scope.data);
                }, function (error) {
                    alert('This is embarassing. An error has occurred. Please check the log for details');
                });
            }; 
            


        }).controller ('CRUDController' , ($http, $scope, $uibModalInstance, Task) => {

            $scope.data = Task; // bind data to show model's input fild.
            // $scope.editData = edit; 

            $scope.ok = () => {
                $uibModalInstance.close(true);
            };

            $scope.cancel = () => {
                $uibModalInstance.dismiss();
            };

            $scope.save = (state, id) => {
                
                var url = 'api/task';
                var method = 'POST';
                console.log(state);
                

                //append item id to the URL if the form is in edit mode
                // if (edit === 'edit') {
                //     url += "/" + id;
        
                //     method = "PUT";
                // }

                $http({
                    method: method,
                    animation: false,
                    url: url,
                    data:{'name':$scope.data.name, 'description':$scope.data.description}
                }).then(function(response){
                    // console.log(response);
                    $scope.tasks = $scope.data.tasks;
                    // console.log(val);
                    alert('success');
                    // window.location.reload();
                },function(response){
                    alert('failed');
                });   
            };       
            

        });

        </script>
	</body>
</html>