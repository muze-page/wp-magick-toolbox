---
globs: "**/*"
keywords: ["commit", "git", "message", "branch", "merge", "rebase"]
---
# Git 工作流规范（Magick AI）

## Commit Message 格式

```
<模块>: <类型> <一句话描述>

类型：
  feat     — 新功能
  fix      — 修复 bug
  refactor — 重构（不改行为）
  test     — 新增/修改测试
  docs     — 文档更新
  chore    — 工具/配置/杂项

示例：
  open-platform: feat 新增 ability rate-limit 配置项
  settings-shell: fix 折叠面板状态刷新后丢失
  mcp: refactor 统一 config 读取方式
  docs: 更新 contract-test-patterns 指南
```

## 禁止操作

- ❌ `--no-verify` 跳过 pre-commit hook（紧急热修复除外且有审查）
- ❌ force push 到 main/master
- ❌ `git commit --amend` 到已推送的提交

## Trunk-Based Development

- 默认基于 main 分支开发
- 并行工作使用 worktree
- 长时间任务使用 save point 模式

## 提交前检查

- Pre-commit hook 自动检查 commit message 格式
- `check:quality-budget` 自动检查代码质量预算
