import { SECRET_PATHS } from "@/generated/settings-types";
import type {
  DomesticCommentSecurity,
  DomesticCompliance,
  DomesticLoginSecurity,
  DomesticWechat,
  FunctionAuxiliary,
  FunctionSeo,
  OptimizeAdmin,
  OptimizeMedium,
  OptimizeSite,
  Option,
  PageComment,
  PageFeature,
  PageFunction,
  PageJurisdiction,
  PerformanceDbClean,
  PerformanceMediaHealth,
  PerformanceOss,
  PerformanceSearchEnhance,
  PerformanceSeoChecker,
  SecretPath,
} from "@/generated/settings-types";

export { SECRET_PATHS };
export type {
  DomesticCommentSecurity,
  DomesticCompliance,
  DomesticLoginSecurity,
  DomesticWechat,
  FunctionAuxiliary,
  FunctionSeo,
  OptimizeAdmin,
  OptimizeMedium,
  OptimizeSite,
  Option,
  PageComment,
  PageFeature,
  PageFunction,
  PageJurisdiction,
  PerformanceDbClean,
  PerformanceMediaHealth,
  PerformanceOss,
  PerformanceSearchEnhance,
  PerformanceSeoChecker,
  SecretPath,
};

//准备对象类型

//准备类型
export type DataLocal = {
  url_site: string;
  ajaxurl?: string;
  nonce?: string;
  apiBase?: string;
  restNonce?: string;
  webpSupported?: boolean;
};

export interface SecretStatusEntry {
  configured: boolean;
}

export type SecretStatus = Record<SecretPath, SecretStatusEntry>;

export type SecretChange =
  | { operation: "replace"; value: string }
  | { operation: "clear" };

export type SecretChanges = Partial<Record<SecretPath, SecretChange>>;

export interface SettingsResponse {
  success: boolean;
  data: Option;
  secretStatus: SecretStatus;
}

export interface SettingsSavePayload {
  settings: Option;
  secretChanges: SecretChanges;
}

/**
 * Axios 返回类型
 */
export interface axiosType {
  success: boolean; //状态
  data: {
    data?: any; //返回值
    message?: string; //成功信息
    error?: string; //失败信息
  };
}

/**
 * 诊断相关类型
 * @since 2.5.0
 */
export interface DiagnosticItem {
  id: string;
  title: string;
  status: "good" | "warning" | "critical";
  message: string;
}

export interface DiagnosticModuleRisk {
  module_id: string;
  tier: "high_risk" | "experimental";
  title: string;
  message: string;
}

export interface ConfigDiffItem {
  path: string;
  label: string;
  module: string;
  before: any;
  after: any;
  riskLevel: "none" | "low" | "high";
}

export interface DiagnosticSummary {
  status: "good" | "warning" | "critical";
  items: DiagnosticItem[];
  module_risks: DiagnosticModuleRisk[];
  generated_at: string;
}

export interface RuntimeFeatureModule {
  id: string;
  label: string;
  category: string;
  category_label: string;
  view: "site" | "content" | "seo" | "china" | "maintenance" | "";
  target_id: string;
  scope: "frontend" | "admin" | "both";
  tier: "core" | "advanced" | "high_risk" | "experimental";
  always_loaded: boolean;
}

export interface RuntimeEditorTool {
  id: string;
  type: "pattern" | "block";
  title: string;
  description: string;
}

export interface RuntimeFeatureStatus {
  plugin: {
    name: string;
    version: string;
  };
  environment: {
    wordpress_version: string;
    php_version: string;
  };
  counts: {
    registered: number;
    active: number;
    always_loaded: number;
    editor_tools: number;
  };
  modules: RuntimeFeatureModule[];
  editor_tools: RuntimeEditorTool[];
  diagnostics: DiagnosticSummary;
  generated_at: string;
}

export interface SearchHealthTerm {
  term: string;
  count: number;
  no_result_count: number;
}

export interface SearchHealthSuspicious {
  term: string;
  count: number;
  reason: string;
}

export interface SearchHealthRecommendation {
  id: string;
  title: string;
  reason: string;
}

export interface SearchHealthSummary {
  range_days: number;
  total_searches: number;
  unique_terms: number;
  top_terms: SearchHealthTerm[];
  no_result_terms: SearchHealthTerm[];
  suspicious_terms: SearchHealthSuspicious[];
  recommendations: SearchHealthRecommendation[];
}

export interface RiskInfo {
  level: "none" | "low" | "high";
  title?: string;
  warning?: string;
  suggestion?: string;
  noDismiss?: boolean;
}

export interface UiSchemaEntry {
  path: string;
  type: string;
  label?: string;
  group?: string;
  feature_id?: string;
  risk?: RiskInfo;
  depends_on?: string | string[];
  preset_tags?: string[];
  risk_tags?: string[];
}

export type UiSchemaMap = Record<string, UiSchemaEntry>;
