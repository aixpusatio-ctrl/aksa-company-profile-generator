<?php

namespace App\Policies;

use App\Models\CompanyProfile;
use App\Models\User;

/**
 * Users may only manage their own company profiles; admins manage all.
 */
class CompanyProfilePolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CompanyProfile $company): bool
    {
        return $company->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->canCreateWebsite();
    }

    public function update(User $user, CompanyProfile $company): bool
    {
        return $company->user_id === $user->id;
    }

    public function publish(User $user, CompanyProfile $company): bool
    {
        return $company->user_id === $user->id;
    }

    public function manageDomains(User $user, CompanyProfile $company): bool
    {
        return $company->user_id === $user->id && $user->canUseCustomDomain();
    }

    public function delete(User $user, CompanyProfile $company): bool
    {
        return $company->user_id === $user->id;
    }
}
