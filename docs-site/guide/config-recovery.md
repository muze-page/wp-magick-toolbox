# 配置变更与恢复

当前 Pre-GA 版本不再提供浏览器 `localStorage` 快照、设置 JSON 导入导出或旧配置迁移备份。这些机制会复制完整配置，并可能把凭据留在浏览器或下载文件中。

## 日常回退

如果某项配置导致异常：

1. 返回对应设置区域。
2. 关闭或改回刚刚变更的项目。
3. 在保存前差异中确认范围。
4. 保存并清理站点缓存。

## 站点级恢复

发布前应使用主机、数据库工具或运维系统进行 WordPress 数据库备份。需要整站恢复时，请恢复经验证的数据库备份，而不是导入来源不明的插件配置 JSON。

## 登录尝试保护紧急恢复

如果代理 IP 配置错误或测试过程中无法登录，优先使用以下任一方式临时关闭“登录尝试保护”。该恢复只影响失败尝试限制，不会关闭“限制匿名作者枚举”。

### 方法一：使用紧急常量

在 `wp-config.php` 的“停止编辑”注释之前加入：

```php
define( 'MABOX_DISABLE_LOGIN_PROTECTION', true );
```

重新登录后台后，关闭“登录尝试保护”并保存，再从 `wp-config.php` 删除该常量。不要把紧急常量作为长期功能开关。

### 方法二：使用 WP-CLI

在 WordPress 根目录执行；如果不在根目录，请把 `/path/to/wordpress` 替换为实际路径：

```bash
wp --path=/path/to/wordpress option patch update \
  Magick_ToolBox_Option_Domestic \
  login_security attempt_limit_enabled false \
  --format=json

wp --path=/path/to/wordpress cache flush
```

第一条命令以 JSON 布尔值 `false` 关闭保护，第二条清理持久对象缓存。可以用以下命令检查保存结果：

```bash
wp --path=/path/to/wordpress option get \
  Magick_ToolBox_Option_Domestic \
  --format=json
```

如果 `login_security.attempt_limit_enabled` 路径不存在，表示当前 Option 尚未保存该能力；无需插入兼容字段。此时应检查是否有其他安全插件、服务器规则或缓存仍在限制登录。

## 凭据恢复

管理端不会显示已保存凭据原值。遗失微信或 OSS 凭据时，应在对应服务重新生成，然后在插件内选择“替换”；无需先清除旧值。
