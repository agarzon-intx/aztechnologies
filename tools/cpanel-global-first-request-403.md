# cPanel: first request to `global/*.php` returns 403 Forbidden, second works

## What is going on

A **403 Forbidden** on the **first** hit to PHP under `global/`, then **200** on the second (for any file in `global/`), is almost never a bug in your PHP code. It is usually one of these **server-side** behaviors:

1. **ModSecurity (OWASP CRS) or host WAF** — the first request matches a rule (path, query, body, headers). The engine may score the request, block once, or write an audit entry; a quick retry can behave differently (headers/cookies, timing, or rule phase).
2. **Imunify360 / similar** — *Proactive Defense* or real-time scan can **block the first execution** of a PHP file in a “new” tree until analysis finishes (seconds). The next request is already allow-listed or cached.
3. **Rare: symlink / metadata cache** — if `global` is reached via **symlink/junction**, a few hosts have been seen to return 403 on first `stat`/`readlink` failure, then succeed. **Permanent fix:** replace the link with a real directory deploy, or fix link permissions.

Your repo’s site `.htaccess` files do **not** deny `global/`; there is no `global/.htaccess` in Git by default. So tuning must happen on **cPanel / host**.

## Permanent fixes (pick the one that matches your logs)

### A) Confirm it is ModSecurity / Imunify (recommended first step)

1. In cPanel open **Errors** (or **Metrics → Errors**) or download **Apache** / **domlog** for the domain.
2. Reproduce the **one** failing request and note the exact **time**, **URL**, and any **ModSecurity** message or **Imunify** reference.
3. If the response includes **`ModSecurity`** or a **unique id**, open **ModSecurity Tools** (if available) and search that id — you will see the **rule id** that fired.

**Permanent mitigation:** ask the host (or use ModSecurity Tools, if you have access) to **disable that rule** for your account or **whitelist** the URI prefix, e.g.:

- `/global/ajax/`
- `/global/javascript/`
- `/global/objects/`

…only as wide as needed. Avoid turning off ModSecurity for the whole domain unless you accept the security trade-off.

### B) Imunify360

1. cPanel → **Imunify360** (or host panel).
2. Check **Proactive Defense** / **Malware** logs for the path under `global/`.
3. **Permanent mitigation:** add an **ignore path** or **exclusion** for your application tree (again: as narrow as possible), e.g. `.../public_html/.../global/**/*.php`, per vendor docs.

### C) “Leech protection”, IP deny, or hotlink rules

cPanel **Leech Protection** / custom **Deny** in `.htaccess` can return 403. Compare failing vs working request: same **cookie** / **referrer** / **HTTPS**? Fix the rule or disable leech protection for that path.

### D) You are opening a directory URL, not a `.php` file

`.../global/ajax/` with **no** `index.php` often returns **403** (directory listing disabled). The “second time works” might be a **different URL** (actual `.php`). **Permanent fix:** only link to concrete files; add no extra server magic.

## Optional repo-side hardening

- A minimal `global/.htaccess` with `Options -Indexes` is committed to avoid **ambiguous** directory requests; it does **not** replace WAF tuning if the 403 is ModSecurity/Imunify.

## What will *not* fix a WAF 403

Changing PHP `include_path`, `session_*`, or application bootstrap **does not** address Apache **403** before PHP runs (or during early request filtering). You need **log-driven** host/WAF configuration.
