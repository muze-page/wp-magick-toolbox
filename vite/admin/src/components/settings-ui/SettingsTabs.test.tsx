import { act, lazy } from "react";
import { cleanup, fireEvent, render, screen } from "@testing-library/react";
import { afterEach, describe, expect, it } from "vitest";

import SettingsTabs, { type SettingsTab } from "@/components/settings-ui/SettingsTabs";

afterEach(() => {
  cleanup();
  window.history.replaceState({}, "", "/");
});

describe("SettingsTabs", () => {
  it("保留标签导航，并在目标分组延迟加载时显示局部状态", async () => {
    let resolveTarget: ((module: { default: () => JSX.Element }) => void) | undefined;
    const LazyTarget = lazy(
      () =>
        new Promise<{ default: () => JSX.Element }>((resolve) => {
          resolveTarget = resolve;
        }),
    );
    const tabs: SettingsTab[] = [
      { key: "first", label: "第一组", prefixes: ["first-"], content: <div>第一组内容</div> },
      { key: "target", label: "目标组", prefixes: ["target-"], content: <LazyTarget /> },
    ];

    render(
      <SettingsTabs
        ariaLabel="测试分组"
        idPrefix="test-settings"
        tabs={tabs}
        targetItemId="target-setting"
      />,
    );

    expect(screen.getByRole("tab", { name: "目标组" })).toHaveAttribute("aria-selected", "true");
    expect(screen.getByRole("tablist", { name: "测试分组" })).toBeInTheDocument();
    expect(screen.getByRole("status")).toHaveTextContent("正在加载当前分组");
    expect(screen.queryByText("第一组内容")).not.toBeInTheDocument();

    await act(async () => {
      resolveTarget?.({ default: () => <div>目标组内容</div> });
    });

    expect(await screen.findByText("目标组内容")).toBeInTheDocument();
    expect(screen.getByRole("tabpanel")).toHaveAccessibleName("目标组");
    expect(new URLSearchParams(window.location.search).get("tab")).toBe("test-settings.target");
  });

  it("保存刷新导致组件重挂载后仍保留用户选择的标签", () => {
    const tabs: SettingsTab[] = [
      { key: "site", label: "站点", content: <div>站点内容</div> },
      { key: "media", label: "媒体", content: <div>媒体内容</div> },
    ];
    window.history.replaceState(
      {},
      "",
      "/wp-admin/plugins.php?page=npcink-site-toolbox&view=site",
    );

    const firstRender = render(
      <SettingsTabs ariaLabel="站点与媒体分组" idPrefix="mabox-site" tabs={tabs} />,
    );
    fireEvent.click(screen.getByRole("tab", { name: "媒体" }));

    expect(screen.getByRole("tab", { name: "媒体" })).toHaveAttribute("aria-selected", "true");
    expect(new URLSearchParams(window.location.search).get("tab")).toBe("site.media");

    firstRender.unmount();
    render(<SettingsTabs ariaLabel="站点与媒体分组" idPrefix="mabox-site" tabs={tabs} />);

    expect(screen.getByRole("tab", { name: "媒体" })).toHaveAttribute("aria-selected", "true");
    expect(screen.getByText("媒体内容")).toBeInTheDocument();
  });
});
