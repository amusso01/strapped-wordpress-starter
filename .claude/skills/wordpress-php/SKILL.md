---
name: wordpress-php
description: Write WordPress code using modern PHP 8+ practices while respecting WordPress conventions. Use when implementing or refactoring theme/plugin PHP, hooks, templates, data flow, sanitization, validation, escaping, SQL, capabilities, nonces, and i18n.
---

# Modern PHP WordPress Skill

Write WordPress code using modern PHP 8+ practices without fighting WordPress core conventions.

## Rules

- Use PHP 8+ syntax where it improves clarity.
- Prefer strict typing, typed properties, parameter types, and return types.
- Prefer class-based architecture for non-trivial features.
- Keep templates dumb.
- Prepare data before rendering.
- Prefer `<?= ?>` over `echo` in templates for simple output.
- Sanitize input early.
- Validate before saving or processing.
- Escape output late, based on context.
- Use `wp_kses()` only when HTML output is intentionally allowed.
- Use WordPress APIs before custom implementations.
- Use `$wpdb->prepare()` for custom SQL.
- Prefer early returns over nested conditionals.
- Avoid global mutable state.
- Avoid mixing business logic with markup.
- Use descriptive names and small methods.
- Prefer maintainable production code over tutorial-style code.
- Respect WordPress hooks, capabilities, nonces, and i18n.
