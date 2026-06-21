# Free Apache + PHP on macOS (Laragon-style)

MAMP PRO is paid; these are **100% free** ways to run your six `*.test` sites with **Apache**.

Repo: `/Users/aztechnologies/Desarrollo/Aztechnologies/WEB`

---

## Before anything

```bash
cd /Users/aztechnologies/Desarrollo/Aztechnologies/WEB
bash tools/recreate-site-symlinks.sh
```

---

## Option 1 — XAMPP (easiest free bundle)

Apache + PHP + MySQL in one installer (like Laragon, but free).

### Install

1. https://www.apachefriends.org/download.html — **XAMPP for macOS**
2. Install to `/Applications/XAMPP`
3. Open **XAMPP Control** (or `sudo /Applications/XAMPP/xamppfiles/xampp startapache`)

Default Apache port is often **80** or check in XAMPP UI.

### Hosts file

```bash
sudo nano /etc/hosts
```

Add:

```
127.0.0.1 elite.test huskies.test lidep.test soccer.test nuestrodeporte.test vollidep.test voleibalmetepec.test demo.test
```

### Virtual hosts

Edit:

```text
/Applications/XAMPP/xamppfiles/etc/extra/httpd-vhosts.conf
```

Paste at the end (use your real path; no `${WEB}` if Apache complains):

```apache
# Aztechnologies
<VirtualHost *:80>
    ServerName elite.test
    DocumentRoot "/Users/aztechnologies/Desarrollo/Aztechnologies/WEB/elite"
    <Directory "/Users/aztechnologies/Desarrollo/Aztechnologies/WEB/elite">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName huskies.test
    DocumentRoot "/Users/aztechnologies/Desarrollo/Aztechnologies/WEB/huskies"
    <Directory "/Users/aztechnologies/Desarrollo/Aztechnologies/WEB/huskies">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName lidep.test
    ServerAlias soccer.test
    DocumentRoot "/Users/aztechnologies/Desarrollo/Aztechnologies/WEB/lidep"
    <Directory "/Users/aztechnologies/Desarrollo/Aztechnologies/WEB/lidep">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName nuestrodeporte.test
    DocumentRoot "/Users/aztechnologies/Desarrollo/Aztechnologies/WEB/nuestrodeporte"
    <Directory "/Users/aztechnologies/Desarrollo/Aztechnologies/WEB/nuestrodeporte">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName vollidep.test
    DocumentRoot "/Users/aztechnologies/Desarrollo/Aztechnologies/WEB/vollidep"
    <Directory "/Users/aztechnologies/Desarrollo/Aztechnologies/WEB/vollidep">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName voleibalmetepec.test
    DocumentRoot "/Users/aztechnologies/Desarrollo/Aztechnologies/WEB/voleibalmetepec"
    <Directory "/Users/aztechnologies/Desarrollo/Aztechnologies/WEB/voleibalmetepec">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

In `/Applications/XAMPP/xamppfiles/etc/httpd.conf`, ensure this line is **uncommented**:

```apache
Include etc/extra/httpd-vhosts.conf
```

Restart Apache from XAMPP.

### PHP path (Terminal)

```bash
/Applications/XAMPP/xamppfiles/bin/php -v
```

### Test

http://elite.test

If Apache uses another port (e.g. 8080), use `http://elite.test:8080` and update `website =` in each `ini/config.ini`.

---

## Option 2 — Homebrew Apache + PHP (free, no GUI)

Good if you prefer CLI and current PHP versions.

### Install (Terminal)

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
echo 'eval "$(/opt/homebrew/bin/brew shellenv)"' >> ~/.zprofile
eval "$(/opt/homebrew/bin/brew shellenv)"

brew install httpd php
```

### Start Apache (runs on port **8080** by default)

```bash
brew services start httpd
```

Open: http://elite.test:8080 — set in `config.ini`:

```ini
website = http://elite.test:8080
```

(or configure `Listen 80` in httpd.conf if nothing else uses port 80)

### Virtual hosts

Edit:

```text
/opt/homebrew/etc/httpd/extra/httpd-vhosts.conf
```

Use the same six `<VirtualHost>` blocks as in the XAMPP section above.

In `/opt/homebrew/etc/httpd/httpd.conf`:

1. Uncomment: `Include /opt/homebrew/etc/httpd/extra/httpd-vhosts.conf`
2. Find `#LoadModule rewrite_module` → remove `#` to enable mod_rewrite
3. Confirm `Listen 8080` (or 80)

Restart:

```bash
brew services restart httpd
```

### PHP with Homebrew Apache

```bash
brew install php
# Link PHP module — Homebrew prints hints after install; often:
echo 'LoadModule php_module "/opt/homebrew/opt/php/lib/httpd/modules/libphp.so"' >> /opt/homebrew/etc/httpd/httpd.conf
echo '<FilesMatch \.php$>' >> /opt/homebrew/etc/httpd/httpd.conf
echo '    SetHandler application/x-httpd-php' >> /opt/homebrew/etc/httpd/httpd.conf
echo '</FilesMatch>' >> /opt/homebrew/etc/httpd/httpd.conf
brew services restart httpd
```

Check: `php -v` and load http://elite.test:8080

---

## Option 3 — Laravel Valet (free, **nginx** not Apache)

If Apache is not required, Valet is the fastest multi-site `*.test` setup:

See `tools/MACOS-DEV-SETUP.md` → Option B.

```bash
brew install php composer
composer global require laravel/valet
valet install
bash tools/setup-macos-valet-sites.sh
```

---

## Comparison

| Tool        | Cost | Server  | Multi `*.test` | Difficulty |
|------------|------|---------|----------------|------------|
| XAMPP      | Free | Apache  | Yes (vhosts)   | Easy       |
| Homebrew   | Free | Apache  | Yes (vhosts)   | Medium     |
| Valet/Herd | Free | Nginx   | Yes (auto)     | Easy       |
| Laragon    | Free | Apache  | Yes            | Windows only |

---

## MySQL

- **Remote** (current): `servername = www.aztechnologies.tech` in `config.ini`
- **Local** (XAMPP/Homebrew): start MySQL, import DB, use `servername = 127.0.0.1`

XAMPP MySQL: start from XAMPP Control.  
Homebrew: `brew install mysql` → `brew services start mysql`

---

## Troubleshooting

| Issue | Fix |
|-------|-----|
| Port 80 in use | Use 8080 or stop other web servers (AirPlay Receiver off in System Settings if needed) |
| 403 Forbidden | `Require all granted` in `<Directory>` |
| 404 on `/ajax/` | `bash tools/recreate-site-symlinks.sh` |
| PHP downloads instead of running | Apache PHP module not loaded (see Homebrew section) |
| Wrong site | Check `ServerName` and `/etc/hosts` |

Logs:

- XAMPP: `/Applications/XAMPP/xamppfiles/logs/error_log`
- Homebrew: `/opt/homebrew/var/log/httpd/error_log`
- App: `WEB/logs/php_errors.log`
