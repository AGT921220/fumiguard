<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'tenant_id',
        'plan_key',
        'status',
        'stripe_customer_id',
        'stripe_subscription_id',
        'current_period_end',
        'limits_json',
    ];

    protected function casts(): array
    {
        return [
            'current_period_end' => 'datetime',
            'limits_json' => 'array',
        ];
    }
}

