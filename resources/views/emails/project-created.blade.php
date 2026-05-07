@php($organisation = app_organisation())

@if ($organisation?->logo)
    <p><img src="{{ asset('storage/'.$organisation->logo) }}" alt="{{ $organisation->name }}" style="height: 56px; width: auto;"></p>
@endif

<p>Hello,</p>

<p>A new {{ $organisation?->name ?: 'ePPMS' }} project has been created.</p>

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
