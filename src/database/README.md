# Cartly Database

## Import under XAMPP

1. Start Apache + MySQL in XAMPP.
2. Open phpMyAdmin (http://localhost/phpmyadmin).
3. Run `schema.sql` (creates the `cartly` database and tables).
4. Run `seed.sql` for sample data.
5. If you want the spec folder layout, the same files are mirrored at:
   - `migrations/001_cartly_schema.sql`
   - `seeders/001_cartly_seed.sql`

CLI alternative:

```bash
mysql -u root < src/database/schema.sql
mysql -u root cartly < src/database/seed.sql
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
