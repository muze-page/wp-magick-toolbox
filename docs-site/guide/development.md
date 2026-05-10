# 开发指南

## 添加新功能

### 1. 创建 PHP 后端

在 `admin/partials/[category]/` 下创建功能文件：

```php
<?php
if (!class_exists('Npcink_Page_New_Feature')) {
    class Npcink_Page_New_Feature {
        private static $option;

        public static function run($config) {
            self::$option = $config;
            add_action('wp_footer', array(__CLASS__, 'display'));
        }

        public static function display() {
            $val = MaBox_Admin::get_config(self::$option, 'new_feature.enabled');
            if ($val === true) {
                echo '<div>New Feature</div>';
            }
        }
    }
}
```

### 2. 注册模块

在 `admin/modules/registry.php` 中添加：

```php
'page.new_feature' => [
    'class'     => 'Npcink_Page_New_Feature',
    'file'      => 'admin/partials/page/new_feature.php',
    'option_key'=> 'page.new_feature.enabled',
    'category'  => 'page',
    'scope'     => 'frontend',
    'dependencies' => [],
    'risk_tags' => ['recommended'],
],
```

### 3. 添加前端类型定义

在 `vite/admin/src/tool/interface.tsx` 中添加：

```typescript
export type PageFunction = {
    // ... existing fields
    'new_feature.enabled': boolean;
    'new_feature.custom_text'?: string;
};
```

### 4. 添加默认值

在 `vite/admin/src/tool/defaultVar.tsx` 中添加：

```typescript
const PageFunction = {
    // ... existing fields
    'new_feature.enabled': false,
    'new_feature.custom_text': '',
};
```

### 5. 添加 UI 控件

在对应 Tab 组件（如 `vite/admin/src/components/page/function.tsx`）中添加：

```tsx
<Form.Item label="新功能开关">
    <FeatureSwitch
        module="page"
        featureKey="new_feature"
        optionData={optionData}
        setOptionData={setOptionData}
    />
</Form.Item>
```

## 安全规范

| 场景 | 要求 |
|------|------|
| SQL 查询 | 必须使用 `$wpdb->prepare()` |
| 输出到 HTML | 使用 `esc_html()` / `esc_url()` / `esc_attr()` |
| AJAX 端点 | 使用 `check_ajax_referer` + `current_user_can` |
| REST 端点 | 使用 `permission_callback` 验证权限 |
| 用户输入 | 使用 `sanitize_text_field()` 清洗 |

## 前端开发

### 项目结构

```
vite/
├── admin/     # 后台设置界面
├── count/     # 图表展示组件
└── public/    # 前端展示组件
```

### 开发服务器

每个项目独立运行：

```bash
cd vite/admin && npm run dev
```

代理配置在 `vite.config.ts` 底部，替换为本地 WordPress 地址。

### 构建

```bash
cd vite/admin && npm run build
```

构建产物在 `dist/` 目录，仅保留此目录即可部署。

## 测试

```bash
# 运行 PHPUnit 测试
vendor/bin/phpunit

# 运行 Vitest 测试
cd vite/admin && npm run test
```

## CI/CD

GitHub Actions 自动运行：
- PHP 7.4 ~ 8.3 多版本测试
- TypeScript 类型检查
- ESLint 代码检查
- Vite 构建验证
