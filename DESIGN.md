# Design System: TuCancha

## 1. Visual Theme & Atmosphere

A confident, sport-charged interface with contained energy — like walking into a premium sports complex at night: clean lines, bright floodlights, fresh turf. The atmosphere is warm yet professional, merging Argentine casual boldness with modern SaaS clarity. Layouts breathe heavily with generous whitespace, but punctuated by moments of dense kinetic information (stats bands, value grids) that create dramatic rhythm.

- **Density:** 5/10 — "Daily App Balanced." Not cockpit-dense, not gallery-sparse. Enough info to feel useful, enough space to feel calm.
- **Variance:** 6/10 — "Offset Asymmetric." Slight asymmetry in grids and splits to break digital monotony without feeling chaotic.
- **Motion:** 6/10 — "Fluid CSS." Custom easing curves on all interactions, staggered reveals on scroll, but restrained — no cinematic choreography. The motion supports the content, never competes with it.

## 2. Color Palette & Roles

### Light Surfaces
- **Page Canvas** (`#f7f7f8`) — Primary page background. Warm off-white with barely perceptible warmth. Never pure white for full-page backgrounds.
- **Card Surface** (`#ffffff`) — Card, modal, and container fills. Pure white only on elevated surfaces to create subtle lift against the canvas.
- **Hover Surface** (`#f4f4f4`) — Interactive hover states on light backgrounds. Subtle enough to feel responsive without being jarring.

### Dark Surfaces
- **Ink Black** (`#111111`) — Primary dark surface for hero overlays, dark cards, stats bands, and dark sections. Never pure `#000000`.
- **Deep Forest** (`#0a3d21`) — Dark green gradient base for CTA sections. Evokes night-time sports fields.
- **Forest Mid** (`#0f5c32`) — Gradient midpoint for CTA surfaces.

### Text
- **Primary Text** (`#111111`) — Headlines, body text on light backgrounds. Maximum contrast without pure black harshness.
- **Secondary Text** (`#4a4a4a`) — Supporting paragraphs, descriptions. Warm-tinted neutral, not pure gray.
- **Muted Text** (`#5a5a5a`) — Timeline descriptions, card body text. Slightly lighter than secondary.
- **Hint Text** (`#999999`) — Placeholders, disabled labels, metadata.
- **Inverse Text** (`#ffffff`) — All text on dark surfaces.
- **Inverse Muted** (`rgba(255,255,255,0.55)`) — Secondary text on dark surfaces. Consistent opacity across all dark contexts.

### Brand Accent (Single Accent — Maximum 1)
- **TuCancha Green** (`#22c55e`) — Primary CTA fills, active states, focus rings, accent borders, icon strokes. The singular brand color.
- **Green Hover** (`#16a34a`) — Hover/active state for green CTAs. Darker, more saturated.
- **Green Light** (`#6eeaa0`) — Badge text on dark backgrounds, hero emphasis text, subtle highlights.
- **Green Whisper** (`#f0fdf4`) — Icon wrap backgrounds, pill fills on light surfaces. Barely-there green tint.
- **Green Border** (`#bbf7d0` / `#dcfce7`) — Subtle borders on green-tinted elements (pills, year badges).

### Semantic
- **Error Red** (`#e53935`) — Validation errors, destructive actions.
- **Warning Amber** (`#f5b301`) — Warnings, attention-needed states.
- **Info Blue** (`#3b82f6`) — Informational badges, help text.
- **Success Green** (`#22c55e`) — Matches primary. Confirmation states, success toasts.

### Borders & Dividers
- **Standard Border** (`#ececec`) — Card borders, section dividers.
- **Light Border** (`#f0f0f0`) — Subtler dividers, input borders at rest.
- **Dark Hairline** (`rgba(255,255,255,0.06)`) — Dividers on dark surfaces (stats band items).
- **Shell Border** (`rgba(0,0,0,0.03)`) — Double-bezel outer shell borders. Nearly invisible.

### Shadows
- **Whisper** (`0 2px 12px rgba(0,0,0,.03)`) — Subtle card elevation.
- **Default** (`0 4px 16px rgba(0,0,0,.06)`) — Standard card shadow.
- **Elevated** (`0 8px 24px rgba(0,0,0,.08)`) — Modals, dropdown menus.
- **Dramatic** (`0 12px 32px rgba(0,0,0,.12)`) — Active states, overlays.
- **Green Glow** (`0 4px 16px rgba(34,197,94,0.25)`) — CTA buttons, green-accented elevated states.

## 3. Typography Rules

### Font Stack
- **Display & Body:** `'Figtree', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif` — Geometric, modern, highly legible. Excellent weight range (400–900).
- **Monospace (if needed):** System monospace for code/metadata contexts.

### Scale & Hierarchy
- **Hero Display:** `clamp(40px, 6vw, 80px)` — Weight 900, letter-spacing `-0.045em`, line-height `0.95`. Maximum impact.
- **Section Title:** `clamp(28px, 3.5vw, 46px)` — Weight 900, letter-spacing `-0.035em`, line-height `1.08`. Confident and readable.
- **Card Heading:** `18px–20px` — Weight 800, letter-spacing `-0.025em`. Clear hierarchy without competing with section titles.
- **Body:** `15px–16px` — Weight 400, line-height `1.75–1.8`, max-width `65ch`. Relaxed, readable.
- **Eyebrow / Label:** `10px–12px` — Weight 700-800, letter-spacing `.08em–.12em`, uppercase. Pill-shaped badges.
- **Small / Meta:** `13px` — Weight 500-700, for labels, dates, metadata.

### Typography Anti-Patterns
- BANNED: Font sizes below 12px anywhere.
- BANNED: Body text wider than 65 characters per line.
- BANNED: Headings without negative letter-spacing (default tracking looks loose at large sizes).
- BANNED: Using font-weight 400 for headings (minimum 700 for any heading).

## 4. Component Stylings

### Buttons
- **Primary CTA:** Background `#22c55e`, text `#052e14` (dark green for contrast), weight 800, border-radius `14px`, padding `15px 34px`. Box-shadow with green glow. Hover: background `#16a34a`, `translateY(-3px)`, text becomes white, enhanced shadow. Active: `translateY(-1px) scale(0.97)`, reduced shadow. Transition: `300ms ease-out-expo`.
- **Ghost / Secondary:** Transparent background, `rgba(255,255,255,0.82)` text, 1px border `rgba(255,255,255,0.2)`, border-radius `14px`. Hover: subtle background fill, stronger border, lift. Active: `scale(0.97)`.
- **Dark CTA:** Background `#111`, white text, weight 700, pill-shaped `999px` radius. Hover: `translateY(-1px)`, enhanced shadow.
- **BANNED:** Neon outer glows on buttons. Custom mouse cursors. Buttons without `:active` feedback.

### Cards (Double-Bezel Architecture)
All major cards use the **Doppelrand** technique:
- **Outer Shell:** `background: rgba(0,0,0,0.02)`, `border-radius: 1.5rem–2.25rem`, `padding: 4px–6px`, `border: 1px solid rgba(0,0,0,0.03)`.
- **Inner Core:** Own background (white or `#111`), `border-radius: calc(outer - padding)` for concentric curves, own content padding `40px–72px`.
- **Accent Stripe:** 3px left border with green gradient `linear-gradient(to bottom, #22c55e, #16a34a)` for visual anchor.
- **Hover:** Entire shell lifts with `translateY(-6px)` and enhanced shadow with green tint. Icon inside scales `1.08` with slight background color shift.
- **Active:** `translateY(-2px) scale(0.985)` for tactile press feedback.
- **Dark Variant:** Shell background `rgba(17,17,17,0.06)`, inner core `#111`. Text becomes white/inverse-muted.

### Pills / Badges
- **Eyebrow Pill:** `padding: 5px 16px`, `border-radius: 999px`, background `#f0fdf4`, border `1px solid #bbf7d0`, text `#166534`, `11px` uppercase weight 800.
- **Culture Pill (dark surface):** `padding: 13px 22px`, `border-radius: 1rem`, background `rgba(255,255,255,0.05)`, border `rgba(255,255,255,0.08)`. Hover: background brightens, border shifts to green tint, `translateY(-2px)`.
- **Hero Badge:** backdrop-blur `8px`, green-tinted background, green border, green text.

### Inputs & Forms
- Label above input, error text below in red.
- Focus ring: `2px solid #22c55e` with subtle green glow shadow.
- Border at rest: `1px solid #ececec`. Focus: green border.
- Padding: `10px 14px`, border-radius `10px–12px`.

### Icons
- **Library:** Lucide icons exclusively.
- **Stroke width:** `1.5` (refined, not thick-generic). Never `2` or above.
- **Color:** `stroke: #22c55e` for accent icons, `stroke: currentColor` for contextual icons.
- **Size:** `14px` for inline/pills, `20px–22px` for card icons, `26px` for stat icons.

## 5. Layout Principles

### Grid & Containment
- **Max content width:** `1280px` centered with `24px` horizontal padding.
- **Wide container:** `1400px` for marketing layouts.
- **Narrow content:** `720px` for single-column text blocks.
- **Grid system:** CSS Grid over Flexbox for multi-column layouts. No `calc()` percentage hacks.

### Spacing Philosophy (8px base)
- **Section vertical padding:** `80px–100px` on desktop. The design breathes heavily between sections.
- **Card internal padding:** `32px–72px` depending on card importance.
- **Grid gaps:** `16px` for tight grids (values), `64px` for split layouts (manifesto).
- **Mobile reduction:** Sections compress to `48px–72px` vertical padding. Card padding reduces proportionally.

### Responsive Breakpoints
- **Mobile:** `< 480px` — Single column, reduced padding `16px–20px`, smaller radii.
- **Small tablet:** `< 640px` — Brand icon replaces full logo, hamburger menu activates.
- **Tablet:** `< 768px` — Typography scales down, parallax disables, section padding reduces.
- **Small desktop:** `< 900px` — Multi-column grids collapse to single column.
- **Desktop:** `900px+` — Full layout with all columns active.

### Layout Anti-Patterns
- BANNED: Horizontal scroll on any viewport.
- BANNED: `h-screen` — always use `min-height: 100dvh` for full-height sections.
- BANNED: Elements smaller than `44px` tap target on touch devices.
- BANNED: Text smaller than `14px` on mobile viewports.

## 6. Motion & Interaction

### Easing Curves (Custom — never default `ease` or `linear`)
- **Ease-out Expo** (`cubic-bezier(0.16, 1, 0.3, 1)`) — Primary UI easing. Enters fast, settles slowly. Used for hovers, lifts, reveals.
- **Ease-out Quart** (`cubic-bezier(0.25, 1, 0.5, 1)`) — Slightly less dramatic. Used for subtle transitions.
- **Ease-in-out Expo** (`cubic-bezier(0.77, 0, 0.175, 1)`) — For on-screen movement, morphing, continuous motion.
- **Drawer Curve** (`cubic-bezier(0.32, 0.72, 0, 1)`) — iOS-like drawer and sheet animations.

### Duration Guide
- **Button feedback:** `100–160ms`
- **Hover states:** `300–400ms` with ease-out-expo
- **Card lifts:** `400–500ms` with ease-out-expo
- **Scroll reveals (AOS):** `600–700ms`
- **Blob drift:** `18–22s` infinite alternate (decorative only)

### Animation Patterns
- **Stagger on grids:** `50–80ms` delay between items. Short enough to feel cascading, not slow.
- **Scroll entry:** `data-aos="fade-up"` with custom durations. Never instant appearance.
- **Hover underline reveal:** `::after` pseudo-element with `scaleX(0)` → `scaleX(1)` transition.
- **Active press:** `scale(0.97)` on `:active` for all interactive elements. Instant tactile feedback.
- **Icon hover scale:** Icons inside cards scale `1.08` on parent hover with background color shift.

### Performance Rules
- ONLY animate `transform` and `opacity`. Never `top`, `left`, `width`, `height`, `padding`, `margin`.
- `backdrop-filter: blur()` ONLY on fixed/sticky elements (header, overlays). Never on scrolling content.
- `will-change: transform` sparingly, only on actively animating elements.
- `background-attachment: fixed` disabled on mobile (`< 768px`) — causes performance issues on iOS.

### Reduced Motion
```css
@media (prefers-reduced-motion: reduce) {
  /* Disable all transform-based motion */
  /* Keep opacity transitions for state indication */
  /* Disable decorative blob animations */
}
```

## 7. Anti-Patterns (Banned)

### Visual
- No pure black `#000000` — use `#111111` (Ink Black)
- No pure gray text — use tinted neutrals (`#4a4a4a`, `#5a5a5a`)
- No neon/outer glow shadows on any element
- No oversaturated colors beyond the defined palette
- No gradient text on headlines
- No generic 3-column equal-width card layouts without visual differentiation

### Typography
- No `Inter`, `Roboto`, `Arial`, `Open Sans`, or `Helvetica` as primary font
- No generic serif fonts (`Times New Roman`, `Georgia`, `Garamond`)
- No heading without tight letter-spacing
- No body text without controlled line-length (`max-width: 65ch`)

### Motion
- No `linear` or default `ease-in-out` easing on UI transitions
- No animations on frequently-repeated keyboard actions
- No animation durations exceeding `300ms` on standard UI interactions
- No `ease-in` on entering elements (feels sluggish)
- No `scale(0)` entry animations — start from `scale(0.95)` minimum

### Layout
- No horizontal scroll on any viewport
- No `h-screen` — use `min-h-[100dvh]`
- No interactive elements below `44px` touch target
- No text below `14px` on mobile
- No centered hero layouts (use asymmetric or left-aligned when variance > 4)

### Content
- No "Scroll to explore", scroll arrows, or bouncing chevrons
- No AI copywriting cliches ("Elevate", "Seamless", "Unleash", "Next-Gen")
- No broken image links — all images must resolve
- No fake round numbers in stats (`99.99%`, `50%`)

### Code
- No `transition: all` — always specify exact properties
- No animating layout-triggering properties (`top`, `left`, `width`, `height`)
- No `backdrop-blur` on scrolling containers
- No `background-attachment: fixed` on mobile
- No `z-index` values above `60` outside the defined scale
