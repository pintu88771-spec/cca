# Chanda Collection App – Final Requirements (v1.1)

Festival fund collection web app for Chhath Puja 2026 (reusable for Durga Puja, Saraswati Puja and others)

Target go-live: 17 November 2026 | Hosting: shared hosting (Hostinger / BigRock) | Committee: not registered

## 1. Goal

A mobile-friendly website where devotees (bhaktpremi) donate chanda online or through volunteers, can top up later, and see themselves honoured on the home page. The public home page is one full screen: the top donor in a large banner with photo and details, the 2nd and 3rd donors smaller and faded, updating live, and a phone-style bottom menu. The competition motivates bigger donations and helps the committee collect more.

## 2. Reality check on timeline and hosting

- Today is 7 November, go-live is 17 November: only about 10 days. The scope below is therefore split into **MVP (must be live by about 14 November)** and **Phase 2 (after the festival or if time remains)**.
- Shared hosting usually supports **PHP + MySQL** but not Node.js or long-running WebSocket servers. So the stack is PHP and MySQL, and "live update" is done with **auto-refresh polling every 5 to 10 seconds** (it looks live to users and works on any shared host).
- The committee is not registered, so **no 80G / tax-exemption receipts**. Receipts are issued as a simple **"Acknowledgement of Donation"** with the committee name, with no tax claim. If the committee registers later, this can be upgraded.
- A payment gateway (for example Razorpay) needs business or individual KYC approval, which may take days and may be hard for an unregistered committee. So the plan is: **offline and manual-UPI flow first (works on day 1)**, and the gateway is added behind an on/off switch once approved.

## 3. Recommended tech stack

| Layer | Choice | Why |
| --- | --- | --- |
| Backend | PHP 8+ (Laravel if the host supports Composer/SSH, otherwise a lightweight custom PHP with PDO) | Runs on shared hosting |
| Database | MySQL / MariaDB | Provided by every shared host |
| Frontend | HTML, CSS (Tailwind or Bootstrap), vanilla JavaScript (or Alpine.js) | Light, fast on phones |
| Live updates | AJAX polling every 5–10 s on the home page | No WebSocket needed |
| Payments | Phase A: UPI QR + UTR verification; Phase B: Razorpay (or similar) with webhook | Fits the approval time |
| Language | Hindi + English toggle | Local audience |
| SSL | Free SSL from the host (mandatory) | Security |

## 4. Roles and permissions

There are **four roles**: the **Super Admin** (the developer, exactly 1 account), **Admin** (up to 5 accounts), **Volunteer** and **Bhaktpremi** (donor).

### 4.1 Super Admin (Developer) – exactly one account

- Exactly one super admin account can exist (enforced in the database and in code). This is the developer.
- Has full access to everything in the system, and is the only one who manages the **Settings page** and **user accounts**. That includes everything an admin can do: record, approve, cancel and export donations, view reports, and donate or top up like any other donor.
- **User accounts:** create, disable and reset passwords for up to 5 admins and for volunteers, and view all accounts.
- Has an exclusive **Settings page** protected by a **security PIN**:
  - Festival name, year, theme colour, target amount, banner text.
  - UPI ID and QR image, payment gateway keys (when added), receipt header and footer.
  - Badge rank cutoffs (which ranks get which badge).
  - Change the PIN, maintenance mode, database backup and export.
- PIN rules: minimum 6 digits, stored only as a **bcrypt/argon2 hash** (never plain text), 5 wrong attempts locks for 15 minutes, PIN session expires after 10 minutes of inactivity, and every settings change is written to an audit log.

### 4.2 Admin (up to 5 accounts)

- Created by the super admin; logs in with mobile number and password. No more than 5 active admin accounts can exist.
- Runs the day-to-day collection: approves or rejects pending donations, cancels entries with a written reason, sees all donors and reports, and exports CSV/Excel.
- Cannot open the Settings page and cannot create or disable accounts.

### 4.3 Volunteer

- Logs in with mobile number and password created by the super admin.
- Can record **cash / direct UPI** donations for a donor (name, mobile, amount, mode, optional photo).
- Sees only their own collection total and entries.
- Entries start as **Pending** and become counted after admin approval (this prevents mistakes and fraud).

### 4.4 Bhaktpremi (donor)

- Self-registers with mobile number (OTP if an SMS provider is added, otherwise mobile + password).
- Has a profile: photo, name, city/locality, optional message, total paid, donation history, current rank and badge, receipts.
- Can donate and **top up** any time.
- Can choose **anonymous** (counted, but not shown publicly) and gives consent to show photo and name on the public page.
- After the first approved donation, can open the **Donors List** (all donors, rank-wise, with badges; see 5.7).

### 4.5 Permission summary

| Action | Super Admin | Admin | Volunteer | Bhaktpremi |
| --- | --- | --- | --- | --- |
| Settings page (PIN) | Yes | No | No | No |
| Create / disable accounts (admins max 5, volunteers) | Yes | No | No | No |
| Record offline donation | Yes | Yes | Yes (pending approval) | No |
| Donate / top up online | Yes | Yes | Yes | Yes |
| Approve or reject pending donations | Yes | Yes | No | No |
| View own profile and history | Yes | Yes | Yes | Yes |
| View all donors and reports | Yes | Yes | Own entries only | No |
| Donors List (rank-wise with badges) | Yes | Yes | Yes | Yes (after first approved payment) |
| Export data | Yes | Yes | No | No |

All accounts can see their own profile with the amount paid and a **"Top up / Give more"** button.

## 5. Main features

### 5.1 Public home page (the live honour board)

- **One full screen only:** no scrolling, no top menu, no extra sections. Everything fits on a phone screen (on a computer it is shown as a centred full-height screen).
- Top strip: festival title, countdown to the festival, total collected, and a target progress bar.
- **Hero banner: Rank 1 donor** with large photo, name, locality, total amount, and their message.
- **Rank 2 and Rank 3** shown below as smaller cards with reduced opacity (about 60–70%).
- Auto-refresh every 5–10 seconds. When Rank 1 changes, show a celebration animation (confetti) and a New leader! message.
- **Bottom menu (phone-style bar) with three icons:**
  - **Left, lock icon:** login for staff (super admin and admin).
  - **Centre, user icon:** login or registration for volunteers and bhaktpremi; once logged in it opens their profile.
  - **Right, donors icon:** opens a chat-window-style popup sliding up from the bottom with the **top 50 donors by amount paid**, each with rank, photo, name, badge and amount. It refreshes live and closes with a tap outside or a close button.
- A small **Donate Now** button on the screen takes the donor to login and then the donation form.
- Anonymous donors are counted in the total but never shown on the banner or in the popup.
- **Ranking rule:** by total approved amount in the current festival. Ties go to the donor who reached that amount first.

### 5.2 Donation flow

1. Donor picks or enters an amount (quick buttons such as 101, 251, 501, 1001, 2100, or custom).
2. **Online:** shows the committee UPI QR / UPI link. Donor pays, then enters the UTR / transaction ID and optionally a screenshot. Status becomes **Pending verification** and the admin confirms it. (When a gateway is added, the payment is confirmed automatically by webhook.)
3. **Offline:** the volunteer records cash, and the admin approves.
4. After approval the donor's total, rank and receipt update, and the home page reflects it on the next refresh.

### 5.3 Top up

- A button on the profile and on the banner ("Pay more to become the leader"). It shows the amount needed to reach the next rank, for example "₹X more to become #1".

### 5.4 Receipts

- Auto-numbered acknowledgement (for example CH2026-0001) as a printable page or PDF, with the committee name, donor name, amount, date, and mode.
- Clear text: "Voluntary contribution to the festival committee". No tax or 80G claims.

### 5.5 Admin dashboard

- Total collected, today's collection, pending approvals, mode-wise split (cash / UPI / online), volunteer-wise collection, and top donors.
- Donor and donation search, filters by date and volunteer.
- Export to Excel/CSV.
- Reject or cancel a donation only with a written reason (never hard-delete).

### 5.6 Transparency page (nice to have)

- Public summary of total collected and total spent by category, to build trust.

### 5.7 Donors list for paid donors (rank-wise badges)

- A bhaktpremi sees this list only **after their first approved donation**. Super admin, admin and volunteer can always see it.
- Shows **all** donors (not only the top 50), ordered by rank, each with photo, name, locality, badge and total paid, with search by name.
- Anonymous donors appear as Anonymous with their rank and badge, without name or photo.
- Badges are given by rank, and the super admin can edit the cutoffs in Settings. Suggested default:

| Rank | Badge |
| --- | --- |
| 1 | Platinum (crown) |
| 2 | Gold |
| 3 | Silver |
| 4 to 10 | Bronze |
| 11 and below | Supporter |

- The same badge is shown on the profile and in the top-50 popup on the home screen.

## 6. Security requirements

- HTTPS everywhere; passwords hashed with bcrypt/argon2.
- Settings PIN hashed as above, with lockout and session timeout; only the super admin can open it. Exactly one super admin account exists (database constraint) and at most 5 admin accounts can be active.
- Role checks on the **server for every request**, never only in the UI.
- Prepared statements (PDO) against SQL injection, output escaping against XSS, CSRF tokens on every form.
- Image upload checks: allowed types (JPG/PNG/WebP), maximum size (for example 2 MB), re-encoded and renamed, stored outside executable folders.
- Rate limiting on login and registration.
- Amounts are never trusted from the browser alone; only admin-approved or webhook-verified payments count.
- Gateway webhook (later): signature verification and idempotency so one payment is never counted twice.
- Audit log for settings changes, approvals, edits, and cancellations.
- Secrets (DB password, gateway keys) kept in a config file outside the public web folder.
- Daily automatic database backup (host cron or the host's backup tool) and a manual export button.
- Donor consent for showing photo and name publicly; admin can hide an inappropriate photo.

## 7. Data model (tables)

| Table | Main fields |
| --- | --- |
| users | id, role (super\_admin / admin / volunteer / bhaktpremi), name, mobile (unique), email, password\_hash, photo, locality, message, show\_public, is\_anonymous\_default, status, created\_at |
| festivals | id, name, year, theme, target\_amount, start\_date, end\_date, is\_active |
| donations | id, festival\_id, donor\_id, amount, mode (cash / upi / gateway), utr\_no, screenshot, collected\_by (volunteer id), status (pending / approved / rejected / cancelled), reason, is\_anonymous, receipt\_no, created\_at, approved\_by, approved\_at |
| settings | key, value (UPI ID, QR image, banner text, refresh interval, badge rank cutoffs, etc.) |
| security\_pin | id, pin\_hash, failed\_attempts, locked\_until, updated\_at (single row) |
| audit\_logs | id, user\_id, action, details, ip, created\_at |
| login\_attempts | mobile/ip, attempts, last\_attempt |

Ranking is calculated from the sum of **approved** donations per donor for the active festival, and a small cached total per donor can be kept for speed. Badges come from the rank and need no extra table.

## 8. Screens

**Public:** Home (single full-screen live honour board with bottom menu: lock, user, donors popup), Donate, Login, Register, Transparency.

**Bhaktpremi:** My Profile, Edit Profile, My Donations, Top Up, Receipt, Donors List (after the first approved payment).

**Volunteer:** Dashboard, Add Cash/UPI Donation, My Entries, Donors List.

**Super Admin:** Settings (PIN protected), User Accounts (create / disable admins and volunteers, reset passwords), Audit Log, plus every Admin screen (Dashboard, Pending Approvals, Donors, Donations, Reports/Export, Donors List).

**Admin (up to 5):** Dashboard, Pending Approvals, Donors, Donations, Reports/Export, Donors List.

## 9. Scope and phases

### MVP: target ready by 14 November (must have)

- Login and registration for all four roles; one super admin and up to 5 admins (limits enforced).
- Settings page and user-account management (super admin only) with hashed PIN.
- Donation by UPI (UTR entry) and by volunteer cash entry, with admin approval.
- Profile with total paid and the **Top up** button.
- Full-screen live home page: Rank 1 hero banner, faded Rank 2 and 3, total and target progress, bottom menu (lock, user, donors popup with top 50 and badges), 5–10 s refresh.
- Anonymous option and consent checkbox; rank-based badges and the rank-wise Donors List for paid donors.
- Basic receipt, basic admin dashboard, CSV export.
- Hindi and English text toggle.
- Daily backup.

### Phase 2: after launch or if time remains

- Razorpay / gateway with automatic confirmation.
- OTP login via SMS or WhatsApp.
- PDF receipts and confetti polish.
- Transparency page, multi-festival switching, push or WhatsApp notifications.

## 10. Assumptions I made (please correct if wrong)

1. There is **one** super admin (the developer) who has all access and alone manages settings and user accounts, and **up to 5** admins who run the daily collection work. Only the super admin creates or disables accounts.
2. The super admin has all access: everything an admin can do, plus Settings and account management.
3. The left lock icon is the login for staff (super admin and admin), and the centre user icon is the login or registration for volunteers and bhaktpremi. You wrote volunteer login for both icons, so please confirm.
4. Badges are given by rank (1 Platinum, 2 Gold, 3 Silver, 4 to 10 Bronze, others Supporter), and the super admin can edit the cutoffs.
5. The top-50 popup and all ranks use total approved amount in the current festival, and anonymous donors are left out of the popup.
6. The Donate Now button asks the donor to log in first, so there are no guest donations.
7. Donations are counted only after admin approval (until a gateway is live).
8. Mobile-number login with password for MVP; OTP comes later.
9. Hosting supports PHP 8+ and MySQL, and has free SSL.
10. Language: Hindi and English.
11. Committee name and UPI ID will be supplied by you and entered in Settings.

## 11. Open items I need from you before coding

1. Hosting details: which host (Hostinger or BigRock), PHP version, and whether you get SSH/Composer. This decides Laravel versus plain PHP.
2. Committee name, logo, and UPI ID plus QR for receiving payments.
3. Will the gateway be attempted before the festival, or only manual UPI for now?
4. Confirm or change the assumptions in section 10, especially the lock and user icons and the rank-based badges.
5. Preferred colour theme (for example saffron, red, and gold for a festive look).

## 12. Acceptance checklist (how we know it is done)

- Only one super admin account can exist and at most 5 admin accounts; only the super admin opens the settings page, which asks for the PIN, and the PIN is hashed in the database.
- A donor can register, donate by UPI with UTR, and after admin approval sees the updated total and rank.
- A volunteer can add a cash donation, and it counts only after approval.
- Top up works from the profile and adds to the same donor total.
- The public home page fits on one screen with the bottom menu (lock, user, donors icons) and no scrolling.
- Home page shows #1 large, #2 and #3 faded, and changes within about 10 seconds of an approval.
- The donors icon opens a chat-style popup with the top 50 donors and their badges.
- A bhaktpremi sees the full rank-wise Donors List with badges only after their first approved payment.
- Anonymous donations count in the total but never appear on the banner or in the popup.
- All pages work on a small phone screen and over HTTPS.
- Wrong PIN and login attempts are rate-limited, and changes are in the audit log.
