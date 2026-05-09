import { message } from "antd";

const FAVORITES_KEY = "mabox_favorites";

export function getFavorites(): string[] {
  try {
    const stored = localStorage.getItem(FAVORITES_KEY);
    if (stored) {
      return JSON.parse(stored);
    }
  } catch (e) {
    console.error("读取收藏失败", e);
  }
  return [];
}

export function addFavorite(featureId: string): boolean {
  try {
    const favorites = getFavorites();
    if (!favorites.includes(featureId)) {
      favorites.push(featureId);
      localStorage.setItem(FAVORITES_KEY, JSON.stringify(favorites));
      message.success("已添加到常用功能");
      return true;
    }
    return false;
  } catch (e) {
    console.error("添加收藏失败", e);
    return false;
  }
}

export function removeFavorite(featureId: string): boolean {
  try {
    const favorites = getFavorites().filter((id) => id !== featureId);
    localStorage.setItem(FAVORITES_KEY, JSON.stringify(favorites));
    message.success("已从常用功能移除");
    return true;
  } catch (e) {
    console.error("移除收藏失败", e);
    return false;
  }
}

export function isFavorite(featureId: string): boolean {
  return getFavorites().includes(featureId);
}

export function toggleFavorite(featureId: string): boolean {
  if (isFavorite(featureId)) {
    removeFavorite(featureId);
    return false;
  } else {
    addFavorite(featureId);
    return true;
  }
}

export function reorderFavorites(newOrder: string[]): boolean {
  try {
    localStorage.setItem(FAVORITES_KEY, JSON.stringify(newOrder));
    return true;
  } catch (e) {
    console.error("重排收藏失败", e);
    return false;
  }
}
