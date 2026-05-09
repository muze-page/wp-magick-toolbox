---
globs:
  - "magick-ai/src/**/*.ts"
  - "cloud/frontend/src/**/*.{ts,tsx}"
keywords: ["TypeScript", "interface", "type", "generic", "utility", "enum", "narrowing"]
---
# TypeScript 通用编码规范（来源: cursor.directory + opencode-rules）

## 核心原则

- 编写直白、可读、可维护的代码
- 遵循 SOLID 原则和设计模式
- 使用强类型，避免 `any`
- 对复杂结构创建自定义 type/interface
- 使用 `import type` 导入仅用于类型的导入
- 不可变属性用 `readonly`
- 简单操作用箭头函数

## 命名约定

| 类型 | 约定 | 示例 |
|------|------|------|
| 类 | PascalCase | `class UserService` |
| 变量/函数/方法 | camelCase | `getUserData()` |
| 文件/目录 | kebab-case | `user-service.ts` |
| 常量/环境变量 | UPPERCASE | `MAX_RETRY_COUNT` |
| 类型/接口 | PascalCase | `interface UserProps` |

## 函数规范

- 使用描述性名称：动词+名词（如 `getUserData`）
- 优先用默认参数和对象解构
- 对公共 API 使用 JSDoc 文档注释

## 类型安全

```typescript
// 正确 — 精确类型
interface ApiResponse<T> { data: T; total: number; }
function fetchItems(): Promise<ApiResponse<Item[]>> { ... }

// 禁止
const data = response as any;
// @ts-ignore
```

## 错误处理

- 对可能失败的操作使用 Result 类型或 try-catch
- 空 catch 块必须至少记录错误
- 使用 discriminated unions 而非 throwing 做流程控制

## 性能

- 大数据集操作用 `Promise.all()` 并发
- 避免不必要的深拷贝
- 使用 `Array.isArray()` 而非 `instanceof Array`
