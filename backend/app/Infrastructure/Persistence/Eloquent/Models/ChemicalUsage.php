<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemicalUsage extends Model
{
    use HasFactory;

    protected $table = 'chemical_usages';

    protected $fillable = [
        'tenant_id',
        'service_report_id',
        'chemical_name',
        'quantity',
        'unit',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
        ];
    }
}

