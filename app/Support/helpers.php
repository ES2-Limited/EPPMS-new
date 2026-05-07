<?php

use App\Models\Organization;
use Illuminate\Support\Facades\Cache;

if (! function_exists('app_organisation')) {
    function app_organisation(): ?Organization
    {
        return Cache::remember('organisation', 3600, fn (): ?Organization => Organization::query()->first());
    }
}

if (! function_exists('app_organisation_logo_url')) {
    function app_organisation_logo_url(): ?string
    {
        return app_organisation()?->logo_url;
    }
}
