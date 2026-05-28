import Security from "./security";
import Beautify from "./beautify";
import { PreviewPanel } from "@/components/settings-ui";

const App: React.FC = () => {
  return (
    <div className="mabox-login-layout">
      <div className="mabox-login-form">
        <Beautify />
        <Security />
      </div>
      <div className="mabox-login-preview">
        <PreviewPanel>
          <div style={{ textAlign: "center", color: "#999", padding: "32px 0" }}>
            登录页预览区域
          </div>
        </PreviewPanel>
      </div>
    </div>
  );
};

export default App;
