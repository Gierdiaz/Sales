<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ViaCepService
{
    public function getAddressByCep(string $cep)
    {
        $response = Http::get('https://viacep.com.br/ws/' . $cep . '/json/');

        if ($response->failed() || isset($response->json()['erro'])) {
            throw new \Exception("CEP inválido ou não encontrado");
        }

        return $response->json();
    }
}
