import React from "react";
import { Alert } from "antd";

interface RiskNoticeProps {
  title?: string;
  warning: string;
  suggestion?: string;
  className?: string;
}

const RiskNotice: React.FC<RiskNoticeProps> = ({
  title,
  warning,
  suggestion,
  className,
}) => {
  return (
    <Alert
      type="warning"
      showIcon
      className={className}
      message={title || "风险提示"}
      description={
        <div>
          <p style={{ marginBottom: suggestion ? 4 : 0 }}>{warning}</p>
          {suggestion && (
            <p style={{ color: "#666" }}>建议：{suggestion}</p>
          )}
        </div>
      }
      style={{ marginBottom: 12 }}
    />
  );
};

export default RiskNotice;