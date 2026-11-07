<img width="1366" height="618" alt="image" src="https://github.com/user-attachments/assets/b1ac6757-3c75-4a2b-b3db-2c41b3239ff3" />

# Chanda Collection App

A mobile-first festival chanda (donation) collection web app for **Chhath Puja 2026**, reusable for Durga Puja, Saraswati Puja and similar festivals.

Built for **shared hosting (PHP 8.2 + MySQL/MariaDB)** with no Node.js runtime required in production.

---

## What is built so far (v0.1.0)

- **Live honour board home page** — the signature public screen:
  - Rank #1 hero banner with photo/initials, name, locality, amount, message
  - Rank #2 and #3 cards, faded, live updating
  - Festival title, countdown, total collected, target progress bar
  - Phone-style bottom dock: Staff (lock), Account (user), Donors (trophy)
  - Top-50 donors bottom sheet with search, badges and live refresh
  - Confetti + "New leader!" celebration when rank #1 changes
  - Hindi / English toggle (persisted per device)
  - Auto-refresh every 8 seconds via JSON polling
- **Database schema** for all four roles, donations, settings, PIN, audit log and rate limiting
- **Demo seed data** so the UI can be reviewed before real donations arrive
- **Modern UI system** (Tailwind CSS v4, compiled) + vanilla JS (no runtime framework)

Everything else from the requirements (auth, donation flow, approvals, dashboard, receipts) is mapped in [docs/ROADMAP.md](docs/ROADMAP.md).

---

## Requirements

- PHP **8.2+** (XAMPP is fine)
- MySQL **8+** / MariaDB **10.4+**
- Node **20+** only for building CSS (not needed on the production server)

---

## Quick start (XAMPP)

1. **Configure the database**

   ```powershell
   C:\xampp\mysql\bin\mysql.exe -u root < database\schema.sql
   C:\xampp\mysql\bin\mysql.exe -u root < database\seed.sql
   ```

2. **Check config** — `config/config.php` ships with XAMPP defaults (`root`, empty password, database `cca`). For production, copy `config/config.example.php` and fill in real values. `config/config.php` is git-ignored.

3. **Build the CSS** (one-time / after editing `resources/css/app.css`):

   ```powershell
   cmd /c "npm install"
   cmd /c "npm run build:css"
   ```

4. **Run it**

   Option A — PHP dev server:

   ```powershell
   C:\xampp\php\php.exe -S 127.0.0.1:8080 -t public
   ```

   Open <http://127.0.0.1:8080>

   Option B — Apache: put the project in `htdocs\cca` and open <http://localhost/cca/public/>. The root `.htaccess` also forwards `http://localhost/cca/` to `public/`.

> **Important:** the web root is **`public/`**. Never point the domain at the project root in production.

---

## Demo accounts

| Role | Mobile | Password |
| --- | --- | --- |
| Super Admin | `9000000001` | `Chanda@123` |
| Admin | `9000000002` | `Chanda@123` |
| Volunteer | `9000000003` | `Chanda@123` |
| Donor | `9811000010` | `Chanda@123` |
| Settings PIN (demo) | — | `123456` |

Change every demo credential before going live.

---

## Project structure

```text
cca/
├─ public/            ← web root (index.php, api, assets, uploads)
├─ app/               ← PHP application (Core, Controllers, Repositories, Support)
├─ config/            ← config.php (git-ignored) + example
├─ resources/         ← view templates + Tailwind source
├─ database/          ← schema.sql + seed.sql
├─ docs/              ← project tree, UI guidelines, roadmap, changelog
└─ storage/           ← logs, backups (not web-accessible)
```

Full annotated tree: **[docs/PROJECT_TREE.md](docs/PROJECT_TREE.md)** — update it whenever files/features change.

---

## Documentation

| File | Purpose |
| --- | --- |
| [docs/PROJECT_TREE.md](docs/PROJECT_TREE.md) | Living file tree + feature map + where to add things |
| [docs/UI_GUIDELINES.md](docs/UI_GUIDELINES.md) | Design tokens, components, motion and accessibility rules |
| [docs/ROADMAP.md](docs/ROADMAP.md) | MVP / Phase 2 checklist and progress |
| [docs/CHANGELOG.md](docs/CHANGELOG.md) | Version history |
| `Chanda Collection App – Final Requirements (v1.1).md` | Source requirements |

---

## Demo mode

`config/config.php` → `app.demo_mode = true` shows a realistic sample leaderboard when the database is unreachable or has no approved donations. Set it to `false` on production so the board shows the real (possibly empty) state.
