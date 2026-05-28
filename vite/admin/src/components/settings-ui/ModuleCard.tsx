import React from "react";
import FeatureSwitch from "@/basic/feature-switch";
import Preview from "@/basic/preview";
import StatusTag from "./StatusTag";

interface ModuleCardProps {
  title: string;
  description: string;
  featureId: string;
  enabled: boolean;
  onChange: (checked: boolean) => void;
  tags?: string[];
  preview?: { title: string; img: string };
  onDetails?: () => void;
  children?: React.ReactNode;
  className?: string;
}

const ModuleCard: React.FC<ModuleCardProps> = ({
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
    <div className={`mabox-module-card ${className || ""}`} id={featureId}>
      <div className="mabox-module-card-header">
        <div className="mabox-module-card-info">
          <div className="mabox-module-card-title-row">
            <span className="mabox-module-card-title">{title}</span>
            {tags?.map((tag) => (
              <StatusTag key={tag} status={tag as any} />
            ))}
          </div>
          <div className="mabox-module-card-desc">{description}</div>
        </div>
        <div className="mabox-module-card-actions">
          <FeatureSwitch featureId={featureId} checked={enabled} onChange={onChange} />
          {preview && <Preview title={preview.title} img={preview.img} />}
          {onDetails && (
            <button className="mabox-module-card-details-btn" onClick={onDetails}>
              详情
            </button>
          )}
        </div>
      </div>
      {enabled && children && (
        <div className="mabox-module-card-body">{children}</div>
      )}
    </div>
  );
};

export default ModuleCard;
