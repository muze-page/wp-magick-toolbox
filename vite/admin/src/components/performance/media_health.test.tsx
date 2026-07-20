import { cleanup, fireEvent, render, screen, waitFor } from "@testing-library/react";
import { afterEach, beforeEach, describe, expect, it, vi } from "vitest";

import MediaHealth from "@/components/performance/media_health";
import { DataContext, emptySecretStatus } from "@/tool/dataContext";
import { defaultVarOption } from "@/tool/defaultVar";

const apiMocks = vi.hoisted(() => ({
  checkMedia: vi.fn(),
  fixMediaAlt: vi.fn(),
}));

vi.mock("@/api", () => ({
  performanceApi: apiMocks,
}));

function renderMediaHealth() {
  render(
    <DataContext.Provider
      value={{
        optionData: defaultVarOption,
        updateOption: vi.fn(),
        refreshOption: vi.fn(),
        lastSavedOption: defaultVarOption,
        setLastSavedOption: vi.fn(),
        secretStatus: emptySecretStatus(),
        secretChanges: {},
        setSecretChange: vi.fn(),
        clearSecretChanges: vi.fn(),
        settingsState: "ready",
        settingsError: null,
      }}
    >
      <MediaHealth />
    </DataContext.Provider>,
  );
}

const getComputedStyle = window.getComputedStyle.bind(window);

beforeEach(() => {
  vi.spyOn(window, "getComputedStyle").mockImplementation((element) => getComputedStyle(element));
  apiMocks.checkMedia.mockReset().mockResolvedValue({
    success: true,
    data: {
      issues: [{ type: "缺少 Alt", severity: "warning", count: 4 }],
    },
  });
  apiMocks.fixMediaAlt.mockReset().mockResolvedValue({ success: true, data: { fixed: 4 } });
});

afterEach(() => {
  cleanup();
  vi.restoreAllMocks();
});

describe("媒体库体检操作反馈", () => {
  it("在体检区保留检查摘要和问题列表", async () => {
    renderMediaHealth();
    fireEvent.click(screen.getByRole("button", { name: "开始体检" }));

    expect(await screen.findByRole("status")).toHaveTextContent("体检完成：发现 1 类问题。");
    expect(screen.getByText("4 个")).toBeInTheDocument();
  });

  it("在体检区保留修复数量，并显示请求失败", async () => {
    renderMediaHealth();
    fireEvent.click(screen.getByRole("button", { name: "批量补全 Alt" }));
    expect(await screen.findByRole("status")).toHaveTextContent("已补全 4 张图片的 Alt。");

    apiMocks.checkMedia.mockRejectedValueOnce(new Error("network unavailable"));
    fireEvent.click(screen.getByRole("button", { name: "开始体检" }));
    await waitFor(() => {
      expect(screen.getByRole("alert")).toHaveTextContent("体检失败，请重试。");
    });
  });
});
