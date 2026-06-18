---
name: fdry-gutenberg-blocks
description: Implement ACF Gutenberg blocks in this theme using the fdry pattern. Use when creating a new custom block from a design or image, wiring its PHP render file, SCSS partial, main stylesheet import, Gutenberg bootstrap require, and matching ACF JSON with Options and Content tabs.
---

# Fdry Gutenberg Blocks

Use this skill when building a new custom ACF Gutenberg block for this theme.

Base the structure on `components/blocks/text-block.php`, but treat block-specific content fields as block-specific, not global defaults.

## Files to create or update

For a block named `<name>`:

- Create `components/blocks/<name>-block.php`
- Create `src/assets/styles/blocks/_<name>.scss`
- Add `@use "blocks/<name>";` to `src/assets/styles/_main.scss`
- Add `require get_template_directory() . '/components/blocks/<name>-block.php';` to `library/function-guttenberg.php`
- Create or update the matching ACF JSON file in `acf-json/`

Shared foundation (already in the theme — do not recreate):

- PHP helpers: `library/function-blocks.php`
- Padding SCSS: `src/assets/styles/common/_padding-block.scss`
- Appearance SCSS: `src/assets/styles/common/_appearance-block.scss`
- Max-width utilities: `src/assets/styles/common/_helper.scss` (`.content-max`, `.content-max--medium`, `.content-max--narrow`)

## Block naming rules

Normalize the block name to lowercase hyphen-case.

Choose the block name from the component pattern, not from the text shown inside the design.

- Do not name the block after the headline, paragraph, or copy visible in the image
- Use a generic, reusable name based on the visual structure, for example `hero`, `logo-grid`, `svg-text`, `image-text`, or `cta-banner`
- The block name should describe the kind of component being built, not the specific page content shown in the mockup
- If the user gives you the block name explicitly, use that name
- If the user does not give you a name, infer a meaningful generic component name from the layout

- PHP file: `<name>-block.php`
- ACF block name: `fd-<name>-block`
- Section class: `fd-<name>-block`
- Block location in ACF JSON: `"value": "acf/fd-<name>-block"`

Prefer a render callback name that stays readable and unique, for example `foundry_gutenblock_<camelName>Block`.

Implement block work on `main` unless the user explicitly asks for a different branch. This skill does not prescribe feature branches, commits, pushes, or pull requests.

## Block API and ACF block version

Every block registration must include both version keys:

- `'api_version' => 3` -- WordPress/Gutenberg block API version. Required for the iframe editor (mandatory from WordPress 7.0). Without it, WP 6.9+ logs a deprecation warning.
- `'acf_block_version' => 3` -- ACF's own block version. Enables the ACF Blocks V3 editing model where the preview always shows in the canvas and fields are edited via the expanded editor (slide-out panel opened by the pencil icon in the block toolbar).
- `'hide_fields_in_sidebar' => true` -- Hides fields from the sidebar panel entirely. Editors must use the Expanded Editor (pencil icon) to edit fields, which gives more room than the cramped sidebar.

Do **not** include `'mode' => 'edit'` or `'mode' => 'preview'`. ACF Blocks V3 removes the concept of modes entirely.

```php
acf_register_block(array(
    'name'              => 'fd-<name>-block',
    'api_version'       => 3,
    'acf_block_version' => 3,
    'hide_fields_in_sidebar' => true,
    'title'             => __('<Title>', 'foundry'),
    'description'       => __('<Description>', 'foundry'),
    'render_callback'   => 'foundry_gutenblock_<camelName>Block',
    'supports'          => ['align' => ['wide', 'center', 'full']],
    'category'          => 'foundry-category',
    'icon'              => array(
        'background' => '#0F1F2D',
        'foreground' => '#ffffff',
        'src'        => '<dashicon>',
    ),
    'keywords'          => ['foundry', ...],
));
```

## Layout layers (critical)

Background colour and vertical padding must live on the **same element** — the `<section>` — so padding sits **inside** the coloured area.

| Layer | Classes | Controls |
|---|---|---|
| Section | `fd-<name>-block` + `fdry-block-appearance--{light\|dark\|silver}` + `fdry-block-padding` | Background colour, vertical padding; technical `id` for scoped padding CSS |
| Anchor | `fd-block-anchor` (optional inner `<div>`) | Editor `anchor_id` for in-page nav (`#slug`); omitted when empty |
| Inner | `content-block` | Horizontal gutters |
| Inner | `content-max` (+ optional `--medium` / `--narrow`) | Centred column max-width |
| Content | `fd-<name>-block__content` (or block-specific wrapper) | Block content / typography |

```html
<section id="fd-<name>-{block-id}" class="fd-<name>-block fdry-block-appearance fdry-block-appearance--light fdry-block-padding">
    <div id="how-it-works" class="fd-block-anchor" aria-hidden="true"></div><!-- only when anchor_id is set -->
    <div class="content-block">
        <div class="content-max">
            <div class="fd-<name>-block__content">
                <!-- block content -->
            </div>
        </div>
    </div>
</section>
```

The `<section>` keeps a **technical** `id` from `fdry_block_id()` for scoped padding styles. The optional **`anchor_id`** field outputs a separate inner `<div class="fd-block-anchor">` — never put the editor anchor on the `<section>`.

**Do not** put background colour on `content-block` or `content-max`. **Do not** put vertical padding on inner wrappers.

Block-specific options such as `max_width` or `content_align` are implemented in the block PHP/SCSS — not in `function-blocks.php`.

## Appearance variants

| Label | ACF `appearance` | Background | Text / headings |
|---|---|---|---|
| Light (white) | `light` | `$color__bright` | `$color__text` body, `$color__main` headings |
| Dark | `dark` | `$color__main-dark` | `$color__bright` body + headings |
| Silver | `silver` | `$color__silver` (#F4F6F9) | Same as light |

Silver is a theme token (`$color__silver`) and Gutenberg palette slug `silver`. When a block supports silver, validate and build the appearance class in the block PHP (see `text-block.php`).

## PHP block pattern

Shared **Options** fields (defined in each block's own ACF JSON — not separate clone field groups):

- `anchor_id` — optional in-page anchor slug (text); always first in Options
- `padding_top` — shared padding
- `padding_bottom` — shared padding (50/50 row with padding top)
- `appearance` — when the block supports light / dark / silver

Block-specific Options (e.g. `max_width`, `content_align`) belong in the block PHP under a `// Block-specific options` comment — not in `function-blocks.php`.

Add a few brief comments in the PHP block file. Do not over-comment, but do add comments where they help the structure read quickly.

- Add `// Content` above block content fields and early-return logic
- Add `// Default options — shared across fdry blocks` before `fdry_block_id()`, padding, and appearance
- Add `// Block-specific options` before any field logic unique to this block
- Add `// Render` immediately before the HTML output starts

In the render callback, use the shared helpers from `library/function-blocks.php` for padding and block id. Handle appearance and block-specific options in the block file:

```php
function foundry_gutenblock_<camelName>Block( $block, $content = '', $is_preview = false ) {
    // Content
    // Early return if empty (unless $is_preview)

    // Default options — shared across fdry blocks
    $block_id  = fdry_block_id( $block, 'fd-<name>' );
    $anchor_id = fdry_block_anchor_id( get_field( 'anchor_id' ) );
    $padding   = fdry_block_padding( get_field( 'padding_top' ), get_field( 'padding_bottom' ) );
    $appearance = fdry_block_appearance_class( get_field( 'appearance' ) ); // or inline when block supports silver

    // Block-specific options
    // e.g. max_width, content_align — map to classes in this file only

    // Render
    ?>
    <?= fdry_block_padding_style( $block_id, $padding['pt'], $padding['pb'] ); ?>
    <section
        id="<?= esc_attr( $block_id ); ?>"
        class="fd-<name>-block <?= esc_attr( $appearance ); ?><?= $padding['class']; ?>"
    >
        <?php if ( $anchor_id !== '' ) : ?>
            <div id="<?= esc_attr( $anchor_id ); ?>" class="fd-block-anchor" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="content-block">
            <div class="content-max">
                <!-- block content here -->
            </div>
        </div>
    </section>
    <?php
}
```

Helper behaviour:

- `fdry_block_anchor_id()` sanitizes `anchor_id` to a valid HTML id (via `sanitize_title` + `sanitize_html_class`); returns empty string when unset — do not output the anchor `<div>` when empty
- `fdry_block_padding()` clamps values to **0–200** (any integer px; applied via scoped CSS variables + `.fdry-block-padding`)
- `fdry_block_padding_style()` outputs scoped `--padding-pt` / `--padding-pb` only when values are set
- `fdry_block_appearance_class()` returns `fdry-block-appearance fdry-block-appearance--light|dark` (defaults to light)
- `.fdry-block-padding` class is appended only when at least one padding value is set

Do **not** reimplement padding clamping or inline style tags inline in each block. Do **not** add block-specific option helpers to `function-blocks.php`.

For softer off-white backgrounds on a light block, override in the block SCSS (e.g. `&.fdry-block-appearance--light { background-color: #f5f5f5; }`). Do not add per-block color picker fields unless the user explicitly asks. Prefer the `silver` appearance token when the design calls for it.

## SCSS pattern

Each custom block must have its own SCSS partial in `src/assets/styles/blocks/`.

- File name: `_<name>.scss`
- Root selector: `.fd-<name>-block`
- Put only block-specific layout and visual styles there
- Rely on `.fdry-block-padding` for shared top/bottom spacing
- Rely on `.fdry-block-appearance--light|--dark|--silver` for shared text, heading, and button colours
- Rely on `.content-max` / `.content-max--medium` / `.content-max--narrow` for column width — do not set max-width on the block root
- Add block-level appearance overrides only when the design needs something the shared tokens do not cover

After creating the partial, add it to `src/assets/styles/_main.scss` with:

```scss
@use "blocks/<name>";
```

## ACF JSON pattern

Each block must have **one field group JSON file** in `acf-json/` assigned to that block via `"param": "block"`.

The base structure always includes these tabs:

1. `Options`
2. `Content`

Do **not** create separate shared field groups for padding or appearance. Do **not** use ACF Clone fields pointing at library groups. Add the fields directly in each block's `Options` tab.

### Options tab field order (consistent across all blocks)

Always follow this order in the `Options` tab:

1. **`anchor_id`** — **always first** (text, full width). Optional in-page anchor for navigation (`#slug`). Omit markup when empty.
2. **`padding_top`** + **`padding_bottom`** — side by side (`wrapper.width`: `50` each)
3. **`appearance`** — when the block supports appearance (full width, button group)
4. **Block-specific option fields** — any layout/control fields unique to the block (e.g. `max_width`, `content_align`)

When two block-specific fields share a 50/50 row, keep them **together after appearance**. Use ACF `conditional_logic` on fields that only apply in certain cases so a hidden field does not leave a gap in an earlier row (e.g. show `content_align` only when `max_width` is `medium` or `narrow`).

#### Shared field settings

**`anchor_id`** — text (label: `ID anchor link`)

- full width
- instruction: optional slug for in-page links (e.g. `how-it-works` → `#how-it-works`)
- leave empty to omit the anchor `<div>`

**`padding_top`** / **`padding_bottom`** — number

- instruction: `The value is in pixels (px).`
- width: `50`
- min: `0`
- max: `200`
- step: `1`
- append: `px`

**`appearance`** — button group (when the block uses appearance)

- choices: `light` : Light | `dark` : Dark | `silver` : Silver (include only variants the block supports)
- default: `light`
- return format: `value`
- layout: `horizontal`
- full width (`wrapper.width` empty)

#### Block-specific options (examples)

Add after appearance when the design needs them — not every block will have these:

- **`max_width`** — button group, `wrapper.width`: `50`
- **`content_align`** — button group, `wrapper.width`: `50`, conditional on medium/narrow max width

Reference: `acf-json/group_6a33e996e4507.json`.

### Content tab defaults

After the `Content` tab, add the fields needed for the actual block content, for example:

- `title`
- `text`
- `content`
- `link_label`
- `link_url`
- repeater or image fields when needed

Choose field types based on the design the user provides.

If the design includes a main visual element, treat it as editable block content by default.

- If the mockup shows a photo, illustration, logo, or background image that is central to the design, create an ACF image field for it
- Prefer an ACF image field over inventing a decorative CSS gradient or placeholder artwork
- For image-based hero sections, assume the main image should be editable unless the user explicitly says it is fixed
- Use CSS-generated visuals only for secondary decoration, not as a replacement for a primary design asset

### JSON rules

- Generate the field group key with `uniqid( 'group_' )` and save as `acf-json/{key}.json` (see `acf-json-sync` skill)
- Write JSON in expanded, human-readable form with 4-space indentation
- Set `"param": "block"`
- Set `"value": "acf/fd-<name>-block"`
- Update `"modified"` to a Unix timestamp strictly greater than the current time so ACF shows sync
- Reuse ACF’s standard field properties and structure from existing files in `acf-json/`

ACF JSON does not support comments. Reflect the structure by keeping fields grouped cleanly under the `Options` and `Content` tabs instead of trying to add inline comments to the JSON.

The existing reference example is `acf-json/group_6a33e996e4507.json`.

## When the user gives an image

If the user provides a mockup, screenshot, or design reference:

1. Infer the minimum field set needed to build the block.
2. Keep shared controls (`anchor_id`, then `padding_top` + `padding_bottom`, then `appearance` when needed) first in `Options`; add block-specific options after appearance.
3. Put editorial content fields in `Content`.
4. If the design contains a main image-driven element, add an image field for it by default.
5. Build the block PHP, SCSS partial, main stylesheet import, Gutenberg require, and ACF JSON together in one pass.

Do not invent extra global options that are not visible in the design unless the user asks for them.
Do not derive the block name from the copy visible in the mockup unless the user explicitly asked for that exact name.

## Implementation checklist

- Block name is generic and component-based, not taken from the visible page copy
- New block file created in `components/blocks/`
- Block registered with `acf_register_block()` including `'api_version' => 3` and `'acf_block_version' => 3`
- PHP file includes brief structural comments for Content, Default options, Block-specific options, and render sections
- Render callback uses `fdry_block_id()`, `fdry_block_anchor_id()`, `fdry_block_padding()`, and `fdry_block_padding_style()`; appearance and block-specific options stay in the block file
- Optional `anchor_id` renders inner `<div class="fd-block-anchor">` only when set — not on the `<section>`
- Section owns appearance background + vertical padding; inner wrappers use `content-block` > `content-max` > content wrapper
- New SCSS partial created in `src/assets/styles/blocks/`
- New SCSS partial imported in `src/assets/styles/_main.scss`
- New block file required in `library/function-guttenberg.php`
- ACF JSON created or updated in `acf-json/` (one group per block, not clone library groups)
- `Options` tab order: `anchor_id` first, then `padding_top` + `padding_bottom`, then `appearance` (if used), then block-specific fields
- `Content` tab contains the block’s actual editable content fields
- Primary design images are created as editable ACF image fields by default
- `modified` timestamp is newer than current time

## Project references

- PHP helpers: `library/function-blocks.php`
- Padding SCSS: `src/assets/styles/common/_padding-block.scss`
- Appearance SCSS: `src/assets/styles/common/_appearance-block.scss`
- Max-width SCSS: `src/assets/styles/common/_helper.scss`
- PHP example: `components/blocks/text-block.php`
- SCSS example: `src/assets/styles/blocks/_text.scss`
- SCSS entrypoint: `src/assets/styles/_main.scss`
- Gutenberg bootstrap: `library/function-guttenberg.php`
- ACF JSON example: `acf-json/group_6a33e996e4507.json`
