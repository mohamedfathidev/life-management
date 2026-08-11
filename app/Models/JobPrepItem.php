<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPrepItem extends Model
{
    use HasFactory;

    protected $fillable = ['job_application_id', 'title', 'is_done', 'position'];

    protected $casts = [
        'is_done' => 'boolean',
        'position' => 'integer',
    ];

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }
}
