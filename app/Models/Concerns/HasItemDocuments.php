<?php

namespace App\Models\Concerns;

use App\Models\ItemDocument;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/** Gives a model a per-item required-documents checklist. */
trait HasItemDocuments
{
    public function documents(): MorphMany
    {
        return $this->morphMany(ItemDocument::class, 'documentable')->orderBy('position')->orderBy('id');
    }
}
