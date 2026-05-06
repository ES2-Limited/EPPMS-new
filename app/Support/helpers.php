<?php

use App\Models\Organization;
use Illuminate\Support\Facades\Cache;

if (! function_exists('app_organisation')) {
    function app_organisation(): ?Organization
    {
        return Cache::rememberForever('app_organisation', fn () => Organization::query()->first());
    }
}
