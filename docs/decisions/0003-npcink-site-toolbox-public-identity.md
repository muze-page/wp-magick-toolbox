# ADR-0003: 公开身份统一为 Npcink Site Toolbox

## 状态

已接受

## 日期

2026-07-17

## 背景

项目仍处于首次公开分发前，没有真实用户、兼容负担或需要迁移的历史安装。`Magick Toolbox`、`magick-toolbox`、`wp-magick-toolbox` 与内部 `MaBox` 命名并存，增加了发布包、文档、REST 地址和仓库地址之间的辨认成本。

Npcink 已确定为品牌名，因此应在公开发布前一次性固定面向用户和开发者的产品身份。此次调整只解决公开身份不一致，不借机重写已经稳定工作的内部实现。

## 决策

- 插件显示名统一为 `Npcink Site Toolbox`。
- WordPress.org 目标 slug、gettext 文本域、后台页面 slug、主文件名、发布 ZIP 根目录和文件名统一为 `npcink-site-toolbox`。
- Composer 包名使用 `npcink/site-toolbox`，GitHub 仓库计划更名为 `npcink-site-toolbox`。
- 公开 REST namespace 使用 `npcink-site-toolbox/v1`，公开 nonce action 与 header 分别使用 `npcink_site_toolbox_public_api` 和 `x-npcink-site-toolbox-nonce`。
- 版本提升至 `3.1.0`，作为一次完整、可回滚的发布身份迁移。
- GitHub 仓库只在最终提交通过完整本地验证、精确 SHA CI 和发布包验证后更名。
- 保留 `MaBox_*` 类名、`MAGICK_MIXTURE_*` 常量名、现有 WordPress Option key 和其他内部存储标识；它们不属于本次公开身份迁移。
- 保留旧标签、旧 Release、历史总结和归档代码中的原始名称与地址，避免改写历史证据。

## 理由

首次公开分发前是清理公开契约最便宜的窗口。统一名称可以减少安装目录、发布脚本、REST 文档、后台入口和支持说明之间的映射成本，也能避免未来在已有用户环境中再做破坏性迁移。

内部符号和数据键不会被用户直接使用，重命名它们没有相应产品收益，却会扩大回归面。公开边界统一、内部实现稳定，是当前收益和风险最合理的平衡。

## 备选方案

### 只修改显示名

拒绝。显示名、slug、REST namespace、ZIP 根目录和仓库名继续分裂，发布与排障成本不会真正下降。

### 全量重命名所有 `Magick`、`MaBox` 和 Option key

拒绝。这会把品牌迁移扩大为架构重写，增加配置丢失和模块回归风险，但不会改善用户体验。

### 等 WordPress.org 审核后再改

拒绝。获批后的 slug 和已有安装路径会形成兼容负担，成本明显更高。

## 影响

- 新发布包安装目录为 `npcink-site-toolbox/`，入口文件为 `npcink-site-toolbox.php`。
- 后台入口为 `plugins.php?page=npcink-site-toolbox`。
- REST 客户端和调用方必须使用 `/wp-json/npcink-site-toolbox/v1/*`。
- 旧开发版本不会获得兼容转发；项目目前没有用户，因此采用干净切换。
- ADR-0001 被本决策取代，但其内容作为 3.0.1 阶段的历史决策保留。
