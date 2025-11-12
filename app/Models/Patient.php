<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Address;
use App\Models\PriceTable;

class Patient extends Model
{
    protected $fillable = [
        'name',
        'legal_guardian',
        'email',
        'social_name',
        'phone',
        'date_of_birth',
        'notes',
        'landline',
        'cpf',
        'cpf_legal_guardian',
        'rg',
        'sex',
        'photo',
        'address_id',
        'clinic_id'
    ];

    /**
     * Get the URL of the patient's photo.
     *
     * If a photo is set, this method returns the URL to the photo using the tenant_asset function,
     * which resolves the asset path in a multi-tenant context. If no photo is set, it returns null.
     *
     * @return string|null The URL to the patient's photo, or null if not set.
     * @see tenant_asset()
     */
    public function getPhotoAttribute()
    {
        return $this->attributes['photo']
            ? tenant_asset($this->attributes['photo'])
            : null;
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function priceTable()
    {
        return $this->hasMany(PriceTable::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
}
