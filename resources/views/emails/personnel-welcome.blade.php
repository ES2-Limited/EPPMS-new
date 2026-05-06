<p>Hello {{ $user->name }},</p>

<p>Your NCAA ePPMS account has been created.</p>

<p>
    Login URL: <a href="{{ url('/admin/login') }}">{{ url('/admin/login') }}</a><br>
    Email: {{ $user->email }}<br>
    Password: {{ $password }}
</p>

<p>Please sign in and change your password after your first login.</p>
