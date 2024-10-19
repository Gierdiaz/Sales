<?php

namespace App\Contracts;

use App\DTO\ContactDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ContactRepositoryInterface
{
    public function getAllContacts(): LengthAwarePaginator;

    public function searchContacts(array $searchTerms);

    public function getContactById($id);

    public function createContact(ContactDTO $contactDTO);

    public function updateContact($id, array $data);

    public function deleteContact($id);
}
