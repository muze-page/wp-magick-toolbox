export const ADMIN_VIEWS = [
  "overview",
  "site",
  "content",
  "seo",
  "china",
  "maintenance",
  "about",
] as const;

export type AdminView = (typeof ADMIN_VIEWS)[number];

export const DEFAULT_ADMIN_VIEW: AdminView = "overview";

export const TARGETABLE_ADMIN_VIEWS: readonly AdminView[] = [
  "site",
  "content",
  "seo",
  "china",
  "maintenance",
];

export function isAdminView(value: string | null | undefined): value is AdminView {
  return ADMIN_VIEWS.includes(value as AdminView);
}

export function normalizeAdminView(value: string | null | undefined): AdminView {
  return isAdminView(value) ? value : DEFAULT_ADMIN_VIEW;
}

export function adminViewSupportsTargetItem(view: AdminView): boolean {
  return TARGETABLE_ADMIN_VIEWS.includes(view);
}

export function getAdminViewFromSearch(search: string): AdminView {
  return normalizeAdminView(new URLSearchParams(search).get("view"));
}

export function getTargetItemFromSearch(search: string): string | null {
  const target = new URLSearchParams(search).get("target");
  if (!target || !/^[A-Za-z0-9][A-Za-z0-9_-]{0,159}$/.test(target)) return null;
  return target;
}

export function getSettingsTabFromSearch(
  search: string,
  scope: string,
  allowedTabs: readonly string[],
): string | undefined {
  const requestedValue = new URLSearchParams(search).get("tab");
  const scopePrefix = `${scope}.`;
  if (!requestedValue?.startsWith(scopePrefix)) return undefined;

  const requestedTab = requestedValue.slice(scopePrefix.length);
  return allowedTabs.includes(requestedTab) ? requestedTab : undefined;
}

export function createAdminViewUrl(currentUrl: string, view: AdminView): string {
  const url = new URL(currentUrl);
  url.searchParams.set("view", view);
  url.searchParams.delete("target");
  return `${url.pathname}${url.search}${url.hash}`;
}

export function createAdminTargetUrl(
  currentUrl: string,
  view: AdminView,
  targetItemId: string,
): string {
  const url = new URL(currentUrl);
  url.searchParams.set("view", view);
  url.searchParams.set("target", targetItemId);
  url.searchParams.delete("tab");
  return `${url.pathname}${url.search}${url.hash}`;
}

export function createSettingsTabUrl(currentUrl: string, scope: string, tab: string): string {
  const url = new URL(currentUrl);
  url.searchParams.set("tab", `${scope}.${tab}`);
  return `${url.pathname}${url.search}${url.hash}`;
}

export function writeAdminViewToHistory(
  view: AdminView,
  mode: "push" | "replace" = "push",
): void {
  const nextUrl = createAdminViewUrl(window.location.href, view);
  if (mode === "replace") {
    window.history.replaceState({ view }, "", nextUrl);
    return;
  }
  window.history.pushState({ view }, "", nextUrl);
}

export function writeAdminTargetToHistory(view: AdminView, targetItemId: string): void {
  const nextUrl = createAdminTargetUrl(window.location.href, view, targetItemId);
  window.history.pushState({ view, target: targetItemId }, "", nextUrl);
}

export function writeSettingsTabToHistory(scope: string, tab: string): void {
  const scopedTab = `${scope}.${tab}`;
  const nextUrl = createSettingsTabUrl(window.location.href, scope, tab);
  window.history.replaceState({ ...window.history.state, tab: scopedTab }, "", nextUrl);
}
