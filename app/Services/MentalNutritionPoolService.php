<?php

namespace App\Services;

use App\Enums\MentalNutritionSourceType;
use App\Models\DiaryChange;
use App\Models\DiaryReason;
use App\Models\RecoveryChange;
use App\Models\RecoveryCommitment;
use App\Models\RecoveryDamage;
use App\Models\RecoveryDream;
use App\Models\RecoveryHardMoment;
use App\Models\RecoveryIdea;
use App\Models\RecoveryMistake;
use App\Models\RecoveryPledge;
use App\Models\RecoveryStory;
use App\Models\RecoveryTopic;
use App\Models\User;
use App\Support\MentalNutritionItem;
use Illuminate\Support\Collection;

/**
 * Builds the pool of candidate items for daily "التغذية الذهنية", spanning
 * every recovery tab (تعلّم/أضرار/أحلام/تغييرات/أخطاء/حكايات/تعهد/أصعب اللحظات/
 * أفكار/الالتزامات) plus the diary's "ليه مبتغيرش"/"إيه اللي غيّرني" logs —
 * not «تعلّم» alone.
 */
class MentalNutritionPoolService
{
    public function __construct(private readonly User $user)
    {
    }

    /** @return Collection<int, MentalNutritionItem> */
    public function pool(): Collection
    {
        return collect()
            ->merge($this->topics())
            ->merge($this->damages())
            ->merge($this->dreams())
            ->merge($this->changes())
            ->merge($this->mistakes())
            ->merge($this->stories())
            ->merge($this->pledge())
            ->merge($this->diaryReasons())
            ->merge($this->diaryChanges())
            ->merge($this->hardMoments())
            ->merge($this->ideas())
            ->merge($this->commitments())
            ->values();
    }

    /** Resolve one specific item (for rendering a past log's content), scoped to the user. */
    public function resolve(MentalNutritionSourceType $type, int $id): ?MentalNutritionItem
    {
        return match ($type) {
            MentalNutritionSourceType::Topic => $this->topics()->firstWhere('id', $id),
            MentalNutritionSourceType::Damage => $this->damages()->firstWhere('id', $id),
            MentalNutritionSourceType::Dream => $this->dreams()->firstWhere('id', $id),
            MentalNutritionSourceType::Change => $this->changes()->firstWhere('id', $id),
            MentalNutritionSourceType::Mistake => $this->mistakes()->firstWhere('id', $id),
            MentalNutritionSourceType::Story => $this->stories()->firstWhere('id', $id),
            MentalNutritionSourceType::Pledge => $this->pledge()->firstWhere('id', $id),
            MentalNutritionSourceType::DiaryReason => $this->diaryReasons()->firstWhere('id', $id),
            MentalNutritionSourceType::DiaryChange => $this->diaryChanges()->firstWhere('id', $id),
            MentalNutritionSourceType::HardMoment => $this->hardMoments()->firstWhere('id', $id),
            MentalNutritionSourceType::Idea => $this->ideas()->firstWhere('id', $id),
            MentalNutritionSourceType::Commitment => $this->commitments()->firstWhere('id', $id),
        };
    }

    /** @return Collection<int, MentalNutritionItem> */
    private function topics(): Collection
    {
        return RecoveryTopic::ownedBy($this->user)->get()->map(fn (RecoveryTopic $t) => new MentalNutritionItem(
            type: MentalNutritionSourceType::Topic,
            id: $t->id,
            title: $t->title,
            body: $t->content,
            isHtml: true,
            url: route('recovery.topics.show', $t),
        ));
    }

    /** @return Collection<int, MentalNutritionItem> */
    private function damages(): Collection
    {
        return RecoveryDamage::ownedBy($this->user)->main()->get()->map(fn (RecoveryDamage $d) => new MentalNutritionItem(
            type: MentalNutritionSourceType::Damage,
            id: $d->id,
            title: $d->title,
            body: $d->description,
            isHtml: true,
            url: route('recovery.damages.show', $d),
        ));
    }

    /** @return Collection<int, MentalNutritionItem> */
    private function dreams(): Collection
    {
        return RecoveryDream::ownedBy($this->user)->get()->map(fn (RecoveryDream $d) => new MentalNutritionItem(
            type: MentalNutritionSourceType::Dream,
            id: $d->id,
            title: $d->title,
            body: collect($d->benefits)->implode('، ') ?: null,
            isHtml: false,
            url: route('recovery.dreams'),
        ));
    }

    /** @return Collection<int, MentalNutritionItem> */
    private function changes(): Collection
    {
        return RecoveryChange::ownedBy($this->user)->whereNotNull('why')->where('why', '!=', '')->get()
            ->map(fn (RecoveryChange $c) => new MentalNutritionItem(
                type: MentalNutritionSourceType::Change,
                id: $c->id,
                title: $c->title,
                body: $c->why,
                isHtml: false,
                url: route('recovery.changes.show', $c),
            ));
    }

    /** @return Collection<int, MentalNutritionItem> */
    private function mistakes(): Collection
    {
        return RecoveryMistake::ownedBy($this->user)->get()->map(fn (RecoveryMistake $m) => new MentalNutritionItem(
            type: MentalNutritionSourceType::Mistake,
            id: $m->id,
            title: $m->title,
            body: $m->note,
            isHtml: true,
            url: route('recovery.mistakes.show', $m),
        ));
    }

    /** @return Collection<int, MentalNutritionItem> */
    private function stories(): Collection
    {
        return RecoveryStory::ownedBy($this->user)->get()->map(fn (RecoveryStory $s) => new MentalNutritionItem(
            type: MentalNutritionSourceType::Story,
            id: $s->id,
            title: $s->title,
            body: $s->content,
            isHtml: true,
            url: route('recovery.stories.show', $s),
        ));
    }

    /** @return Collection<int, MentalNutritionItem> */
    private function pledge(): Collection
    {
        $pledge = RecoveryPledge::ownedBy($this->user)->first();

        if (! $pledge || ! $pledge->body) {
            return collect();
        }

        return collect([new MentalNutritionItem(
            type: MentalNutritionSourceType::Pledge,
            id: $pledge->id,
            title: 'تعهدك أمام الله',
            body: $pledge->body,
            isHtml: false,
            url: route('recovery.pledge'),
        )]);
    }

    /** @return Collection<int, MentalNutritionItem> */
    private function diaryReasons(): Collection
    {
        return DiaryReason::ownedBy($this->user)->get()->map(fn (DiaryReason $r) => new MentalNutritionItem(
            type: MentalNutritionSourceType::DiaryReason,
            id: $r->id,
            title: 'ليه مبتغيرش؟',
            body: $r->body,
            isHtml: false,
            url: route('diary.reasons'),
        ));
    }

    /** @return Collection<int, MentalNutritionItem> */
    private function diaryChanges(): Collection
    {
        return DiaryChange::ownedBy($this->user)->get()->map(fn (DiaryChange $c) => new MentalNutritionItem(
            type: MentalNutritionSourceType::DiaryChange,
            id: $c->id,
            title: 'إيه اللي غيّرني؟',
            body: $c->body,
            isHtml: false,
            url: route('diary.changes'),
        ));
    }

    /** @return Collection<int, MentalNutritionItem> */
    private function hardMoments(): Collection
    {
        return RecoveryHardMoment::ownedBy($this->user)->get()->map(fn (RecoveryHardMoment $m) => new MentalNutritionItem(
            type: MentalNutritionSourceType::HardMoment,
            id: $m->id,
            title: $m->title,
            body: $m->plan ?: $m->description,
            isHtml: (bool) $m->plan,
            url: route('recovery.hard-moments.show', $m),
        ));
    }

    /** @return Collection<int, MentalNutritionItem> */
    private function ideas(): Collection
    {
        return RecoveryIdea::ownedBy($this->user)->get()->map(fn (RecoveryIdea $i) => new MentalNutritionItem(
            type: MentalNutritionSourceType::Idea,
            id: $i->id,
            title: 'فكرة راودتني',
            body: $i->body,
            isHtml: false,
            url: route('recovery.ideas'),
        ));
    }

    /** @return Collection<int, MentalNutritionItem> */
    private function commitments(): Collection
    {
        return RecoveryCommitment::ownedBy($this->user)->get()->map(fn (RecoveryCommitment $c) => new MentalNutritionItem(
            type: MentalNutritionSourceType::Commitment,
            id: $c->id,
            title: $c->title,
            body: $c->description,
            isHtml: false,
            url: route('recovery.commitments'),
        ));
    }
}
