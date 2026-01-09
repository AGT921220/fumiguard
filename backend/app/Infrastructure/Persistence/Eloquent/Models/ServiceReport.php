<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceReport extends Model
{
    use HasFactory;

    protected $table = 'service_reports';

    protected $fillable = [
        'tenant_id',
        'work_order_id',
        'certificate_folio',
        'status',
        'locked',
        'started_at',
        'finalized_at',
        'checklist',
        'notes',
        'report_pdf_path',
        'certificate_pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'locked' => 'boolean',
            'started_at' => 'datetime',
            'finalized_at' => 'datetime',
            'checklist' => 'array',
        ];
    }
}

