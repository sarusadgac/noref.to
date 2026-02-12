<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CreatedBy
{
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeCreatedBy(Builder $query, User|int $user): Builder
    {
        return $query->where('created_by', $user instanceof User ? $user->id : $user);
    }
}
