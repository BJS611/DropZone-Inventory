# DropZone Inventory Design System

Bold, technical, high-contrast, streetwear-inspired inventory management.

## Overview

DropZone Inventory uses the original DropZone visual language for a technical inventory management application.

The interface is built on a dark foundation with high-contrast neon accents:
- red-orange for actions and urgency
- electric cyan for information and secondary interaction
- signal yellow for warnings
- green for successful states
- red for errors and destructive actions

The visual style is sharp, dense, industrial, and confident.

This design system is for inventory management, not ecommerce.

Do not introduce:
- shopping cart UI
- checkout UI
- payment UI
- fake scarcity
- fake countdowns
- decorative gradients
- generic white SaaS dashboards

Actual inventory quantities, borrowing counts, and stock alerts must always come from application data.

---

# Colors

- **Primary** `#FF3D00`: primary CTAs, add actions, save actions, important stock operations
- **Secondary** `#00E5FF`: links, secondary actions, information highlights, navigation accents
- **Tertiary** `#FFEA00`: warnings, low-stock states, attention markers
- **Background** `#121212`: global page background
- **Surface** `#1E1E1E`: cards, panels, tables, dialogs, forms
- **Success** `#00E676`: successful states, normal stock indicators
- **Warning** `#FFEA00`: low stock, attention-required states
- **Error** `#FF1744`: errors, destructive actions, out-of-stock states
- **Info** `#00E5FF`: informational states

Recommended additional neutral tokens:

- `#FAFAFA`: primary text
- `#B0B0B0`: secondary text
- `#757575`: muted text / placeholder
- `#444444`: input and control borders
- `#333333`: dividers and card borders
- `#2A2A2A`: hover surfaces
- `#1A1A1A`: disabled surfaces

---

# Typography

- **Headline Font:** Bebas Neue
- **Body Font:** Work Sans
- **Mono Font:** Space Mono

## Display

Bebas Neue, 64px, regular, line-height 1.0, tracking 0.04em.

Use for:
- major dashboard numbers
- major page statements
- major application branding

## Headline

Bebas Neue, 44px, regular, line-height 1.1, tracking 0.03em.

Use for:
- page titles
- major section headings

## Subhead

Bebas Neue, 32px, regular, line-height 1.2, tracking 0.02em.

Use for:
- secondary section headings
- important inventory summaries

## Body Large

Work Sans, 18px, regular, line-height 1.6.

## Body

Work Sans, 15px, regular, line-height 1.6.

## Body Small

Work Sans, 14px, regular, line-height 1.5.

## Caption

Work Sans, 12px, medium, line-height 1.4, tracking 0.03em.

Use for:
- stock labels
- metadata
- timestamps
- supporting text

## Overline

Work Sans, 11px, bold, line-height 1.2, tracking 0.1em, uppercase.

Use for:
- section labels
- status labels
- category markers

## Code

Space Mono, 14px, regular, line-height 1.5.

Use for:
- SKU
- transaction IDs
- technical identifiers

---

# Spacing

Base unit: `8px`

Scale:

```text
4
8
12
16
24
32
48
64
96
```

Component padding:
- small: 8px
- medium: 16px
- large: 24px

Section spacing:
- mobile: 48px
- tablet: 64px
- desktop: 96px

Avoid arbitrary spacing values unless required by a real layout constraint.

---

# Border Radius

- **None:** `0px` — inventory cards, tables, major panels
- **Small:** `2px` — badges, status chips, filters
- **Medium:** `8px` — buttons, inputs, selects
- **Large:** `12px` — dialogs and overlays
- **XL:** `20px` — optional promotional/informational panels
- **Full:** `9999px` — avatars and notification dots

Inventory cards must remain square.

---

# Elevation

Use dark shadows and controlled neon glow.

- **Subtle:** 2px offset, 4px blur, black 40%
- **Medium:** 4px offset, 12px blur, black 50%, orange glow 8%
- **Large:** 8px offset, 24px blur, black 60%, orange glow 12%
- **Overlay:** 20px offset, 50px blur, black 80%
- **Glow Orange:** 20px orange glow at 30%, 60px orange glow at 10%
- **Glow Cyan:** 20px cyan glow at 25%, 60px cyan glow at 8%

Glow should be selective, not applied to every component.

---

# Buttons

## Primary

```text
background: #FF3D00
text: #FFFFFF
font: Work Sans 14px / 700
padding: 12px 24px
radius: 8px
hover: #E63600
active: #CC3000
```

Use for:
- Tambah Barang
- Simpan
- Stock In
- Stock Out
- Buat Peminjaman
- Confirm action

Primary buttons may use orange glow on important actions.

## Secondary

```text
background: transparent
text: #00E5FF
border: 1.5px #00E5FF
radius: 8px
hover background: rgba(0, 229, 255, 0.08)
```

## Ghost

```text
background: transparent
text: #B0B0B0
hover background: #2A2A2A
hover text: #FAFAFA
```

## Destructive

```text
background: #FF1744
text: #FFFFFF
radius: 8px
hover: #D50032
```

## Sizes

- Small: 34px height
- Medium: 42px height
- Large: 52px height

Disabled state:
- 40% opacity
- disabled cursor

Transitions: approximately 150ms.

---

# Cards

## Default

```text
background: #1E1E1E
border: 1px #333333
radius: 0px
```

Content padding:
- 16px for normal cards
- 24px for larger sections

Hover:
- border becomes `#444444`

## Elevated

Use medium shadow.

Use stronger orange glow only for:
- critical inventory alerts
- important KPI cards
- important operational actions

---

# Inputs

## Text Input

```text
background: #1E1E1E
border: 1px #444444
text: #FAFAFA
placeholder: #757575
radius: 8px
padding: 0 14px
height: 42px
font: Work Sans 15px / 400
```

Focus:

```text
border: #FF3D00
ring: 3px rgba(255, 61, 0, 0.20)
```

Error:

```text
border: #FF1744
ring: 3px rgba(255, 23, 68, 0.20)
```

Disabled:

```text
background: #1A1A1A
opacity: 40%
```

Label:
- Work Sans
- 13px
- 500
- `#B0B0B0`

Helper text:
- 12px
- `#757575`

---

# Chips

## Filter Chip

```text
height: 32px
padding: 0 12px
radius: 2px
border: 1px #444444
```

Selected:

```text
background: #FF3D00
text: #FFFFFF
border: #FF3D00
```

Hover:

```text
background: #2A2A2A
```

## Status Chip

Success:

```text
background: rgba(0, 230, 118, 0.15)
text: #00E676
```

Warning:

```text
background: rgba(255, 234, 0, 0.15)
text: #FFEA00
```

Error:

```text
background: rgba(255, 23, 68, 0.15)
text: #FF1744
```

Info:

```text
background: rgba(0, 229, 255, 0.15)
text: #00E5FF
```

Status should always include text or an icon. Never communicate status by color alone.

---

# Lists

Default list item:

```text
height: 46px
padding: 0 16px
font: Work Sans 15px / 400
divider: 1px #333333
hover: #2A2A2A
selected: rgba(255, 61, 0, 0.10)
```

Icon:
- 20px
- 12px gap

---

# Tables

Inventory tables are high-density operational UI.

Use:
- dark surface
- clear column hierarchy
- compact row height
- dividers `#333333`
- monospace for SKU and transaction ID
- status chips for condition and stock state

Required visual priority:

```text
Item name
↓
SKU / category
↓
stock state
↓
location
↓
metadata
↓
actions
```

Actions should remain visually secondary to the data itself.

---

# Dashboard KPI Cards

KPI cards use:

```text
background: #1E1E1E
border: 1px #333333
radius: 0px
padding: 24px
```

Primary number:
- Bebas Neue
- 44px or 64px depending on hierarchy

Examples:
- Total Items
- Total Stock
- Low Stock
- Out of Stock
- Currently Borrowed

Use semantic colors:
- green for healthy/normal
- yellow for attention
- red for critical
- cyan for information

---

# Sidebar

Desktop sidebar:
- dark surface
- square edge
- compact navigation
- strong active marker

Active navigation may use:
- orange accent
- subtle orange glow
- orange text

Inactive:
- `#B0B0B0`

Hover:
- `#2A2A2A`
- `#FAFAFA`

Mobile:
- convert sidebar into a drawer

---

# Forms

Form structure:

```text
Section heading
Description
Field label
Input
Helper
Validation error
```

Complex forms should be grouped:

```text
Basic Information
Classification
Stock
Location
Supplier
Status
```

Do not create huge ungrouped blocks of fields.

---

# Tooltips

```text
background: #2A2A2A
text: #FAFAFA
font: Work Sans 12px / 400
padding: 8px 12px
radius: 6px
max-width: 200px
border: 1px #444444
delay: 200ms
```

---

# Inventory Status Visuals

## Normal

```text
color: #00E676
```

## Low Stock

```text
color: #FFEA00
```

## Out of Stock

```text
color: #FF1744
```

Use both:
- semantic color
- explicit label

Example:

```text
LOW STOCK
```

not merely a yellow dot.

---

# Icons

Use a consistent icon library such as Lucide.

Do not use emoji as primary UI icons.

Suggested icons:
- `Package`
- `Warehouse`
- `Search`
- `Filter`
- `Plus`
- `Pencil`
- `Trash2`
- `Eye`
- `ArrowDownToLine`
- `ArrowUpFromLine`
- `ArrowLeftRight`
- `ClipboardList`
- `Users`
- `Shield`
- `FileText`
- `History`

---

# Motion

Transitions should be:
- fast
- sharp
- approximately 150ms

Allowed:
- hover
- focus
- dialog enter/exit
- drawer open/close
- state transitions

Avoid:
- bounce
- wiggle
- playful spring
- confetti
- floating decorative animation

---

# Responsive Rules

Mobile-first.

Minimum test widths:

```text
320px
375px
768px
1024px
1280px
1440px
```

Mobile:
- one-column forms
- responsive navigation
- table becomes controlled list/card or horizontal scroll
- actions remain reachable
- no unintended horizontal overflow

Desktop:
- higher information density
- sidebar visible
- full inventory table
- multi-column dashboard

---

# Do's

- Use bold Bebas Neue headings.
- Use orange for important actions.
- Use cyan for secondary information.
- Use yellow for low-stock warnings.
- Use green for healthy state.
- Use red for errors and destructive actions.
- Keep inventory cards square.
- Keep real-time stock values connected to database data.
- Use compact, technical layouts.
- Use mobile-first responsive behavior.
- Use visible focus states.
- Use real status labels in addition to color.

# Don'ts

- Don't create an ecommerce layout.
- Don't use a shopping cart.
- Don't use fake countdowns.
- Don't fabricate inventory numbers.
- Don't create fake urgency.
- Don't use gradients.
- Don't use generic white SaaS dashboard styling.
- Don't use huge soft rounded cards.
- Don't use playful animations.
- Don't use orange for ordinary body text.
- Don't apply glow everywhere.
