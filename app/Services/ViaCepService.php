<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ViaCepService
{
    public function getAddressByCep(string $cep)
    {
        $response = Http::get('https://viacep.com.br/ws/' . $cep . '/json/');

        if ($response->failed() || isset($response->json()['erro'])) {
            Log::channel('cep')->error('Erro ao buscar endereço pelo CEP', [
                'cep' => $cep,
                'response' => $response->json(),
            ]);

            throw new \Exception("CEP inválido ou não encontrado");
        }

        return $response->json();
    }
}
