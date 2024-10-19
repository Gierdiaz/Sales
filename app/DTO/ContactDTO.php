<?php

namespace App\DTO;

class ContactDTO
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $email,
        public string $number,
        public string $cep,
        public string $address
    ) {
    }
}
