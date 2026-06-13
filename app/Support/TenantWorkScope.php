<?php

namespace App\Support;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

class TenantWorkScope
{
    protected static function currentUser(): ?User
    {
        return Filament::auth()->user();
    }

    protected static function currentUserId(): ?int
    {
        return self::currentUser()?->getKey();
    }

    public static function currentUserIsWorker(): bool
    {
        return self::currentUser()?->hasRole('worker') === true;
    }

    public static function orders(Builder $query): Builder
    {
        if (! self::currentUserIsWorker()) {
            return $query;
        }

        return $query->where('assigned_user_id', self::currentUserId());
    }

    public static function projects(Builder $query): Builder
    {
        if (! self::currentUserIsWorker()) {
            return $query;
        }

        return $query->whereHas(
            'orders',
            fn (Builder $query): Builder => $query->where('assigned_user_id', self::currentUserId()),
        );
    }

    public static function proofUploads(Builder $query): Builder
    {
        if (! self::currentUserIsWorker()) {
            return $query;
        }

        return $query->whereHas(
            'order',
            fn (Builder $query): Builder => $query->where('assigned_user_id', self::currentUserId()),
        );
    }

    public static function clients(Builder $query): Builder
    {
        if (! self::currentUserIsWorker()) {
            return $query;
        }

        return $query->whereHas(
            'projects.orders',
            fn (Builder $query): Builder => $query->where('assigned_user_id', self::currentUserId()),
        );
    }
}