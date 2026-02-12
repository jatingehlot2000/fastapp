<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_type_id',
        'property_type_name',
        'purpose',
        'title',
        'description',
        'area_sqft',
        'bhk_type',
        'bedrooms',
        'bathrooms',
        'toilet_type',
        'furnishing',
        'preferred_tenant',
        'floor_number',
        'total_floors',
        'property_age',
        'city',
        'state',
        'locality',
        'address_line',
        'landmark',
        'zip_code',
        'latitude',
        'longitude',
        'monthly_rent',
        'security_deposit',
        'is_live',
        'sector',
        'house_no',
    ];

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }
}
