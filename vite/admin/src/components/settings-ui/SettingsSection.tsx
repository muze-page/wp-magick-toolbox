import React from "react";

interface SettingsSectionProps {
  title: string;
  description?: string;
  children: React.ReactNode;
  className?: string;
  id?: string;
}

const SettingsSection: React.FC<SettingsSectionProps> = ({
  title,
  description,
  children,
  className,
  id,
}) => {
  return (
    <div className={`mabox-section ${className || ""}`} id={id}>
      <div className="mabox-section-header">
        <h2 className="mabox-section-title">{title}</h2>
        {description && (
          <p className="mabox-section-desc">{description}</p>
        )}
      </div>
      <div className="mabox-section-body">{children}</div>
    </div>
  );
};

export default SettingsSection;
