---
globs:
  - "magick-ai/**/*.php"
  - "magick-ai-cloud-addon/**/*.php"
keywords: ["WordPress", "plugin", "hook", "theme", "wp", "shortcode", "post_type", "taxonomy", "transient", "cron"]
---
# WordPress 通用开发规范（来源: cursor.directory）

## 核心原则

- 提供精确、技术性的 PHP 和 WordPress 示例
- 遵循 WordPress PHP 编码标准
- 优先用面向对象方式提升模块化
- 避免代码重复，优先迭代和模块化
- 函数/变量/文件名使用描述性命名
- 目录命名：小写+连字符（如 `wp-content/themes/my-theme`）

## PHP 编码实践

- 文件头启用严格类型：`declare(strict_types=1);`
- 优先使用 WordPress 核心函数和 API
- 错误处理：
  - 使用 `WP_DEBUG_LOG` 记录错误
  - 对预期异常使用 try-catch
- 数据验证用 WordPress 内置函数（`sanitize_*`）
- 表单提交必须验证 nonce

## 数据库

- 始终用 `$wpdb` 抽象层
- 动态查询用 `$wpdb->prepare()` 防 SQL 注入
- Schema 变更用 `dbDelta()` 函数
- 缓存用 Transients API

## 扩展性

- 用 hooks（actions + filters）扩展功能，绝不修改核心文件
- 子主题保留更新兼容性
- 权限用 WordPress 角色和 capabilities 系统
- 自定义 post types 和 taxonomies 按需使用

## 资源管理

- 脚本和样式用 `wp_enqueue_script()` / `wp_enqueue_style()`
- AJAX 优先用 REST API，其次 `admin-ajax.php`
- 后台任务用 `wp_cron()` 或 Action Scheduler
- 配置数据用 Options API 存储

## 国际化

- 所有文本用 WordPress 本地化函数：`__()`, `_e()`, `esc_html__()`
- 文本域统一用插件 slug
