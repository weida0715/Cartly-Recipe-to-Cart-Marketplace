# Cartly Database

## Import under XAMPP

1. Start Apache + MySQL in XAMPP.
2. Open phpMyAdmin (http://localhost/phpmyadmin).
3. Run `schema.sql` (creates the `cartly` database and tables).
4. Run `seed.sql` for sample data.
5. From the project root, run `php src/database/seed_assets.php` to copy the
   seeded stock images into `src/public/uploads/seeded/`.
6. If you want the spec folder layout, the same files are mirrored at:
   - `migrations/001_cartly_schema.sql`
   - `seeders/001_cartly_seed.sql`

If the database was already imported before voucher codes became store-scoped,
run `migrations/002_store_scoped_voucher_codes.sql` once instead of recreating
the database.

To add administrator-managed delivery fees to an existing database, run
`migrations/007_application_settings.sql` once.

CLI alternative:

```bash
mysql -u root < src/database/schema.sql
mysql -u root cartly < src/database/seed.sql
php src/database/seed_assets.php
```

## Default logins (seed)

| Role     | Username  | Password    |
|----------|-----------|-------------|
| Admin    | admin     | password123 |
| Merchant | merchant  | password123 |
| Customer | customer  | password123 |

> The seed password hash is bcrypt for `password123`. If login fails because
> your PHP install rejects the hash, replace it: log in once, then use
> phpMyAdmin to update other users' password column with the same hashed value.

## Notes

- The schema includes recipe-to-cart snapshots, report moderation fields, and
  password reset token fields used by the current application.
- Uploaded images are stored under `src/public/uploads/` and are referenced by the
  application as relative paths.
- Versioned seed image sources live under `src/database/stock_images/`; the seed
  asset script copies them into the public uploads folder for local XAMPP use.
