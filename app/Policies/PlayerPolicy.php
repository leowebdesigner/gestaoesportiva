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
        return $this->adminOnly($user, __('messages.player.delete_forbidden'));
    }

    public function forceDelete(User $user, Player $player): Response
    {
        return $this->adminOnlyAsNotFound($user);
    }

    public function restore(User $user, Player $player): Response
    {
        return $this->adminOnlyAsNotFound($user);
    }

    private function adminOnly(User $user, ?string $message = null): Response
    {
        return $user->isAdministrator()
            ? Response::allow()
            : Response::deny($message ?? __('messages.errors.unauthorized_action'));
    }

    private function adminOnlyAsNotFound(User $user): Response
    {
        return $user->isAdministrator()
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
