
<!DOCTYPE html>
<html>
	<head>
		<title>AngularJS PHP CRUD (Create, Read, Update, Delete) using Bootstrap Modal</title>
	
		<link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
		<link rel="stylesheet" href="{{asset('css/datatables.bootstrap.css')}}">
		<link rel="stylesheet" href="{{asset('css/ui-bootstrap-custom-2.5.0-csp.css')}}">

        
		
	</head>
	<body ng-app="crudApp" ng-controller="crudController">

        {{-- defind popup model --}}
        <script type='text/ng-template' id="crudmodal.html"> 

            <div class="modal-header">
                <h3 class="modal-title" id="modal-title">I'm a modal!</h3>
            </div>
            <div class="modal-body" id="modal-body">
                <form>                    
                    <div class="form-group">
                        <label>Enter First Name</label>
                        <input type="text"  ng-model="data.name"  id="fName" value = [[name]] class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Enter Description </label>
                        <input type="text"  ng-model="data.description" id="description" value = [[description]] class="form-control" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="submit" id="submit"  ng-click="save()" >OK</button>
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
				<table datatable="ng" dt-options="vm.dtOptions" class="table table-bordered table-striped">
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
							<td ng-bind="task.name"></td>
                            <td ng-bind="task.description"></td>
                            <td ng-bind="task.id"></td>
							<td><button type="button" ng-click="fetchSingleData(edit,task.id)" class="btn btn-warning btn-xs">Edit</button></td>
							<td><button type="button" ng-click="deleteData(task.id)" class="btn btn-danger btn-xs">Delete</button></td>
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
                        resolve : { 'Task' : () => { return task ;} }                        
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

            // $scope.fetchData = function(){
            //     var url = 'api/' ;
            //     $http({
            //         method : 'Get',
            //         url : url ,
            //     }).then(function (response) {
            //         $scope.tasks = response.data.tasks;
            //     }, function (error) {
            //         console.log(error);
            //         alert('This is embarassing. An error has occurred. Please check the log for details');
            //     });
            // };            

            // $scope.fetchSingleData = function(data, id){
            //     $scope.openModal(data, id);
		        
            // };


        }).controller ('CRUDController' , ($http, $scope,$uibModalInstance , Task) => {

            $scope.data = Task; // bind data to show model's input fild.

            $scope.ok = () => {
                $uibModalInstance.close(true);
            };

            $scope.cancel = () => {
                $uibModalInstance.dismiss();
            };
            

            $scope.save = () => {

                // console.log();

                var url = 'api/insert/';
                var method = 'POST';
                $http({
                        method: method,
                        url: url,
                        data:{'name':$scope.data.name, 'description':$scope.data.description}
                        
                    }).then(function (response) {
                        console.log(response);
                        // location.reload();
                    }), (function (error) {
                        console.log(error);
                        alert('This is embarassing. An error has occurred. Please check the log for details');
                    });
                    
                
                   
            };

        });

        </script>
	</body>
</html>