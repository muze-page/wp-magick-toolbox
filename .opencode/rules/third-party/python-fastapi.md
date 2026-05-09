---
globs:
  - "cloud/app/**/*.py"
  - "cloud/tests/**/*.py"
keywords: ["FastAPI", "Python", "Pydantic", "async", "endpoint", "router", "dependency", "middleware", "SQLAlchemy"]
---
# FastAPI Python 开发规范（来源: cursor.directory，适配 Magick AI Cloud）

## 核心原则

- 编写精确、技术性的 Python 示例
- 优先函数式、声明式编程；仅在必要时使用类
- 避免代码重复，优先模块化
- 变量名使用描述性命名，辅助动词（`is_active`, `has_permission`）
- 目录和文件用小写+下划线（`routers/user_routes.py`）

## 类型与验证

- 所有函数签名必须有类型提示
- 输入验证用 Pydantic v2 模型，不用原始字典
- 使用 Pydantic 的 `BaseModel` 统一输入/输出 schema

```python
# 正确
from pydantic import BaseModel

class CreateItemRequest(BaseModel):
    name: str
    quantity: int = 1

@router.post("/items")
async def create_item(req: CreateItemRequest) -> ItemResponse:
    ...
```

## 路由与依赖

- 纯函数用 `def`，异步操作用 `async def`
- 路由定义使用声明式，带明确返回类型注解
- 用 FastAPI 依赖注入管理状态和共享资源
- 最小化 `@app.on_event`；优先用 lifespan context manager

```python
# 正确 — 依赖注入
@router.get("/items/{item_id}")
async def get_item(
    item_id: str,
    db: AsyncSession = Depends(get_db),
) -> ItemResponse:
    ...
```

## 错误处理

- 在函数开头处理错误和边界情况
- 用 early return 避免深层嵌套
- 预期错误用 `HTTPException`
- 异常错误通过 middleware 统一处理

```python
# 正确 — early return pattern
if not item:
    raise HTTPException(status_code=404, detail="Item not found")
# happy path follows
```

## 性能

- I/O 密集型操作用异步（数据库调用、外部 API）
- 静态和频繁访问数据用 Redis 缓存
- 大数据集用延迟加载
- 最小化阻塞 I/O 操作

## 中间件

- 使用 middleware 处理日志、错误监控、性能追踪
- 安全头：CORS、CSP

## 关键约定

1. 依赖 FastAPI 依赖注入管理状态和共享资源
2. 关注 API 性能指标（响应时间、延迟、吞吐量）
3. 路由中限制阻塞操作——优先异步和非阻塞流
