<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access denied - {{ app_organisation()?->name ?? 'ePPMS' }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #f8fafc; color: #0f172a; }
        main { width: min(92vw, 520px); padding: 40px; border: 1px solid #e2e8f0; border-radius: 24px; background: #fff; text-align: center; box-shadow: 0 24px 60px rgba(15, 23, 42, .08); }
        img { height: 64px; width: auto; max-width: 180px; object-fit: contain; margin-bottom: 24px; }
        h1 { margin: 0 0 12px; font-size: 32px; }
        p { margin: 0 0 28px; color: #475569; }
        a { display: inline-block; padding: 10px 18px; border-radius: 999px; background: #d97706; color: #fff; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <main>
        @if (app_organisation()->logo_url ?? null)
            <img src="{{ app_organisation()->logo_url }}" alt="{{ app_organisation()->name ?? 'ePPMS' }}">
        @endif

        <h1>{{ app_organisation()->name ?? 'ePPMS' }}</h1>
        <p>You do not have access to this page.</p>

        @auth
            <a href="{{ url('/admin') }}">Back to dashboard</a>
        @endauth
    </main>
</body>
</html>
