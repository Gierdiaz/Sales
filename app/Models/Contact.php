<?php

namespace App\Models;

use App\ValueObjects\Address;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};

class Contact extends Model
{
    use HasUuids;
    use HasFactory;
    use SoftDeletes;

    protected $table = 'contacts';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'number',
        'cep',
        'address',
    ];

    protected $casts = [
        'address'    => 'array', // Converte automaticamente JSON para array
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Getter para transformar array em objeto Address
     */
    public function getAddressObjectAttribute(): ?Address
    {
        return Address::fromArray($this->address);
    }

    /**
     * Setter para armazenar o objeto Address como JSON
     */
    public function setAddressObjectAttribute(Address $address): void
    {
        $this->attributes['address'] = json_encode($address->toArray());
    }
}
