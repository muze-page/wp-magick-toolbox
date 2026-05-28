import Compose from "@/components/shortcode/compose";
import Pendant from "@/components/shortcode/pendant";
import { SettingsSection } from "@/components/settings-ui";

const App: React.FC = () => {
  return (
    <SettingsSection
      title="短代码模块中心"
      description="启用对应短代码后，在经典编辑器中，有短代码下拉框可供选择，古登堡编辑器中，有魔法短代码区块可供选择"
    >
      <Compose />
      <Pendant />
    </SettingsSection>
  );
};

export default App;
