---
globs: "magick-ai/src/settings-shell/runtime-modules/**/*.ts"
keywords: ["settings-shell", "panel", "component", "modal", "workspace", "DataTable", "Button", "createElement", "renderShell", "primitive", "shell", "view"]
---
# Settings Shell UI 规范（Magick AI）

## 核心原则

页面必须先选 workspace mode，再用共享 primitive 组合。**禁止在 `*-view.ts` 里手拼 section/table/header 骨架。**

## 组件导入

统一从 `./components` 导入，禁止子文件直接导入：

```typescript
// 正确
import { renderShellButton, renderMetricTileGrid, renderSettingsModalShell } from './components';

// 禁止
import { renderShellButton } from './components/base/Button';
```

## Workspace Mode 匹配表

| 页面类型 | 模式 | 使用的 Primitive |
|---------|------|-----------------|
| 目录/列表页 | Catalog | `renderWorkspaceExplainerSection` + DataViews 表格 |
| 导入页 | Import | 连接选择 + 候选摘要 + 单一路径主动作 |
| 治理列表 | Governance | 搜索/筛选 + 行级状态 + 轻操作 |
| 轻壳页 | Light Shell | `renderWorkspaceStatusSection` + 延迟加载 |
| 导航+编辑 | Navigator+Editor | 左选择器 + 右最终值编辑器 |

## Section Primitive（已验证存在于代码库）

```typescript
// 说明段
renderWorkspaceExplainerSection( createElement, { title, body } );

// 标准段
renderWorkspaceSection( createElement, { title, body } );

// 状态段（轻壳页）
renderWorkspaceStatusSection( createElement, { items } );
```

## 表格 Primitive

```typescript
// DataViews 列表页
renderSettingsDataViewsTable( createElement, { fields, data, pagination } );

// 实例台账/导入表
renderDataTableSectionShell( createElement, { columns, rows } );

// 实例类表格
renderInstancesTableView( createElement, { items, actions } );
```

## 预览 Modal

```typescript
renderPreviewModalShell( createElement, {
    ModalComponent,
    title: '预览',
    summary,      // 顶部：renderPreviewSummaryStrip
    leftPane,     // 左侧：renderPreviewSettingsPane（输入/配置）
    rightPane,    // 右侧：renderPreviewResultPanel（结果/警告）
    footer,       // 可选次级动作
} );
// 禁止：右侧放配置、左侧放结果、手拼 modal 骨架
```

## 样式规则

- 优先用 `mai-*` utility 和 `ui-classes` 语义别名
- 禁止在 settings-shell 里写裸 `flex` / `gap-*`
- 改布局找 `data-panel` / `data-slot` / `data-field-id`，不靠猜 `className`
- 页面模块禁止传 `summaryClassName`、`panelClassName`、`iconClassName`

## 禁止模式

- ❌ 在 `*-view.ts` 手写 `section + h3 + div` 结构
- ❌ 从已移除的兼容壳路径导入组件
- ❌ 新增页面私有的 tab 或 hero 样式变体
- ❌ 保留已无引用的 retired helper 导出

## Build 产物规则

改了 `src/settings-shell/**` 或 `assets/css/settings-shell.css` 后：
1. 必须运行 `pnpm run build`
2. 必须提交对应的 `build/**` 变更
3. Review 时先看 `src/` 源文件，`build/` 仅作验证证据
