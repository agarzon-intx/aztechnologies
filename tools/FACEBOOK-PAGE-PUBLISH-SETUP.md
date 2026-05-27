# Facebook Page flyer publishing — Meta app setup

Publishing uses **upload** `POST /{page-id}/photos` (`published=false`) then **publish** `POST /{page-id}/feed` with `attached_media` (avoids deprecated `publish_actions`). Required permissions on your **app** and **Page access token**:

| Permission | Why |
|------------|-----|
| **pages_show_list** | List Pages you manage (`GET /me/accounts`) |
| **pages_read_engagement** | Dependency for posting and reading Page content |
| **pages_manage_posts** | Publish photo posts to the Page |
| **pages_manage_metadata** | Often required for photo upload (error **#283** without it) |

### Common errors

| Error | Fix |
|-------|-----|
| **#200** `pages_read_engagement` not available | Add permission on app in App Review, regenerate token |
| **#283** `pages_manage_metadata` required | Add **pages_manage_metadata** on app + token (see below) |
| **#3** Application does not have capability | Do not use Page albums API (this project uses photo posts) |

## 1. Meta app type

1. Open [developers.facebook.com](https://developers.facebook.com/) → **My Apps** → your app.
2. Use case: **“Manage everything on your Page”** / **Business**.
3. Add **Facebook Login** / **Pages** product if needed.

## 2. App Review → Permissions

1. **App Review** → **Permissions and Features**.
2. Request **Advanced Access** (or enable in Development for your own Page) for **all four**:
   - `pages_show_list`
   - `pages_read_engagement`
   - `pages_manage_posts`
   - **`pages_manage_metadata`**
3. Use case: *“Publish league schedule flyer images to our Facebook Page feed.”*

## 3. Page access token (not User token)

1. [Graph API Explorer](https://developers.facebook.com/tools/explorer/) → select **your app**.
2. **Get User Access Token** → enable all four permissions above → Generate.
3. Run: **`GET /me/accounts`**
4. Copy that Page’s **`access_token`** and **`id`** (`page_id`).
5. Update `.local/facebook.env` or `ini/config.ini` `[facebook]`.

**Do not** use the User token from step 2 for publishing — only the Page token from `/me/accounts`.

## 4. Long-lived token (optional)

Use [Access Token Debugger](https://developers.facebook.com/tools/debug/accesstoken/) to extend or exchange for a long-lived Page token.

## 5. Verify locally

While logged in:

`http://elite.test/ajax/Flyers/facebookTokenCheck.php`

## 6. Project config

**Local:** `C:\cursor\aztechnologies\.local\facebook.env`

```ini
[default]
enabled = 1
page_id = YOUR_PAGE_NUMERIC_ID
access_token = YOUR_PAGE_ACCESS_TOKEN
```

**Production:** deploy changed files with `tools/deploy-production-files.ps1` or `tools/deploy-production-promote.ps1` (do not upload `*/ini/` on sites). Facebook secrets in `.local/facebook.env` only.

---

## Share on Facebook (personal profile / groups)

Meta no longer allows apps to auto-post to a **personal timeline** (`publish_actions` is deprecated). The flyer menu includes **Share on Facebook**, which:

1. Generates PNGs on the server (same as PNG ZIP).
2. Stores each image under `tmp/flyer-share/` with a secret token (48h).
3. Opens Facebook’s Share Dialog (`sharer.php`) for each image — you confirm where to post (profile, group, or Page).

**Requirements**

- `website` in `ini/config.ini` must be the public site URL (HTTPS on production) so Facebook can load the preview image.
- PNG export must work (Imagick or ImageMagick/Ghostscript — same as PNG ZIP).
- No Facebook app token needed for sharing.

**Deploy files:** `global/include/flyer_*.php`, `global/ajax/Flyers/*`, `*/pdf/flyerShare*.php`, `main.js.php`, etc. via `tools/deploy-production-promote.ps1` (see `.cursor/rules/auto-deploy-production.mdc`).

**Note:** `http://elite.test` previews may not work inside Facebook until you use a publicly reachable HTTPS URL on production.
