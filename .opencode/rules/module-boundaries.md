---
globs: "magick-ai/src/**/*.ts"
keywords: ["import", "cross-module", "boundary", "module", "dependency", "refer", "settings-shell", "open-platform", "mcp", "cloud", "addon"]
---
# Magick AI 模块边界约束

## 模块文件范围表

每个模块只能改自己的文件。跨模块改动必须分独立会话。

| 模块 | 允许范围 | 禁止引用 |
|------|---------|---------|
| `settings-shell` | `src/settings-shell/**`, `config/settings*.php` | `includes/open-platform/**`, `includes/mcp/**`, `cloud/**` |
| `open-platform` | `includes/open-platform/**`, `includes/controllers/class-rest-open-platform-controller.php` | `src/settings-shell/**`, `includes/mcp/**`, `cloud/**` |
| `mcp` | `includes/mcp/**` | `includes/open-platform/**`, `src/settings-shell/**`, `cloud/**` |
| `abilities` | `includes/abilities/**` | `src/settings-shell/**`, `cloud/**` |
| `router` | `includes/router/**` | `src/settings-shell/**`, `cloud/**` |
| `billing` | `includes/billing/**` | `src/settings-shell/**`, `cloud/**` |
| `cloud-service` | `cloud/app/**`, `cloud/tests/**` | `magick-ai/includes/**`, `magick-ai/src/**` |
| `cloud-frontend` | `cloud/frontend/**` | `magick-ai/**`, `cloud/app/**` |
| `cloud-addon` | `magick-ai-cloud-addon/**` | `magick-ai/includes/**`, `cloud/**` |

## 跨面通信规则

- PHP 插件间通信必须通过 WordPress hooks 或 REST API，禁止直接 `require/include`
- TypeScript 模块间通信必须通过公开 API 接口
- `settings-shell` 的 TS 不能 `import` `cloud/frontend` 的模块
- `cloud-addon` 的 PHP 不能引用 `magick-ai/includes/` 的内部类

## 工具链约束

- `magick-ai/` 内不直接调用 `.vscode/local-exec.sh` → 用 `pnpm run local:php/wp/composer`
- `magick-ai/` 内不直接调用 `scripts/local-wp.sh` → 用 `pnpm run local:*`
- 不要手动编辑 `composer.json` 不同步 `composer.lock` → 通过 `pnpm run local:composer`

## 禁止模式

- ❌ 跨模块直接引用（如 settings-shell import open-platform 内部函数）
- ❌ 在同一会话里混改两个模块
- ❌ 创建第二真源（`ai/docs/` 和 `magick-ai/docs/` 同一内容双写）
