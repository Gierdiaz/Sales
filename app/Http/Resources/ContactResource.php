<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isIndex = Route::currentRouteName() === 'contacts.index';
        $isShow  = Route::currentRouteName() === 'contacts.show';

        $resourceArray = [
            'id'      => $this->id,
            'name'    => $this->name,
            'phone'   => $this->phone,
            'email'   => $this->email,
            'number'  => $this->number,
            'cep'     => $this->cep,
            'address' => $this->address,
        ];

        if ($isShow) {
            $resourceArray['links'] = [
                'index' => route('contacts.index'),
            ];
        } elseif ($isIndex) {
            $resourceArray['links'] = [
                'show' => route('contacts.show', $this->id),
            ];
        }

        return $resourceArray;
    }
}
