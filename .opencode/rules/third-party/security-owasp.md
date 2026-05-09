---
globs:
  - "**/*.{php,ts,tsx,py}"
keywords: ["security", "XSS", "CSRF", "injection", "CORS", "authentication", "authorization", "secret", "token", "password", "hash", "encrypt"]
---
# 通用安全规范（来源: OWASP + cursor.directory）

## 输入验证（所有语言）

- 永远不信任用户输入——全部验证和清理
- 使用白名单验证代替黑名单
- 在服务端验证，不依赖客户端验证

## 输出编码

- PHP: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses()`
- Python: `html.escape()`, Jinja2 自动转义
- React/JSX: 默认转义，`dangerouslySetInnerHTML` 仅限可信内容

## SQL 注入防范

- PHP: `$wpdb->prepare()` — 100% 覆盖
- Python: SQLAlchemy 参数化查询
- 绝不拼接用户输入到 SQL

## CSRF 防护

- PHP/WordPress: `wp_nonce_*()` / `check_ajax_referer()`
- Python/FastAPI: CSRF middleware
- 状态变更请求（POST/PUT/DELETE）必须验证 token

## 认证与授权

- 密码用 `password_hash()` / bcrypt，绝不明文存储
- 密钥/token 用环境变量，不硬编码
- 权限检查在逻辑执行前，不是事后
- JWT token 设置合理过期时间（< 24h）

## 敏感数据

- API 密钥、凭证绝不提交到 Git
- `.env` 文件加入 `.gitignore`
- 日志中不输出密码、token、密钥

## 依赖安全

- 定期更新依赖
- 移除未使用的包
- 检查已知漏洞（`npm audit`, `composer audit`, `pip-audit`）

## HTTP 安全头

```
Strict-Transport-Security: max-age=31536000
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
Content-Security-Policy: default-src 'self'
```

## CORS

- 不要用 `Access-Control-Allow-Origin: *` 在生产环境
- 精确指定允许的 origin 列表
