@extends('layouts.main')

@section('title', 'Tenders')
@section('subtitle', 'View')

@section ('breadcrumbs' )
     <li class="breadcrumb-item"><a href="{{route('tenders.index')}}">Tenders</a></li>
@endsection

@section('rightbar-content')

<div class="row" >

    <div class="col-lg-12">
                <div class="card m-b-30 border-light">
                    <div class="card-header border-light ">
                        <h3 class="card-title ">View Tender</h3>
                        <div class="widgetbar">
                            <button disabled class="btn btn-secondary" type="button" >New</button>
                            <button disabled="" class="btn btn-secondary" type="button" >Save</button>
                            <button  disabled  class="btn btn-secondary" type="button" >Delete</button>
                        </div>
                    </div>
                    <div class="card-body pt-5">
                        
                       <div class='row'>
                            <div class='col-md-5 offset-md-1'>
                                <div class="group">
                                    <label>Tender Serial</label>
                                    <div>{{$tender->id}}</div>
                                </div>

                                <div class="group">
                                    <label>Code</label>
                                    <div>{{$tender->code}}</div>
                                </div>

                                <div class="group">
                                    <label>Name</label>
                                    <div>{{$tender->name}}</div>
                                </div>
                                
                                <div class="group">
                                    <label>Added Date</label>
                                    <div>{{date('d-m-Y' , strtotime($tender->dateAdded))}}</div>
                                </div>
                                
                                <div class="group">
                                    <label>Closing Date</label>
                                    <div>{{date('d-m-Y h:i A' , strtotime($tender->dateClosing))}}</div>
                                </div>
                                
                                <div class="group">
                                    <label>Posted By</label>
                                    <div>{{$tender->postedBy->fullName}}</div>
                                </div>
                                
                                <div class="group">
                                    <label>Status</label>
                                    <div>{{$tender->statusText}}</div>
                                </div>
                                
                                
                                
                                
                       </div>
                           
                           <div class="col-md-4">
                               
                               <div class="group">
                                    <label>Tender File</label>
                                            <div><a class="color-red" href="{{route('download-tender' , $tender->id)}}">
                                                    <i class="fa fa-file-pdf-o"></i> Download File
                                        </a></div>
                                </div>
                               
                               @if(count($tender->items))
                               
                               <h5>Tender Items</h5>
                               
                               <table class="table table-bordered">
                                   
                                   <tr>
                                       <th style="width:10%">SR Number</th>
                                       <th style="width:10%">ITCode</th>
                                       <th>Description</th>
                                   </tr>
                                   
                                   @foreach($tender->items as $item)
                                   
                                   @if($item->item)
                                   
                                   <tr>
                                       <td>{{$item->item->code}}</td>
                                       <td>{{$item->item->ITCode}}</td>
                                       <td>{{$item->item->description}}</td>
                                   </tr>
                                   
                                   @endif
                                   
                                   @endforeach
                                   
                                   
                                   
                               </table>
                               
                               @endif
                               
                           </div>
                    </div>
                        
                    </div>
                </div>
    </div>
</div>
@endsection




    