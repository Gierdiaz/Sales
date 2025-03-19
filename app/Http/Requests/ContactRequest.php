<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'name'   => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'phone'  => 'required|string|regex:/^(\+55)?\d{10,11}$/',
            'email'  => 'required|email|max:255',
            'number' => 'nullable|string|max:10|regex:/^[a-zA-Z0-9\-\/]+$/',
            'cep'    => 'required|string|regex:/^\d{5}-?\d{3}$/',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.regex'    => 'O nome só pode conter letras, espaços e traços.',
            'name.max'      => 'O nome não pode ter mais que 255 caracteres.',

            'phone.required' => 'O campo telefone é obrigatório.',
            'phone.regex'    => 'O telefone deve conter apenas números e pode iniciar com "+". Exemplo: +5511998765432',

            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email'    => 'Forneça um endereço de e-mail válido.',
            'email.max'      => 'O e-mail não pode ter mais que 255 caracteres.',

            'number.string' => 'O número deve ser um texto.',
            'number.max'    => 'O número não pode ter mais que 10 caracteres.',
            'number.regex'  => 'O número só pode conter letras, números, traços ou barras.',

            'cep.required' => 'O campo CEP é obrigatório.',
            'cep.regex'    => 'O CEP deve estar no formato 00000-000 ou 00000000.',
        ];
    }
}
