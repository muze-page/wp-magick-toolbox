import { SettingsSection } from "@/components/settings-ui";
import { AboutPlugin, Proposal, Links } from "@/components/about/collapse";
import Source from "@/components/about/table";

const App: React.FC = () => {
  return (
    <>
      <SettingsSection title="关于插件">
        <AboutPlugin />
      </SettingsSection>
      <SettingsSection title="我有建议">
        <Proposal />
      </SettingsSection>
      <SettingsSection title="联系方式">
        <Links />
      </SettingsSection>
      <SettingsSection title="来源">
        <Source />
      </SettingsSection>
    </>
  );
};

export default App;
