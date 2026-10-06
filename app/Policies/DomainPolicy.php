<?php

namespace App\Policies;

use App\Models\Domain;
use App\Models\User;

class DomainPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function update(User $user, Domain $domain): bool
    {
        return $domain->companyProfile?->user_id === $user->id;
    }

    public function delete(User $user, Domain $domain): bool
    {
        return $domain->companyProfile?->user_id === $user->id;
    }
}
