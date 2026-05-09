---
globs: "magick-ai/**/*.php"
keywords: ["nonce", "escaping", "capability", "sanitize", "prepare", "sql", "auth", "permission", "input", "output"]
---
# WordPress 安全规范（Magick AI）

## 数据输出

所有输出到 HTML 的内容必须转义：

```php
// 项目实际用法
echo esc_html( $data );
echo esc_attr( $value );
esc_url( $url );
esc_html_e( '文本', 'magick-ai' );

// 禁止
echo $data;                  // 裸输出
echo $_GET['key'];          // 未过滤超全局变量
```

## 输入处理

```php
// 项目实际用法 — sanitize_text_field + wp_unslash
$value = sanitize_text_field( wp_unslash( $_POST['key'] ?? '' ) );
$value = filter_input( INPUT_GET, 'key', FILTER_UNSAFE_RAW );  // 后续手动过滤

// 禁止 — 已弃用的 FILTER_SANITIZE_STRING (PHP 8.1+)
// $value = filter_input( INPUT_GET, 'key', FILTER_SANITIZE_STRING ); // ❌
```

## 数据库查询

所有 SQL 必须用 `$wpdb->prepare()`，禁止字符串拼接：

```php
// 正确
$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}table WHERE id = %d", $id );
$wpdb->prepare( "WHERE name LIKE %s", '%' . $wpdb->esc_like( $search ) . '%' );

// 禁止
"SELECT * FROM {$wpdb->prefix}table WHERE id = " . $id;
```

## Nonce 验证

项目实际使用的 nonce action 名称：

```php
// AJAX 处理器
check_ajax_referer( 'magick_ai_debug_runner', 'nonce' );
check_ajax_referer( 'magick_ai_sync_instance_tags', 'nonce' );
check_ajax_referer( 'magick_ai_tool_worker_probe', 'nonce' );

// 生成 nonce 时耦合对应的 action
wp_create_nonce( 'magick_ai_debug_runner' );
```

## 权限检查

```php
// REST permission_callback
current_user_can( 'manage_options' );

// 禁止
'permission_callback' => '__return_true',  // 公开端点例外时必须有注释说明原因
```

## 文件头防御

每个 PHP 文件必须：

```php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
```
