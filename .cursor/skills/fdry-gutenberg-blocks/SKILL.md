---
name: fdry-gutenberg-blocks
description: Implement ACF Gutenberg blocks in this theme using the fdry pattern. Use when creating a new custom block from a design or image, wiring its PHP render file, SCSS partial, main stylesheet import, Gutenberg bootstrap require, and matching ACF JSON with Options and Content tabs.
---

# Fdry Gutenberg Blocks

Use this skill when building a new custom ACF Gutenberg block for this theme.

Base the structure on `components/blocks/wysiwyg-block.php`, but treat background color and the WYSIWYG field as block-specific, not global defaults.

## Files to create or update

For a block named `<name>`:

- Create `components/blocks/<name>-block.php`
- Create `src/assets/styles/blocks/_<name>.scss`
- Add `@use "blocks/<name>";` to `src/assets/styles/_main.scss`
- Add `require get_template_directory() . '/components/blocks/<name>-block.php';` to `library/function-guttenberg.php`
- Create or update the matching ACF JSON file in `acf-json/`

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
    'title'             => __('<Title>', 'fdry'),
    'description'       => __('<Description>', 'fdry'),
    'render_callback'   => 'foundry_gutenblock_<camelName>Block',
    'supports'          => ['align' => ['wide', 'center', 'full']],
    'category'          => 'foundry-category',
    'icon'              => array(
        'background' => '#323C4E',
        'foreground' => '#ffffff',
        'src'        => '<dashicon>',
    ),
    'keywords'          => ['foundry', ...],
));
```

## PHP block pattern

Every block should support these shared fields:

- `padding_top`
- `padding_bottom`

Add a few brief comments in the PHP block file. Do not over-comment, but do add comments where they help the structure read quickly.

- Add `// Options` above the shared option fields such as `padding_top` and `padding_bottom`
- Add `// Content` above the block content fields such as `background_image`, `title`, `subtitle`, links, or other editorial fields
- Add a short setup comment before derived values and inline style preparation
- Add a short render comment immediately before the HTML output starts
- Prefer structural comments that explain sections, not obvious line-by-line narration

In the render callback:

1. Read `padding_top` and `padding_bottom` with `get_field()`.
2. Clamp them to `0..200` when present.
3. Build a block id as `fd-<name>-` plus `$block['id']` fallback.
4. Output a `<style>` block only when at least one padding value exists.
5. Set `--padding-pt` and `--padding-pb` custom properties on the section id.
6. Render a `<section>` with:
   - `id="<generated id>"`
   - `class="fd-<name>-block"`
   - append `fdry-block-padding` when padding exists
7. Inside the section, always render:

```php
<div class="content-block">
  <!-- block content here -->
</div>
```

Keep the shared wrapper generic. Add only the block-specific markup inside `.content-block`.

Do not add background color support unless the user explicitly asks for it.

## SCSS pattern

Each custom block must have its own SCSS partial in `src/assets/styles/blocks/`.

- File name: `_<name>.scss`
- Root selector: `.fd-<name>-block`
- Put only block-specific styles there
- Rely on `.fdry-block-padding` for shared top/bottom spacing

After creating the partial, add it to `src/assets/styles/_main.scss` with:

```scss
@use "blocks/<name>";
```

## ACF JSON pattern

Each block must have a field group JSON file in `acf-json/`.

The base structure always includes these tabs:

1. `Options`
2. `Content`

### Options tab defaults

Always include these two fields first in `Options`:

- `padding_top`
- `padding_bottom`

Use the same structure as the existing WYSIWYG block:

- field type: `number`
- instruction: `The value is in pixels (px).`
- width: `50`
- min: `0`
- max: `200`
- step: `1`
- append: `px`

After those two shared fields, add any block-specific option fields in the same `Options` tab.

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

- Write JSON in expanded, human-readable form with 4-space indentation
- Set `"param": "block"`
- Set `"value": "acf/fd-<name>-block"`
- Update `"modified"` to a Unix timestamp strictly greater than the current time so ACF shows sync
- Reuse ACF’s standard field properties and structure from existing files in `acf-json/`

ACF JSON does not support comments. Reflect the structure by keeping fields grouped cleanly under the `Options` and `Content` tabs instead of trying to add inline comments to the JSON.

The existing reference example is `acf-json/group_699a00100001.json`, but do not copy its block-specific fields unless the new design actually needs them.

## When the user gives an image

If the user provides a mockup, screenshot, or design reference:

1. Infer the minimum field set needed to build the block.
2. Keep shared controls in `Options`.
3. Put editorial content fields in `Content`.
4. If the design contains a main image-driven element, add an image field for it by default.
5. Build the block PHP, SCSS partial, main stylesheet import, Gutenberg require, and ACF JSON together in one pass.

Do not invent extra global options that are not visible in the design unless the user asks for them.
Do not derive the block name from the copy visible in the mockup unless the user explicitly asked for that exact name.

## Implementation checklist

- Block name is generic and component-based, not taken from the visible page copy
- New block file created in `components/blocks/`
- Block registered with `acf_register_block()` including `'api_version' => 3` and `'acf_block_version' => 3`
- PHP file includes brief structural comments for Options, Content, setup, and render sections
- Render callback outputs the shared section/content-block structure
- Padding custom properties handled the same way as the WYSIWYG block
- New SCSS partial created in `src/assets/styles/blocks/`
- New SCSS partial imported in `src/assets/styles/_main.scss`
- New block file required in `library/function-guttenberg.php`
- ACF JSON created or updated in `acf-json/`
- `Options` tab includes `padding_top` and `padding_bottom`
- `Content` tab contains the block’s actual editable content fields
- Primary design images are created as editable ACF image fields by default
- `modified` timestamp is newer than current time

## Project references

- PHP example: `components/blocks/wysiwyg-block.php`
- SCSS example: `src/assets/styles/blocks/_wysiwyg.scss`
- SCSS entrypoint: `src/assets/styles/_main.scss`
- Gutenberg bootstrap: `library/function-guttenberg.php`
- ACF JSON example: `acf-json/group_699a00100001.json`
