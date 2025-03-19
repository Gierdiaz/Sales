<?php

namespace App\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

class Address implements Arrayable
{
    public function __construct(
        public string $cep,
        public string $logradouro,
        public string $bairro,
        public string $localidade,
        public string $uf,
        public string $ibge,
        public string $gia,
        public string $ddd,
        public string $siafi
    ) {
    }

    /**
     * Converte o objeto Address para um array.
     */
    public function toArray(): array
    {
        return [
            'cep'        => $this->cep,
            'logradouro' => $this->logradouro,
            'bairro'     => $this->bairro,
            'localidade' => $this->localidade,
            'uf'         => $this->uf,
            'ibge'       => $this->ibge,
            'gia'        => $this->gia,
            'ddd'        => $this->ddd,
            'siafi'      => $this->siafi,
        ];
    }

    /**
     * Converte um array para um objeto Address.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['cep'] ?? '',
            $data['logradouro'] ?? '',
            $data['bairro'] ?? '',
            $data['localidade'] ?? '',
            $data['uf'] ?? '',
            $data['ibge'] ?? '',
            $data['gia'] ?? '',
            $data['ddd'] ?? '',
            $data['siafi'] ?? ''
        );
    }
}
