<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Contact;
use Illuminate\Support\Facades\Route;

/**
 * @property Contact $resource
 */
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
            'id'      => $this->resource->id,
            'name'    => $this->resource->name,
            'phone'   => $this->resource->phone,
            'email'   => $this->resource->email,
            'number'  => $this->resource->number,
            'cep'     => $this->resource->cep,
            'address' => $this->resource->address,
        ];

        if ($isShow) {
            $resourceArray['links'] = [
                'index' => route('contacts.index'),
            ];
        } elseif ($isIndex) {
            $resourceArray['links'] = [
                'show' => route('contacts.show', $this->resource->id),
            ];
        }

        return $resourceArray;
    }
}
