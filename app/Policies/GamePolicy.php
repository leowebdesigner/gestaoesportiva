<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GamePolicy
{
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
        if ($user->isAdministrator()) {
            return Response::allow();
        }

        return Response::deny(__('messages.game.delete_forbidden'));
    }

    public function forceDelete(User $user, Game $game): Response
    {
        if ($user->isAdministrator()) {
            return Response::allow();
        }

        return Response::denyAsNotFound();
    }

    public function restore(User $user, Game $game): Response
    {
        return $user->isAdministrator()
            ? Response::allow()
            : Response::denyWithStatus(404);
    }
}
