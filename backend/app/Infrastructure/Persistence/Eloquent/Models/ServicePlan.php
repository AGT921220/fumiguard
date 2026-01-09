<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePlan extends Model
{
    use HasFactory;

    protected $table = 'service_plans';

    protected $fillable = [
        'tenant_id',
        'name',
        'checklist_template',
        'certificate_template',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'checklist_template' => 'array',
            'certificate_template' => 'array',
            'is_active' => 'boolean',
        ];
    }
}

