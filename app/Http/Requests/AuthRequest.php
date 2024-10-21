<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('auth.register')) {
            return [
                'name'     => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ];
        }

        if ($this->routeIs('auth.login')) {
            return [
                'email'    => 'required|string|email|max:255',
                'password' => 'required|string|min:8',
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.regex'    => 'O nome só pode conter letras, espaços e traços.',
            'name.max'      => 'O nome não pode ter mais que 255 caracteres.',

            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email'    => 'Forneça um endereço de e-mail válido.',
            'email.max'      => 'O e-mail não pode ter mais que 255 caracteres.',
            'email.unique'   => 'Este e-mail já está em uso.',

            'password.required' => 'O campo senha é obrigatório.',
            'password.min'      => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'As senhas não correspondem.',
            'password.regex'     => 'A senha deve conter pelo menos uma letra maiúscula, uma letra minúscula, um número e um símbolo.',
        ];
    }
}
