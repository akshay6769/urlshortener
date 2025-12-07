# URL Shortener (plain PHP)

## Setup
1. Create DB: `mysql < sql/schema.sql`
2. Edit `src/config.php` with DB credentials and APP_URL.
3. Seed superadmin: `php seed.php`
4. Start server: `php -S localhost:8000 -t public`
5. Visit `http://localhost:8000`

## Features
- SuperAdmin / Admin / Member roles
- Companies
- Invitation (email link)
- Create short URLs (Admins & Members)
- Public redirect `/{code}`
- CSV download of URLs
