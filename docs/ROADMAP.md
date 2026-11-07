# Roadmap

Source requirements: `Chanda Collection App – Final Requirements (v1.1).md`
Target go-live: **17 November 2026** · MVP ready: **14 November 2026**

Legend: ✅ done · 🚧 in progress · ⬜ planned

---

## Phase 0 — Foundation (done)

- ✅ PHP 8.2 + MySQL architecture on shared-hosting constraints
- ✅ Database schema: users, festivals, donations, settings, security_pin, audit_logs, login_attempts
- ✅ Super-admin uniqueness enforced by DB generated-column unique index
- ✅ Seed data (festival, 4 roles, donations, badges, settings, demo PIN)
- ✅ Config outside web root, PDO prepared statements, output escaping helpers
- ✅ Modern design system + compiled Tailwind build
- ✅ Public live honour board with polling endpoint

---

## MVP — must be live by 14 Nov

### Auth & roles
- ⬜ Mobile + password login for all four roles; session regeneration
- ⬜ Donor registration (name, mobile, password, locality, photo optional)
- ⬜ Server-side role guards on every route (never UI-only)
- ⬜ Rate limit login/registration (`login_attempts`)
- ⬜ CSRF token on every form
- ⬜ Super admin: create/disable admins (max 5 active) and volunteers, reset passwords
- ⬜ Profile view + edit + photo upload
- 🚧 Public home (complete and working; auth entry points pending)

### Donations
- ⬜ Donate form: quick amounts (101, 251, 501, 1001, 2100) + custom
- ⬜ UPI flow: QR / UPI ID → UTR + optional screenshot → status `pending`
- ⬜ Volunteer cash/UPI entry → pending → admin approval
- ⬜ Admin approve / reject / cancel **with written reason** (no hard delete)
- ⬜ Ranking from approved amounts only; ties by first reached
- ⬜ Top-up flow with “₹X more to become #1”
- ✅ Anonymous donations counted in total, excluded from banner/top-50
- ✅ Badges by rank (Platinum / Gold / Silver / Bronze / Supporter), cutoffs in settings

### Public honour board
- ✅ One full screen, no scroll, centred on desktop
- ✅ Rank 1 hero, rank 2–3 faded cards
- ✅ Festival title, countdown, total, target progress
- ✅ Bottom dock: lock (staff), user (account), trophy (donors)
- ✅ Top-50 chat-style slide-up sheet with search + badges
- ✅ 8-second live polling
- ✅ Confetti + “New leader!” on rank #1 change
- ✅ Hindi / English toggle
- ⬜ Wire staff/account buttons to real login screens
- ⬜ Wire Donate button to the donation flow

### Donors list (paid donors)
- ⬜ Full rank-wise list for donors after first approved payment
- ⬜ Anonymous rows shown as “Anonymous” with rank + badge
- ⬜ Search by name
- ✅ Data layer (leaderboard ranking) ready

### Admin dashboard & reports
- ⬜ Total, today, pending count, mode split (cash / UPI / online)
- ⬜ Volunteer-wise collection, top donors
- ⬜ Search / filter by date and volunteer
- ⬜ CSV export
- ⬜ Audit log viewer

### Settings (super admin, PIN)
- ⬜ PIN gate: 6+ digits, hash, 5 attempts → 15 min lock, 10 min idle expiry
- ⬜ Festival name/year/theme/target/banner text
- ⬜ UPI ID + QR upload, receipt header/footer
- ⬜ Badge cutoff editor
- ⬜ Maintenance mode, manual DB export
- ⬜ Every change written to audit log

### Receipts
- ⬜ Auto-numbered `CH2026-0001`
- ⬜ Printable acknowledgement (HTML; PDF in Phase 2)
- ⬜ Non-tax wording + committee name

### Operations & security
- ⬜ HTTPS enforcement in production
- ⬜ Image validation: JPG/PNG/WebP, ≤2MB, re-encode, random name, non-executable folder
- ⬜ Daily automatic DB backup + manual export
- ⬜ Secrets only in `config/config.php` outside web root
- ⬜ `demo_mode = false` on production
- ⬜ Change all demo credentials and the settings PIN

---

## Phase 2 — after the festival

- ⬜ Razorpay (or similar) with webhook signature verification + idempotency
- ⬜ OTP login via SMS / WhatsApp
- ⬜ PDF receipts
- ⬜ Confetti polish / sound toggle
- ⬜ Public transparency page (collected vs spent by category)
- ⬜ Multi-festival switching UI
- ⬜ Push / WhatsApp notifications
- ⬜ Registered-committee upgrade (80G) if applicable

---

## Open items from the requirements (§11)

1. Hosting: Hostinger or BigRock? PHP version, SSH/Composer availability.
2. Committee name, logo, UPI ID and QR.
3. Gateway before the festival, or manual UPI only?
4. Confirm the lock/user icon roles and rank-based badges.
5. Preferred colour theme — current UI assumes **saffron / marigold / gold on deep plum**.

---

## Definition of done (acceptance checklist, §12)

- ⬜ Exactly one super admin; max 5 active admins; PIN-gated settings; PIN hashed.
- ⬜ Donor registers → pays by UPI with UTR → after approval sees updated total + rank.
- ⬜ Volunteer cash entry counts only after approval.
- ⬜ Top-up adds to the same donor total.
- ✅ Home fits one screen, no scroll, dock with lock/user/donors.
- ✅ #1 large, #2–3 faded, updates within ~10 seconds.
- ✅ Donors icon opens top-50 sheet with badges.
- ⬜ Paid donor sees full rank-wise donors list.
- ✅ Anonymous donations count but never appear publicly.
- 🧪 All pages usable at 360px width and over HTTPS.
- ⬜ Wrong PIN / login attempts rate-limited; changes audited.
