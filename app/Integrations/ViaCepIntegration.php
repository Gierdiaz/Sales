<?php

namespace App\Integrations;

use App\ValueObjects\Address;
use Illuminate\Support\Facades\{Http, Log};

class ViaCepIntegration
{
    public function getAddressByCep(string $cep)
    {
        $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");

        if ($response->failed() || isset($response->json()['erro'])) {
            Log::error('Erro ao buscar endereço pelo CEP', [
                'cep'      => $cep,
                'details'  => $response->body(),
                'response' => $response->json(),
            ]);

            throw new \Exception("CEP inválido ou não encontrado");
        }

        return Address::fromArray($response->json());
    }
}
