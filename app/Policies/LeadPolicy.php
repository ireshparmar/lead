<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeadPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        return null; // see the note above in Gate::before about why null must be returned here.
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin') ||  $user->hasRole('Agent') || $user->hasRole('Staff');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lead $model): bool
    {
        return $user->hasRole('Admin') ||  $user->hasRole('Agent') || $user->hasRole('Staff');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin') ||  $user->hasRole('Agent') || $user->hasRole('Staff');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lead $model): bool
    {
        return $user->hasRole('Admin') ||  $user->hasRole('Staff');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lead $model): bool
    {
        return $user->hasRole('Admin') ||  $user->hasRole('Agent') || $user->hasRole('Staff');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lead $model): bool
    {
        return $user->hasRole('Admin') ||  $user->hasRole('Agent') || $user->hasRole('Staff');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lead $model): bool
    {
        return $user->hasRole('Admin') ||  $user->hasRole('Agent') || $user->hasRole('Staff');
    }
}
