# Laravel 12 warehouse application

The Laravel application is in this directory. The legacy PHP MVC application at the repository root remains intact and can continue to serve as the fallback.

## Apache/XAMPP without replacing the legacy site

Use the optional `xampp-laravel-vhost.conf` at the repository root as a separate local virtual host. Add this include to XAMPP's `apache\conf\httpd.conf`, then restart Apache:

```apache
Include "D:/project/xampp-laravel-vhost.conf"
```

The included file already contains `Listen 127.0.0.1:8081`; do not add a second `Listen` directive for that address. Map `warehouse-laravel.local` to `127.0.0.1` in `C:\Windows\System32\drivers\etc\hosts` and visit `http://warehouse-laravel.local:8081`. The virtual host points to `laravel\public`, is limited to local requests, and does not change the existing port-80 document root or legacy app.

Before using the project on another XAMPP installation, open PowerShell in `D:\project\laravel` and run:

```powershell
Copy-Item .env.example .env
composer install
php artisan key:generate
```

Set the `DB_*` values in `.env` to match XAMPP's MySQL connection. If Composer reports that ZIP support is missing, enable `extension=zip` in `C:\xampp\php\php.ini` and restart the terminal.

Alternatively, explicitly set Apache's `DocumentRoot` and matching `<Directory>` to `D:/project/laravel/public`. Save the previous document root first if the legacy site must remain available.

## Existing warehouse database

Laravel uses the existing `warehouse_db` tables and column names. Authentication looks up `users.username`, validates the existing `users.password` hashes, and requires `users.status = 1`. ID 6 alone bypasses RBAC. Other accounts, including role `admin`, receive permissions only through `roles`, `permissions`, and `role_permissions`.

The parent's Laravel `.env` contains the live database connection. Do not edit the existing database schema or run `php artisan migrate` against `warehouse_db`: this repository does not include an automatic schema/data conversion. The Laravel migrations are only a schema template for a separate, disposable Laravel test database; they do not contain production seed data.

From this directory, check routes and run tests with:

```powershell
php artisan route:list
php artisan test
```

The test suite uses PHPUnit's in-memory SQLite configuration and creates schema only within that isolated test database.
