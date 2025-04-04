<?php

namespace App\Policies\Appointment;

use App\Models\User;
use App\Models\Appointment\AppointmentCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_vet::appointment::appointment::category');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AppointmentCategory $appointmentCategory): bool
    {
        return $user->can('view_vet::appointment::appointment::category');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_vet::appointment::appointment::category');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AppointmentCategory $appointmentCategory): bool
    {
        return $user->can('update_vet::appointment::appointment::category');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AppointmentCategory $appointmentCategory): bool
    {
        return $user->can('delete_vet::appointment::appointment::category');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_vet::appointment::appointment::category');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, AppointmentCategory $appointmentCategory): bool
    {
        return $user->can('force_delete_vet::appointment::appointment::category');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_vet::appointment::appointment::category');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, AppointmentCategory $appointmentCategory): bool
    {
        return $user->can('restore_vet::appointment::appointment::category');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_vet::appointment::appointment::category');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, AppointmentCategory $appointmentCategory): bool
    {
        return $user->can('replicate_vet::appointment::appointment::category');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_vet::appointment::appointment::category');
    }
}
