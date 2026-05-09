# Project Skills Boundary

This repository now separates skills into two surfaces:

- `.github/skills` for core engineering and WordPress/plugin work
- `.github/skills-marketing` for optional marketing and growth work

## Core Project Skills

These are directly aligned with the day-to-day work in this repository and should be treated as the default skill set for local plugin, settings-shell, provider/runtime, and WordPress admin work:

- `wp-plugin-development`
- `wp-rest-api`
- `wp-performance`
- `wp-phpstan`
- `wp-project-triage`
- `wp-wpcli-and-ops`
- `wordpress-router`
- `wp-abilities-api`

## Optional Product / Growth Skills

These can still be useful for product launch, docs, marketing pages, content strategy, ASO, SEO, and pricing work, but they are not the primary operating surface for this codebase. They now live under `.github/skills-marketing`:

- `ai-seo`
- `seo-audit`
- `content-production`
- `content-creator`
- `pricing-strategy`
- `launch-strategy`
- `marketing-*`
- `social-*`
- `paid-ads`
- `cold-email`
- `ad-creative`
- `referral-program`
- `popup-cro`
- `paywall-upgrade-cro`
- `x-twitter-growth`

## Recommendation

- Use the core project skills by default for engineering work in this repository.
- Treat the marketing and growth skills as an optional extension set.
- Keep the core engineering skills in `.github/skills`.
- Keep the marketing and growth extension set in `.github/skills-marketing`.
- Apply them in a stable order:
  - route first with `wordpress-router`
  - triage second with `wp-project-triage`
  - then choose one primary implementation skill
  - only then add a supporting WordPress helper if the task explicitly needs it
  - keep Magick-specific local skills ahead of generic WordPress skills for cloud-boundary and product-surface decisions

## Notes

- Keep this directory free of editor/system junk such as `.DS_Store`.
- Avoid contradictory top-level docs. If the catalog grows or shrinks, update `README.md` and `SKILL.md` together.
