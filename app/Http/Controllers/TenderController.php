<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{Tender , TenderItem,Item};
use App\Http\Requests\TenderRequest;
use DB;

class TenderController extends Controller {

    //

    public function index(Request $request) {

        $this->isAllowed('Tenders', 'View');

        $tenders = Tender::query();

        $tenders->when($request->code, function($query) use($request) {

            $query->where('code', 'LIKE', $request->code . '%');
        });
        
        $tenders->when($request->itemCode, function($query) use($request) {

            $item = Item::where('code' , $request->itemCode)->first();
            
            if($item):
            $query->whereHas('items' , function($q) use($item){
                
                $q->where('item_id' , $item->id);
                
            });
            endif;
            
        });
        
        
        $tenders->when($request->name, function($query) use($request) {
            $query->where('name', 'like', '%' . $request->name . '%');
        });
        
        $tenders->when( is_numeric($request->status), function($query) use($request) {
            $query->where('status', $request->status );
        });
        
        $tenders->when(!empty($request->dateFrom) && !empty($request->dateTo), function($query) use($request) {

            $from = date('Y-m-d', strtotime($request->dateFrom));
            $to = date('Y-m-d', strtotime($request->dateTo));

            $query->whereBetween('dateClosing', [$from, $to]);
        });
        $tenders->when($request->category, function($query) use($request) {

            $query->where('category_id', $request->category);
        });


        $sortOrder = 'DESC';



        if ($request->sort == 'ASC'):
            $sortOrder = 'ASC';
        elseif ($request->sort == 'DESC'):
            $sortOrder = 'DESC';
        endif;



        $columns = ['id', 'category_id', 'code', 'dateAdded', 'dateClosing', 'status'];

        if ($request->order > 0 && $request->order <= count($columns)):
            $tenders->orderBy($columns[$request->order - 1], $sortOrder);
        // dd($sortOrder);
        else:
            $tenders->orderBy('id' , 'desc');
        endif;


        return view('tenders.index', ['tenders' => $tenders->paginate(50)->appends($request->all())]);
    }

    public function create() {

        $this->isAllowed('Tenders', 'Create');

        $categories = \App\Category::active()->orderBy('name')->get();

        return view('tenders.form', compact('categories'));
    }

    public function edit(Tender $tender) {

        $this->isAllowed('Tenders', 'Create');

        $categories = \App\Category::active()->orderBy('name')->get();
        $model = $tender->id;

        return view('tenders.form', compact('categories', 'model'));
    }

    public function store(TenderRequest $request) {

        $this->isAllowed('Tenders', 'Create');

        $tender = new Tender($request->all());

        //Converting date to seconds
        $dateAdded = strtotime($request->dateAdded);
        $dateClosing = strtotime($request->dateClosing);

        //Date closing should be greater than date added
        if ($dateAdded > $dateClosing):
            return json_error('Supplied dates are invalid.');
        endif;

        
        //Converting dates to DB format
        $tender->dateAdded = date('Y-m-d' , $dateAdded );
        $tender->dateClosing = date('Y-m-d H:i:s' ,$dateClosing);
        
        //Thender was created by this user
        $tender->createdBy = auth()->id();

        DB::beginTransaction();

        try {

            //Generate a unique file name
            $tender->tenderFile = uniqid(str_slug($tender->code) . '-', true) . '.pdf';

            //If no directory exists in the storage directory, create one
            $path = storage_path('tenders');

            if (!file_exists($path)):
                mkdir($path, 777, true);
            endif;
            

            $request->file('tenderFileX')->move($path, $tender->tenderFile);

            $tender->save();



            if ($request->hasFile('tenderItemsFile')):
                if (!$this->updateItems($request, $tender)):
                    $tender->deleteFile();
                    DB::rollBack();
                    return json_error('Unable to update tender items.');
                endif;
            endif;

            DB::commit();

            return json_response('Tender was created successfully.', route('tenders.edit', $tender->id));
        } catch (\Exception $ex) {

            log_error_message($ex);
            DB::rollBack();
            return json_error(['Unable to create the tender.']);
        }
    }

    public function destroy(Request $request, Tender $tender) {

        $this->isAllowed('Tenders', 'Delete');

        \App\TenderItem::where('tender_id', $tender->id)->delete();

        $tender->deleteFile();
        $tender->delete();

        return json_response('Tender was deleted successfully', route('tenders.index'));
    }

    public function update(TenderRequest $request, Tender $tender) {

        $this->isAllowed('Tenders', 'Update');
        
        
        if ($request->hasFile('tenderItemsFile')):
               $response = $this->validateItemFile($request);
               if(!empty($response)):
                   return json_error($response);
               endif;
        endif;

        $tender->fill($request->all());

        $dateAdded = strtotime($request->dateAdded);
        $dateClosing = strtotime($request->dateClosing);

        if ($dateAdded > $dateClosing):
            return json_error('Supplied dates are invalid.');
        endif;

        $tender->dateAdded = date('Y-m-d',$dateAdded);
        $tender->dateClosing = date('Y-m-d H:i:s',$dateClosing);
        //$tender->createdBy = auth()->id();

        DB::beginTransaction();

        $fileUploaded = null;
        $oldFile = null;

        try {



            $path = storage_path('tenders');

            if (!file_exists($path)):
                mkdir($path, 777, true);
            endif;



            if ($request->hasFile('tenderFileX')):

                $oldFile = $tender->tenderFile;

                $tender->tenderFile = uniqid(str_slug($tender->code) . '-', true) . '.pdf';

                $request->file('tenderFileX')->move($path, $tender->tenderFile);
                $fileUploaded = $path . '/' . $tender->tenderFile;
            endif;

            $tender->save();



           /* if ($request->hasFile('tenderItemsFile')):
                if (!$this->updateItems($request, $tender)):
                    if ($fileUploaded):
                        $tender->deleteFile();
                    endif;
                    DB::rollBack();
                    return json_error('Unable to update tender items.');
                endif;
            endif; */

            if ($oldFile):
                $tender->deleteFile($oldFile);
            endif;

            DB::commit();

            return json_response('Tender was updated successfully.', route('tenders.edit', $tender->id));
        } catch (\Exception $ex) {

            if ($fileUploaded):
                @unlink($fileUploaded);
            endif;

            log_error_message($ex);
            DB::rollBack();
            return json_error(['Unable to update the tender.']);
        }
    }

    public function show(Tender $tender) {

        if (request()->has('json')):
            return response()->json(['data' => $tender]);
        endif;

        return view('tenders.view', compact('tender'));
    }

    private function updateItems(Request $request, Tender $tender, $replace = true) {
        
     //    $this->isAllowed('Tenders', 'Update');

        $handle = $request->file('tenderItemsFile')->openFile();
        
        $items = [];

        if ($replace):
            //Remove all existing items
            \App\TenderItem::where('tender_id', $tender->id)->delete();
        endif;

        try {
            while ($code = @$handle->fgetss()):

                $item = \App\Item::where('code', trim($code))->first();

                if ($item):
                    $items[] = $item->id;
                    $ti = \App\TenderItem::firstOrNew(['tender_id' => $tender->id, 'item_id' => $item->id]);
                    $ti->save();

                endif;

            endwhile;
            
            log_event('Items were added to tender through a file.' , $items , 'tenders' , $tender->id);

            return true;
        } catch (\Exception $ex) {
            log_error_message($ex);
            return false;
        }
    }

    public function download(Tender $tender) {

        $file = storage_path('tenders/' . $tender->tenderFile);

        if (file_exists($file)):
            return response()->download($file);
        else:
            abort(404);
        endif;
    }

    private function validateItemFile(Request $request){
        //$request->file('tenderItemsFile')->move(storage_path('logs'));
        $file = $request->file('tenderItemsFile')->openFile();
        
      
        
        $notFound = [];
        
        while($line = @$file->fgetss()):
            
             $item = Item::where('code', trim($line))->where('active',1)->first();

                if (!$item):
                    
                  $notFound[] =  $line;
                  
                endif;
            
        endwhile;
        
        
        
        if(count($notFound) > 0):
            return  '[' . implode(', ' , $notFound) . '] number(s) do not available in the registry.';
        else:
            return false;
        endif;
        
    }
    
    public function uploadFile(Request $request) {

         $this->isAllowed('Tenders', 'Update');
         
          if ($request->hasFile('tenderItemsFile')):
               $response = $this->validateItemFile($request);
               if(!empty($response)):
                   return json_error($response);
               endif;
        endif;
        
        $tender = Tender::findOrFail($request->tenderID);
        $replace = $request->operation == 1 ? false : true;

        DB::beginTransaction();

        if ($this->updateItems($request, $tender, $replace)):

            DB::commit();
            return json_response('Tender items was updated successfully');

        else:
            DB::rollBack();
            return json_error('Unable update items.');

        endif;
    }
    
    public function getTenderItems( $tender){
        
         $tender = Tender::findOrFail($tender);
        
         $this->isAllowed('Tenders', 'Update');
         
         $items = $tender->items->pluck('item_id');
         
       //  dd($items);
         
         return response()->json(['data' => \App\Item::whereIn('id' , $items)->get() , 'tenderNo' => $tender->code]);
        
    }
    
    public function addTenderItem(Request $request){
        
        $this->validate($request, [
     #       'code' => 'required|exists:items,code',
            'tenderID' => 'required|exists:tenders,id'
        ]);
        
        $item = Item::where('code' , $request->code)->where('active',1)->first();
        
        if(!$item):
            return json_error('Selected item is not available');
        endif;
        
        $tenderItem = TenderItem::firstOrNew([ 'tender_id'=>$request->tenderID , 'item_id' => $item->id ]);
        
        $tenderItem->save();
        
        log_event('Item added to the tender' , $tenderItem->toArray(),'tender_items' , $tenderItem->id);
        
        return json_response('Item was added to the tender.');
        
    }
    
     public function removeTenderItem(Request $request){
        
        $this->validate($request, [
            'itemID' => 'required|exists:items,id',
            'tenderID' => 'required|exists:tenders,id'
        ]);
        
        $item = Item::findOrFail( $request->itemID); //->where('tender_id',$request->tenderID)->get();
        $tenderItem = TenderItem::where('item_id' , $item->id)->where('tender_id',$request->tenderID)->firstOrFail();
        
        //$tenderItem = TenderItem::firstOrNew([ 'tender_id',$request->tenderID , 'item_id' => $item->id ]);
        try{
            $tenderItem->delete();
            
             log_event('Item deleted from tender' , $tenderItem->toArray(),'tender_items' , $tenderItem->id);
            
        } catch (\Exception $ex) {
            log_error_message($ex);
            return json_error('Unable to delete the item.');
        }
        
        
        return json_response('Item was removed from the tender.');
        
    }
    
    public function searchTenderItem(Request $request){
        
        $term = $request->term;

        if (strlen($term) < 3):
            return [];
        endif;

        $items = Item::query();

       // if (is_numeric($term)):
            $items->where('code', $term);
       // endif;

        $items->orWhere(function($query) use($term) {
            foreach (explode(' ', $term) as $t):
                $query->where('description', 'like', '%' . $t . '%');
            endforeach;
        }
        );

        $items->select('code as id', 'description as text')
                ->orderBy('description')->get();
        
        $data = [];
        
        foreach( $items->get() as $p):
            
            
            $data[] = ['id' => str_pad(  $p->id ,8 , '0' ,STR_PAD_LEFT  ) , 'text' => $p->text];
            
        endforeach;
        

        return response()->json($data);
        
    }

}
