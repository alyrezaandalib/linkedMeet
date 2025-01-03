<?php

namespace App\Policies;

use App\Models\AppVersion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppVersionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_app::version');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AppVersion $appVersion): bool
    {
        return $user->can('view_app::version');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_app::version');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AppVersion $appVersion): bool
    {
        return $user->can('update_app::version');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AppVersion $appVersion): bool
    {
        return $user->can('delete_app::version');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AppVersion $appVersion): bool
    {
        return $user->can('restore_app::version');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AppVersion $appVersion): bool
    {
        return $user->can('restore_app::version');
    }
}
