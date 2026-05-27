# macOS local dev (PHP + web server)

> **Start here:** `tools/MAC-MIGRATION.md` (symlinks, XAMPP, SFTP deploy, daily workflow).

Closest match to **Laragon** on Windows: **Laravel Valet** (nginx + PHP + `*.test` DNS) or **XAMPP** (see `tools/FREE-APACHE-SETUP.md`).

Your sites expect:

- Document root: `WEB/<site>/` (elite, huskies, lidep, …)
- URLs: `http://elite.test`, `http://lidep.test`, etc. (no trailing slash in `config.ini`)
- PHP extensions: **mysqli** (required), **imagick** (optional, for flyer PNG)

---

## Option A — Laravel Herd (easiest, GUI)

1. Download and install: https://herd.laravel.com/macos
2. Open **Herd** → add each site folder as a “site” (or use **Park** on parent folder; see below).
3. Enable **PHP 8.2+** and **mysqli** in Herd settings.
4. For each league site, point the site path to:
   - `/Users/aztechnologies/Desarrollo/Aztechnologies/WEB/elite`
   - … same for `huskies`, `lidep`, `nuestrodeporte`, `vollidep`, `voleibalmetepec`
5. Herd assigns `https://elite.test` (and http). Your `config.ini` uses `http://elite.test` — fine.

**Park (all sites at once):** In Herd, “Park” the repo root only if you accept extra domains (`global.test`, `tools.test`). Safer: link the six site folders individually.

After install, in Terminal from the repo:

```bash
bash tools/recreate-site-symlinks.sh
```

---

## Option B — Homebrew + Laravel Valet (CLI, Laragon-like)

Run these in **Terminal.app** (not inside a sandboxed IDE terminal if sudo fails).

### 1. Install Homebrew

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

Apple Silicon — add brew to your shell (the installer prints this; typical):

```bash
echo 'eval "$(/opt/homebrew/bin/brew shellenv)"' >> ~/.zprofile
eval "$(/opt/homebrew/bin/brew shellenv)"
```

### 2. Install PHP, Composer, Valet

```bash
brew install php composer
composer global require laravel/valet
echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> ~/.zprofile
export PATH="$HOME/.composer/vendor/bin:$PATH"
valet install
```

(`valet install` asks for your password once — configures nginx + dnsmasq for `*.test`.)

### 3. Link this project’s sites

From the repo:

```bash
cd /Users/aztechnologies/Desarrollo/Aztechnologies/WEB
bash tools/setup-macos-valet-sites.sh
bash tools/recreate-site-symlinks.sh
```

### 4. Verify

```bash
valet links
curl -sI http://elite.test | head -5
php -v
php -m | grep -i mysqli
```

Open in browser: http://elite.test

---

## MySQL

`config.ini` uses remote host `www.aztechnologies.tech` by default. You need:

- Network access, and your IP allowed on the server, **or**
- A local MySQL dump and `servername = 127.0.0.1` in each `ini/config.ini`

Test connection (after PHP is installed):

```bash
php -r '$m=@new mysqli("www.aztechnologies.tech","USER","PASS","SCHEMA"); echo $m->connect_error ?: "OK\n";'
```

(Use credentials from one site’s `ini/config.ini`.)

---

## Flyer / ImageMagick (optional)

```bash
brew install imagemagick ghostscript
```

Copy and edit:

```bash
cp .local/flyer-export.env.example .local/flyer-export.env
```

Example macOS paths:

```ini
magick_path = /opt/homebrew/bin/magick
gs_path = /opt/homebrew/bin/gs
```

---

## Troubleshooting

| Problem | Fix |
|--------|-----|
| `elite.test` doesn’t resolve | Run `valet install` again; restart Terminal; check `valet status` |
| 404 on `/ajax/...` | Run `bash tools/recreate-site-symlinks.sh` |
| Blank page / 500 | See `logs/php_errors.log`; enable display_errors in Valet/Herd PHP ini for dev |
| Wrong `include_path` | `site_paths.php` should load first; ignore cPanel paths in `.user.ini` on Mac |
| PHP not found | `eval "$(/opt/homebrew/bin/brew shellenv)"` and open a new terminal |

---

## Quick smoke test (single site, no Valet)

Only for a quick check — **not** for daily dev (no `*.test`, one site only):

```bash
cd /Users/aztechnologies/Desarrollo/Aztechnologies/WEB/elite
php -S localhost:8080
```

Open http://localhost:8080 — cookies/aliases may differ from `elite.test`.
