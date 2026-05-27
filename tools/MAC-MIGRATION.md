# macOS migration guide (canonical)

This repo is developed on **Mac only**. Windows / Laragon / PowerShell deploy scripts are **legacy** — do not use them for day-to-day work.

**Repo root:** `/Users/aztechnologies/Desarrollo/Aztechnologies/WEB`

---

## One-time setup checklist

### 1. Site symlinks → `global/`

After every clone or pull (Git has `core.symlinks = false`):

```bash
cd /Users/aztechnologies/Desarrollo/Aztechnologies/WEB
bash tools/recreate-site-symlinks.sh
```

### 2. Local web server (XAMPP + Apache)

See **`tools/FREE-APACHE-SETUP.md`** (XAMPP section):

- Virtual hosts for `elite.test`, `huskies.test`, …
- `/etc/hosts` → `127.0.0.1`
- Enable vhosts in `httpd.conf`: `Include etc/extra/httpd-vhosts.conf`
- If **403 Forbidden**: `chmod o+x ~` (see `tools/xampp-fix-403-macos.sh`)
- If redirect to **https://…/dashboard/**: `.htaccess` HTTPS skip for `*.test` (already patched)

### 3. `config.ini` per site

Active **`path`** (Mac):

```ini
path = /Users/aztechnologies/Desarrollo/Aztechnologies/WEB/elite
website = http://elite.test
```

Do **not** deploy `{site}/ini/` to Production — server keeps its own config.

### 4. Production deploy (SFTP)

Credentials: **`.local/sftp-development.env`** (never commit).

```bash
pip3 install --user paramiko   # once

# Single file(s)
bash tools/deploy-production-files.sh global/ajax/Admin/GamesCoach/changeWeeksAdmin.php

# Catch-up all git M/?? uploads + real deletions
bash tools/deploy-production-promote.sh
```

Report: `.local/deploy-production-promote-*.txt`

**Deploy rules:** upload `global/**` and site files **except** `{site}/ini/**` and `{site}/imagenes/**`. Skip git `D` on `elite/ajax` etc. (symlinks, not real deletes).

Run deploy in **Terminal.app** — Cursor’s agent sandbox may block SSH/DNS.

### 5. Optional: auto-deploy on save

Requires [fswatch](https://github.com/emcrisostomo/fswatch): `brew install fswatch`

```bash
bash tools/start-deploy-production-watch.sh
```

---

## Daily workflow

| Task | Command |
|------|---------|
| Start Apache | XAMPP Control → Start Apache |
| Fix symlinks | `bash tools/recreate-site-symlinks.sh` |
| Edit code | Cursor |
| Deploy one file | `bash tools/deploy-production-files.sh path/to/file` |
| Full promote | `bash tools/deploy-production-promote.sh` |
| PHP errors | `logs/php_errors.log` |

---

## Flyers / ImageMagick (optional)

```bash
brew install imagemagick ghostscript
cp .local/flyer-export.env.example.macos .local/flyer-export.env
```

---

## Legacy (Windows — archived)

| Old (Windows) | Mac replacement |
|---------------|-----------------|
| `C:\cursor\aztechnologies` | `/Users/aztechnologies/Desarrollo/Aztechnologies/WEB` |
| Laragon `*.test` | XAMPP vhosts + `/etc/hosts` |
| `tools\recreate-site-junctions.ps1` | `bash tools/recreate-site-symlinks.sh` |
| `deploy-production-files.ps1` (FTP) | `bash tools/deploy-production-files.sh` (SFTP) |
| `deploy-production-promote.ps1` | `bash tools/deploy-production-promote.sh` |
| `deploy-production-watch.ps1` | `bash tools/start-deploy-production-watch.sh` |
| `IMAGICK_WINDOWS_SETUP.md` | Homebrew imagemagick + `.local/flyer-export.env` |

PowerShell scripts remain in `tools/` for reference only.

---

## MySQL

- **Remote (current):** `servername = www.aztechnologies.tech` in each `ini/config.ini`
- **Local:** import dump, set `servername = 127.0.0.1`, use XAMPP MySQL

---

## Git

```bash
git config core.symlinks false   # typical on Mac after Windows clone
```

Commit code changes; never commit `.local/`, `logs/`, or site `ini/` secrets.
