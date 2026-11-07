# UI Guidelines

> The visual system for the Chanda Collection App. Every new screen (donor, volunteer, admin, auth) must follow these rules so the app feels like one product.

**Direction:** premium festive night — deep plum background, marigold and saffron light, glass cards, soft glows, confident Hindi/English typography. Modern, not temple-poster clichéd.

---

## 1. Tech stack

| Piece | Choice |
| --- | --- |
| CSS | **Tailwind CSS v4** (compiled with `@tailwindcss/cli`) |
| Source | `resources/css/app.css` |
| Output | `public/assets/css/app.css` (generated — never edit by hand) |
| Build | `npm run build:css` (dev: `npm run dev:css`) |
| JS | Vanilla ES2020, `public/assets/js/app.js` — no runtime framework |
| Fonts | **Outfit** (Latin) + **Noto Sans Devanagari** (Hindi) via Google Fonts |

Do **not** add Bootstrap, jQuery or a second CSS framework. Do not load Tailwind from a CDN in production.

---

## 2. Design tokens

Defined in `@theme` inside `resources/css/app.css`.

### Colour

| Token | Hex | Use |
| --- | --- | --- |
| `ink` | `#07040d` | page background |
| `plum` | `#150a20` | raised surfaces / sheet |
| `cream` | `#fff6ea` | primary text |
| `gold` | `#ffd166` | highlights, amounts, rank #1 |
| `marigold` | `#ffb020` | primary accent |
| `saffron` | `#ff7a18` | CTA gradient start |
| `rose` | `#ff2e63` | warning / festive accent |
| `mint` | `#3ddc97` | live / success dot |

Text opacity scale: `text-cream/80` primary, `/55` secondary, `/45` captions, `/25` separators.

### Typography

| Role | Size / weight |
| --- | --- |
| Hero name | 22px / 700 (20px compact) |
| Hero amount | 30px / 800 (24–26px compact) |
| Total collected | 26px / 800 |
| Section title | 19px / 700 |
| Body / row name | 12–12.5px / 600 |
| Caption / locality | 9–11px / 400–500 |
| Label (uppercase) | 8.5–9px / 600, tracking `0.16em` |

Use `.label` for eyebrows. Never go below 8.5px on mobile.

### Radius & spacing

- Cards: `18px` (runners), `22–26px` (hero, dock, sheets)
- Pills / chips: `999px`
- Screen padding: `16px`
- Card gaps: `10px` (`gap-2.5`)
- Prefer 4px increments.

### Elevation

Glass surfaces = `background: linear-gradient(white 8% → 2%)` + `1px` white border at 9% + `backdrop-filter: blur(18px)` + deep shadow. Use the `.glass` class; do not invent a new glass recipe.

---

## 3. Core components (reuse, don't recreate)

| Class | Where | Notes |
| --- | --- | --- |
| `.stage` | every full-screen page | 440px max width, `100dvh`, centred on desktop |
| `.aurora` | layout background | fixed ambient glows + dotted mask |
| `.glass` | stat cards, sheets | standard glass surface |
| `.text-gradient` | money / hero amount | gold → saffron gradient text |
| `.hero-card` | rank #1 | animated conic border + shine |
| `.avatar` / `--sm` / `--md` / `--xl` | all people | initials placeholder; `data-tone="0–4"` cycles gradient |
| `.avatar-ring` | hero | gold conic ring + pulse |
| `.badge--platinum/gold/silver/bronze/supporter` | ranks | driven by `App\Support\Badge` |
| `.runner-card` + `.is-faded` | rank 2 / 3 | opacity 0.68 |
| `.dock` / `.dock-btn` | bottom nav | 3 items only, per requirements |
| `.donate-fab` | home | primary donation CTA |
| `.sheet-root` / `.sheet-panel` | top-50 donors | slide-up, backdrop blur, `82dvh` |
| `.donor-row` | leaderboards | rank + avatar + name + badge + amount |
| `.toast` | feedback | top pill, 3.2s, `role="status"` |
| `.confetti-canvas` | celebration | only when `prefers-reduced-motion` is off |

New shared UI belongs in `@layer components`; new layout utilities stay in the template.

---

## 4. Layout rules

- Mobile-first. Design at **390 × 844**; verify at **360 × 640** and **430 × 932**.
- The public home is **one screen, no scroll** (`body { overflow: hidden }`).
- Use `height: 100dvh` (not `100vh`) for phones with browser bars.
- The phone-stage is centred on desktop (`max-width: 440px`).
- Dock and sheets respect `env(safe-area-inset-bottom)`.
- Breakpoints in use: height-based compaction at **≤660px**, **≤620px**, **≤560px**.
- Never allow the dock to overlap rank cards — content must end above the dock.

---

## 5. Motion

- Duration: **180–360ms** UI, **700–900ms** celebration.
- Easing: `cubic-bezier(0.22, 1, 0.36, 1)` for enters.
- Motion must communicate state: number count-up, progress fill, new-leader pop, confetti.
- No infinite motion on text. Background glow / shine loops are subtle and decorative.
- **Always** wrap motion in `@media (prefers-reduced-motion: reduce)` — the global rule already does; JS also checks it before confetti/count-up.

---

## 6. Accessibility

- Touch targets ≥ 44px (dock buttons are full-height; edit/close buttons must be ≥ 40px).
- Every icon-only button has `aria-label`.
- Sheet is `role="dialog"`, `aria-modal="true"`, traps Tab, closes on Esc and backdrop, returns focus to the trigger.
- Colour contrast: cream on ink passes; never put gold text on gold.
- Use real `<button>` / `<input>` elements; keep focus-visible outline.
- Live regions: total/toast use `aria-live="polite"`.
- Hindi mode sets `<html lang="hi">`; Devanagari must never be replaced by Latin fallback.

---

## 7. Copy & language

- Two languages only: **English** and **हिंदी**.
- All UI strings live in the JS dictionary (`public/assets/js/app.js`) with `data-i18n` attributes; server-rendered strings should be structural, not duplicated in three places.
- Tone: warm, respectful, short. “दान करें”, “शीर्ष दानवीर”, “हर योगदान मायने रखता है”.
- Money is always Indian-formatted: `₹2,45,300` (`inr()` in PHP, `Intl.NumberFormat('en-IN')` in JS).
- Never claim 80G / tax exemption — use “Acknowledgement of Donation” and “Voluntary contribution to the festival committee”.

---

## 8. Page templates for the remaining screens

Use `.stage` + a compact page header + `.glass` cards. Suggested patterns:

- **Auth:** centred glass card, logo mark, 44px inputs, one primary gradient button, quiet secondary link.
- **Donor profile:** hero mini-card (avatar, rank, badge, total), then history rows, sticky “Top up” CTA.
- **Volunteer:** stat tiles (today / total / pending), recent entry rows, single primary “Add donation”.
- **Admin:** top stat grid 2×2, filter chips, table-like rows that collapse into cards on mobile; sticky export button.
- **Receipt:** print stylesheet on white, committee header, receipt number, amount, mode, clear non-tax note.

Keep the bottom dock only on the public home and donor-facing screens. Admin/volunteer areas use a back header + section tabs, not the 3-icon dock.

---

## 9. Do / Don't

| Do | Don't |
| --- | --- |
| Reuse `.glass`, `.badge`, `.avatar` | Introduce new shadows/borders per page |
| Keep one primary gradient CTA per screen | Stack multiple gradient buttons |
| Test at 360px width and short heights | Assume desktop height |
| Use `textContent` for user data in JS | Inject user names with `innerHTML` |
| Show `Anonymous` without photo | Reveal anonymous donor identity anywhere |
| Respect `prefers-reduced-motion` | Force confetti on reduced-motion users |
| Update this file when adding components | Let screens drift into a second design language |
