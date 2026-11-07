# Project Tree & Build Map

> **Living document.** Update this file every time a file is added, moved or completed.
> It is the fastest way for a future session (human or AI) to understand the app without re-reading everything.

**Last updated:** 2026-11-07 · **Version:** 0.1.0

---

## Legend

| Mark | Meaning |
| --- | --- |
| ✅ | Built and working |
| 🧪 | Built, needs manual/device testing |
| 🚧 | In progress / partial |
| ⬜ | Planned (not started) |
| 🔒 | Security-sensitive — review before production |

---

## File tree

```text
cca/
├─ .htaccess                              ✅ root → public/, blocks internals & .md/.sql
├─ .gitignore                             ✅
├─ README.md                              ✅ setup + quick start
├─ package.json                           ✅ Tailwind v4 build scripts
├─ package-lock.json                      ✅
│
├─ Chanda Collection App – Final Requirements (v1.1).md    ✅ source of truth
├─ Chanda Collection App – Final Requirements (v1.1).pdf   ✅
│
├─ app/
│  ├─ bootstrap.php                       ✅ config, autoload, BASE_PATH, error mode
│  ├─ Core/
│  │  ├─ Config.php                       ✅ dot-notation config accessor
│  │  ├─ Database.php                     ✅ PDO singleton, graceful null fallback
│  │  ├─ Auth.php                         ⬜ sessions, login, role guards
│  │  ├─ Csrf.php                         ⬜ CSRF token per form/session
│  │  ├─ RateLimiter.php                  ⬜ login/registration throttling
│  │  ├─ SecurityPin.php                  ⬜ hashed settings PIN + lockout
│  │  ├─ Session.php                      ⬜ secure session bootstrap
│  │  ├─ Settings.php                     ⬜ cached settings repository
│  │  └─ Validator.php                    ⬜ input validation helpers
│  ├─ Controllers/
│  │  ├─ HomeController.php               ✅ public honour board
│  │  ├─ AuthController.php               ⬜ login / register / logout
│  │  ├─ DonationController.php           ⬜ donate, UTR submit, top-up
│  │  ├─ ProfileController.php            ⬜ donor profile + history
│  │  ├─ ReceiptController.php            ⬜ acknowledgement / printable receipt
│  │  ├─ DonorsController.php             ⬜ full rank-wise donors list
│  │  ├─ VolunteerController.php          ⬜ volunteer dashboard + entries
│  │  ├─ AdminController.php              ⬜ dashboard, approvals, reports
│  │  ├─ SettingsController.php           🔒 PIN-protected settings
│  │  └─ UserAdminController.php          🔒 accounts (super admin only)
│  ├─ Repositories/
│  │  ├─ LeaderboardRepository.php        ✅ ranking, totals, badges, demo fallback
│  │  ├─ DonationRepository.php           ⬜ create/approve/reject/cancel
│  │  ├─ UserRepository.php               ⬜ users, role limits, search
│  │  ├─ FestivalRepository.php           ⬜ active festival / multi-festival
│  │  ├─ SettingsRepository.php           ⬜ key/value reads/writes
│  │  └─ AuditLogRepository.php           ⬜ audit writes/queries
│  ├─ Support/
│  │  ├─ helpers.php                      ✅ e(), url(), asset(), view(), inr(), initials()
│  │  ├─ Badge.php                        ✅ rank → badge cutoffs + labels
│  │  ├─ DemoData.php                     ✅ sample leaderboard
│  │  ├─ Receipt.php                      ⬜ receipt numbering + HTML/PDF
│  │  ├─ ImageUpload.php                  🔒 validate + re-encode uploads
│  │  └─ Export.php                       ⬜ CSV/Excel export
│  └─ Views/                              ⬜ reserved (views live in resources/views)
│
├─ config/
│  ├─ config.example.php                  ✅ template (safe to commit)
│  └─ config.php                          ✅ local XAMPP config (git-ignored) 🔒
│
├─ database/
│  ├─ schema.sql                          ✅ all tables + constraints + indexes
│  ├─ seed.sql                            ✅ festival, users, donations, settings, PIN
│  └─ migrations/                         ⬜ future incremental migrations
│
├─ docs/
│  ├─ PROJECT_TREE.md                     ✅ this file
│  ├─ UI_GUIDELINES.md                    ✅ design system + component rules
│  ├─ ROADMAP.md                          ✅ MVP/Phase 2 checklist
│  └─ CHANGELOG.md                        ✅ version history
│
├─ public/                                ← WEB ROOT
│  ├─ .htaccess                           ✅ front controller + security headers
│  ├─ index.php                           ✅ front controller / router
│  ├─ api/
│  │  └─ home.php                         ✅ JSON leaderboard (polling endpoint)
│  ├─ assets/
│  │  ├─ css/app.css                      ✅ compiled Tailwind (generated — do not edit)
│  │  └─ js/app.js                        ✅ honour board interactivity
│  ├─ uploads/                            ⬜ donor photos / screenshots
│  │  └─ .htaccess                        🔒 blocks PHP execution
│  └─ (login.php, donate.php, …)          ⬜ planned page entry points if not routed
│
├─ resources/
│  ├─ css/app.css                         ✅ Tailwind source + design tokens + components
│  ├─ js/                                 ⬜ (JS currently lives in public/assets/js)
│  └─ views/
│     ├─ layouts/
│     │  └─ public.php                    ✅ head, fonts, aurora, toast, confetti, JSON boot
│     ├─ public/
│     │  └─ home.php                      ✅ full-screen live honour board + donors sheet
│     ├─ auth/                            ⬜ login, register
│     ├─ donor/                           ⬜ profile, edit, history, top-up, receipt
│     ├─ volunteer/                       ⬜ dashboard, add donation, entries
│     ├─ admin/                           ⬜ dashboard, approvals, donors, reports
│     └─ partials/                        ⬜ shared nav, badge chips, pagination
│
├─ storage/                               ← NOT web-accessible
│  ├─ logs/                               ⬜ app/audit output
│  └─ backups/                            ⬜ daily DB backups
│
└─ node_modules/                          (build-only, never deploy)
```

---

## Feature map — where to add the next thing

| Feature to build | Start here | Then |
| --- | --- | --- |
| Login / register / logout | `app/Core/Auth.php`, `AuthController` | `resources/views/auth/*`, routes in `public/index.php` |
| Role guards (server-side) | `app/Core/Auth.php` | call at the top of every controller action |
| CSRF on forms | `app/Core/Csrf.php` | every POST controller + hidden input in views |
| Donate (UPI + UTR) | `DonationRepository` | `DonationController`, `resources/views/donor/donate.php` |
| Volunteer cash entry | `DonationRepository` | `VolunteerController`, pending-by-default rule |
| Approve / reject / cancel | `DonationRepository` | `AdminController` + audit log + reason required |
| Top-up | reuse donate flow | prefill donor + show “₹X more to #1” from leaderboard |
| Receipts | `app/Support/Receipt.php` | `ReceiptController` + printable view |
| Donors list (paid only) | `LeaderboardRepository` | `DonorsController` + `resources/views/donor/donors.php` |
| Admin dashboard | `AdminController` | aggregate queries + CSV `Export` |
| Settings (PIN) | `SecurityPin` + `SettingsRepository` | `SettingsController` + views |
| Account limits (1 super admin / 5 admins) | DB constraint + `UserRepository` | `UserAdminController` |
| Audit log | `AuditLogRepository` | call on every sensitive mutation |
| Image uploads | `app/Support/ImageUpload.php` | store in `public/uploads/`, re-encode + random name |
| Daily backup | host cron or `storage/backups` | document the cron command in README |

---

## Rules for keeping this tree useful

1. **Update the mark in the same commit/change** as the code (⬜ → ✅ / 🚧).
2. When adding a folder, add it here even if empty (with `⬜` purpose).
3. Keep the **Feature map** table in sync — it is the entry point for future work.
4. Never list `node_modules/`, `vendor/`, generated CSS or secrets in detail.
5. Bump **Last updated** and note notable changes in `CHANGELOG.md`.
6. Security-sensitive rows stay 🔒 until reviewed against requirements §6.
