/**
 * FeatureSwitch - 封装 Switch + 收藏星标
 * 
 * 替代原生 Switch，为每个功能开关添加收藏功能。
 * 配合组件级的 RISKY_FIELDS + checkRiskyFeature 实现风险检测。
 * 
 * 用法：
 * <Form.Item label="功能名称" name="field_name">
 *   <FeatureSwitch featureId="category-field_name" />
 * </Form.Item>
 */
import React from "react";
import { Switch } from "antd";
import { StarOutlined, StarFilled } from "@ant-design/icons";
import { isFavorite, toggleFavorite } from "@/tool/favorites";

interface FeatureSwitchProps {
  /** 功能唯一标识，格式: "category-field_name"，用于收藏和搜索定位 */
  featureId: string;
  /** 传递给原生 Switch 的其他 props */
  [key: string]: any;
}

const FeatureSwitch: React.FC<FeatureSwitchProps> = ({ featureId, ...restProps }) => {
  const favorited = isFavorite(featureId);

  const handleFavoriteClick = (e: React.MouseEvent) => {
    e.stopPropagation();
    e.preventDefault();
    toggleFavorite(featureId);
  };

  return (
    <div style={{ display: "inline-flex", alignItems: "center", gap: 8 }}>
      <Switch {...restProps} />
      <span
        onClick={handleFavoriteClick}
        style={{
          cursor: "pointer",
          fontSize: 16,
          color: favorited ? "#faad14" : "#d9d9d9",
          transition: "color 0.2s",
          lineHeight: 1,
          display: "inline-flex",
          alignItems: "center",
        }}
        title={favorited ? "从常用功能中移除" : "加入常用功能"}
      >
        {favorited ? <StarFilled /> : <StarOutlined />}
      </span>
    </div>
  );
};

export default FeatureSwitch;
