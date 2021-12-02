<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest {

    const REGEX = '@^(?=.*[0-9]+)(?=.*[a-z]+)(?=.*[A-Z]+).{6,20}$@';
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
       // var_dump($this->all());
        
        $rules = [
            'firstName' => 'required|max:20',
            'lastName' => 'required|max:20',
            'email' => 'required|max:100|email|unique:users,email',
            'username' => 'required|max:100|unique:users,username',
            'role_id' => 'required|exists:roles,id',
            'imageFile' => 'nullable|image|max:2048'
        ];


        if ($this->isMethod('post')):

            $rules['password'] = 'required|min:6|max:20|regex:' . self::REGEX;
            $rules['confirm'] = 'required|min:6|max:20|same:password';
            
            
        elseif ($this->isMethod('put')):
            $rules['email'] = 'required|max:100|email|unique:users,email,' . $this->id;
            $rules['username'] = 'required|max:100|unique:users,username,' . $this->id;
            $rules['confirm'] = 'required_with:password|same:password|min:6|max:20|regex:' . self::REGEX;
        endif;
        
        return $rules;
    }
    
    public function messages(): array {
        //parent::messages();
        
        return [
            
            'password.regex' => 'Password should be at least 6 characters long should contain uppercase letter,lowercase letter and number',
            'confirm.regex'=>'Password should be at least 6 characters long should contain uppercase letter,lowercase letter and number'
            
        ];
        
    }

}
