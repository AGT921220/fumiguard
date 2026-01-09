<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $table = 'sites';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'name',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'lat',
        'lng',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
        ];
    }
}

