@php($organisation = app_organisation())

@if ($organisation?->logo_url)
    <p><img src="{{ $organisation->logo_url }}" alt="{{ app_organisation()->name ?? 'ePPMS' }}" style="height: 56px; width: auto;"></p>
@endif

<h1>{{ app_organisation()->name ?? 'ePPMS' }}</h1>

<p>Hello,</p>

<p>A new {{ app_organisation()->name ?? 'ePPMS' }} project has been created.</p>

<p>
    Project: <strong>{{ $project->name }}</strong><br>
    Contractor: {{ $project->contractor?->user?->name ?? 'N/A' }}<br>
    Consultant: {{ $project->consultant?->user?->name ?? 'N/A' }}<br>
    Cost: NGN {{ number_format((float) $project->cost, 2) }}<br>
    Award date: {{ $project->award_date?->format('M d, Y') ?? 'N/A' }}
</p>

@if ($projectUrl)
    <p>Project link: <a href="{{ $projectUrl }}">{{ $projectUrl }}</a></p>
@endif
