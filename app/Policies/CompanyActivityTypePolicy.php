<?php

namespace App\Policies;

use App\Models\CompanyActivityType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyActivityTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_company::activity::type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CompanyActivityType $companyActivityType): bool
    {
        return $user->can('view_company::activity::type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_company::activity::type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CompanyActivityType $companyActivityType): bool
    {
        return $user->can('update_company::activity::type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CompanyActivityType $companyActivityType): bool
    {
        return $user->can('delete_company::activity::type');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CompanyActivityType $companyActivityType): bool
    {
        return $user->can('restore_company::activity::type');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CompanyActivityType $companyActivityType): bool
    {
        return $user->can('force_delete_company::activity::type');
    }
}
