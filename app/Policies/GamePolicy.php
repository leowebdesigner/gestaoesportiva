<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;
use App\Policies\Concerns\AdminAuthorization;
use Illuminate\Auth\Access\Response;

class GamePolicy
{
    use AdminAuthorization;

    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    public function view(User $user, Game $game): Response
    {
        return Response::allow();
    }

    public function create(User $user): Response
    {
        return Response::allow();
    }

    public function update(User $user, Game $game): Response
    {
        return Response::allow();
    }

    public function delete(User $user, Game $game): Response
    {
        return $this->adminOnly($user, __('messages.game.delete_forbidden'));
    }

    public function forceDelete(User $user, Game $game): Response
    {
        return $this->adminOnlyAsNotFound($user);
    }

    public function restore(User $user, Game $game): Response
    {
        return $this->adminOnlyAsNotFound($user);
    }
}
