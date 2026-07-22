import type {
  RuntimeFeatureModule,
  RuntimeFeatureStatus,
} from "@/tool/interface";

export const scopeLabels: Record<RuntimeFeatureModule["scope"], string> = {
  frontend: "仅前台",
  admin: "仅后台",
  both: "前后台",
};

export const tierLabels: Record<RuntimeFeatureModule["tier"], string> = {
  core: "稳定",
  advanced: "进阶",
  high_risk: "高风险",
  experimental: "实验性",
};

export const diagnosticLabels: Record<RuntimeFeatureStatus["diagnostics"]["status"], string> = {
  good: "状态良好",
  warning: "需要关注",
  critical: "需要处理",
};

export function buildSupportReport(data: RuntimeFeatureStatus): string {
  const moduleLines = data.modules.map((module) => {
    const status = module.always_loaded ? "始终加载" : "已启用";
    return `- ${module.label} [${module.id}] | ${status} | ${scopeLabels[module.scope]} | ${tierLabels[module.tier]}`;
  });
  const toolLines = data.editor_tools.map((tool) =>
    `- ${tool.title} [${tool.id}] | ${tool.type === "pattern" ? "区块样板" : "动态区块"}`,
  );
  const diagnosticLines = data.diagnostics.items.map((item) =>
    `- ${item.title}: ${item.status} | ${item.message}`,
  );

  return [
    `${data.plugin.name} ${data.plugin.version}`,
    `WordPress ${data.environment.wordpress_version} | PHP ${data.environment.php_version}`,
    `运行模块 ${data.counts.active}/${data.counts.registered} | 始终加载 ${data.counts.always_loaded} | 编辑器工具 ${data.counts.editor_tools}`,
    `诊断状态: ${diagnosticLabels[data.diagnostics.status]}`,
    `生成时间: ${data.generated_at}`,
    "",
    "运行模块:",
    ...(moduleLines.length > 0 ? moduleLines : ["- 无"]),
    "",
    "编辑器工具:",
    ...(toolLines.length > 0 ? toolLines : ["- 无"]),
    "",
    "环境检查:",
    ...(diagnosticLines.length > 0 ? diagnosticLines : ["- 无"]),
  ].join("\n");
}
