<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
        
        $rules=['description' => 'required|max:255'];
        
        if($this->isMethod('post')):
            $rules['ITCode'] = 'required|max:30|unique:items,ITCode';
            $rules['code'] ='required|max:30|unique:items,code';
        else:
             $rules['ITCode'] = 'required|max:30|unique:items,ITCode,' . $this->id;
            $rules['code'] ='required|max:30|unique:items,code,' . $this->id;
        endif;
        
        return $rules;
        
    }
}
