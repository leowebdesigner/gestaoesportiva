<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TeamPolicy
{
    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    public function view(User $user, Team $team): Response
    {
        return Response::allow();
    }

    public function create(User $user): Response
    {
        return Response::allow();
    }

    public function update(User $user, Team $team): Response
    {
        return Response::allow();
    }

    public function delete(User $user, Team $team): Response
    {
        if ($user->isAdministrator()) {
            return Response::allow();
        }

        return Response::deny(__('messages.team.delete_forbidden'));
    }

    public function forceDelete(User $user, Team $team): Response
    {
        if ($user->isAdministrator()) {
            return Response::allow();
        }

        return Response::denyAsNotFound();
    }

    public function restore(User $user, Team $team): Response
    {
        return $user->isAdministrator()
            ? Response::allow()
            : Response::denyWithStatus(404);
    }
}
