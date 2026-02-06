<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\AdminAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use AdminAuthorization;

    /**
     * Determine if the user can update another user's external status.
     * Only administrators can do this.
     */
    public function setExternalStatus(User $authUser, User $targetUser): Response
    {
        return $this->adminOnly($authUser, __('messages.user.update_external_forbidden'));
    }
}
