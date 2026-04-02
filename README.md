# Sacred Kompass v7 — Setup & Developer Guide

---

## What's new in v7

- **Transparent hero nav** — Navbar is fully transparent over the hero section and transitions to a glass-morphism style on scroll.
- **Nav hide/show on scroll** — Navbar slides out when scrolling down, reappears when scrolling up.
- **Hero layout refined** — Features strip removed. Content vertically centred so CTAs are always visible on load. Background image no longer bounces on scroll.
- **Founders: 3-card layout + popup modals** — Left large card = team/group, top-right = Kalai, bottom-right = Christophe. Clicking any card opens a full-screen modal with bio, tags, and a CTA.
- **Contact form redesigned** — First/last name split row, service interest dropdown, textarea, front-end validation with live error states.
- **Philosophy strip restored** — Three numbered pillar cards are back between the hero and the about section.
- **Footer text legibility** — All footer text brightened significantly.
- **Fully responsive** — Hero, hamburger menu, and all cards tested down to 320px.

---

## Step 1 — Upload and activate the theme

### Option A — WP Admin (recommended)
1. Appearance → Themes → Add New → Upload Theme
2. Upload `sacred-kompass-v7.zip` → Install Now → Activate

### Option B — FTP / SSH
1. Delete `/wp-content/themes/sacred-kompass-v7/` if it exists
2. Upload the `sacred-kompass-v7` folder to `/wp-content/themes/`
3. Appearance → Themes → Activate **Sacred Kompass**

---

## Step 2 — Seed default content

Visit: `https://yoursite.com/wp-admin/?sk_reseed=1` (must be logged in as admin)

This seeds all default text, founder cards, values, offerings, and FAQ items.
Run once after every fresh install or theme file replacement. Safe to repeat.

---

## Step 3 — Edit all content

Go to **★ Sacred Kompass** in the WP Admin sidebar. All settings live on one scrollable page.

| Section | Fields |
|---------|--------|
| ✦ Hero | Animated headline lines (3 stages), sub-text, two CTA buttons, background image URL, right panel image URL |
| ✦ About | Eyebrow, heading, body HTML, pull quote, tradition tags |
| ✦ Philosophy Strip | Three numbered pillar cards — title, description (Add/Remove rows) |
| ✦ Quote Band | Vision quote, highlight phrase, attribution |
| ✦ Founders | Section header + `founders_team_image` URL for the large left card + individual founder cards with photo URL, role, origin, bio, tags (Add/Remove) |
| ✦ Core Values | Value cards — title + description (Add/Remove) |
| ✦ Contact | Eyebrow, sub-text, Forminator form ID |
| ✦ Footer & Social | Email, phone, tagline, copyright, social URLs |

Click **Save All Changes** at the top or bottom after editing.

---

## Step 4 — Add your logo

1. **Appearance → Customize → Site Identity**
2. Click **Select Logo** → upload your logo file
3. Click **Publish**

The logo appears in the header (44 px tall) and footer (52 px tall).
Text fallback: "Sacred *Kompass*" if no logo is uploaded.

**Recommended logo specs:**
- Format: PNG with transparent background, or SVG
- Dimensions: at least 240 × 80 px (width can be larger, height scales)

---

## Step 5 — Manage Offerings

**★ Sacred Kompass → ✦ Offerings** (native WP post type)

Each offering is a CPT post with these meta fields:
- **Title** — offering name
- **Category Tag** — short label e.g. `Personal`, `Corporate`
- **Description** — card body text
- **Price** — optional; leave blank to hide
- **Featured Image** — optional card image (4:3 ratio recommended)

---

## Step 6 — Manage FAQ

**★ Sacred Kompass → ✦ FAQ** (native WP post type)

Each FAQ item is a CPT post:
- **Title** — the question displayed as the accordion header
- **Answer** (meta box) — the expanded answer text

---

## Step 7 — Add founder & team photos

**Large left card (Team):**
In **★ Sacred Kompass → ✦ Founders**, paste a URL into the **Team Photo URL** field (`founders_team_image`).

**Individual founder cards (Kalai, Christophe):**
In each founder row, paste the photo URL into the **Portrait Photo URL** field.

To get a URL: Media → Add New → upload → click image → copy **File URL** from the right panel.

Recommended dimensions:
- Team/group card: landscape or portrait, min **900 × 1000 px**
- Individual cards: portrait orientation, min **520 × 420 px**

---

## Step 8 — Contact form (Forminator)

The fallback form (shown when no Forminator ID is set) includes:
- First name + Last name (side by side)
- Email address
- Area of interest (dropdown)
- Message (textarea)
- Client-side validation with live error feedback

**To use Forminator instead:**
1. Install and activate **Forminator** (free, by WPMU DEV)
2. Forminator → Forms → Create New → add your fields → Publish
3. Note the form ID from the URL (e.g. `form_id=42` → ID is `42`)
4. **★ Sacred Kompass → ✦ Contact Section → Forminator Form ID** → enter `42` → Save

The Forminator form will automatically inherit the dark theme styling.

---

## Step 9 — Hero images

The hero has two image slots:

| Option key | Where it shows |
|---|---|
| `hero_bg_image` | Full-bleed background across both columns |
| `hero_right_image` | Right panel image (desktop only, hidden on mobile) |

Set either or both from **★ Sacred Kompass → ✦ Hero**. Both accept direct image URLs.

Recommended: at least **1400 × 900 px**, high-quality JPEG or WebP.

---

## Troubleshooting

| Issue | Fix |
|---|---|
| Sections render blank | Run `?sk_reseed=1` in WP admin |
| Logo not showing | Appearance → Customize → Site Identity → Select Logo → Publish |
| Founders modal not opening | Check browser console; ensure `main.js` is enqueued via `wp_footer` |
| Philosophy strip missing | Check **★ Sacred Kompass → ✦ Philosophy Strip** has saved pillars, or run reseed |
| Nav not transparent on hero | Ensure the homepage uses `front-page.php` (body class includes `.home`) |
| Forminator form unstyled | Theme injects dark-mode overrides only when Forminator is active and an ID is set |

---

## File reference

```
sacred-kompass-v7/
├── functions.php              Settings page, helpers, enqueue, logo, auto-setup
├── header.php                 Nav bar — glassmorphism + hamburger mobile menu
├── footer.php                 Footer — brightened text, logo support
├── front-page.php             Homepage template (includes all section parts)
├── page.php                   All standard WP Pages
├── style.css                  Master stylesheet (tokens → components → responsive)
│
├── inc/
│   ├── cpt.php                Offerings + FAQ CPTs + native meta boxes
│   ├── content.php            Default content seeder (no ACF needed)
│   ├── acf-fields.php         Stub (ACF not required)
│   └── setup.php              Stub
│
├── assets/
│   └── js/
│       └── main.js            Scroll logic, nav hide/show, reveals, modals,
│                              hamburger, FAQ accordion, form validation
│
└── template-parts/home/
    ├── hero.php               Split hero — bg image, left text, right panel
    ├── philosophy-strip.php   Three numbered pillar cards
    ├── about.php              About section with pull-quote
    ├── offerings.php          5-column offering cards (CPT)
    ├── quote-band.php         Dark quote band
    ├── founders.php           3-card asymmetric layout + popup modals
    ├── testimonials.php       Core values grid
    ├── faq.php                Accordion FAQ (CPT)
    └── cta.php                Contact section — dark bg + improved form
```

---

*Sacred Kompass v7 — Zero plugins required. One menu. One scroll. Every pixel intentional.*
