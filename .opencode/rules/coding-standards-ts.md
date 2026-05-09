---
globs: "magick-ai/src/**/*.ts"
keywords: ["TypeScript", "type", "any", "as any", "ts-ignore", "ts-expect-error", "createElement", "render", "component"]
---
# TypeScript 编码约束（Magick AI）

## 类型安全 — 零容忍

```typescript
// 禁止
const data = response as any;
// @ts-ignore
// @ts-expect-error

// 正确 — 定义精确类型
interface ApiResponse { data: Item[]; total: number; }
const data: ApiResponse = response;
```

## 渲染模式（Settings Shell）

Settings Shell 基于 `createElement` 函数式渲染，**不使用 JSX**：

```typescript
// 正确
export function renderMyView( createElement: Function, options: MyViewProps ) {
    return createElement( 'div', { className: 'mai-my-view' },
        renderShellButton( createElement, { label: '保存', variant: 'primary' } )
    );
}

// 禁止 — 在 settings-shell 中使用 JSX
export function MyView() { return <div>...</div>; }
```

## 命名约定

| 类型 | 约定 | 示例 |
|------|------|------|
| 渲染函数 | `render[Name]` | `renderShellButton` |
| 类型/接口 | PascalCase + Props 后缀 | `ShellButtonProps` |
| CSS 类名 | `mai-*` 前缀 | `mai-shell-btn-primary` |
| data 属性 | `data-panel`, `data-slot`, `data-field-id` | — |
| testid | `data-testid="mai-*"` | `data-testid="mai-preview-trigger"` |

## 禁止模式

```
❌ console.log 残留 → 提交前必须清理
❌ as any → 修复类型而非压制
❌ @ts-ignore / @ts-expect-error → 同上
❌ 空 catch 块
❌ settings-shell 中 import cloud/frontend 模块 → 模块边界违规
❌ 手写 section/table/header 骨架 → 用 workspace primitive
```

## Build 要求

改 `src/settings-shell/**` 后：
1. `pnpm run build`
2. 提交 `build/**` 产物
3. `build/` 中不得包含 `Module build failed`
