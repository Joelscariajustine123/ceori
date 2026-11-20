# CEORI Skintiva – WordPress block theme

Custom Full Site Editing theme for **CEORI Skin Clinic, Calicut**. The layout follows the
Skintiva dermatology theme's design language (cream frames, Manrope + Playfair italic
headings, pill buttons, eyebrow labels, full-width header with a hamburger side panel,
portrait hero with a stacked treatment card, sticky introduction quote, cut-out "why choose us"
portrait with connector lines, full-width category strip, 2 × 2 testimonial grid, subscribe-style
call-to-action banner and photo footer band). All code and assets are original; no Skintiva or Gutenverse code is used.

- Requires WordPress 6.6+ (tested on 7.1) and PHP 7.4+.
- Built for Elementor (free) + Elementor Header & Footer Builder; works without them using built-in fallbacks.
- Content is imported with the companion **CEORI Content Import** plugin.

## What lives where

| Path | Purpose |
|---|---|
| `theme.json` | Design tokens: palette, fluid type scale, spacing, radii, block styles |
| `header.php`, `footer.php`, `page*.php`, `single.php`, `index.php` | Classic templates: Elementor header/footer and page layouts, with block-pattern fallbacks |
| `inc/elementor.php`, `inc/elementor/` | Elementor integration and the 26 CEORI widgets |

| `patterns/` | Every homepage and page section; all appear in the inserter under **CEORI** |
| `inc/content-types.php` | Treatment categories (on Pages), Specialists, Testimonials, Results, Enquiries |
| `inc/blocks.php` | Dynamic blocks (see below) |
| `inc/forms.php` | Appointment request handling (REST + no-JS fallback, spam protection, email) |
| `inc/seo.php` | Meta, Open Graph, schema, sitemap/robots, redirects (steps aside for Rank Math/Yoast) |
| `inc/settings.php` | **CEORI Clinic** settings screen |
| `inc/treatment-markup.php` | Shared structure of every treatment page |
| `assets/css/theme.css`, `assets/js/theme.js` | Front-end styles and interactions (no dependencies) |
| `assets/fonts/` | Manrope and Playfair Display (SIL Open Font License), self-hosted |

## Dynamic blocks (category "CEORI" in the inserter)

Side panel menu · Treatment cards · Treatment categories · Related treatments · Specialists · Patient testimonials ·
Results gallery · Appointment request form · Clinic info · Related articles · Share buttons · Breadcrumbs.

Each block has a live preview and its options in the right-hand sidebar.

## Editing content (no code needed)

| To change… | Go to |
|---|---|
| Phone, email, address, hours, social links, WhatsApp, GTM, redirects | **CEORI Clinic** (left menu) |
| Homepage sections, About, Contact | **Pages** → edit the page |
| A treatment | **Pages** → the treatment. Title = H1, Excerpt = hero intro, Featured image = hero image, Treatment category in the sidebar |
| Add a treatment | **Pages → Add New**, choose template **Treatment Detail**, insert pattern *Treatment page content (starter)*, set category, excerpt and featured image |
| Category cards (summary, image, order) | **Pages → Treatment categories** |
| Specialists | **Specialists** (photo, designation, verified qualifications, optional profile page) |
| Testimonials / Results gallery | **Testimonials** / **Results Gallery** – shown only when published **and** the consent box is ticked |
| Appointment requests | **Enquiries** (status and notes per request) |
| Menu | **Appearance → Editor → Navigation** (desktop header; on tablets and phones the same menu appears inside the side panel) |
| Side panel (hamburger) | Contents come from CEORI Clinic settings and the treatment categories; the **Book an Appointment** button lives there and in every hero/CTA |
| Header / footer | **Appearance → Editor → Patterns → Template parts** |
| Colours and fonts | **Appearance → Editor → Styles** |

## Notes

- Testimonials and the results gallery hide their whole section until approved content exists,
  so the site never shows empty or unverified sections.
- Search engines: with Rank Math or Yoast active, the theme outputs only clinic schema
  (MedicalClinic, Physician, MedicalProcedure). Without one, it also outputs titles, meta
  descriptions, canonical URLs, Open Graph, breadcrumbs and BlogPosting schema.
- Google Tag Manager (`GTM-5VD5HMWK`) is output only on non-local sites and is skipped
  automatically when Site Kit or GTM4WP is active, so it is never installed twice.
- Local QA only: `?ceori_nolazy=1` turns off lazy loading when `WP_ENVIRONMENT_TYPE` is `local`.
