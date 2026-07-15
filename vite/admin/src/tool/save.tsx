import { useContext, useMemo, useState } from "react";
import { Button, message } from "antd";
import { DataContext } from "@/tool/dataContext";
import { saveOption } from "@/axios/save";
import { diffConfig, diffSecretChanges } from "@/tool/diff";
import DiffModal from "@/components/diff-modal";
import { ConfigDiffItem } from "@/tool/interface";

const App: React.FC = () => {
  const {
    optionData,
    refreshOption,
    lastSavedOption,
    secretStatus,
    secretChanges,
    clearSecretChanges,
    settingsState,
  } = useContext(DataContext);
  const [saving, setSaving] = useState(false);
  const [diffVisible, setDiffVisible] = useState(false);
  const [diffs, setDiffs] = useState<ConfigDiffItem[]>([]);
  const changes = useMemo(
    () => [
      ...diffConfig(lastSavedOption, optionData),
      ...diffSecretChanges(secretStatus, secretChanges),
    ],
    [lastSavedOption, optionData, secretChanges, secretStatus],
  );
  const changeCount = changes.length;

  const doSave = async () => {
    setSaving(true);
    let saved = false;
    try {
      await saveOption(optionData, secretChanges);
      saved = true;
      clearSecretChanges();
      await refreshOption();
      message.success("保存成功");
    } catch (error) {
      if (saved) {
        message.warning("设置已保存，但重新读取失败；保存功能已禁用，请重新读取后继续");
      } else {
        message.error("保存失败，请重试");
      }
    } finally {
      setSaving(false);
    }
  };

  const handleSaveClick = () => {
    if (changes.length === 0) return;

    setDiffs(changes);
    setDiffVisible(true);
  };

  const handleConfirmSave = () => {
    setDiffVisible(false);
    doSave();
  };

  let statusText = "已保存";
  let buttonText = "保存";
  let statusKind = "saved";

  if (saving) {
    statusText = "正在保存…";
    buttonText = "正在保存…";
    statusKind = "saving";
  } else if (settingsState === "loading") {
    statusText = "正在读取设置…";
    statusKind = "loading";
  } else if (settingsState === "error") {
    statusText = "设置不可用";
    statusKind = "error";
  } else if (changeCount > 0) {
    statusText = `${changeCount} 项待保存`;
    buttonText = "查看并保存";
    statusKind = "pending";
  }

  const saveDisabled = saving || settingsState !== "ready" || changeCount === 0;

  return (
    <div className={`mabox-save-trust mabox-save-trust--${statusKind}`}>
      <span className="mabox-save-status" role="status" aria-live="polite" aria-atomic="true">
        {statusText}
      </span>
      <Button
        className="mabox-save-action"
        type="primary"
        onClick={handleSaveClick}
        loading={saving}
        disabled={saveDisabled}
      >
        {buttonText}
      </Button>
      <DiffModal
        visible={diffVisible}
        diffs={diffs}
        onConfirm={handleConfirmSave}
        onCancel={() => setDiffVisible(false)}
      />
    </div>
  );
};

export default App;
