import React from "react";

interface PreviewPanelProps {
  children?: React.ReactNode;
  className?: string;
  style?: React.CSSProperties;
}

const PreviewPanel: React.FC<PreviewPanelProps> = ({ children, className, style }) => {
  return (
    <div className={`mabox-preview-panel ${className || ""}`} style={style}>
      <div className="mabox-preview-panel-label">预览</div>
      <div className="mabox-preview-panel-body">
        {children || <span style={{ color: "#999" }}>暂无预览</span>}
      </div>
    </div>
  );
};

export default PreviewPanel;