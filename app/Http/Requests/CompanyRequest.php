<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CompanyRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            return [
                'cnpj' => 'required|string|max:14|unique:companies,cnpj',
                'trade_name' => 'string|max:255|unique:companies,trade_name',
                'legal_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:255',
                'size' => 'required|string|in:MEI,ME,EPP,EMP,EG',
                'user_id' => 'required|integer|exists:users,id'
            ];
        } else {
            return [
                'cnpj' => 'string|max:14',
                'trade_name' => 'string|max:255|unique:companies,trade_name',
                'legal_name' => 'string|max:255|unique:companies,legal_name',
                'email' => 'email|max:255',
                'phone' => 'string|max:20',
                'address' => 'string|max:255',
                'size' => 'string|in:MEI,ME,EPP,EMP,EG',
                'user_id' => 'integer|exists:users,id'
            ];
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cnpj.required' => 'CNPJ is required',
            'cnpj.max' => 'CNPJ must have at most 14 characters',
            'cnpj.unique' => 'CNPJ must be unique',
            'trade_name.max' => 'Trade name must have at most 255 characters',
            'trade_name.unique' => 'Trade name must be unique',
            'legal_name.required' => 'Legal name is required',
            'legal_name.max' => 'Legal name must have at most 255 characters',
            'legal_name.unique' => 'Legal name must be unique',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email must have at most 255 characters',
            'phone.required' => 'Phone is required',
            'phone.max' => 'Phone must have at most 20 characters',
            'address.required' => 'Address is required',
            'address.max' => 'Address must have at most 255 characters',
            'size.required' => 'Size is required',
            'size.in' => 'Size must be one of MEI, ME, EPP, EMP, EG',
            'user_id.required' => 'User ID is required',
            'user_id.integer' => 'User ID must be an integer',
            'user_id.exists' => 'User ID must exist in the users table',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'cnpj' => 'CNPJ',
            'trade_name' => 'Trade name',
            'legal_name' => 'Legal name',
            'email' => 'Email',
            'phone' => 'Phone',
            'address' => 'Address',
            'size' => 'Size',
            'user_id' => 'User ID',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        $errors = $validator->errors()->toArray();
        throw new HttpResponseException(response()->json(['errors' => $errors], 422));
    }
}
