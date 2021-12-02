<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
        $rules = [
            'name' => 'required|max:255',
            'code' => 'required|unique:tenders,code',
            'dateAdded' => 'required|date',
            'dateClosing' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'tenderItemsFile' => 'nullable|file|mimetypes:text/plain|max:4096'
        ];
        
        if($this->isMethod('POST')):
            
            $rules['tenderFileX'] = 'required|file|mimetypes:application/pdf|max:4096';
         else:
             
              $rules['tenderFileX'] = 'nullable|mimetypes:application/pdf|max:4096';
              $rules['code'] = 'required|unique:tenders,code,' . $this->id;
             
        endif;
        
        return $rules;
        
    }
}
