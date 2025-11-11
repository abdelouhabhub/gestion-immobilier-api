<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Property $property): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'agent']);
    }

    public function update(User $user, Property $property): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'agent' && $property->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Property $property): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'agent' && $property->user_id === $user->id) {
            return true;
        }

        return false;
    }
}
