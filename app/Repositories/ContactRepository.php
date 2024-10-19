<?php

namespace App\Repositories;

use App\Contracts\ContactRepositoryInterface;
use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactRepository implements ContactRepositoryInterface
{
    public function getAllContacts(): LengthAwarePaginator
    {
        return Contact::query()->orderBy('created_at', 'desc')->paginate(10);
    }

    public function searchContacts(array $searchTerms)
    {
        $query = Contact::query();

        foreach ($searchTerms as $key => $value) {
            if ($value) {
                $query->orWhere($key, 'LIKE', "%{$value}%");
            }
        }
    
        return $query->paginate(10);
    }

    public function getContactById($id)
    {
        return Contact::findOrFail($id);
    }

    public function createContact(array $data)
    {
        return Contact::create($data);
    }

    public function updateContact($id, array $data)
    {
        return Contact::where('id', $id)->update($data);
    }

    public function deleteContact($id)
    {
        return Contact::destroy($id);
    }
}
