<?php

namespace App\Policies\Concerns;

use App\Models\User;
use Illuminate\Auth\Access\Response;

trait AdminAuthorization
{
    protected function adminOnly(User $user, ?string $message = null): Response
    {
        return $user->isAdministrator()
            ? Response::allow()
            : Response::deny($message ?? __('messages.errors.unauthorized_action'));
    }

    protected function adminOnlyAsNotFound(User $user): Response
    {
        return $user->isAdministrator()
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
