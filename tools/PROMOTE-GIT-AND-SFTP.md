# Promote: Git + SFTP (Development)

> **macOS (primary):** see **`tools/MAC-MIGRATION.md`** — Production deploy uses SFTP shell scripts, not this Windows/PuTTY flow.

Use this checklist when **another agent** (or you) needs to **commit, push, and deploy** the `aztechnologies` repo to **cPanel Development** over SFTP.

Repository root (Windows): `C:\cursor\aztechnologies`  
Default Git branch: `initial-upload`  
Remote: `origin` (GitHub)

---

## 0. Git / GitHub token for `git push`

**Do not put a real token inside this file in Git.** If this markdown is in the repo, anyone with repo access can read it.

Store the token **only** in a **local, ignored** file under **`.local/`** (the whole `.local/` directory is in `.gitignore`).

### Option A — add to `.local/sftp-development.env` (recommended)

Add a line (same file you use for SFTP; **never commit**):

```env
GITHUB_TOKEN=ghp_REPLACE_WITH_YOUR_GITHUB_PERSONAL_ACCESS_TOKEN
```

Create the PAT in GitHub: **Settings → Developer settings → Personal access tokens** (classic: `repo` scope, or fine-grained: Contents read/write for this repository).

### Option B — session only (PowerShell)

```powershell
$env:GITHUB_TOKEN = 'ghp_REPLACE_WITH_YOUR_TOKEN'
cd C:\cursor\aztechnologies
git push https://github.com/agarzon-intx/aztechnologies.git HEAD:initial-upload
# When prompted: Username = your GitHub username ; Password = paste PAT
```

Or push without prompt (uses Bearer header; token still visible in shell history — prefer Option A):

```powershell
$env:GITHUB_TOKEN = 'ghp_REPLACE_WITH_YOUR_TOKEN'
cd C:\cursor\aztechnologies
git -c http.extraHeader="AUTHORIZATION: bearer $env:GITHUB_TOKEN" push origin initial-upload
```

### For another agent (Cursor / automation)

Give the agent **Cursor User Rules**, **project secrets**, or paste the token **in chat** for that session only — not in committed markdown. The agent can read **`.local/sftp-development.env`** if you add `GITHUB_TOKEN=...` there on your machine.

**Placeholder (not a real token — replace before use):**

```text
GITHUB_TOKEN=ghp_yourTokenHere
```

---
## 1. What to commit

- Stage **intended** paths only (PHP, `global/`, site `ini/config.ini`, etc.).
- **Do not** stage unless you mean it:
  - `logs/php_errors.log`
  - Mass **`D`** (deleted) entries under each site for `ajax`, `assets`, `Form`, … — those are usually **broken junctions/symlinks** on disk, not real deletes from Git. Restoring: `tools/recreate-site-junctions.ps1` or `git checkout -- <path>`.

```powershell
cd C:\cursor\aztechnologies
git status --short
git add <paths...>
git commit -m "Clear message describing the change."
git push origin initial-upload
```

---

## 2. Full-tree deploy to Development (recommended)

Script: **`tools/deploy-development-sftp.ps1`**

- Builds **`git archive`** of `HEAD` (or optional ref), **excludes** every site’s **`imagenes/`** only (images are **not** uploaded). Each site’s **`ini/`** is **included** from Git and overwrites Development `ini` on extract.
- Uploads the tarball with **PuTTY `pscp`**.
- On the server: **`tar -xzf`** under **`SFTP_REMOTE_PATH`** (preserves symlinks that exist **inside** the archive).
- Then **SSH via `plink`**: for each site, **`imagenes`** under Development is **`rm -rf`** and **`ln -s`** to **Production** so **`imagenes` always points at Production** for that site.

### Prerequisites

- **PuTTY** installed (default paths):
  - `C:\Program Files\PuTTY\pscp.exe`
  - `C:\Program Files\PuTTY\plink.exe`
- **`.local/sftp-development.env`** at repo root (not committed). Keys:

| Variable | Required | Meaning |
|----------|----------|---------|
| `SFTP_HOST` | yes | SSH host |
| `SFTP_USER` | yes | SSH user |
| `SFTP_PASSWORD` | yes | SSH password (keep local only) |
| `SFTP_REMOTE_PATH` | yes | **Development** parent path (contains `elite/`, `huskies/`, …) e.g. `/home1/aztechn1/public_html/Development` |
| `SFTP_PRODUCTION_BASE` | yes | **Production** parent path (same layout: `elite/`, `huskies/`, …) e.g. `/home1/aztechn1/public_html/Production` |

If **`SFTP_PRODUCTION_BASE`** is missing, the script **throws** (deploy is blocked until it is set).

### Run

```powershell
cd C:\cursor\aztechnologies
powershell -NoProfile -ExecutionPolicy Bypass -File tools\deploy-development-sftp.ps1
```

Optional: deploy a specific ref instead of `HEAD`:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File tools\deploy-development-sftp.ps1 initial-upload
```

### Sites handled for `imagenes` symlink

`elite`, `huskies`, `lidep`, `nuestrodeporte`, `vollidep`, `voleibalmetepec` — each gets:

`<SFTP_REMOTE_PATH>/<site>/imagenes` → symlink → `<SFTP_PRODUCTION_BASE>/<site>/imagenes`

**`ini/`** is **not** symlinked; it comes from the archive (same as the rest of the tracked tree).

If a Production **`imagenes`** path for a site is missing, the script prints **WARN** and skips that symlink.

---

## 3. One-off file upload (SFTP only)

For a **single file** (not full tree), use **`pscp`** with paths from `.local/sftp-development.env` (read host, user, password, `SFTP_REMOTE_PATH`).

Example pattern:

```powershell
$pscp = 'C:\Program Files\PuTTY\pscp.exe'
# load $h, $u, $pw, $base from .local\sftp-development.env
& $pscp -batch -pw $pw "C:\cursor\aztechnologies\voleibalmetepec\pdf\SomeFile.php" "${u}@${h}:$base/voleibalmetepec/pdf/SomeFile.php"
```

Do **not** upload **`imagenes/`** as a real folder on full syncs; the promote script omits it from the archive and restores the Production **`imagenes`** symlink after extract. **`ini/`** is deployed from Git with the rest of the tree.

---

## 4. Related files

| File | Purpose |
|------|---------|
| `tools/deploy-development-sftp.ps1` | Full promote: archive (no `imagenes`; **`ini/` included**) → SFTP → tar → Production **`imagenes`** symlink only |
| `tools/IMAGENES-SYMLINK-REQUIRED.txt` | Permanent rule + manual FTP notes |
| `tools/recreate-site-junctions.ps1` | Windows Laragon: recreate **junctions** from each site → `global/` (does **not** set Linux `imagenes`) |
| `.local/sftp-development.env` | Credentials + paths; optional **`GITHUB_TOKEN`** for `git push` (never commit) |

---

## 5. Quick reload line for another agent

Copy-paste:

> On Windows, repo `C:\cursor\aztechnologies`. **Git:** `GITHUB_TOKEN` in `.local/sftp-development.env` (or use Credential Manager / PAT at prompt); `git add` only intended files, `git commit`, `git push origin initial-upload`. **Deploy:** `powershell -NoProfile -ExecutionPolicy Bypass -File tools\deploy-development-sftp.ps1` — requires PuTTY and `.local\sftp-development.env` with `SFTP_HOST`, `SFTP_USER`, `SFTP_PASSWORD`, `SFTP_REMOTE_PATH`, **`SFTP_PRODUCTION_BASE`**. Script **excludes** `*/imagenes` from archive, **includes** `*/ini`, and **symlinks** each Development `imagenes` to Production. Details: **`tools/PROMOTE-GIT-AND-SFTP.md`**.
