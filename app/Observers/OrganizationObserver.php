<?php

namespace App\Observers;

use App\Models\Organization;
use Illuminate\Support\Facades\Cache;

class OrganizationObserver
{
    public function saved(Organization $organization): void
    {
        $this->clearCache();
    }

    public function deleted(Organization $organization): void
    {
        $this->clearCache();
    }

    public function restored(Organization $organization): void
    {
        $this->clearCache();
    }

    public function forceDeleted(Organization $organization): void
    {
        $this->clearCache();
    }

    protected function clearCache(): void
    {
        Cache::forget('organisation');
    }
}
