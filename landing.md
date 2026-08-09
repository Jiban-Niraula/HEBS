# Landing Page UI Documentation

This document records the current landing page design for the HEBS public website. It covers the visible UI, content flow, layout behavior, colors, typography, component styling, data sources, and generated build artifacts.

## Implementation Files

- Landing page component: `resources/js/pages/Public/Home.tsx`
- Public site shell: `resources/js/layouts/PublicLayout.tsx`
- Shared page heading helper: `resources/js/components/shared/SectionHeading.tsx`
- Public home data API: `app/Http/Controllers/Public/HomeController.php`
- Main stylesheet: `resources/css/app.css`
- Laravel root HTML template: `resources/views/app.blade.php`
- Built Vite manifest: `public/build/manifest.json`
- Built CSS bundle: `public/build/assets/app-D1kSVwXg.css`
- Built JS bundle: `public/build/assets/app-DHoLt2z9.js`

## Current Page Character

The landing page is an institutional school homepage. It is designed to feel practical, credible, and parent/student focused rather than decorative or marketing-heavy.

The current page emphasizes:

- School identity and contact access.
- Direct navigation to school areas.
- A strong hero image of the school.
- Notices and upcoming events immediately after the hero.
- About-school positioning.
- Academic program routing.
- Gallery/media moments.
- Extra activities and school life.
- Footer with visit/contact resources and embedded map.

The structure is already functional. The main frontend boost opportunities are visual identity, richer real imagery, stronger admissions/conversion flow, and better use of backend data that is already available.

## Page Structure

The home page is rendered by `Home` in `resources/js/pages/Public/Home.tsx`.

Rendered order:

1. `PublicLayout`
2. `<Head title={props.school.name} />`
3. Hero banner
4. Notices and events section
5. About school section
6. Programs section
7. Gallery section
8. Extra activities section
9. Popup notice modal, only when an active popup notice exists
10. Shared public footer from `PublicLayout`

## Public Header

The shared public header is defined in `resources/js/layouts/PublicLayout.tsx`.

### Top Bar

The top bar is a dark institutional strip above the main navigation.

Content:

- School email link.
- School phone link.
- Office hours.
- School App link.
- Facebook shortcut shown as `f`.

Current styling:

- Background: `var(--brand)`, currently finalized as `#143b59`.
- Text color: light blue-gray tones such as `#d4e0ea`.
- Font size around `.79rem` for the top bar and `.78rem` for grouped topbar content.
- Hidden on screens below `899px`.

### Main Header

The main header is a white navigation bar.

Content:

- Brand link to `/`.
- Logo image: `/images/hebs-facebook-profile.jpg`.
- School name.
- School address.
- Desktop navigation.
- Contact button.
- Mobile menu button.

Current styling:

- Background: `#fff`.
- Bottom border: `1px solid #e6ebed`.
- Box shadow: `0 2px 10px rgb(20 59 89 / 8%)`.
- Header height: `88px` on desktop.
- Mobile height: around `64px`.
- Logo mark size: `52px` desktop, smaller on mobile.
- Logo mark is rounded/circular in earlier rules, but the final footer logo becomes square-ish with `border-radius: 2px`.
- School name font size around `.92rem`.
- Address font size around `.72rem`.

### Desktop Navigation

Navigation groups:

- Home
- About Us
- Resources
- Updates
- Academic
- Levels

Dropdown children:

- About Us: About, Teachers, Administration, Support Team, Message From Executives
- Resources: Gallery, Downloads
- Updates: Events, News, Notice
- Academic: Scholarship, Fee Structure
- Levels: Pre School, Pre-Primary Education, Primary Education, Secondary Education

Current styling:

- Desktop nav uses flex layout.
- Top-level links are vertically centered and match header height.
- Link font size is finalized around `1rem` on desktop.
- Link color: `#394b57` / heading-adjacent gray.
- Hover color: `var(--brand)`.
- Dropdown menu has white background, thin border, blue top border, and soft shadow.
- Dropdown item font size around `.9rem`.

### Contact Button

The contact link is styled like a primary institutional button.

Current styling:

- Background: `var(--brand)` / `#143b59`.
- Hover background: `var(--brand-dark)` / `#0d2a40`.
- Text: white.
- Border radius: `2px`.
- Font weight: `600`.
- Font size: `.86rem`.

### Mobile Navigation

Below `899px`:

- Top bar is hidden.
- Desktop navigation and header actions are hidden.
- Menu button appears.
- Drawer opens as a left-side overlay.

Drawer behavior:

- Body scroll is locked while open.
- Escape key closes menu.
- Clicking overlay closes menu.
- Dropdown groups expand/collapse.

Drawer styling:

- Drawer width: `min(88vw, 380px)`.
- White background.
- Full height.
- Scrollable.
- Drawer item text: `1.08rem`, font weight `600`.
- Drawer submenu text: `.9rem`.

## Hero Section

The hero is implemented by `HeroBanner` in `Home.tsx`.

Current behavior:

- The backend sends multiple `heroSlides`.
- The page currently uses only `props.heroSlides[0]`.
- This means the hero is static, despite class names like `hero-carousel`.

Content:

- Eyebrow: `Montessori to Grade 12`
- Headline: `A steady foundation for a meaningful future.`
- Supporting text: `A school community where academic discipline, personal guidance, and character grow together.`
- Background image: first hero slide image, currently `/images/school-hero.jpg`.

Current layout:

- Full-width hero.
- Background image fills the hero with `background-size: cover`.
- Copy is centered in the final CSS rules.
- Hero content max width: around `760px`.
- Headline max width: around `700px`.
- Lede max width: around `600px`.
- On mobile, headline max width becomes around `330px`.

Current sizing:

- Desktop hero min-height: `min(760px, calc(100svh - 108px))`.
- Mobile hero min-height: `calc(100svh - 64px)`.
- Earlier rules include `575px`, `500px`, and `430px` fallbacks, but final full-viewport rules are authoritative.

Current visual treatment:

- Hero image is absolutely positioned.
- Image overlay is a flat dark layer in final rules:
  - Desktop: `rgb(8 25 45 / 52%)`
  - Mobile: `rgb(8 25 45 / 58%)`
- Earlier rules used left-to-right gradients, but final rules center the copy and use a simpler uniform overlay.
- Earlier rules include a slow image zoom animation. Later institutional finish removes most section animation, but hero media transition/hover rules still exist.

Current typography:

- Final site font is `Figtree`, with `Arial, sans-serif` fallback.
- Earlier CSS used Poppins and Georgia, but final rules override headings to use Figtree.
- Letter spacing is finalized to `0` for headings, paragraphs, and links.

## Notices And Events Band

Implemented by `NoticeSection` in `Home.tsx`.

This section appears immediately after the hero, making operational information highly visible.

### Section Layout

Desktop:

- Two-column grid.
- Final grid ordering:
  - Notice board first.
  - Events second.
- Notice column width: `minmax(420px, 520px)`.
- Events column: remaining space.
- Gap: around `42px` in earlier rules, `24px` on tablet/mobile.

Mobile:

- Single-column stack below `899px`.

Background:

- Final background: `#eef2f4`.
- Earlier textured backgrounds were added, then disabled by final institutional rules.

### Notice Board

Content:

- Kicker: `Official communication`
- Heading: `Notices Board`
- Link: `View all`
- Latest notices from backend.
- Each item displays:
  - Date
  - Title
  - Category
  - Priority
  - Arrow

Backend:

- Controller currently returns latest 4 published notices.
- Frontend slices up to 5 notices, so the practical maximum is currently 4 unless backend count is increased.

Visual styling:

- Board background: white.
- Border: `1px solid var(--border)`.
- Border radius: `2px`.
- Box shadow: `0 4px 14px rgb(36 55 70 / 6%)`.
- Header background: red, currently around `#b63b35`.
- Header text: white.
- Notice rows use thin borders.
- Notice date and arrow use the same red accent in prominent board rules.
- Hover adds white background and left padding shift.

### Upcoming Events

Content:

- Kicker: `School calendar`
- Heading: `Upcoming events`
- Up to 3 upcoming or ongoing events.
- Each item displays:
  - Date
  - Title
  - Time
  - Venue
  - Arrow

Visual styling:

- White panel.
- Top border in blue.
- Padding around `25px 28px 14px`.
- Event row grid:
  - Date column around `90px`
  - Content column
  - Arrow column
- Row min-height: `86px`.
- Date/arrow color from blue accent.
- Hover changes title color and nudges row padding.

## About School Section

Implemented inline in `Home.tsx`.

Content:

- Heading: `About School`
- Subtitle: `An institutional environment where students are known, guided, and encouraged to participate.`
- Body copy about academic foundation, respectful relationships, activity, and family communication.
- Link: `Read about our school →`
- Image label: `School community`
- Facts:
  - Established: from `props.school.establishedYear`
  - Learning pathway: `Pre-primary to +2`
  - Location: from `props.school.address`

Layout:

- Full-width section.
- Inner `.container`.
- Desktop two-column editorial grid:
  - Text left.
  - Image right.
- Grid gap around `84px` in earlier rules.
- Facts row becomes three columns on desktop.
- Mobile stacks to one column.

Visual styling:

- Final section background: white.
- Earlier subtle dot pattern was disabled by final institutional finish.
- Section padding finalized around `78px`, then `58px` below `899px`.
- Text link uses brand blue and a warm arrow accent in earlier rules.
- Image frame uses the campus image as CSS background.
- Image min-height around `450px` desktop and `330px` in small-screen earlier rules.
- Facts use uppercase labels, muted text, and bold values.

## Programs Section

Implemented inline in `Home.tsx`.

Content source:

- The displayed course cards are currently hardcoded in the `courseCards` array.
- Backend `programs` are loaded but not used by this section yet.

Displayed cards:

1. Pre-primary
2. Primary
3. Secondary
4. +2

Each card includes:

- Number: `01`, `02`, `03`, `04`
- Program name
- Short description
- `Learn more →`
- Link to `/academics/{slug}`
- Image area

Current image:

- Every card uses `/images/school-hero.jpg`.

Layout:

- Desktop: `repeat(4, 1fr)`.
- Tablet: `repeat(2, 1fr)`.
- Small mobile: still `1fr 1fr`, with tighter gaps.

Visual styling:

- Section background: `#f5f7f8` in final rules.
- Card:
  - Border: `1px solid var(--border)`.
  - Top border: `3px solid var(--brand)`.
  - Border radius: `2px`.
  - Background: `#fff`.
  - Box shadow: `0 4px 14px rgb(36 55 70 / 6%)`.
  - Hover lift: `translateY(-2px)`.
  - Hover shadow: `0 10px 22px rgb(36 55 70 / 10%)`.
- Card image:
  - Min-height: `145px` final.
  - Earlier tablet min-height: `105px`.
  - `background-size: cover`.
  - Hover zoom: `scale(1.07)`.
- Card content:
  - Padding: `16px 18px 18px`.
  - Mobile padding: `13px 14px 15px`.
  - Paragraph font size around `.72rem`, `.68rem` on mobile.
  - CTA color: `var(--brand)`.

## Gallery Section

Implemented by `GallerySection` in `Home.tsx`.

Content source:

- Uses backend gallery albums first.
- Adds fallback items if fewer albums are available.

Fallback items:

- School life
- Classroom activity
- Community events

Each gallery item includes:

- Category/meta label.
- Name/title.
- Description.
- Image background.
- Link to `/gallery`.

Layout:

- Desktop:
  - CSS grid with `1.2fr .8fr`.
  - Two rows of `190px`.
  - First item spans two rows.
- Tablet:
  - Two-column grid.
  - First item spans full row.
  - Row heights: `230px 170px`.
- Mobile:
  - Single column.
  - Three rows of `180px`.

Visual styling:

- Final background: white.
- Image tiles use CSS background images.
- Gradient overlay is set inline in React:
  - `linear-gradient(180deg, rgb(16 42 67 / 4%) 35%, rgb(16 42 67 / 82%) 100%)`
- Tile text is white.
- Meta label is uppercase with thin underline.
- Border radius: `2px`.
- Box shadow: `0 4px 14px rgb(36 55 70 / 7%)`.
- Hover:
  - Slight dark overlay increase.
  - `translateY(-2px)`.
  - Stronger shadow.

## Extra Activities Section

Implemented by `VictorySection` in `Home.tsx`.

Section title:

- `Extra Activities`

Section subtitle:

- `Life beyond books`

Cards:

1. Sports and achievements
2. Cultural participation
3. Learning milestones

Image source:

- Uses first three gallery covers when available.
- Falls back to Unsplash URLs.

Card link:

- All cards link to `/gallery`.

Layout:

- Desktop: 3-column grid.
- Tablet: 2-column grid.
- Mobile: 1-column grid.

Visual styling:

- Final section background: `#eef2f1`.
- Border top: `1px solid #dfe6e4`.
- Padding: `68px 0`.
- Card min-height: `240px` desktop, `190px` mobile.
- Card text anchored to bottom.
- Inline background combines image and bottom dark gradient:
  - `linear-gradient(180deg, rgb(8 25 45 / 2%), rgb(8 25 45 / 72%))`
- Border radius: `2px`.
- Box shadow: `0 4px 14px rgb(36 55 70 / 7%)`.
- Hover:
  - `translateY(-2px)`.
  - `0 10px 22px rgb(36 55 70 / 11%)`.

## Popup Notice Modal

Implemented by `OpeningPopup` in `Home.tsx`.

Rendered only when `props.popupNotice` exists.

Behavior:

- Reads session storage with key `hebs-popup-{notice.id}`.
- Opens when no dismissal exists, or when frequency is `every_visit`.
- Focuses close button when opened.
- Locks body scroll.
- Escape key closes modal.
- Close writes `dismissed` to session storage.

Content:

- Close button.
- Eyebrow with notice priority.
- Title.
- Summary.
- Content.
- Optional primary action if `buttonUrl` and `buttonLabel` are present.
- Secondary close button.

Styling:

- Fixed overlay.
- Dark translucent background: `rgb(15 23 42 / 62%)`.
- Dialog width: `min(100%, 540px)`.
- Max-height: `min(680px, calc(100vh - 36px))`.
- Top border: `6px solid var(--accent)`.
- Emergency priority changes border color to `#b91c1c`.
- White dialog background.
- Large shadow: `0 24px 70px rgb(15 23 42 / 30%)`.

## Footer

Footer is part of `PublicLayout`.

Footer content:

- School logo.
- Label: `The school`.
- School name.
- Motto.
- Copy: `A complete learning pathway from Montessori to Grade 12.`
- Facebook social shortcut.
- Explore links.
- Resource links.
- Visit/contact block.
- Phone, email, address.
- Google Maps directions link.
- Embedded Google map iframe.
- Copyright.
- Privacy Policy and Website Terms links.

Footer styling:

- Background: `var(--brand-dark)`, currently `#0d2a40`.
- Text: light blue-gray.
- Grid desktop columns: `1.5fr 1fr 1fr 1.2fr`.
- Footer logo:
  - `64px` square.
  - Border: `2px solid rgb(255 255 255 / 35%)`.
  - Final border radius: `2px`.
  - `object-fit: cover`.
- Social circle:
  - `28px`.
  - Thin light border.
  - White text.
- Map:
  - Full width.
  - Minimum height: `150px`.
  - Thin translucent border.

## Color System

The file has multiple historical `:root` blocks. The final authoritative public color system near the bottom of `resources/css/app.css` is:

```css
:root {
  --brand: #143b59;
  --brand-dark: #0d2a40;
  --accent: #d39a2c;
  --heading: #243746;
  --body: #52616b;
  --muted: #7a8790;
  --border: #dfe5e8;
  font-family: "Figtree", Arial, sans-serif;
}
```

Primary colors used:

- Brand blue: `#143b59`
- Dark brand blue: `#0d2a40`
- Warm accent/gold: `#d39a2c`
- Heading: `#243746`
- Body text: `#52616b`
- Muted text: `#7a8790`
- Border: `#dfe5e8`
- Page background: `#f5f7f8`
- White surface: `#fff`
- Notices red: around `#b63b35`
- Events/action blue appears in legacy rules as `#1769aa`
- Footer light text: blue-gray tones

Section backgrounds:

- Body: `#f5f7f8`
- Notices/events: `#eef2f4`
- About: `#fff`
- Programs: `#f5f7f8`
- Gallery: `#fff`
- Extra activities: `#eef2f1`
- Footer: `#0d2a40`

## Typography

Template font loading:

- `resources/views/app.blade.php` loads Google Font `Figtree` with weights `400`, `500`, `600`, `700`.

Final font rules:

```css
html, body, button, input, textarea, select {
  font-family: "Figtree", Arial, sans-serif;
}

h1, h2, h3, h4, h5, h6 {
  font-family: "Figtree", Arial, sans-serif;
}

h1, h2, h3, h4, h5, h6, p, a {
  letter-spacing: 0;
}
```

Earlier CSS used Poppins and Georgia for headings, but final institutional rules override the public interface to use Figtree consistently.

Typography feel:

- Quiet, modern sans-serif.
- No negative letter spacing in final rules.
- Section headings use medium/semibold weight.
- Navigation text is larger than before after final alignment rules.
- Small metadata uses uppercase in several places.

## Spacing And Layout System

Container:

- Final homepage container: `width: min(1280px, calc(100% - 32px))`.
- Earlier global container: `width: min(1180px, calc(100% - 40px))`.
- Final alignment rules make main sections use the wider `1280px` container.

Section spacing:

- Final `.section` padding: `78px` top and bottom.
- Below `899px`: `58px` top and bottom.
- Notices section has custom padding around `58px 0 64px` in earlier rules, with responsive tightening.
- Extra activities uses `68px 0`.

Borders:

- Thin borders are common.
- Main border variable: `#dfe5e8`.
- Cards use `1px solid var(--border)`.
- Program cards use a `3px` brand top border.

Radius:

- Most current surfaces use very small radius: `2px`.
- This gives an institutional, squared-off look.

Shadows:

- Header: soft low shadow.
- Cards/panels: low elevation, commonly `0 4px 14px rgb(36 55 70 / 6-7%)`.
- Hover cards: stronger shadow around `0 10px 22px`.

Motion:

- Earlier animated section entries were added.
- Final institutional finish disables major section animations.
- Hover transitions remain for cards, images, and rows.
- Reduced motion support exists:

```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    scroll-behavior: auto !important;
    animation-duration: .01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: .01ms !important;
  }
}
```

## Responsive Behavior

Breakpoints used heavily:

- `1250px`: desktop nav compression.
- `1180px`: brand/nav adjustment.
- `1050px`: admin layout, less relevant to landing page.
- `899px`: main tablet/mobile switch.
- `760px`: admin layout.
- `560px`: small mobile landing page rules.
- `480px`: admin metrics.

Landing page responsive behavior:

- Top bar hidden below `899px`.
- Desktop nav hidden below `899px`.
- Mobile drawer enabled below `899px`.
- Hero becomes mobile-height aware with `100svh`.
- Notices/events stack below `899px`.
- Program cards become two columns on tablet/mobile.
- Gallery becomes two columns on tablet and one column on small mobile.
- Extra activities becomes two columns on tablet and one column on small mobile.
- Text overflow protection is added on small screens:
  - `overflow-wrap: anywhere`
  - `min-width: 0`
  - `max-width: 100vw`
  - horizontal overflow hidden on `html, body, #app, .site-shell`

## Backend Data Used By Landing Page

The home API is in `app/Http/Controllers/Public/HomeController.php`.

Data returned:

- `school`
- `popupNotice`
- `programs`
- `notices`
- `news`
- `events`
- `gallery`
- `achievements`
- `images`
- `heroSlides`

Currently used by landing page:

- `school`
- `popupNotice`
- `notices`
- `events`
- `gallery`
- `images`
- `heroSlides[0]`

Currently underused or unused:

- `programs`: backend returns dynamic programs, but home page uses hardcoded `courseCards`.
- `news`: backend returns latest news, but no news section is rendered on the current landing page.
- `achievements`: backend returns static achievements, but current extra activities cards do not use this array.
- `heroSlides[1]` and `heroSlides[2]`: sent but not used.

## Current Assets

Local public images:

- `public/images/hebs-facebook-profile.jpg`
- `public/images/school-hero.jpg`

Usage:

- Logo/header mark: `hebs-facebook-profile.jpg`
- Footer logo: `hebs-facebook-profile.jpg`
- Hero image: `school-hero.jpg`
- About section image: `school-hero.jpg`
- Program cards: `school-hero.jpg`
- Gallery fallback images: `school-hero.jpg`
- Some activity fallback images use Unsplash URLs.

Current asset limitation:

- The same school image is reused in many places, so several sections can feel visually repetitive.

## Generated Build Stage

The project currently has a Vite production build in `public/build`.

Manifest:

```json
{
  "resources/js/app.tsx": {
    "file": "assets/app-DHoLt2z9.js",
    "name": "app",
    "src": "resources/js/app.tsx",
    "isEntry": true,
    "css": [
      "assets/app-D1kSVwXg.css"
    ]
  }
}
```

Generated bundle sizes:

```text
190638 public/build/assets/app-DHoLt2z9.js
 75808 public/build/assets/app-D1kSVwXg.css
266446 total
```

The full generated files are:

- `public/build/assets/app-DHoLt2z9.js`
- `public/build/assets/app-D1kSVwXg.css`

These files are minified production output. They are not ideal to paste fully into documentation because the JS bundle is about 190 KB and includes React production code. The exact generated code remains available in the build files above.

### Generated HTML Template

The Laravel shell template that loads the app:

```html
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/jpeg" href="/images/hebs-facebook-profile.jpg">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
        @viteReactRefresh
        @vite(['resources/js/app.tsx'])
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
```

In a production Laravel render with the current manifest, `@vite(['resources/js/app.tsx'])` resolves to the generated CSS and JS files.

Expected production asset includes:

```html
<link rel="stylesheet" href="/build/assets/app-D1kSVwXg.css">
<script type="module" src="/build/assets/app-DHoLt2z9.js"></script>
```

### Generated CSS Bundle Excerpt

The generated CSS starts like this:

```css
:root{color-scheme:light;--brand: #12355b;--brand-dark: #0b2038;--accent: #b8860b;--heading: #172033;--body: #334155;--muted: #64748b;--background: #f7f9fc;--surface: #ffffff;--border: #d7dee8;--focus: #2563eb;font-family:Poppins,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,sans-serif}body,button,input,select,textarea{font-family:Poppins,ui-sans-serif,system-ui,sans-serif}h1,h2,h3,.brand strong,.footer-grid strong{font-family:Poppins,ui-sans-serif,system-ui,sans-serif;letter-spacing:-.025em}*{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:var(--background);color:var(--body);line-height:1.6}
```

Important: the generated CSS contains the full stylesheet order, including early legacy rules and later final overrides. The final visual output is controlled by the later rules near the bottom of `resources/css/app.css`, especially the final public header, landing-page visual system, institutional finish, and navigation/content alignment blocks.

### Generated JS Bundle Excerpt

The generated JS starts with React production code:

```js
function yc(e){return e&&e.__esModule&&Object.prototype.hasOwnProperty.call(e,"default")?e.default:e}var na={exports:{}},sl={},ta={exports:{}},T={};/**
 * @license React
 * react.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
```

The bundle contains:

- React production runtime.
- React DOM production runtime.
- Local SPA/runtime code.
- Public pages.
- Admin pages.
- Landing page component code.
- Client-side route/page resolution logic.

## Current UI Strengths

- Clear public-school information architecture.
- Notices and events are placed in a useful, high-priority location.
- Header contains practical contact information.
- Layout is responsive across desktop, tablet, and mobile.
- Popup notice behavior is accessible enough for keyboard close and focus.
- Section order makes sense for school visitors.
- Gallery and extra activity sections provide visual breaks.
- Final style system is restrained and consistent.

## Current UI Weaknesses

- Hero content is generic and does not yet strongly communicate HEBS identity.
- Hero is named like a carousel but only one slide is used.
- Program cards are hardcoded instead of using backend programs.
- Program card images are all the same.
- News data is returned by the backend but not displayed.
- Achievements data is returned but not directly displayed.
- Some CSS has many historical/legacy rules before final overrides, which makes the visual system harder to maintain.
- Social icons use text placeholders such as `f`.
- There is no strong admissions call-to-action section on the current landing page.
- Real image variety is limited.

## Recommended Frontend Boost Direction

Best next improvements:

1. Replace repeated program images with distinct real school/program visuals.
2. Use backend `programs` for the program section.
3. Add a strong admissions or enquiry call-to-action section.
4. Add a news/highlights section or stop returning unused `news` from the homepage response.
5. Convert the static hero into either a real carousel or rename/simplify the implementation.
6. Improve hero copy to include stronger school-specific identity.
7. Replace text placeholders with proper icons.
8. Consolidate CSS by removing obsolete overridden blocks after confirming visual parity.
9. Improve popup notice visual polish.
10. Add richer mobile spacing checks after frontend changes.

