# Sacred Kompass v6 — Change Log

## v6.0.0 Changes

### 1. Hero Section — New Split Layout
- **New layout**: Full-bleed background image + left text column + right portrait image panel — matching the provided reference design.
- **Background image**: Set in *SK Settings → Hero Section → Background Image* (paste URL or drag & drop to preview).
- **Right panel image**: Set in *SK Settings → Hero Section → Right Panel Image* (paste URL or drag & drop to preview).
- Feature strip at the bottom (Inner Clarity · Spiritual Guidance · Transform Your Life) — auto-rendered.
- Text content (headings, sub, CTAs) unchanged — all still editable in SK Settings.
- Fully responsive: right panel hidden on mobile, single-column with full-bleed bg retained.

### 2. Mobile Navbar Fix
- Added **✕ close button** inside the mobile overlay (top-right corner, 44×44px tap target).
- Fixed CTA button overflow on medium-width screens (hidden below 800px, before hamburger kicks in at 768px).
- Hamburger `aria-expanded` state now syncs correctly on open/close.
- Overlay `z-index` stacking corrected so hamburger always sits above overlay.

### 3. Founders — New Asymmetric Layout
- New layout matches wireframe: **1 large primary card (left) + stacked secondary cards (right)**.
- First team member (lowest Order number) → big card. All others → stacked smaller cards.
- All hover animations preserved identically from v5.

### 4. Founders — CPT (sk_team)
- New **Team Members** custom post type registered (`sk_team`).
- Appears in WP Admin under *Sacred Kompass → Team Members*.
- Each member has: First Name, Last Name, Origin, Role, Bio, Expertise Tags.
- **Portrait photo**: use the Featured Image box (standard WP media upload) OR paste a URL in the *Portrait Photo* meta box sidebar — with drag & drop preview.
- **Display order**: use *Page Attributes → Order* (lower = displayed first = primary/large card).
- **Add/Remove**: standard WP Add New / Trash. No limit on team member count.
- Falls back to the old JSON-based founders (SK Settings) if no CPT entries exist.

## Install / Upgrade
Same as v5 — upload theme folder, activate. No database migration needed.
Run `/?sk_reseed=1` (logged in as admin) only if you want to reset all settings to defaults.
