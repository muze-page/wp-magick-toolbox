# ADR-0001: WordPress.org 公开身份使用 Magick Toolbox

## 状态

已接受

## 日期

2026-07-16

## 背景

GitHub `v3.0.0` 已以 `WP Magick Toolbox` 发布，但 WordPress.org 官方 Readme Validator 禁止显示名或固定链接以受限词 `WP` 开头。公开目录身份必须在首次提交前稳定；获批后的 slug 不能随意更改。

当前 `Contributors: npcink` 也无法解析为 WordPress.org 用户。项目不能在未知账号上声明贡献者身份。

## 决策

- 从 3.0.1 起，插件公开显示名使用 `Magick Toolbox`。
- WordPress.org 目标 slug 和 gettext 文本域统一为 `magick-toolbox`。
- 在用户确认真实、可登录且大小写准确的 WordPress.org 用户名之前，`readme.txt` 不声明 `Contributors`；确认后再加入该账号。
- GitHub 仓库名继续使用 `wp-magick-toolbox`，不重命名仓库、远端 URL、开发目录或现有发布归档。
- 主文件名、`MAGICK_MIXTURE_NAME`、其他 `MAGICK_MIXTURE_*` 常量名以及现有 WordPress Option key 保持不变。公开身份调整本身不要求迁移或重置设置。
- WordPress.org 发布 ZIP 必须来自完成 3.0.1 版本同步并通过 CI 的同一提交；不改写已发布的 `v3.0.0` 标签或附件。

## 理由

WordPress.org slug 是面向安装、更新和翻译系统的公开身份；仓库名是开发与协作地址，两者不要求相同。保留仓库名可以避免无价值的 Git URL、CI 徽章和开发文档迁移。

运行时常量名和 Option key 是内部加载及数据定位契约，与目录显示名无关。为品牌调整重命名它们只会扩大验证面，并可能造成配置丢失，没有产品收益。

## 备选方案

### 保留 `WP Magick Toolbox`

拒绝。官方 Validator 将该名称判为不可提交。

### 同时重命名仓库、主文件、常量和 Option key

拒绝。它不会提高 WordPress.org 可接受性，却会引入路径、构建、数据迁移和回滚风险。

### 继续声明 `npcink` 为 Contributor

拒绝。WordPress.org 当前无法解析该用户，公开身份必须来自真实账号。

## 影响

- WordPress 后台插件列表和 WordPress.org 页面显示 `Magick Toolbox`。
- 目录 URL 目标为 `/plugins/magick-toolbox/`，最终可用性仍以提交页面分配结果为准。
- GitHub 仓库、开发命令和现有本地配置保持稳定。
- 用户需要创建或确认 WordPress.org 账号、稳定接收审核邮件，并在提交前提供准确用户名。
