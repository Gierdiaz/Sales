<?php

namespace App\Repositories;

use App\Contracts\ContactRepositoryInterface;
use App\DTO\ContactDTO;
use App\Models\Contact;
use App\ValueObjects\Address;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactRepository implements ContactRepositoryInterface
{
    public function paginateContacts(): LengthAwarePaginator
    {
        return Contact::query()->orderBy('created_at', 'desc')->paginate(10);
    }

    public function findContactsByFilters(array $searchTerms): LengthAwarePaginator
    {
        $query = Contact::query();

        foreach ($searchTerms as $key => $value) {
            if ($value) {
                $query->where($key, 'LIKE', "%{$value}%");
            }
        }

        return $query->paginate(10);
    }

    public function findContactById($id)
    {
        return Contact::findOrFail($id);
    }

    public function insertContact(ContactDTO $contactDTO)
    {
        return Contact::create([
            'name'    => $contactDTO->name,
            'phone'   => $contactDTO->phone,
            'email'   => $contactDTO->email,
            'number'  => $contactDTO->number,
            'cep'     => $contactDTO->cep,
            'address' => $contactDTO->address->toArray(),
        ]);
    }

    public function modifyContact($id, array $data)
    {
        if (isset($data['address']) && $data['address'] instanceof Address) {
            $data['address'] = $data['address']->toArray();
        }

        return Contact::where('id', $id)->update($data);
    }

    public function removeContact($id)
    {
        return Contact::destroy($id);
    }
}
