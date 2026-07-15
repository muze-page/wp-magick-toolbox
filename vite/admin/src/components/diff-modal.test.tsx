import { cleanup, fireEvent, render, screen } from "@testing-library/react";
import { afterEach, beforeEach, describe, expect, it, vi } from "vitest";

import DiffModal from "@/components/diff-modal";
import { ConfigDiffItem } from "@/tool/interface";

afterEach(() => {
  cleanup();
  vi.restoreAllMocks();
});
const getComputedStyle = window.getComputedStyle.bind(window);

beforeEach(() => {
  vi.spyOn(window, "getComputedStyle").mockImplementation((element) => getComputedStyle(element));
});

function renderDiffModal(diffs: ConfigDiffItem[]) {
  const onConfirm = vi.fn();
  const onCancel = vi.fn();
  const result = render(
    <DiffModal visible diffs={diffs} onConfirm={onConfirm} onCancel={onCancel} />,
  );
  return { ...result, onConfirm, onCancel };
}

describe("DiffModal", () => {
  it("普通变更只显示用户标签和值，不暴露内部 path 且使用中性结果样式", () => {
    renderDiffModal([{
      path: "internal.private.setting_path",
      label: "列表显示数量",
      module: "optimize",
      before: 10,
      after: 20,
      riskLevel: "none",
    }]);

    expect(screen.getByText("列表显示数量")).toBeInTheDocument();
    expect(screen.getByText("10")).toHaveClass("mabox-diff-value--before");
    const afterValue = screen.getByText("20");
    expect(afterValue).toHaveClass("mabox-diff-value--after");
    expect(afterValue).not.toHaveClass("mabox-diff-value--high-risk");
    expect(screen.queryByText("internal.private.setting_path")).not.toBeInTheDocument();
    expect(document.querySelector(".mabox-diff-values")).toBeInTheDocument();
    expect(screen.getByText("原值：")).toHaveClass("mabox-visually-hidden");
    expect(screen.getByText("新值：")).toHaveClass("mabox-visually-hidden");
  });

  it("高风险开启保留显式警告、危险确认与高风险值样式", () => {
    const { onConfirm } = renderDiffModal([{
      path: "optimize.medium.no_auto_size",
      label: "禁止缩略图",
      module: "optimize",
      before: false,
      after: true,
      riskLevel: "high",
    }]);

    expect(screen.getByText("检测到 1 项高风险功能将被开启，请谨慎确认。")).toBeInTheDocument();
    expect(screen.getByText("高风险")).toBeInTheDocument();
    expect(screen.getByText("开启")).toHaveClass("mabox-diff-value--high-risk");

    fireEvent.click(screen.getByRole("button", { name: "确认保存" }));
    expect(onConfirm).toHaveBeenCalledTimes(1);
  });
});
