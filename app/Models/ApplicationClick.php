<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationClick extends Model
{
    protected $fillable = [
        'portal_application_id',
        'application_name',
        'target_url',
        'clicked_at',
        'ip_hash',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'clicked_at' => 'datetime',
        ];
    }

    public function portalApplication(): BelongsTo
    {
        return $this->belongsTo(PortalApplication::class);
    }
}
