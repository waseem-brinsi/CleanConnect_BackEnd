<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class registerRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'phoneNumber' => 'required|unique:users,phoneNumber',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:5|confirmed',
            'role' => 'nullable|string|in:user,admin',
            'address' => 'nullable|string|max:255',



        ];
    }
/*    public function messages()
    {
        return [
            'name.required' => 'The name field is required x.',
            'email.required' => 'The email field is required x.',
            'email.email' => 'The email must be a valid email address x.',
            'email.unique' => 'The email has already been taken x.',
            'password.required' => 'The password field is required x.',
            'password.min' => 'The password must be at least 8 characters x.',
            'password.confirmed' => 'The password confirmation does not match x.',
        ];

    }*/
}
