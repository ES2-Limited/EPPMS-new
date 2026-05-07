<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## ePPMS Queue Deployment Notes

Set `QUEUE_CONNECTION=database` when queued email delivery should use Laravel's database queue. The jobs table is included in the application migrations.

For cPanel deployments, configure a cron entry similar to:

```bash
cd /home/username/public_html/demo2.eppms.ng && php artisan queue:work --stop-when-empty
```

## UAT Checklist

- Sign in with the seeded admin account: `dsd@eppms.ng` / `Admin@1234`.
- Configure organisation name, logo, address, phone, email, and website from Organisation Settings.
- Create office locations.
- Create directorates.
- Create departments and link them to directorates.
- Create units and link them to departments.
- Create personnel records and assign the correct system roles.
- Create contractor and consultant firm accounts.
- Create contractor personnel accounts and link them to parent firms.
- Create a project with contractor, consultant, office, directorate, department, award, and finance details.
- Add project milestones.
- Add tasks under milestones.
- Assign project personnel as project manager or project member.
- Verify only allowed internal users can mark tasks as done.
- Verify contractors, contractor personnel, and consultants cannot mark tasks as done.
- Upload task images and confirm they appear on task pages.
- Upload milestone images and confirm they appear on milestone pages.
- Confirm the project gallery shows recent task images.
- Add comments at project, milestone, and task levels.
- Confirm dashboard stats respect role/project scope.
- Confirm project histogram respects role/project scope and filters.
- Generate a project report for a 100% complete project.
- Generate a milestone report.
- Generate the personnel report as an authorised internal role.
- Confirm queued emails are written to the database queue when `QUEUE_CONNECTION=database` is enabled.
- Verify role-based access for admin, organization admin, management admin, auditor, scoped internal users, contractors, contractor personnel, and consultants.

## Windows IIS Production Notes

Install optimized dependencies:

```bash
composer install --no-dev --optimize-autoloader
```

Configure `.env` for SQL Server:

```env
DB_CONNECTION=sqlsrv
DB_HOST=your-sql-server-host
DB_PORT=1433
DB_DATABASE=eppms
DB_USERNAME=your-sql-server-user
DB_PASSWORD=your-sql-server-password
```

Required PHP extensions include `sqlsrv` and `pdo_sqlsrv`.

For a fresh install, generate the application key:

```bash
php artisan key:generate
```

Run production migrations and seed roles:

```bash
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder --force
```

Create the Windows storage junction from the project root:

```bat
mklink /J public\storage storage\app\public
```

Grant `IIS_IUSRS` write permissions to:

- `storage`
- `bootstrap/cache`

Build and refresh production caches:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan permission:cache-reset
php artisan shield:generate --all --panel=admin --option=permissions --minimal --no-interaction
```

Restart IIS:

```bat
iisreset
```

For queued email processing on Windows, run a scheduled task that executes:

```bash
php artisan queue:work --stop-when-empty
```
