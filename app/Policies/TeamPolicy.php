<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use App\Policies\Concerns\AdminAuthorization;
use Illuminate\Auth\Access\Response;

class TeamPolicy
{
    use AdminAuthorization;

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
        return $this->adminOnly($user, __('messages.team.delete_forbidden'));
    }

    public function forceDelete(User $user, Team $team): Response
    {
        return $this->adminOnlyAsNotFound($user);
    }

    public function restore(User $user, Team $team): Response
    {
        return $this->adminOnlyAsNotFound($user);
    }
}
