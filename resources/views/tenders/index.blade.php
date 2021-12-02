@extends('layouts.main')

@section('title','Tenders')
@section('subtitle','Tenders')

@section('rightbar-content')

<div class="row" ng-controller="TenderController">

    <div class="col-lg-12">
                <div class="card m-b-30 border-light">
                    <div class="card-header border-light ">
                        <h3 class="card-title ">Tenders</h3>
                        <div class="widgetbar">
                            @if(auth()->user()->isPermitted('Tenders','Create'))
                            <a  href="{{route('tenders.create')}}" class="btn btn-primary" type="button" >New</a>
                            @else
                                <a  href="javascript:;" class="btn btn-secondary" type="button" >New</a>
                            @endif
                            <button disabled class="btn btn-secondary" type="button" >Save</button>
                            <button disabled class="btn btn-secondary" type="button" >Delete</button>
                        </div>
                    </div>
                    <div class="card-body pt-5">
                        
                        <form action="" method="get">
                            
                            <div class="row">
                                
                                <div class="col-md-2 form-group">
                                    <label>Tender Type</label>
                                    <select name="category" class="form-control">
                                        <option value="">Select</option>
                                       
                                        @foreach(\App\Category::active()->get() as $category)
                                        <option @if(request()->category == $category->id ) selected @endif value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                        
                                    </select>
                                </div>
                                
                                 <div class="col-md-2 form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option  @if(request()->status == ''   ) selected @endif  value="">All</option>
                                        <option  @if(request()->status == '0'   ) selected @endif  value="0">Open</option>
                                        <option @if(request()->status == '1'  ) selected @endif  value="1">Closed</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2 form-group">
                                    <label>Tender No</label>
                                    <input type="text" name="code" class="form-control-sm form-control" value="{{request()->code ?? ''}}" />
                                </div>
                                
                                
                                
                                 <div class="col-md-2 form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control-sm form-control" value="{{request()->name ?? ''}}" />
                                </div>
                                
                                <div class="col-md-2 form-group">
                                    <label>Item Code</label>
                                    <input type="text" name="itemCode" class="form-control-sm form-control" value="{{request()->itemCode ?? ''}}" />
                                </div>
                                
                                 <div class="col-md-4 form-group">
                                    <label>Date Between</label>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input placeholder="From" type="text" name="dateFrom" class="date form-control-sm form-control" value="{{request()->dateFrom ?? ''}}" />
                                
                                        </div>
                                        <div class="col-md-6">
                                            <input placeholder="To" type="text" name="dateTo" class="date form-control-sm form-control" value="{{request()->dateTo ?? ''}}" />
                            
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <div class="col-md-2 form-group">
                                    <label>Order By</label>
                                    <select name="order" class="form-control">
                                        <option @if(request()->order == 1 ) selected @endif  value="1">Serial</option>
                                        <option @if(request()->order == 2 ) selected @endif  value="2">Category</option>
                                        <option @if(request()->order == 3 ) selected @endif  value="3">Code</option>
                                        <option @if(request()->order == 4 ) selected @endif  value="4">Date Added</option>
                                        <option @if(request()->order ==5 ) selected @endif  value="5">Date Closing</option>
                                        <option @if(request()->order == 6 ) selected @endif  value="6">Status</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2 form-group">
                                    <label>Sort</label>
                                    <select name="sort" class="form-control">
                                        <option  @if(request()->sort == 'ASC'   ) selected @endif  value="ASC">Ascending</option>
                                        <option @if(request()->sort == 'DESC' || empty(request()->sort ) ) selected @endif  value="DESC">Descending</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2 form-group">
                                    <label class="d-block">&nbsp;</label>
                                    <button class="btn btn-primary btn-sm">Search</button>
                                </div>
                                
                            </div>
                            
                        </form>
                        
                        <table class="table table-bordered table-responsive-lg table-striped mt-5">
                            
                            <thead>
                                <tr>
                                    <th style="width: 10%">Actions</th>
                                    <th style="width: 5%">Serial</th>
                                    <th style="width: 10%">Tender Type</th>
                                    <th style="width: 10%">Tender No</th>
                                    <th>Name</th>
                                    <th style="width: 10%">Added</th>
                                    <th style="width: 15%">Closing</th>
                                    <th style="width: 10%">Status</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                
                                @foreach($tenders as $tender)
                                
                                <tr>
                                    <td>
                                        @if(auth()->user()->isPermitted('Tenders','Update'))
                                        <a class="color-coolblue ml-2" href="{{route('tenders.edit',$tender->id)}}" data-toggle='tooltip'
                                            title="Edit Tender">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        
                                        @endif
                                        
                                      
                                        <a class="color-purple ml-2" href="{{route('tenders.show',$tender->id)}}" data-toggle='tooltip'
                                            title="View Tender">
                                            <i class="fa fa-binoculars"></i>
                                        </a>
                                        
                                        @if(auth()->user()->isPermitted('Tenders','Update'))
                                        <a class="color-red ml-2" href="javascript:;" data-toggle='tooltip' ng-click="openTenderItems({{$tender->id}})"
                                            title="Manage Items">
                                            <i class=" fa fa-list"></i>
                                        </a>
                                        @endif
                                    
                                        
                                    </td>
                                    <td>{{$tender->id}}</td>
                                    <td>{{$tender->category->name}}</td>
                                    <td>{{$tender->code}}</td>
                                    <td>{{$tender->name}}</td>
                                    <td>{{date('d-m-Y' , strtotime($tender->dateAdded))}}</td>
                                    <td>{{date('d-m-Y h:i A' , strtotime($tender->dateClosing))}}</td>
                                    <td>{{$tender->statusText}}</td>
                                </tr>
                                
                                @endforeach
                                
                            </tbody>
                            
                        </table>

                        {{$tenders->links()}}
                        
                        <input type="hidden" ng-init="init()" />
                        
                        <script type="text/ng-template" id="tender-items.html">
                            
                            <div class="modal-header">
                                <h3 class="modal-title" >Tender Items - @{{tenderNo}}</h3>
                            </div>
                            <div class="modal-body" >
                                <form name='dataForm'>
                                 <h5>Modify Tender Items</h5>
                                    <div class='row'>
                                        
                                        <div class='col-md-5'>
                                            <div class="form-group">
                                            
                                            
                                                <input type='file' accept='text/plain' placeholder='Tender item list'
                                                    name='itemList' id='itemList' class='form-control
                                                    '
                                                    onchange='angular.element("#itemList").scope().fileChanged(this) ' />
                                            
                                            </div>
                                        </div>
                                        
                                        <div class='col-md-5'>
                                            <div class="form-group">
                                            
                                            
                                                <div class='custom-control custom-radio custom-control-inline'>
                                                    <input type='radio' name='operation' id='append' ng-value='1' checked
                                                        class='custom-control-input' ng-init='form.operation=1'
                                                        ng-model='form.operation' />
                                                        <label class='custom-control-label' for='append'>Append</label>
                                                </div>
                                            
                                                <div class='custom-control custom-radio custom-control-inline'>
                                                    <input type='radio' name='operation' id='overwrite' ng-value='2' class='custom-control-input'
                                                        ng-model='form.operation' />
                                                        <label class='custom-control-label' for='overwrite'>Replace</label>
                                                </div>
                                            
                                            </div>
                                        </div>
                                        
                                        <div class='col-md-2'>
                                            <button ng-init='url="{{route('tenders.index')}}"' ng-click='upload()' class='btn btn-sm btn-primary'>Upload</button>
                                        </div>

                                    </div>
                                </form>
                                
                                
                                <div class='row mt-5'>
                                
                                    <div class='col-md-12'>
                                        <div class='row'>
                                            <div class='col-md-6'>
                                            
                                                <select id="itemListSearch" name="itemListSearch" class='form-control' ng-model="obj.code" name="itemID"
                                                    >
                                                    <option value=''>Select</option>
                                                </select>
                                            
                                                
                                            </div>
                                           <div class='col-md-4'> <button ng-click='addNew()' type='button' class='btn btn-info btn-sm'>Add New</button> </div>
                                        </div>
                                        
                                        <div class='row mt-5'>
                                        
                                            <div class='col-md-4 code-set' ng-repeat='item in data'>
                                                <div>
                                                    <span><strong ng-bind='item.code'></strong></span> 
                                                    <span ><a class="color-red" ng-click='remove(item)' href='javascript:;'>Remove</a></span>
                                                    <p><small ng-bind='item.description'></small></p>
                                                </div>
                                            </div>
                                        
                                        </div>
                                    </div>
                                
                                
                                </div>
                                <input type="hidden" ng-init="url='{{route('tenders.index')}}';init('{{route("item-search")}}')" />
                                
                            </div>
                            <div class="modal-footer">
                              <!--  <button class="btn btn-primary" type="button" ng-click="ok()">OK</button> -->
                                <button class="btn btn-light-custom" type="button" ng-click="cancel()">Close</button>
                            </div>
                            
                        </script>
                        
                    </div>
                </div>
    </div>
</div>
@endsection


@section('script')
    <script src="{{url('/assets/plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{ url('assets/plugins/datepicker/datepicker.min.js') }}"></script>
    <script src="{{ url('assets/plugins/datepicker/i18n/datepicker.en.js') }}"></script>
    <script src="{{ url('js/datatables.js') }}"></script>
    <script src="{{ url('js/angular/tenders.js?20200918') }}"></script>
    
   

@endsection

@section('style')

<link href="{{ url('/css/datatables.css') }}" rel="stylesheet" />
<link href="{{ url('assets/plugins/datepicker/datepicker.min.css') }}" rel="stylesheet" />
<link href="{{ url('/assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />

<style>
    
    .code-set{
        padding: 5px;
    }
    
    .code-set div{
        
        border:1px solid #c4c4c4;
        padding:3px;
        
    }
    
</style>

@endsection