<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required'],
            'surname' => ['sometimes'],
            'role_id' => ['required', 'exists:roles,id'],
            'phone' => ['sometimes'],
            'email' => ['required', 'unique:users,email'],
            'password' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Імя',
            'surname' => 'Прізвище',
            'role_id' => 'Роль',
            'phone' => 'Телефон',
            'email' => 'Почта',
            'password' => 'Пароль',
        ];
    }
}
