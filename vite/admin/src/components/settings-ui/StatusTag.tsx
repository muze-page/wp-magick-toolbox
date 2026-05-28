import React from "react";
import { Tag } from "antd";

type StatusType = "未配置" | "已配置" | "异常" | "待处理" | "推荐" | "高风险";

interface StatusTagProps {
  status: StatusType;
  className?: string;
}

const statusColorMap: Record<StatusType, string> = {
  "未配置": "default",
  "已配置": "green",
  "异常": "red",
  "待处理": "orange",
  "推荐": "blue",
  "高风险": "volcano",
};

const StatusTag: React.FC<StatusTagProps> = ({ status, className }) => {
  return (
    <Tag
      color={statusColorMap[status] || "default"}
      className={className}
      style={{ margin: 0, fontSize: 11 }}
    >
      {status}
    </Tag>
  );
};

export default StatusTag;
