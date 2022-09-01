<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthRegisterRequest extends FormRequest
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
        return [
            'first_name' => ['required', 'max:25'],
            'last_name' => ['required', 'max:50'],
            'username' => ['required', 'max:25', 'unique:users,username'],
            'gender' => ['nullable', Rule::in(User::GENDERS)],
            'photo' => ['nullable', 'mimes:png,jpg,webp', 'max:2500'],
            'email' => ['required', 'unique:users,email', 'email'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }
}
