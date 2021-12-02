    @extends('layouts.main')

@section('title', 'Tenders')
@section('subtitle', isset($model) ? 'Edit' : 'New')

@section ('breadcrumbs' )
     <li class="breadcrumb-item"><a href="{{route('tenders.index')}}">Tenders</a></li>
@endsection

@section('rightbar-content')

<div class="row" ng-controller="FormController">

    <div class="col-lg-12">
                <div class="card m-b-30 border-light">
                    <div class="card-header border-light ">
                        <h3 class="card-title ">{{isset($model) ? 'Edit Tender' : 'New Tender'}}</h3>
                        <div class="widgetbar">
                            <button disabled class="btn btn-secondary" type="button" >New</button>
                            <button ng-click='save()' class="btn btn-info" type="button" >Save</button>
                            <button ng-click="delete()" @if(!isset($model) || !auth()->user()->isPermitted('Tenders','Delete')) disabled @endif class="btn btn-danger" type="button" >Delete</button>
                        </div>
                    </div>
                    <div class="card-body pt-5">
                        
                        <form name="dataForm" novalidate>
                            
                            <div class="row">
                                
                                <div class="col-md-4 offset-md-1">
                                    
                                    <div class="form-group">
                                        <label class="required" for="category_id">Tender Type</label>
                                        <select required="required" class="form-control" name="category_id"
                                                
                                                ng-class="{'is-invalid' : submitted && dataForm.category_id.$invalid}"
                                                id="category_id" ng-model="data.category_id">
                                            
                                            <option value="">Select</option>
                                            @foreach($categories as $category)
                                            <option ng-value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                            
                                            
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        
                                        <label for="code" class="required">Code</label>
                                        <input type="text" maxlength="30" name="code" id="code" ng-model='data.code' 
                                               class="form-control"
                                               required ng-class="{'is-invalid' : submitted && dataForm.code.$invalid}" />
                                    </div>
                                    
                                    <div class="form-group">
                                        
                                        <label for="name" class="required">Name</label>
                                        <input type="text" maxlength="255" name="name" id="name" ng-model='data.name' 
                                               class="form-control"
                                               required ng-class="{'is-invalid' : submitted && dataForm.name.$invalid}" />
                                    </div>
                                    
                                    <div class="form-group">
                                        
                                        <label for="dateAdded" class="required">Date Added</label>
                                        <div class="input-group">
                                        <input type="text" maxlength="10"  readonly name="dateAdded" id="dateAdded" ng-model='data.dateAdded' 
                                               class="form-control date"
                                               required ng-class="{'is-invalid' : submitted && dataForm.dateAdded.$invalid}" />
                                        
                                        <div class="input-group-append">
                                            <label class="input-group-text"><i class=" fa fa-calendar"></i></label>
                                        </div>
                                        
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        
                                        <label for="dateClosing" class="required">Closing Date</label>
                                        <div class="input-group">
                                        <input type="text" maxlength="20"  name="dateClosing" id="dateClosing" ng-model='data.dateClosing' 
                                               class="form-control time"
                                               required ng-class="{'is-invalid' : submitted && dataForm.dateClosing.$invalid}" />
                                        
                                         
                                        <div class="input-group-append">
                                            <label class="input-group-text"><i class=" fa fa-calendar"></i></label>
                                        </div>
                                        
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <div class="col-md-4">
                                    
                                    <div class="form-group">
                                        
                                        <label class="@if(!isset($model)) required @endif">Tender File</label>
                                        <input accept="application/pdf" type="file" name="tenderFile"
                                               onchange="angular.element('#tenderFile').scope().tenderFileChanged(this)"
                                               id="tenderFile" class="form-control" />
                                        
                                    </div>
                                    
                                    <div class="form-group mb-5" ng-cloak ng-if="data.tenderFile">
                                        
                                        <a class="color-red" ng-href="{{route('tenders.index')}}/download/@{{data.id}}"> <i class="fa fa-file-pdf-o"></i> Tender File</a>
                                        
                                    </div>
                                    
                                    @if(!isset($model))
                                     <div class="form-group">
                                        
                                        <label>Tender Items</label>
                                        <input accept="text/plain" type="file" name="tenderItems"
                                               onchange="angular.element('#tenderItems').scope().tenderItemsChanged(this)"
                                               id="tenderItems" class="form-control" />
                                        
                                    </div>
                                    @endif
                                    
                                    <div class="form-group">
                                        
                                        <label>Status</label>
                                        <select  name="status" required id="status" ng-model="data.status"
                                            class="form-control" >
                                            
                                            <option value="">Select</option>
                                            <option ng-value="0">Open</option>
                                            <option ng-value="1">Closed</option>
                                            
                                        </select>
                                        
                                    </div>
                                    
                                    
                                    
                                </div>
                                
                                
                            </div>
                             <input type="hidden" ng-init="url='{{route('tenders.index')}}'" />
                            @if(isset($model))
                                <input type="hidden" ng-init="init({{$model}})" />
                            @endif
                        </form>
                        

                        
                    </div>
                </div>
    </div>
</div>
@endsection


@section('script')

    <script src="{{ url('assets/plugins/datepicker/datepicker.min.js') }}"></script>
    <script src="{{ url('assets/plugins/datepicker/timepicker.js') }}"></script>
    <script src="{{ url('assets/plugins/datepicker/i18n/datepicker.en.js') }}"></script>
    <script src="{{ url('js/angular/tenders.js') }}"></script>
    
    <script>
    
        $(() => { 
       $('.date').datepicker({
           format : 'dd-mm-yyyy',
           forceParse:false,autoClose : true,
           todayHighlight:true,
           todayBtn:true,
           'language' : 'en'
       }).on('selectDate' , (e)=>{ $(e.target).trigger('change') });
       
       $('.time').datepicker({
           format : 'dd-mm-yyyy',
           forceParse:false,autoClose : true,
           todayHighlight:true,
           todayBtn:true,
           timepicker: true,
           'language' : 'en'
       }).on('selectDate' , (e)=>{ 
           $(e.target).trigger('change') ;
          // console.log('Here');
       });
   
    
    })
    
    </script>

@endsection

@section('style')

<link href="{{ url('assets/plugins/datepicker/datepicker.min.css') }}" rel="stylesheet" />

@endsection