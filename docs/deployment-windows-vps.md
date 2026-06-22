# Cartly Deployment on Windows VPS with XAMPP

This document defines the repo-side deployment model for Cartly on a Windows
VPS using XAMPP, versioned production releases, and per-PR preview
environments.

## Requirements

- Windows VPS with XAMPP, Git for Windows, Composer, PHP CLI, and MySQL tools
- OpenSSH Server or another remote execution method reachable from GitHub
- A checked-out Cartly repo on the VPS
- DNS and TLS already configured for:
  - `https://cartly.dpdns.net`
  - `https://preview.cartly.dpdns.net/pr-{PR_NUMBER}/`

## Deployment Layout

Recommended production layout:

```text
C:\xampp\htdocs\cartly\prod\
  releases\
    2026-06-22-1430-a1b2c3d\
    2026-06-23-1015-b4c5d6e\
  current\
  VERSIONING
```

Recommended preview layout:

```text
C:\xampp\htdocs\cartly\previews\
  pr-14\
    current\
```

Production uses copy-based switching. `prod/current` is replaced with the
selected release contents only after the new release is prepared and validated.
This avoids junction-specific Windows behavior and keeps rollback simple.

## Environment Strategy

Each active release gets its own local `.env` file at the repo root of that
release directory. The bootstrap loader reads that file before config constants
are defined.

Example production `.env`:

```dotenv
APP_NAME=Cartly
APP_ENV=production
APP_BASE_PATH=
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=cartly_prod
DB_USER=cartly_user
DB_PASSWORD=change-me
```

Example preview `.env`:

```dotenv
APP_NAME=Cartly
APP_ENV=preview
APP_BASE_PATH=/pr-14
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=cartly_preview_pr_14
DB_USER=cartly_user
DB_PASSWORD=change-me
```

`APP_BASE_PATH` is the deployment contract for subpath hosting. Internal links,
assets, uploads, redirects, form actions, and JS URLs derive from it through
`BASE_URL`, `ASSET_URL`, and `UPLOAD_URL`.

## VERSIONING Format

`prod/VERSIONING` is updated after each successful production deployment.

Example:

```text
CURRENT_VERSION=2026-06-22-1430-a1b2c3d
CURRENT_COMMIT=a1b2c3d4e5f678901234567890abcdef12345678
DEPLOYED_AT=2026-06-22T14:30:00+08:00
BRANCH=main
PREVIOUS_VERSIONS=2026-06-21-1130-ffeedd0,2026-06-20-0915-aabbccd
```

The root-level tracked `VERSIONING` file in Git is a template/example only.

## Production Deployment Flow

`scripts/deploy-prod.ps1` performs the following:

1. Resolve the target commit for `main`.
2. Generate a release folder name as `yyyy-MM-dd-HHmm-shortsha`.
3. Export the exact target commit into `prod/releases/<release-id>`.
4. Write the release-local `.env`.
5. Run `composer install`.
6. Detect whether tracked database files changed since the previous production deployment.
7. Validate required release files.
8. Replace `prod/current` with the new release contents.
9. Update `prod/VERSIONING`.
10. Optionally restart Apache if a service name was supplied.

### Production Database Behavior

Production deploys preserve live data. If database files changed, the script
logs the change but does not perform a destructive schema or seed reset.
Production database changes should be applied separately using reviewed,
non-destructive migrations or a manual maintenance run.

## Preview Deployment Flow

`scripts/deploy-preview.ps1` performs the following:

1. Validate the PR number.
2. Export the exact PR commit into `previews/pr-<n>/current`.
3. Write preview `.env` with `APP_BASE_PATH=/pr-<n>`.
4. Run `composer install`.
5. Drop and recreate `cartly_preview_pr_<n>`.
6. Import `src/database/schema.sql` and `src/database/seed.sql`.
7. Run `src/database/seed_assets.php`.
8. Optionally restart Apache.

Preview URLs stay stable across updates:

```text
https://preview.cartly.dpdns.net/pr-14/
```

## Preview Cleanup

`scripts/cleanup-preview.ps1` removes only the requested preview deployment:

- filesystem target: `C:\xampp\htdocs\cartly\previews\pr-<n>`
- database target: `cartly_preview_pr_<n>`

The script rejects unexpected paths and database names so it does not touch
production or other preview environments.

## Database Change Detection

Production DB change detection compares the previous deployed production commit
from `prod/VERSIONING` against the target commit and watches:

- `src/database/**`
- `cartly/database/**` if that path is introduced later

Preview deployments always recreate the preview database. Production deploys do
not auto-reset the live database.

## GitHub Actions and Required Secrets

The workflows assume these repository secrets exist:

- `CARTLY_VPS_HOST`
- `CARTLY_VPS_PORT`
- `CARTLY_VPS_USERNAME`
- `CARTLY_VPS_SSH_PRIVATE_KEY`
- `CARTLY_REPO_ROOT`
- `CARTLY_PROD_ROOT`
- `CARTLY_PREVIEW_ROOT`
- `CARTLY_APACHE_SERVICE`
- `CARTLY_DB_HOST`
- `CARTLY_DB_PORT`
- `CARTLY_DB_USER`
- `CARTLY_DB_PASSWORD`
- `CARTLY_PROD_DB_NAME`

`deploy-main.yml` runs on pushes to `main`.

`deploy-preview.yml` runs on PR `opened`, `synchronize`, `reopened`, and
`closed`. Open/update events deploy the preview and upsert a single PR comment.
Close events run cleanup only.

## Rollback

Rollback is manual and release-based:

1. Pick an older folder under `prod/releases`.
2. Replace `prod/current` with that release contents.
3. Update `prod/VERSIONING` to match the selected release.
4. Restart Apache if needed.

If a DB migration was applied separately, roll back that DB change using the
same reviewed maintenance process.

## Troubleshooting

- If preview assets load without `/pr-<n>`, verify `APP_BASE_PATH` in the
  preview `.env` and confirm Apache routes the subpath into the preview
  `current` directory.
- If production deployment runs but the site does not update, compare
  `prod/current` and `prod/VERSIONING` to confirm the copy switch completed.
- If GitHub Actions cannot connect, verify SSH access from GitHub and confirm
  the Windows host accepts the configured key.
- If Composer or MySQL are not found, add them to the system `PATH` for the SSH
  user or update the scripts to call explicit executable paths.
- If Apache restart fails, supply the correct Windows service name or leave it
  blank and restart Apache by another operational process.
