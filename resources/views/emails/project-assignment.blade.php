<p>Hello {{ $user->name }},</p>

<p>You have been assigned to {{ app_organisation()?->name ?: 'ePPMS' }} project: <strong>{{ $project->name }}</strong>.</p>

<p>
    Project role: {{ str($projectRole)->replace('_', ' ')->title() }}<br>
    App URL: <a href="{{ config('app.url') }}">{{ config('app.url') }}</a><br>
    Login URL: <a href="{{ url('/admin/login') }}">{{ url('/admin/login') }}</a>
</p>
