import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import { render, screen, fireEvent, waitFor, cleanup } from "@testing-library/react";
import type { DiagnosticFixSuggestion, DiagnosticFixChange, DiagnosticSummary } from "@/tool/interface";
import Dashboard from "@/components/dashboard";
import { DataContext } from "@/tool/dataContext";
import { defaultVarOption } from "@/tool/defaultVar";

const apiMocks = vi.hoisted(() => ({
  getDiagnosticsSummary: vi.fn(),
  getSearchSummary: vi.fn(),
  getSettings: vi.fn(),
}));

vi.mock("@/api", () => ({
  diagnosticsApi: {
    getSummary: apiMocks.getDiagnosticsSummary,
  },
  searchHealthApi: {
    getSummary: apiMocks.getSearchSummary,
  },
  settingsApi: {
    get: apiMocks.getSettings,
  },
}));

vi.mock("@/components/favorites-panel", () => ({
  default: () => <div data-testid="favorites-panel" />,
}));

vi.mock("@/components/wizard", () => ({
  default: () => <div data-testid="wizard-modal" />,
}));

vi.mock("@/tool/presets", async () => {
  const actual = await vi.importActual<typeof import("@/tool/presets")>("@/tool/presets");
  return {
    ...actual,
    getAllPresets: () => [],
    saveCustomPreset: () => true,
    deleteCustomPreset: () => true,
  };
});

vi.mock("@/tool/snapshot", async () => {
  const actual = await vi.importActual<typeof import("@/tool/snapshot")>("@/tool/snapshot");
  return {
    ...actual,
    getSnapshots: () => [],
    getDefaultConfig: () => ({}),
  };
});

const emptySearchHealth = {
  range_days: 30,
  total_searches: 0,
  unique_terms: 0,
  top_terms: [],
  no_result_terms: [],
  suspicious_terms: [],
  recommendations: [],
};

beforeEach(() => {
  apiMocks.getDiagnosticsSummary.mockReset();
  apiMocks.getSearchSummary.mockReset();
  apiMocks.getSettings.mockReset();

  apiMocks.getSearchSummary.mockResolvedValue({ success: true, data: emptySearchHealth });
  apiMocks.getSettings.mockResolvedValue({});

  Object.defineProperty(window, "matchMedia", {
    writable: true,
    value: vi.fn().mockImplementation((query) => ({
      matches: false,
      media: query,
      onchange: null,
      addListener: vi.fn(),
      removeListener: vi.fn(),
      addEventListener: vi.fn(),
      removeEventListener: vi.fn(),
      dispatchEvent: vi.fn(),
    })),
  });

  class ResizeObserverMock {
    observe() {}
    unobserve() {}
    disconnect() {}
  }
  Object.defineProperty(window, "ResizeObserver", {
    writable: true,
    value: ResizeObserverMock,
  });

  const originalGetComputedStyle = window.getComputedStyle;
  Object.defineProperty(window, "getComputedStyle", {
    writable: true,
    value: (element: Element) => originalGetComputedStyle(element),
  });
});

afterEach(() => {
  cleanup();
  vi.clearAllMocks();
});

describe("DiagnosticFixSuggestion 类型结构", () => {
  it(" DiagnosticFixChange 包含必要字段", () => {
    const change: DiagnosticFixChange = {
      path: "optimize.site.remove_RSS_version",
      label: "移除 WP 版本号",
      before: false,
      after: true,
      risk_level: "low",
    };
    expect(change.path).toBe("optimize.site.remove_RSS_version");
    expect(change.before).toBe(false);
    expect(change.after).toBe(true);
    expect(change.risk_level).toBe("low");
  });

  it("DiagnosticFixSuggestion 包含必要字段", () => {
    const suggestion: DiagnosticFixSuggestion = {
      id: "fix_remove_wp_version",
      title: "移除 WP 版本号",
      reason: "减少信息泄露，提升安全性。",
      severity: "low",
      module: "optimize",
      requires_confirmation: false,
      changes: [
        {
          path: "optimize.site.remove_RSS_version",
          label: "移除 WP 版本号",
          before: false,
          after: true,
          risk_level: "low",
        },
      ],
    };
    expect(suggestion.id).toBe("fix_remove_wp_version");
    expect(suggestion.changes).toHaveLength(1);
    expect(suggestion.requires_confirmation).toBe(false);
  });

  it("DiagnosticSummary 包含新增字段", () => {
    const summary: DiagnosticSummary = {
      score: 60,
      status: "warning",
      items: [],
      recommendations: [],
      risks: [],
      service_hints: [],
      generated_at: "2026-05-29 10:00:00",
      environment: {
        php_version: "8.1",
        wp_version: "6.4",
        plugin_version: "2.5.0",
        permalink: "/%postname%/",
        object_cache: false,
        rest_api_available: true,
        site_url: "https://example.com",
      },
      fix_suggestions: [
        {
          id: "fix_remove_wp_version",
          title: "移除 WP 版本号",
          reason: "减少信息泄露",
          severity: "low",
          module: "optimize",
          requires_confirmation: false,
          changes: [
            {
              path: "optimize.site.remove_RSS_version",
              label: "移除 WP 版本号",
              before: false,
              after: true,
              risk_level: "low",
            },
          ],
        },
      ],
    };
    expect(summary.generated_at).toBe("2026-05-29 10:00:00");
    expect(summary.environment?.php_version).toBe("8.1");
    expect(summary.fix_suggestions).toHaveLength(1);
  });
});

describe("修复建议变更路径解析", () => {
  it("三级路径正确拆分为 father/son/fieldKey", () => {
    const path = "optimize.site.remove_RSS_version";
    const parts = path.split(".");
    expect(parts).toHaveLength(3);
    expect(parts[0]).toBe("optimize");
    expect(parts[1]).toBe("site");
    expect(parts.slice(2).join(".")).toBe("remove_RSS_version");
  });

  it("二级路径不满足三级路径条件", () => {
    const path = "optimize.remove_RSS_version";
    const parts = path.split(".");
    expect(parts.length).toBeLessThan(3);
  });

  it("变更合并到 optionData 后字段正确覆盖", () => {
    const optionData: Record<string, any> = {
      optimize: {
        site: {
          remove_RSS_version: false,
          hide_top_toolbar: false,
        },
      },
    };

    const changes: DiagnosticFixChange[] = [
      {
        path: "optimize.site.remove_RSS_version",
        label: "移除 WP 版本号",
        before: false,
        after: true,
        risk_level: "low",
      },
    ];

    const updates: Record<string, Record<string, any>> = {};
    changes.forEach((change) => {
      const parts = change.path.split(".");
      const father = parts[0];
      const son = parts[1];
      const fieldKey = parts.slice(2).join(".");
      if (!updates[father]) updates[father] = {};
      if (!updates[father][son]) updates[father][son] = { ...(optionData[father]?.[son] || {}) };
      updates[father][son][fieldKey] = change.after;
    });

    expect(updates.optimize.site.remove_RSS_version).toBe(true);
    expect(updates.optimize.site.hide_top_toolbar).toBe(false);
  });

  it("多个变更合并后保留其他字段", () => {
    const optionData: Record<string, any> = {
      optimize: {
        site: {
          remove_RSS_version: false,
          hide_top_toolbar: false,
          cdn_gravatar: false,
        },
      },
    };

    const changes: DiagnosticFixChange[] = [
      {
        path: "optimize.site.remove_RSS_version",
        label: "移除 WP 版本号",
        before: false,
        after: true,
        risk_level: "low",
      },
      {
        path: "optimize.site.hide_top_toolbar",
        label: "隐藏顶部工具条",
        before: false,
        after: true,
        risk_level: "low",
      },
    ];

    const updates: Record<string, Record<string, any>> = {};
    changes.forEach((change) => {
      const parts = change.path.split(".");
      const father = parts[0];
      const son = parts[1];
      const fieldKey = parts.slice(2).join(".");
      if (!updates[father]) updates[father] = {};
      if (!updates[father][son]) updates[father][son] = { ...(optionData[father]?.[son] || {}) };
      updates[father][son][fieldKey] = change.after;
    });

    expect(updates.optimize.site.remove_RSS_version).toBe(true);
    expect(updates.optimize.site.hide_top_toolbar).toBe(true);
    expect(updates.optimize.site.cdn_gravatar).toBe(false);
  });
});

describe("高风险变更检测", () => {
  it("包含 high risk_level 的变更被检测到", () => {
    const suggestions: DiagnosticFixSuggestion[] = [
      {
        id: "fix_low_risk",
        title: "低风险修复",
        reason: "低风险",
        severity: "low",
        module: "optimize",
        requires_confirmation: false,
        changes: [
          { path: "optimize.site.remove_RSS_version", label: "低风险", before: false, after: true, risk_level: "low" },
        ],
      },
      {
        id: "fix_high_risk",
        title: "高风险修复",
        reason: "高风险",
        severity: "high",
        module: "optimize",
        requires_confirmation: true,
        changes: [
          { path: "optimize.medium.no_auto_size", label: "高风险", before: false, after: true, risk_level: "high" },
        ],
      },
    ];

    const hasHighRisk = suggestions.some((f) =>
      f.changes.some((c) => c.risk_level === "high")
    );
    expect(hasHighRisk).toBe(true);
  });

  it("仅包含 low risk_level 的变更不被检测为高风险", () => {
    const suggestions: DiagnosticFixSuggestion[] = [
      {
        id: "fix_low_risk",
        title: "低风险修复",
        reason: "低风险",
        severity: "low",
        module: "optimize",
        requires_confirmation: false,
        changes: [
          { path: "optimize.site.remove_RSS_version", label: "低风险", before: false, after: true, risk_level: "low" },
        ],
      },
    ];

    const hasHighRisk = suggestions.some((f) =>
      f.changes.some((c) => c.risk_level === "high")
    );
    expect(hasHighRisk).toBe(false);
  });
});

describe("Dashboard 一键优化交互", () => {
  it("应用登录验证码修复建议时写入 math 模式到当前配置", async () => {
    apiMocks.getDiagnosticsSummary.mockResolvedValue({
      success: true,
      data: {
        score: 70,
        status: "warning",
        items: [],
        recommendations: [],
        risks: [],
        service_hints: [],
        generated_at: "2026-05-29 10:00:00",
        environment: {
          php_version: "8.1",
          wp_version: "6.4",
          plugin_version: "2.6.1",
          permalink: "/%postname%/",
          object_cache: false,
          rest_api_available: true,
          site_url: "https://example.com",
        },
        fix_suggestions: [
          {
            id: "fix_login_code",
            title: "登录验证码",
            reason: "防御暴力破解登录后台。",
            severity: "low",
            module: "login",
            requires_confirmation: false,
            changes: [
              {
                path: "login.security.login_code",
                label: "登录验证码",
                before: "false",
                after: "math",
                risk_level: "low",
              },
            ],
          },
        ],
      },
    });

    const updateOption = vi.fn();
    render(
      <DataContext.Provider
        value={{
          optionData: defaultVarOption,
          updateOption,
          refreshOption: vi.fn(),
          lastSavedOption: defaultVarOption,
          setLastSavedOption: vi.fn(),
        }}
      >
        <Dashboard />
      </DataContext.Provider>
    );

    const openButton = await screen.findByRole("button", { name: /可一键优化 1 项/ });
    fireEvent.click(openButton);

    expect(await screen.findByText("一键优化预览")).toBeInTheDocument();
    expect(screen.getByText(/"false"/)).toBeInTheDocument();
    expect(screen.getByText(/"math"/)).toBeInTheDocument();

    fireEvent.click(screen.getByRole("button", { name: /应用选中项/ }));

    await waitFor(() => {
      expect(updateOption).toHaveBeenCalledWith(
        "login",
        "security",
        expect.objectContaining({ login_code: "math" })
      );
    });
  });
});
