<?php
namespace App\Policies;

use App\Models\Consignment;
use App\Models\User;

class ConsignmentPolicy
{
    public function view(User $user, Consignment $consignment): bool
    {
        return $user->isAdmin() || $consignment->user_id === $user->id;
    }

    public function manageLots(User $user, Consignment $consignment): bool
    {
        return ($user->isAdmin() || $consignment->user_id === $user->id) && $consignment->isOpen();
    }
}
