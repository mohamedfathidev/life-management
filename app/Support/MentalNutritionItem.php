<?php

namespace App\Support;

use App\Enums\MentalNutritionSourceType;

/**
 * One candidate for "موضوع اليوم" in التغذية الذهنية, normalized across the
 * several recovery/diary tabs it can come from.
 */
final readonly class MentalNutritionItem
{
    public function __construct(
        public MentalNutritionSourceType $type,
        public int $id,
        public string $title,
        public ?string $body,
        public bool $isHtml,
        public string $url,
    ) {
    }

    /** Unique key across the whole pool, used to match against consumption history. */
    public function key(): string
    {
        return $this->type->value.':'.$this->id;
    }
}
