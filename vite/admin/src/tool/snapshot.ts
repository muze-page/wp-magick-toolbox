import { Option } from "@/tool/interface";
import { defaultVarOption } from "@/tool/defaultVar";

const SNAPSHOT_KEY = "mabox_snapshots";
const MAX_SNAPSHOTS = 5;

export interface Snapshot {
  id: string;
  timestamp: number;
  dateStr: string;
  data: Option;
}

export function getSnapshots(): Snapshot[] {
  try {
    const stored = localStorage.getItem(SNAPSHOT_KEY);
    if (stored) {
      return JSON.parse(stored);
    }
  } catch (e) {
    console.error("读取快照失败", e);
  }
  return [];
}

export function createSnapshot(data: Option): Snapshot {
  const snapshots = getSnapshots();
  const now = Date.now();
  const snapshot: Snapshot = {
    id: `snap_${now}`,
    timestamp: now,
    dateStr: new Date().toLocaleString("zh-CN"),
    data: JSON.parse(JSON.stringify(data)),
  };

  snapshots.unshift(snapshot);
  if (snapshots.length > MAX_SNAPSHOTS) {
    snapshots.pop();
  }

  try {
    localStorage.setItem(SNAPSHOT_KEY, JSON.stringify(snapshots));
  } catch (e) {
    console.error("保存快照失败", e);
  }

  return snapshot;
}

export function deleteSnapshot(snapshotId: string): boolean {
  try {
    const snapshots = getSnapshots().filter((s) => s.id !== snapshotId);
    localStorage.setItem(SNAPSHOT_KEY, JSON.stringify(snapshots));
    return true;
  } catch (e) {
    console.error("删除快照失败", e);
    return false;
  }
}

export function restoreSnapshot(snapshotId: string): Option | null {
  try {
    const snapshot = getSnapshots().find((s) => s.id === snapshotId);
    if (snapshot) {
      return snapshot.data;
    }
  } catch (e) {
    console.error("恢复快照失败", e);
  }
  return null;
}

export function clearSnapshots(): boolean {
  try {
    localStorage.removeItem(SNAPSHOT_KEY);
    return true;
  } catch (e) {
    console.error("清除快照失败", e);
    return false;
  }
}

export function getDefaultConfig(): Option {
  return JSON.parse(JSON.stringify(defaultVarOption));
}
