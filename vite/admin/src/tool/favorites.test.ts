import { beforeEach, describe, expect, it, vi } from "vitest";

import {
  FAVORITES_CHANGED_EVENT,
  FAVORITES_STORAGE_KEY,
  addFavorite,
  getFavorites,
  removeFavorite,
  reorderFavorites,
} from "@/tool/favorites";

vi.mock("@/tool/notice", () => ({
  notice: {
    success: vi.fn(),
  },
}));

beforeEach(() => {
  localStorage.clear();
  vi.clearAllMocks();
});

describe("常用功能存储", () => {
  it("忽略损坏、重复和非字符串收藏", () => {
    const consoleError = vi.spyOn(console, "error").mockImplementation(() => undefined);
    localStorage.setItem(FAVORITES_STORAGE_KEY, "not-json");
    expect(getFavorites()).toEqual([]);

    localStorage.setItem(
      FAVORITES_STORAGE_KEY,
      JSON.stringify(["feature-a", "feature-a", 123, "", "feature-b"]),
    );
    expect(getFavorites()).toEqual(["feature-a", "feature-b"]);
    expect(consoleError).toHaveBeenCalledOnce();
    consoleError.mockRestore();
  });

  it("增删和排序后通知当前页面更新", () => {
    const listener = vi.fn();
    window.addEventListener(FAVORITES_CHANGED_EVENT, listener);

    expect(addFavorite("feature-a")).toBe(true);
    expect(addFavorite("feature-b")).toBe(true);
    expect(reorderFavorites(["feature-b", "feature-a", "feature-a"])).toBe(true);
    expect(removeFavorite("feature-b")).toBe(true);

    expect(getFavorites()).toEqual(["feature-a"]);
    expect(listener).toHaveBeenCalledTimes(4);
    window.removeEventListener(FAVORITES_CHANGED_EVENT, listener);
  });
});
