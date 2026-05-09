---
name: "core-project-skills"
description: "Core engineering skill catalog for this repository. Use for WordPress plugin development, settings-shell/admin UI, REST endpoints, provider/runtime work, performance, triage, and local operations."
license: MIT
metadata:
  version: 1.0.0
  category: engineering
---

# Core Project Skills

This directory is the default skill surface for engineering work in this repository.

Prefer these skills first:

- `wp-plugin-development`
- `wp-rest-api`
- `wp-performance`
- `wp-phpstan`
- `wp-project-triage`
- `wp-wpcli-and-ops`
- `wordpress-router`
- `wp-abilities-api`

Additional WordPress-focused helpers remain here for block, interactivity, playground, and blueprint workflows.

Recommended trigger order for this repository:

1. `wordpress-router`
2. `wp-project-triage`
3. one primary implementation skill:
   - `wp-plugin-development`
   - `wp-rest-api`
   - `wp-wpcli-and-ops`
   - `wp-performance`
   - `wp-phpstan`
4. one supporting WordPress helper only when the task explicitly needs it:
   - `wp-abilities-api`
   - `wp-block-development`
   - `wp-block-themes`
   - `wp-interactivity-api`
   - `wp-playground`
   - `wpds`
   - `blueprint`
5. cross-cutting helpers as needed:
   - `api-and-interface-design`
   - `debugging-and-error-recovery`
   - `code-review-and-quality`
   - `documentation-and-adrs`

For Magick-specific product-surface and cloud-boundary work, keep repository-local skills ahead of the generic WordPress catalog:

- `magick-cloud-boundary-guard`
- `magick-settings-surface`
- `magick-admin-utility-ui`

Marketing and growth skills are intentionally not part of this default catalog anymore. They were moved to:

- `/Users/muze/gitee/magick-ai-root/.github/skills-marketing`

Use that directory only when the task is explicitly about launch, content, SEO, pricing, CRO, or growth work.
