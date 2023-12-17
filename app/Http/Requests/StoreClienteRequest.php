<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cpf'           => 'unique:clientes',          
            'email'         => 'email|unique:users', 
            'prefeitura_id' =>'required'
        ];
    }
    public function messages()
{
    return [
      
        'cpf.unique' => 'O cpf informado já está em uso',
        'email.email' => 'O email informado não é válido',
        'email.unique' => 'O email informado já está em uso',
        'required' => 'O campo :attribute deve ser preenchido' 
    ];
}

}
