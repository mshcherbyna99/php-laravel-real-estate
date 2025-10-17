<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'bedrooms',
        'bathrooms',
        'storeys',
        'garages',
    ];

    public function getPriceDecimalAttribute(): float
    {
        return $this->price / 100;
    }

    public function toArray(): array
    {
        $array = parent::toArray();

        if (isset($array['price'])) {
            $array['price'] = $this->price / 100;
        }

        return $array;
    }
}
