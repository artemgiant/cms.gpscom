<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required'],
            'name' => ['required'],
            'surname' => ['sometimes'],
            'role_id' => ['required', 'exists:roles,id'],
            'phone' => ['sometimes'],
            'email' => ['required', 'exists:users,email'],
            'password' => ['sometimes'],
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
