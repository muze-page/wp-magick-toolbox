---
globs:
  - "cloud/frontend/src/**/*.{ts,tsx}"
keywords: ["React", "Next.js", "component", "server", "client", "SSR", "RSC", "layout", "page", "app router"]
---
# Next.js + React 开发规范（来源: cursor.directory，适配 Magick AI Cloud Frontend）

## 项目技术栈

- Next.js 16（App Router）
- React 19
- TypeScript
- Tailwind CSS

## App Router 结构

```
src/app/
├── layout.tsx          # 根布局
├── page.tsx            # 首页
├── admin/              # 后台管理
│   ├── layout.tsx
│   └── page.tsx
├── portal/             # 用户门户
│   ├── layout.tsx
│   └── page.tsx
└── (marketing)/        # 营销页面组
    ├── layout.tsx
    └── page.tsx
```

## 组件模式

### Server Components（默认）

```tsx
// 数据获取在 Server Component 中完成
async function SiteList() {
    const sites = await fetchSites();
    return <SiteTable data={sites} />;
}
```

### Client Components（按需）

```tsx
'use client';

import { useState } from 'react';

function SiteFilter({ onFilter }: { onFilter: (q: string) => void }) {
    const [query, setQuery] = useState('');
    return <input value={query} onChange={(e) => { setQuery(e.target.value); onFilter(e.target.value); }} />;
}
```

## 组件规范

- 一个文件一个组件（除非紧耦合的子组件）
- 用 `interface` 定义 Props 类型
- 导出命名函数组件（不用 default export）
- 状态管理：Server Component 直取数据，Client Component 最小化 `useState`

## Tailwind CSS 规范

- 优先用 Tailwind utility classes
- 长 className 用 `cn()` 工具合并
- 避免内联 `style` 属性
- 响应式用 `sm:` / `md:` / `lg:` 断点

## 错误与加载

```tsx
// error.tsx — 错误边界
'use client';
export default function Error({ error, reset }: { error: Error; reset: () => void }) {
    return <div>出错了: {error.message} <button onClick={reset}>重试</button></div>;
}

// loading.tsx — 加载骨架
export default function Loading() {
    return <Skeleton />;
}
```

## 禁止模式

- ❌ Client Component 中直接 fetch 数据（用 Server Component 或 SWR/React Query）
- ❌ 在 Server Component 中使用 hooks
- ❌ `any` 类型
- ❌ 内联 `style={{}}`
- ❌ 直接操作 DOM（用 React refs）
