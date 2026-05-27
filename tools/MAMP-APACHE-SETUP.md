# Apache local dev (Laragon-style on macOS)

**Laragon only runs on Windows.** On macOS, use **MAMP** (Apache + PHP + MySQL) with virtual hosts for `*.test`.

Repo root: `/Users/aztechnologies/Desarrollo/Aztechnologies/WEB`

---

## 1. Install MAMP

1. Download: https://www.mamp.info/en/downloads/
2. Install **MAMP** (free is enough).
3. Open **MAMP** → **Preferences**:
   - **PHP**: 8.2 or 8.3
   - **Web server**: **Apache** (not Nginx)
   - **Document root**: leave default for now (we use vhosts per site)
4. **Start** Apache (and MySQL only if you use a local database).

Note the Apache port (often **8888** for MAMP free, or **80** if you changed it). Examples below use port **80**; if yours is 8888, use `http://elite.test:8888`.

---

## 2. Symlinks (required once per clone)

In Terminal:

```bash
cd /Users/aztechnologies/Desarrollo/Aztechnologies/WEB
bash tools/recreate-site-symlinks.sh
```

---

## 3. Hosts file (`*.test`)

Edit as admin:

```bash
sudo nano /etc/hosts
```

Add:

```
127.0.0.1 elite.test
127.0.0.1 huskies.test
127.0.0.1 lidep.test
127.0.0.1 nuestrodeporte.test
127.0.0.1 vollidep.test
127.0.0.1 voleibalmetepec.test
```

Save (Ctrl+O, Enter, Ctrl+X).

---

## 4. Apache virtual hosts

Edit MAMP’s vhosts file (path may vary slightly by MAMP version):

```text
/Applications/MAMP/conf/apache/extra/httpd-vhosts.conf
```

At the **end** of the file, paste (adjust port if not 80):

```apache
# Aztechnologies WEB — local dev
WEB="/Users/aztechnologies/Desarrollo/Aztechnologies/WEB"

<VirtualHost *:80>
    ServerName elite.test
    DocumentRoot "${WEB}/elite"
    <Directory "${WEB}/elite">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName huskies.test
    DocumentRoot "${WEB}/huskies"
    <Directory "${WEB}/huskies">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName lidep.test
    DocumentRoot "${WEB}/lidep"
    <Directory "${WEB}/lidep">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName nuestrodeporte.test
    DocumentRoot "${WEB}/nuestrodeporte"
    <Directory "${WEB}/nuestrodeporte">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName vollidep.test
    DocumentRoot "${WEB}/vollidep"
    <Directory "${WEB}/vollidep">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName voleibalmetepec.test
    DocumentRoot "${WEB}/voleibalmetepec"
    <Directory "${WEB}/voleibalmetepec">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**If Apache rejects `${WEB}`**, replace `${WEB}` with the full path on each line.

Ensure vhosts are included — in `/Applications/MAMP/conf/apache/httpd.conf` this line should be **uncommented**:

```apache
Include /Applications/MAMP/conf/apache/extra/httpd-vhosts.conf
```

Restart Apache from MAMP.

---

## 5. `config.ini` (already set for Mac)

Active lines should look like:

```ini
path = /Users/aztechnologies/Desarrollo/Aztechnologies/WEB/elite
website = http://elite.test
```

If you use port **8888**, set e.g. `website = http://elite.test:8888` for each site.

---

## 6. PHP / Apache notes for this project

- **`site_paths.php`** sets `include_path` to `global/` — works without cPanel paths in `.user.ini`.
- **`.user.ini` / `php.ini`** under each site still mention `/home1/aztechn1/...` — ignore on Mac; MAMP’s PHP is used.
- **mysqli**: MAMP PHP usually includes it. Check: MAMP → PHP info, search `mysqli`.
- **imagick** (flyers): optional; install separately or use `.local/flyer-export.env` (see `.local/flyer-export.env.example.macos`).

Errors: `WEB/logs/php_errors.log`

---

## 7. Test

1. MAMP: Apache **green**
2. Browser: http://elite.test (or `:8888`)
3. Terminal:

```bash
/Applications/MAMP/bin/php/php8.2.26/bin/php -v
curl -sI http://elite.test | head -5
```

(Adjust PHP binary path to your MAMP version folder under `/Applications/MAMP/bin/php/`.)

---

## If you develop on **Windows with Laragon** instead

1. Put repo at e.g. `C:\cursor\aztechnologies`
2. In each `ini/config.ini`, activate Windows path:

```ini
path = C:\cursor\aztechnologies\elite
```

(comment out the macOS `path` line)

3. Laragon → **Menu → Apache → sites-enabled** or auto virtual hosts: folder name `.test`
4. Run: `powershell -ExecutionPolicy Bypass -File tools\recreate-site-junctions.ps1`
5. `website = http://elite.test` (no trailing slash)

---

## MySQL

Configs default to remote host `www.aztechnologies.tech`. For Laragon/MAMP local MySQL, import your DB and set in each `config.ini`:

```ini
servername = 127.0.0.1
```

(and local username/password/schema)
