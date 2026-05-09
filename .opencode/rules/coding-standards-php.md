---
globs: "magick-ai/**/*.php"
keywords: ["type", "error", "catch", "log", "debug", "deprecated", "comment", "phpstan", "ABSPATH", "var_dump", "print_r"]
---
# PHP 编码硬约束（Magick AI）

## 类型安全

禁止压制类型错误：

```php
// 禁止
/** @phpstan-ignore-next-line */
// phpcs:ignore
/** @var mixed $data */

// 正确 — 修复类型错误本身
// PHPStan baseline 行数不能膨胀，修复而非压低
```

## 错误处理

```php
// 禁止 — 空 catch 块
try { ... } catch ( Exception $e ) {}

// 正确 — 至少记录
try { ... } catch ( Exception $e ) {
    error_log( 'Magick AI: ' . $e->getMessage() );
}
```

## 调试残留（提交前必须清理）

```
❌ var_dump($data);
❌ print_r($data);
❌ error_log('DEBUG: ...');     # 非必要的 debug 日志
```

## 弃用函数（零容忍）

禁止使用 WordPress 弃用函数。如已存在调用，必须替换为当前推荐版本。

## 函数守卫

不要用 `function_exists()` 做功能守卫，用自动加载替代。

## 硬编码禁止

```
❌ 密钥/token/密码 → 必须用环境变量或 wp-config.php 常量
❌ 绝对路径 → 用 plugin_dir_path( __FILE__ )
❌ SQL 拼接 → 必须用 $wpdb->prepare()
```

## 质量门禁

| 场景 | 命令 |
|------|------|
| 小改动 | `cd magick-ai && pnpm run check:fast` |
| 代码变更 | `cd magick-ai && pnpm run check:changed` |
| public API 变更 | `cd magick-ai && pnpm run check:risk` |
| 发版前 | `cd magick-ai && pnpm run check:release` |
