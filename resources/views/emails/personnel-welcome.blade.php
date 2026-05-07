@php($organisation = app_organisation())

@if (app_organisation_logo_url())
    <p><img src="{{ app_organisation_logo_url() }}" alt="{{ $organisation?->name ?? 'ePPMS' }}" style="height: 56px; width: auto;"></p>
@endif

<p>Hello {{ $user->name }},</p>

<p>Your {{ $organisation?->name ?? 'ePPMS' }} account has been created for {{ $accountType ?? $organisation?->name ?? 'ePPMS' }} access.</p>

<p>
    App URL: <a href="{{ config('app.url') }}">{{ config('app.url') }}</a><br>
    Login URL: <a href="{{ url('/admin/login') }}">{{ url('/admin/login') }}</a><br>
    Email: {{ $user->email }}<br>
    Password: {{ $password }}
</p>

<p>Please sign in and change your password after your first login.</p>
