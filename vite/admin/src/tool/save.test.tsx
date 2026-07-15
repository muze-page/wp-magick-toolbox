import { act, cleanup, fireEvent, render, screen, waitFor } from "@testing-library/react";
import { message } from "antd";
import { afterEach, beforeEach, describe, expect, it, vi } from "vitest";

import {
  DataContext,
  emptySecretStatus,
  OptionContextType,
} from "@/tool/dataContext";
import { defaultVarOption } from "@/tool/defaultVar";
import { Option } from "@/tool/interface";
import Save from "@/tool/save";

const saveMocks = vi.hoisted(() => ({
  saveOption: vi.fn(),
}));
const getComputedStyle = window.getComputedStyle.bind(window);

vi.mock("@/axios/save", () => saveMocks);

function cloneOption(): Option {
  return JSON.parse(JSON.stringify(defaultVarOption)) as Option;
}

function renderSave(overrides: Partial<OptionContextType> = {}) {
  const optionData = cloneOption();
  const value: OptionContextType = {
    optionData,
    updateOption: vi.fn(),
    refreshOption: vi.fn().mockResolvedValue(undefined),
    lastSavedOption: cloneOption(),
    setLastSavedOption: vi.fn(),
    secretStatus: emptySecretStatus(),
    secretChanges: {},
    setSecretChange: vi.fn(),
    clearSecretChanges: vi.fn(),
    settingsState: "ready",
    settingsError: null,
    ...overrides,
  };

  return { value, ...render(<DataContext.Provider value={value}><Save /></DataContext.Provider>) };
}

afterEach(() => {
  cleanup();
  vi.restoreAllMocks();
});

beforeEach(() => {
  vi.spyOn(window, "getComputedStyle").mockImplementation((element) => getComputedStyle(element));
  saveMocks.saveOption.mockReset();
  saveMocks.saveOption.mockResolvedValue({ success: true });
});

describe("Save", () => {
  it("读取中显示稳定状态并禁用保存", () => {
    renderSave({ settingsState: "loading" });

    expect(screen.getByRole("status")).toHaveTextContent("正在读取设置…");
    expect(screen.getByRole("button", { name: /保\s*存/ })).toBeDisabled();
  });

  it("设置读取失败时显示不可用状态并禁用保存", () => {
    renderSave({ settingsState: "error", settingsError: "network unavailable" });

    expect(screen.getByRole("status")).toHaveTextContent("设置不可用");
    expect(screen.getByRole("button", { name: /保\s*存/ })).toBeDisabled();
  });

  it("无改动时显示已保存、禁用按钮且不弹 toast", () => {
    const infoSpy = vi.spyOn(message, "info");
    renderSave();

    expect(screen.getByRole("status")).toHaveTextContent("已保存");
    const saveButton = screen.getByRole("button", { name: /保\s*存/ });
    expect(saveButton).toBeDisabled();
    fireEvent.click(saveButton);
    expect(infoSpy).not.toHaveBeenCalled();
    expect(screen.queryByRole("dialog")).not.toBeInTheDocument();
  });

  it("用普通设置与凭据 diff 的真实总数提示待保存项", async () => {
    const optionData = cloneOption();
    optionData.domestic.login_security.attempt_limit_count += 1;
    const secretChanges = {
      "domestic.wechat.appsecret": {
        operation: "replace" as const,
        value: "replacement-secret",
      },
    };

    renderSave({ optionData, secretChanges });

    expect(screen.getByRole("status")).toHaveTextContent("2 项待保存");
    const saveButton = screen.getByRole("button", { name: "查看并保存" });
    expect(saveButton).toBeEnabled();
    fireEvent.click(saveButton);

    expect(await screen.findByText("失败尝试上限")).toBeInTheDocument();
    expect(screen.getByText("微信 AppSecret")).toBeInTheDocument();
  });

  it("确认后立即进入保存中状态，成功后清空凭据 draft 并回读", async () => {
    let resolveSave: ((value: { success: boolean }) => void) | undefined;
    saveMocks.saveOption.mockReturnValue(new Promise((resolve) => {
      resolveSave = resolve;
    }));
    const clearSecretChanges = vi.fn();
    const refreshOption = vi.fn().mockResolvedValue(undefined);
    const secretChanges = {
      "domestic.wechat.appsecret": {
        operation: "replace" as const,
        value: "replacement-secret",
      },
    };

    renderSave({ secretChanges, clearSecretChanges, refreshOption });

    fireEvent.click(screen.getByRole("button", { name: "查看并保存" }));
    fireEvent.click(await screen.findByRole("button", { name: "确认保存" }));

    expect(screen.getByRole("status")).toHaveTextContent("正在保存…");
    expect(screen.getByRole("button", { name: /正在保存/ })).toBeDisabled();

    await act(async () => {
      resolveSave?.({ success: true });
    });

    await waitFor(() => {
      expect(saveMocks.saveOption).toHaveBeenCalledWith(expect.any(Object), secretChanges);
      expect(clearSecretChanges).toHaveBeenCalledTimes(1);
      expect(refreshOption).toHaveBeenCalledTimes(1);
    });
  });

  it("设置已保存但回读失败时保留诚实警告", async () => {
    const warningSpy = vi.spyOn(message, "warning").mockImplementation(() => {
      return (() => {}) as ReturnType<typeof message.warning>;
    });
    const refreshOption = vi.fn().mockRejectedValue(new Error("read failed"));
    const secretChanges = {
      "domestic.wechat.appsecret": { operation: "clear" as const },
    };

    renderSave({ secretChanges, refreshOption });
    fireEvent.click(screen.getByRole("button", { name: "查看并保存" }));
    fireEvent.click(await screen.findByRole("button", { name: "确认保存" }));

    await waitFor(() => {
      expect(warningSpy).toHaveBeenCalledWith(
        "设置已保存，但重新读取失败；保存功能已禁用，请重新读取后继续",
      );
    });
  });

  it("写入失败时保留待保存内容并显示失败反馈", async () => {
    saveMocks.saveOption.mockRejectedValue(new Error("write failed"));
    const errorSpy = vi.spyOn(message, "error").mockImplementation(() => {
      return (() => {}) as ReturnType<typeof message.error>;
    });
    const clearSecretChanges = vi.fn();
    const refreshOption = vi.fn();
    const secretChanges = {
      "domestic.wechat.appsecret": { operation: "clear" as const },
    };

    renderSave({ secretChanges, clearSecretChanges, refreshOption });
    fireEvent.click(screen.getByRole("button", { name: "查看并保存" }));
    fireEvent.click(await screen.findByRole("button", { name: "确认保存" }));

    await waitFor(() => {
      expect(errorSpy).toHaveBeenCalledWith("保存失败，请重试");
    });
    expect(clearSecretChanges).not.toHaveBeenCalled();
    expect(refreshOption).not.toHaveBeenCalled();
    expect(screen.getByRole("status")).toHaveTextContent("1 项待保存");
  });
});
