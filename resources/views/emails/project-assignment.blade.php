@php($organisation = app_organisation())

@if ($organisation?->logo_url)
    <p><img src="{{ $organisation->logo_url }}" alt="{{ app_organisation()->name ?? 'ePPMS' }}" style="height: 56px; width: auto;"></p>
@endif

<h1>{{ app_organisation()->name ?? 'ePPMS' }}</h1>

<p>Hello {{ $user->name }},</p>

<p>You have been assigned to {{ app_organisation()->name ?? 'ePPMS' }} project: <strong>{{ $project->name }}</strong>.</p>

<p>
    Project role: {{ str($projectRole)->replace('_', ' ')->title() }}<br>
    @if ($projectUrl)
        Project link: <a href="{{ $projectUrl }}">{{ $projectUrl }}</a><br>
    @endif
    App URL: <a href="{{ config('app.url') }}">{{ config('app.url') }}</a><br>
    Login URL: <a href="{{ url('/admin/login') }}">{{ url('/admin/login') }}</a>
</p>
