<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurrenceRule extends Model
{
    use HasFactory;

    protected $table = 'recurrence_rules';

    protected $fillable = [
        'tenant_id',
        'frequency',
        'day_of_month',
        'interval_months',
        'starts_on',
    ];

    protected function casts(): array
    {
        return [
            'day_of_month' => 'integer',
            'interval_months' => 'integer',
            'starts_on' => 'date',
        ];
    }
}

