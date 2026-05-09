# Core Project Skills

This directory is the default engineering skill surface for this repository.

Use these skills for:

- WordPress plugin development
- REST API and router work
- settings-shell and admin UI implementation
- runtime, provider, and performance work
- project triage, CLI, and local ops

## Primary Skills

- `wp-plugin-development`
- `wp-rest-api`
- `wp-performance`
- `wp-phpstan`
- `wp-project-triage`
- `wp-wpcli-and-ops`
- `wordpress-router`
- `wp-abilities-api`

## Supporting WordPress Skills

- `wp-block-development`
- `wp-block-themes`
- `wp-interactivity-api`
- `wp-playground`
- `wpds`
- `blueprint`
- `dev`
- `opt`

## Recommended Trigger Order

Use the project skills in this order for engineering work in this repository:

1. `wordpress-router`
   - Start here when the task touches WordPress code and you need to classify the repo slice first.
2. `wp-project-triage`
   - Use immediately after routing when you need deterministic repo/tooling/test signals.
3. One primary implementation skill:
   - `wp-plugin-development` for plugin/bootstrap/admin/runtime work
   - `wp-rest-api` for REST/controller/schema/permission work
   - `wp-wpcli-and-ops` for local WP operations and reproducible site commands
   - `wp-performance` for profiling or performance bottlenecks
   - `wp-phpstan` for static-analysis setup or fixes
4. One supporting WordPress skill only if the task explicitly needs it:
   - `wp-abilities-api`
   - `wp-block-development`
   - `wp-block-themes`
   - `wp-interactivity-api`
   - `wp-playground`
   - `wpds`
   - `blueprint`
5. Cross-cutting helpers when the task scope truly calls for them:
   - `api-and-interface-design`
   - `debugging-and-error-recovery`
   - `code-review-and-quality`
   - `documentation-and-adrs`

For this repository specifically, keep the existing local project skills ahead of the generic WordPress stack when the task is about product-surface or cloud-boundary decisions:

- `magick-cloud-boundary-guard`
- `magick-settings-surface`
- `magick-admin-utility-ui`

## Marketing / Growth Skills

Marketing and growth skills were moved out of this directory and now live under:

- `/Users/muze/gitee/magick-ai-root/.github/skills-marketing`

See [PROJECT-SKILLS-BOUNDARY.md](PROJECT-SKILLS-BOUNDARY.md) for the boundary rule.
