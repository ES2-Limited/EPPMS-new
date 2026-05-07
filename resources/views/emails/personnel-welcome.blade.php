@php($organisation = app_organisation())

@if ($organisation?->logo_url)
    <p><img src="{{ $organisation->logo_url }}" alt="{{ app_organisation()->name ?? 'ePPMS' }}" style="height: 56px; width: auto;"></p>
@endif

<h1>{{ app_organisation()->name ?? 'ePPMS' }}</h1>

<p>Hello {{ $user->name }},</p>

<p>Your {{ app_organisation()->name ?? 'ePPMS' }} account has been created for {{ $accountType ?? app_organisation()->name ?? 'ePPMS' }} access.</p>

<p>
    App URL: <a href="{{ config('app.url') }}">{{ config('app.url') }}</a><br>
    Login URL: <a href="{{ url('/admin/login') }}">{{ url('/admin/login') }}</a><br>
    Email: {{ $user->email }}<br>
    Password: {{ $password }}
</p>

<p>Please sign in and change your password after your first login.</p>
