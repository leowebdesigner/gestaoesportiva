<?php

namespace App\Policies;

use App\Models\Player;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlayerPolicy
{
    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    public function view(User $user, Player $player): Response
    {
        return Response::allow();
    }

    public function create(User $user): Response
    {
        return Response::allow();
    }

    public function update(User $user, Player $player): Response
    {
        return Response::allow();
    }

    public function delete(User $user, Player $player): Response
    {
        if ($user->isAdministrator()) {
            return Response::allow();
        }

        return Response::deny('Apenas administradores podem deletar jogadores.');
    }

    public function forceDelete(User $user, Player $player): Response
    {
        if ($user->isAdministrator()) {
            return Response::allow();
        }

        return Response::denyAsNotFound();
    }

    public function restore(User $user, Player $player): Response
    {
        return $user->isAdministrator()
            ? Response::allow()
            : Response::denyWithStatus(404);
    }
}
