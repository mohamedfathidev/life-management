<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ItemDocument extends Model
{
    protected $fillable = ['user_id', 'parent_id', 'scholarship_document_id', 'name', 'note', 'is_done', 'position', 'documentable_type', 'documentable_id'];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position')->orderBy('id');
    }

    /** The linked document in the general library, if any. */
    public function generalDocument(): BelongsTo
    {
        return $this->belongsTo(ScholarshipDocument::class, 'scholarship_document_id');
    }

    public function isLinked(): bool
    {
        return $this->scholarship_document_id !== null;
    }

    /** Readiness: derived from the linked library document, else the manual flag. */
    public function isReady(): bool
    {
        return $this->isLinked()
            ? (bool) $this->generalDocument?->is_ready
            : $this->is_done;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
