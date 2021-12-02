<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Price;
use App\Item;

class MainController extends Controller {

    //

    public function init() {

        $urlList = [
            'tenderCategories' => url()->temporarySignedRoute('api.tender-categories', now()->addMinutes(30)),
            'priceList' => url()->temporarySignedRoute('api.price-list', now()->addMinutes(30)),
            'downloadPriceList' => url()->temporarySignedRoute('api.download-price-list', now()->addMinutes(30)),
            'itemSearch' => url()->temporarySignedRoute('api.tender-items', now()->addMinutes(30)),
            'tenderSearch' => url()->temporarySignedRoute('api.tender-search', now()->addMinutes(30)),
        ];

        return response()->json($urlList);
    }

    public function getTenderCategories() {

        return response()->json(\App\Category::active()->orderBy('name')->get());
    }

    public function downloadPricelist() {

        $priceList = Price::orderBy('description')
                ->select('code', 'description', 'wholesalePrice', 'retailPrice', 'supplier', 'unit', 'created_at');

        $priceList = $priceList->get();

        $lastUpdated = date($priceList->first()->created_at->format('d-m-Y H:i'));


        $html = view('pdf.price-list', ['data' => $priceList, 'lastUpdated' => $lastUpdated]);

        $pdf = new \Mpdf\Mpdf();

        $pdf->WriteHTML($html, \Mpdf\HTMLParserMode::DEFAULT_MODE);

        $pdf->Output('price-list-' . date('Y-m-d') . '.pdf', \Mpdf\Output\Destination::DOWNLOAD);
    }

    public function getTenders(Request $request) {

        \App\Tender::$useTempURL = true;

        $tenders = \App\Tender::with(['category']);
        $offset = $request->offset ?? 0;
        $pageSize = 100;
        $item = null;



        switch ($request->direction):
            case 1:
                $offset += $pageSize;
                break;
            case -1 :
                $offset -= $pageSize;
                if ($offset < 0):
                    $offset = 0;
                endif;
                break;
        endswitch;

        if (!empty($request->itemID)):
            $item = Item::find($request->itemID);
            $tenders->whereHas('items', function($query) use($request) {
                $query->where('item_id', $request->itemID);
            });
        endif;

        if (!empty($request->tenderID)):
            $tenders->where('code', 'LIKE', '%' . $request->tenderID . '%');

        endif;

        if (!empty($request->tenderType)):
            $tenders->where('category_id', $request->tenderType);

        endif;

        if ($request->status != null):
            $tenders->where('status', $request->status);
        //logger()->info($request->status);
        endif;

        $sort = 'dateClosing';

        if (!empty($request->listBy)):

            switch ($request->listBy):

                case 1:
                    break;

                case 2:
                    $sort = 'code';
                    break;

            endswitch;

        endif;
        $total = $tenders->count();
        $tenders->orderBy($sort, 'DESC');
        $tenders->offset($offset)->limit($pageSize);

        $data = $tenders->get();

        $currentSize = $data->count();
        $prev = $offset > 0;
        $next = ($offset + $pageSize) < $total;


        return response()->json(['data' => $data, 'item' => $item,
                    'offset' => $offset, 'pageSize' => $pageSize, 'currentSize' => $currentSize,
                    'next' => $next, 'prev' => $prev, 'total' => $total]);
    }

    public function getItems(Request $request) {

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

        $items->select('id', 'description as text')
                ->orderBy('description')->get();

        return response()->json(array_values($items->get()->toArray()));
    }

    public function downloadTender($id) {

        $cookie = request()->cookie('data', null);


        $tender = \App\Tender::findOrFail($id);

        $file = storage_path('tenders/' . $tender->tenderFile);

        if (file_exists($file)):
            return response()->download($file);
        else:
            abort(404);
        endif;
    }

}
