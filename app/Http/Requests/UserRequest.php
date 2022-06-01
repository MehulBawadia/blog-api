<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $passwordValidation = 'required|string';
        $roleValidation = 'required|in:admin-user,manager-user,regular-user';
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $passwordValidation = 'nullable';
            $roleValidation = 'nullable';
        }

        return [
            'name' => 'required|max:255',
            'email' => 'required|email:filter|unique:users,email,'. $this->user,
            'password' => $passwordValidation,
            'role' => $roleValidation,
        ];
    }

    /**
     * Display a failed validation error messages.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
