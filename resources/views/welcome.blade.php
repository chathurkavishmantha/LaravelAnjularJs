
<!DOCTYPE html>
<html>
	<head>
		<title>AngularJS PHP CRUD (Create, Read, Update, Delete) using Bootstrap Modal</title>
		<script src="{{asset('js/jquery.min.js')}}"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js"></script>
		<script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
		<script src="{{asset('js/angular-datatables.min.js')}}"></script>
		<script src="{{asset('js/bootstrap.min.js')}}"></script>
		<link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
		<link rel="stylesheet" href="{{asset('css/datatables.bootstrap.css')}}">

        
		
	</head>
	<body ng-app="crudApp" ng-controller="crudController">

        <div class="modal fade" tabindex="-1" role="dialog" id="crudmodal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" ng-submit="submitForm()">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger alert-dismissible" ng-show="error" >
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                
                            </div>
                            <div class="form-group">
                                <label>Enter First Name</label>
                                <input type="text"  ng-model="name" value="" id="fName" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>Enter Description </label>
                                <input type="text"  ng-model="description" id="description" value="" class="form-control" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="hidden_id" value="" />
                            <input type="submit" name="submit" id="submit" class="btn btn-info" value=Save ng-click="save()" />
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


		
		<div class="container" ng-init="fetchData()">
			<br />
				<h3 align="center">AngularJS PHP CRUD (Create, Read, Update, Delete) using Bootstrap Modal</h3>
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
							<td><button type="button" ng-click="fetchSingleData(task)" class="btn btn-warning btn-xs">Edit</button></td>
							<td><button type="button" ng-click="deleteData(task.id)" class="btn btn-danger btn-xs">Delete</button></td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>
        
       


        <script>

        var app = angular.module('crudApp', ['datatables']);
        app.controller('crudController', function($scope, $http){

            $scope.success = false;

            $scope.error = false;

            $scope.openModal = function(task = null){
                var modal_popup = angular.element('#crudmodal');


                $('#crudmodal').on('show.bs.modal', function(e) {

                    $(e.currentTarget).find('#fName').val( task.name );
                    $(e.currentTarget).find('#description').val( task.description );

                    console.log($(e.currentTarget).find('#fName').val( task.name ));
                
                }

                                   
                                    );


                modal_popup.modal('show');
            };

            $scope.closeModal = function(){
                var modal_popup = angular.element('#crudmodal');
                modal_popup.modal('hide');
            };

            $scope.addData = function(){
                $scope.modalTitle = 'Add Data';
                $scope.submit_button = 'Insert';
                $scope.openModal();
            };



            $scope.fetchData = function(){
                
                $http.get("{{ URL('api/ /' )}}")
                    .then(function(response){
                        $scope.tasks = response.data;
                    });
            };

            $scope.save = function(){
                $http({
                    method:"POST",
                    url:"{{ URL('api/insert' )}}",
                    data:{'name':$scope.name, 'description':$scope.description}
                }).then(function(response){
                    alert('success');
                },function(response){
                    alert('failed');
                });

            };

            $scope.fetchSingleData = function(task){
                
                $scope.openModal(task);
		        
            };

            $scope.deleteData = function(id){
                if(confirm("Are you sure you want to remove it?"))
                {
                    $http({
                        method:"POST",
                        url:"insert.php",
                        data:{'id':id, 'action':'Delete'}
                    }).success(function(data){
                        $scope.success = true;
                        $scope.error = false;
                        $scope.successMessage = data.message;
                        $scope.fetchData();
                    });	
                }
            };

            

        });

        </script>
	</body>
</html>
