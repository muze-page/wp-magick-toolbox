import React, { useState } from "react";
import ProviderConfig from "@/components/ai_review/provider_config";
import AuditLog from "@/components/ai_review/audit_log";

const App: React.FC = () => {
  const [activeSection, setActiveSection] = useState<"config" | "logs">("config");

  return (
    <>
      <div style={{ marginBottom: 16, display: "flex", gap: 8 }}>
        <button
          onClick={() => setActiveSection("config")}
          style={{
            padding: "6px 16px",
            border: "none",
            borderRadius: 4,
            cursor: "pointer",
            background: activeSection === "config" ? "#1677ff" : "#f0f0f0",
            color: activeSection === "config" ? "#fff" : "#333",
          }}
        >
          审核配置
        </button>
        <button
          onClick={() => setActiveSection("logs")}
          style={{
            padding: "6px 16px",
            border: "none",
            borderRadius: 4,
            cursor: "pointer",
            background: activeSection === "logs" ? "#1677ff" : "#f0f0f0",
            color: activeSection === "logs" ? "#fff" : "#333",
          }}
        >
          审核日志
        </button>
      </div>

      {activeSection === "config" && <ProviderConfig />}
      {activeSection === "logs" && <AuditLog />}
    </>
  );
};

export default App;
