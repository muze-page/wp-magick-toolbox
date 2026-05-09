---
globs: "magick-ai/includes/{rest,router,abilities,controllers}/**/*.php"
keywords: ["REST", "endpoint", "route", "register_rest_route", "WP_REST_Controller", "api", "permission_callback", "schema", "namespace", "ability", "wp_register_ability"]
---
# WordPress REST API & Abilities 开发规范（Magick AI）

## 命名空间

项目使用两个 REST namespace 常量（定义在 `includes/constants.php`）：

```php
MAGICK_AI_REST_NAMESPACE       // 'magick-ai/v1'        — 管理后台路由
MAGICK_AI_OPEN_REST_NAMESPACE  // 'magick-ai/open/v1'   — Open Platform 公开路由
```

必须使用常量，禁止硬编码字符串。

## 路由注册

```php
// 管理后台路由
register_rest_route( MAGICK_AI_REST_NAMESPACE, '/admin/settings/xxx', array(
    'methods'             => WP_REST_Server::READABLE,
    'callback'            => array( $this, 'handler' ),
    'permission_callback' => function() {
        return current_user_can( 'manage_options' );
    },
    'args' => array(
        'page' => array(
            'type'     => 'integer',
            'default'  => 1,
            'sanitize_callback' => 'absint',
        ),
    ),
) );
```

## 权限回调 — 工厂模式

项目使用工厂函数生成 permission_callback，不要内联重复代码：

```php
// 正确 — 使用项目工厂函数
'permission_callback' => magick_ai_build_ability_permission_callback( $group, $capability, $ability_id ),

// 需要可信运行时桥接时
'permission_callback' => magick_ai_build_runtime_bridge_permission_callback( $group, $capability, $ability_id ),
```

## Abilities API 注册

通过 `includes/abilities/registry/register-categories.php` 统一注册：

```php
// 能力分类
wp_register_ability_category( $category_id, $args );

// 能力注册
wp_register_ability( $ability_id, $args );
```

新增 ability 必须：
1. 在 `includes/abilities/` 对应子目录定义 config
2. 通过 registry 统一注册
3. 同步更新 `docs/contracts/` 契约文档

## 控制器注册

新增 REST 控制器必须通过 `includes/rest/controller-registry.php` 统一注册，不要独立调用 `add_action( 'rest_api_init', ... )`。

## 每个 endpoint 必须定义 `args`

```php
'args' => array(
    'field_name' => array(
        'type'              => 'string',
        'required'          => true,
        'sanitize_callback' => 'sanitize_text_field',
        'validate_callback' => function( $value ) { ... },
    ),
),
```

## 新增 Endpoint 后

必须同步更新对应契约文档：`docs/contracts/` 下对应文件。
