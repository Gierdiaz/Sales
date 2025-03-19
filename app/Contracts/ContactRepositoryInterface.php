<?php

namespace App\Contracts;

use App\DTO\ContactDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ContactRepositoryInterface
{
    public function paginateContacts(): LengthAwarePaginator;

    public function findContactsByFilters(array $searchTerms): LengthAwarePaginator;

    public function findContactById($id);

    public function insertContact(ContactDTO $contactDTO);

    public function modifyContact($id, array $data);

    public function removeContact($id);
}
