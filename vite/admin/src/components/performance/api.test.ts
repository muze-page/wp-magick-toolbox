import { beforeEach, describe, expect, it, vi } from "vitest";

import {
  diagnosticsApi,
  domesticApi,
  performanceApi,
  searchHealthApi,
  settingsApi,
} from "@/api";
import { defaultVarOption } from "@/tool/defaultVar";
import type { SettingsSavePayload } from "@/tool/interface";

const restMocks = vi.hoisted(() => ({
  get: vi.fn(),
  post: vi.fn(),
}));

vi.mock("@/axios/public", () => ({
  restInstance: restMocks,
}));

describe("performanceApi", () => {
  beforeEach(() => {
    restMocks.get.mockReset();
    restMocks.post.mockReset();
    restMocks.get.mockResolvedValue({ success: true, data: {} });
    restMocks.post.mockResolvedValue({ success: true, data: {} });
  });

  it("数据库预览始终显式使用 dry-run", async () => {
    await performanceApi.previewDb("revisions");

    expect(restMocks.post).toHaveBeenCalledWith("/performance/db/preview", {
      type: "revisions",
      dry_run: true,
    }, { maboxNotify: false });
  });

  it("数据库清理默认 dry-run，只有显式传 false 才执行", async () => {
    await performanceApi.cleanDb("spam");
    await performanceApi.cleanDb("spam", false);

    expect(restMocks.post).toHaveBeenNthCalledWith(1, "/performance/db/clean", {
      type: "spam",
      dry_run: true,
    }, { maboxNotify: false });
    expect(restMocks.post).toHaveBeenNthCalledWith(2, "/performance/db/clean", {
      type: "spam",
      dry_run: false,
    }, { maboxNotify: false });
  });

  it("对象存储连接测试关闭全局通知并使用设置凭据契约", async () => {
    const payload: SettingsSavePayload = {
      settings: defaultVarOption,
      secretChanges: {
        "performance.oss.access_key": { operation: "replace", value: "access-key" },
      },
    };

    await performanceApi.testOssConnection(payload);

    expect(restMocks.post).toHaveBeenCalledWith(
      "/performance/oss/test",
      payload,
      { maboxNotify: false },
    );
  });

  it.each([
    ["checkMedia", "/performance/media/check"],
    ["fixMediaAlt", "/performance/media/fix-alt"],
    ["checkSeo", "/performance/seo/check"],
    ["fixSeoAlt", "/performance/seo/fix-alt"],
  ] as const)("%s 由调用界面独占反馈", async (method, path) => {
    await performanceApi[method]();

    expect(restMocks.post).toHaveBeenCalledWith(
      path,
      { post_id: undefined },
      { maboxNotify: false },
    );
  });

  it("数据库统计由调用界面独占反馈", async () => {
    await performanceApi.getDbStats();

    expect(restMocks.get).toHaveBeenCalledWith(
      "/performance/db/stats",
      { maboxNotify: false },
    );
  });

  it("已有局部状态的查询和建议接口关闭传输层通知", async () => {
    await domesticApi.checkEnvironment();
    await domesticApi.applyEnvironmentFix(["gravatar"]);
    await diagnosticsApi.getSummary();
    await searchHealthApi.getSummary(30);
    await settingsApi.getSchema();

    expect(restMocks.get).toHaveBeenCalledWith(
      "/domestic/environment/check",
      { maboxNotify: false },
    );
    expect(restMocks.post).toHaveBeenCalledWith(
      "/domestic/environment/apply",
      { fixes: ["gravatar"] },
      { maboxNotify: false },
    );
    expect(restMocks.get).toHaveBeenCalledWith(
      "/diagnostics/summary",
      { maboxNotify: false },
    );
    expect(restMocks.get).toHaveBeenCalledWith(
      "/search-health/summary?days=30",
      { maboxNotify: false },
    );
    expect(restMocks.get).toHaveBeenCalledWith(
      "/settings/schema",
      { maboxNotify: false },
    );
  });
});
