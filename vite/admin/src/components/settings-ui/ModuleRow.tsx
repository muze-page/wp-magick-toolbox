import React from "react";
import FeatureSwitch from "@/basic/feature-switch";
import Preview from "@/basic/preview";
import StatusTag from "./StatusTag";

interface ModuleRowProps {
  title: string;
  description?: string;
  featureId: string;
  enabled: boolean;
  onChange: (checked: boolean) => void;
  tags?: string[];
  preview?: { title: string; img: string };
  onDetails?: () => void;
  children?: React.ReactNode;
  className?: string;
}

const ModuleRow: React.FC<ModuleRowProps> = ({
  title,
  description,
  featureId,
  enabled,
  onChange,
  tags,
  preview,
  onDetails,
  children,
  className,
}) => {
  return (
    <div className={`mabox-module-row ${className || ""}`} id={featureId}>
      <div className="mabox-module-row-info">
        <div className="mabox-module-row-title-row">
          <span className="mabox-module-row-title">{title}</span>
          {tags?.map((tag) => (
            <StatusTag key={tag} status={tag as any} />
          ))}
        </div>
        {description && (
          <div className="mabox-module-row-desc">{description}</div>
        )}
      </div>
      <div className="mabox-module-row-actions">
        <FeatureSwitch featureId={featureId} checked={enabled} onChange={onChange} />
        {preview && <Preview title={preview.title} img={preview.img} />}
        {onDetails && (
          <button className="mabox-module-row-details-btn" onClick={onDetails}>
            详情
          </button>
        )}
      </div>
      {enabled && children && (
        <div className="mabox-module-row-body">{children}</div>
      )}
    </div>
  );
};

export default ModuleRow;