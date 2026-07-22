import { fireEvent, render, screen, waitFor } from "@testing-library/react";
import { beforeEach, describe, expect, it, vi } from "vitest";

import type { RuntimeFeatureStatus } from "@/tool/interface";
import RuntimeStatus from "./runtime-status";
import { buildSupportReport } from "./runtime-status-report";

const apiMocks = vi.hoisted(() => ({
  getFeatureStatus: vi.fn(),
}));

vi.mock("@/api", () => ({
  diagnosticsApi: apiMocks,
}));

const featureStatus: RuntimeFeatureStatus = {
  plugin: {
    name: "Npcink Site Toolbox",
    version: "3.2.0",
  },
  environment: {
    wordpress_version: "7.0.2",
    php_version: "8.4.21",
  },
  counts: {
    registered: 55,
    active: 2,
    always_loaded: 1,
    editor_tools: 2,
  },
  modules: [
    {
      id: "optimize.widgets",
      label: "站点小工具",
      category: "optimize",
      category_label: "站点与媒体",
      view: "site",
      target_id: "",
      scope: "both",
      tier: "core",
      always_loaded: true,
    },
    {
      id: "performance.oss",
      label: "对象存储 / OSS",
      category: "performance",
      category_label: "存储与维护",
      view: "maintenance",
      target_id: "performance-oss-enabled",
      scope: "both",
      tier: "core",
      always_loaded: false,
    },
  ],
  editor_tools: [
    {
      id: "npcink-site-toolbox/resource-download-card",
      type: "pattern",
      title: "资源下载卡片",
      description: "展示下载入口。",
    },
    {
      id: "npcink/github-project",
      type: "block",
      title: "GitHub 项目",
      description: "展示公开仓库资料。",
    },
  ],
  diagnostics: {
    status: "good",
    items: [
      {
        id: "php_version",
        title: "PHP 版本",
        status: "good",
        message: "当前 PHP 版本满足最低要求。",
      },
    ],
    module_risks: [],
    generated_at: "2026-07-21 12:00:00",
  },
  generated_at: "2026-07-21 12:00:00",
};

describe("RuntimeStatus", () => {
  beforeEach(() => {
    apiMocks.getFeatureStatus.mockReset();
    apiMocks.getFeatureStatus.mockResolvedValue({ success: true, data: featureStatus });
    window.history.replaceState(
      {},
      "",
      "/wp-admin/admin.php?page=npcink-site-toolbox&view=about",
    );
  });

  it("shows factual runtime modules and editor tools", async () => {
    const onNavigate = vi.fn();
    render(<RuntimeStatus onNavigate={onNavigate} />);

    expect(await screen.findByRole("heading", { name: "功能与运行状态" })).toBeInTheDocument();
    expect(screen.getByText("2 / 55")).toBeInTheDocument();
    expect(screen.getByText("站点小工具")).toBeInTheDocument();
    expect(screen.getByText("对象存储 / OSS")).toBeInTheDocument();
    expect(screen.getByText("资源下载卡片")).toBeInTheDocument();
    expect(screen.getByText("GitHub 项目")).toBeInTheDocument();
    expect(screen.queryByText("optimize.widgets")).not.toBeInTheDocument();
    expect(screen.queryByText("performance.oss")).not.toBeInTheDocument();
    expect(screen.queryByText("已启用")).not.toBeInTheDocument();
    expect(screen.queryByText("稳定")).not.toBeInTheDocument();
    expect(screen.getAllByText("无需开关")).toHaveLength(2);

    const settingsLink = screen.getByRole("link", { name: "前往对象存储 / OSS设置" });
    expect(settingsLink).toHaveAttribute(
      "href",
      "/wp-admin/admin.php?page=npcink-site-toolbox&view=maintenance&target=performance-oss-enabled",
    );
    fireEvent.click(settingsLink);
    expect(onNavigate).toHaveBeenCalledWith("maintenance", "performance-oss-enabled");
  });

  it("copies a support report without settings or secrets", async () => {
    const writeText = vi.fn().mockResolvedValue(undefined);
    Object.defineProperty(navigator, "clipboard", {
      configurable: true,
      value: { writeText },
    });
    render(<RuntimeStatus />);

    fireEvent.click(await screen.findByRole("button", { name: "复制诊断信息" }));

    await waitFor(() => expect(writeText).toHaveBeenCalledTimes(1));
    const report = writeText.mock.calls[0][0] as string;
    expect(report).toContain("Npcink Site Toolbox 3.2.0");
    expect(report).toContain("performance.oss");
    expect(report).not.toContain("access_key");
    expect(report).not.toContain("secret_key");
    expect(screen.getByRole("status")).toHaveTextContent("已复制脱敏诊断信息");
  });

  it("falls back to document copy on Local HTTP sites", async () => {
    const writeText = vi.fn().mockRejectedValue(new DOMException("NotAllowedError"));
    const execCommand = vi.fn().mockReturnValue(true);
    Object.defineProperty(navigator, "clipboard", {
      configurable: true,
      value: { writeText },
    });
    Object.defineProperty(document, "execCommand", {
      configurable: true,
      value: execCommand,
    });
    render(<RuntimeStatus />);

    fireEvent.click(await screen.findByRole("button", { name: "复制诊断信息" }));

    await waitFor(() => expect(execCommand).toHaveBeenCalledWith("copy"));
    expect(document.querySelector("textarea")).not.toBeInTheDocument();
    expect(screen.getByRole("status")).toHaveTextContent("已复制脱敏诊断信息");
  });

  it("keeps invalid responses out of the working surface and allows retry", async () => {
    apiMocks.getFeatureStatus
      .mockResolvedValueOnce({ success: true, data: { plugin: null } })
      .mockResolvedValueOnce({ success: true, data: featureStatus });
    render(<RuntimeStatus />);

    expect(await screen.findByRole("alert")).toHaveTextContent("运行状态暂时不可用");
    fireEvent.click(screen.getByRole("button", { name: "重新获取" }));

    expect(await screen.findByText("站点小工具")).toBeInTheDocument();
    expect(apiMocks.getFeatureStatus).toHaveBeenCalledTimes(2);
  });
});

describe("buildSupportReport", () => {
  it("describes enablement without claiming external services passed", () => {
    const report = buildSupportReport(featureStatus);

    expect(report).toContain("对象存储 / OSS [performance.oss] | 已启用");
    expect(report).not.toContain("连接成功");
    expect(report).not.toContain("运行正常");
  });
});
