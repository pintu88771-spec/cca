# Changelog

All notable changes to this project are documented here.
Format follows [Keep a Changelog](https://keepachangelog.com/) and semantic versioning.

## [0.1.0] — 2026-11-07

### Added

- Project foundation for PHP 8.2 + MySQL on shared hosting (no Node runtime required).
- Database schema: `users`, `festivals`, `donations`, `settings`, `security_pin`, `audit_logs`, `login_attempts`.
  - DB-level guard allows at most one `super_admin`.
  - Donation amount check, foreign keys and reporting indexes.
- Demo seed: festival 2026, super admin/admin/volunteer, 15 donors + anonymous donor, approved and pending donations, settings, demo PIN.
- Public **live honour board** (single full screen):
  - Rank #1 hero with animated gradient border, crown, avatar ring, message.
  - Faded rank #2 / #3 cards.
  - Total collected, target, Indian-formatted progress bar, countdown.
  - Bottom dock: Staff, Account, Donors.
  - Top-50 donors slide-up sheet with live search.
  - 8-second polling via `public/api/home.php`.
  - Confetti + “New leader!” toast when rank #1 changes.
  - Hindi / English toggle persisted in `localStorage`.
- Design system: Tailwind CSS v4 tokens, glass surfaces, badges, avatars, motion, reduced-motion support, compact-phone breakpoints.
- Security baseline: PDO prepared statements for reads, HTML escaping helper, security headers, upload folder execution block, root `.htaccess` that hides application internals.
- Documentation: README, living project tree, UI guidelines, roadmap, changelog.

### Notes

- `app.demo_mode = true` by default so the board is reviewable before real donations.
- Demo credentials and PIN must be changed before go-live.
- Auth, donation flow, approvals, dashboards, receipts and settings are planned in `docs/ROADMAP.md`.
