<?php

namespace App\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

class Address implements Arrayable
{
    public function __construct(
        public string $logradouro,
        public string $complemento,
        public string $unidade,
        public string $bairro,
        public string $localidade,
        public string $uf,
        public string $estado,
        public string $regiao,
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
            'logradouro' => $this->logradouro,
            'complemento' => $this->complemento,
            'unidade' => $this->unidade,
            'bairro'     => $this->bairro,
            'localidade' => $this->localidade,
            'uf'         => $this->uf,
            'estado'     => $this->estado,
            'regiao'     => $this->regiao,
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
            $data['logradouro'] ?? '',
            $data['complemento'] ?? '',
            $data['unidade'] ?? '',
            $data['bairro'] ?? '',
            $data['localidade'] ?? '',
            $data['uf'] ?? '',
            $data['estado'] ?? '',
            $data['regiao'] ?? '',
            $data['ibge'] ?? '',
            $data['gia'] ?? '',
            $data['ddd'] ?? '',
            $data['siafi'] ?? ''
        );
    }
}
