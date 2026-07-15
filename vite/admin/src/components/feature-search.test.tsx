import { fireEvent, render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";

import FeatureSearch from "@/components/feature-search";

vi.mock("@/tool/featureIndex", () => ({
  fetchFeatureIndex: vi.fn().mockResolvedValue([
    {
      id: "performance-db_clean-enabled",
      label: "数据库清理优化",
      tabKey: "maintenance",
      tabLabel: "维护工具",
      keywords: ["数据库", "清理"],
      tags: ["性能"],
    },
    {
      id: "domestic-login_security-anonymous_author_guard_enabled",
      label: "限制匿名作者枚举",
      tabKey: "china",
      tabLabel: "国内生态",
      keywords: ["登录", "枚举", "作者"],
      tags: ["安全"],
    },
  ]),
}));

vi.mock("@/tool/favorites", () => ({
  isFavorite: vi.fn().mockReturnValue(false),
  toggleFavorite: vi.fn(),
}));

describe("FeatureSearch", () => {
  it("通过可聚焦按钮跳转到语义化视图", async () => {
    const onNavigate = vi.fn();
    render(<FeatureSearch onNavigate={onNavigate} />);

    fireEvent.change(screen.getByRole("textbox", { name: "搜索功能或设置" }), {
      target: { value: "数据库清理" },
    });
    fireEvent.click(await screen.findByRole("button", { name: "打开数据库清理优化" }));

    expect(onNavigate).toHaveBeenCalledWith("maintenance", "performance-db_clean-enabled");
  });

  it("登录安全搜索结果使用 canonical 设置行 id", async () => {
    const onNavigate = vi.fn();
    render(<FeatureSearch onNavigate={onNavigate} />);

    fireEvent.change(screen.getByRole("textbox", { name: "搜索功能或设置" }), {
      target: { value: "作者枚举" },
    });
    fireEvent.click(await screen.findByRole("button", { name: "打开限制匿名作者枚举" }));

    expect(onNavigate).toHaveBeenCalledWith(
      "china",
      "domestic-login_security-anonymous_author_guard_enabled",
    );
  });
});
