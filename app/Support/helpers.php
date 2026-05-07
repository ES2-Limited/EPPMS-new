<?php

use App\Models\Organization;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

if (! function_exists('app_organisation')) {
    function app_organisation(): ?Organization
    {
        $organisation = Cache::rememberForever('app_organisation', fn (): Organization|false => Organization::query()->first() ?: false);

        return $organisation instanceof Organization ? $organisation : null;
    }
}

if (! function_exists('app_organisation_logo_url')) {
    function app_organisation_logo_url(): ?string
    {
        $logo = app_organisation()?->logo;

        if (blank($logo)) {
            return null;
        }

        if (Str::startsWith($logo, ['http://', 'https://', '/storage/'])) {
            return $logo;
        }

        $path = Str::contains($logo, '/') ? $logo : 'organisation/'.$logo;

        return asset('storage/'.$path);
    }
}
