import React, { useContext, useState, useEffect } from "react";
import { Alert, Form, Button } from "antd";
import { DataContext } from "@/tool/dataContext";
import { AntConfig } from "@/tool/tool";
import { SettingsSection, ModuleRow, CheckTable } from "@/components/settings-ui";
import StatusTag from "@/components/settings-ui/StatusTag";
import { MediaHealthIssue, performanceApi } from "@/api";

const fromConfig = AntConfig.from;

interface OperationFeedback {
  type: "success" | "error" | "info";
  message: string;
}

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);
  const publicData = optionData.performance?.media_health || {};
  const [formData, setFormData] = useState(publicData || {});
  const [issues, setIssues] = useState<MediaHealthIssue[]>([]);
  const [checking, setChecking] = useState(false);
  const [fixing, setFixing] = useState(false);
  const [operationFeedback, setOperationFeedback] = useState<OperationFeedback | null>(null);

  const onValuesChange = (changedValues: any, _allValues: any) => {
    setFormData((prev: any) => ({ ...prev, ...changedValues }));
  };

  useEffect(() => {
    updateOption("performance", "media_health", formData);
  }, [formData]);

  const handleCheck = async () => {
    setOperationFeedback(null);
    setChecking(true);
    try {
      const res = await performanceApi.checkMedia();
      if (res.success) {
        const nextIssues = res.data?.issues || [];
        setIssues(nextIssues);
        setOperationFeedback({
          type: "info",
          message: nextIssues.length > 0
            ? `体检完成：发现 ${nextIssues.length} 类问题。`
            : "体检完成：未发现需要处理的问题。",
        });
      } else {
        setOperationFeedback({ type: "error", message: "体检失败，请重试。" });
      }
    } catch {
      setOperationFeedback({ type: "error", message: "体检失败，请重试。" });
    } finally {
      setChecking(false);
    }
  };

  const handleFixAlt = async () => {
    setOperationFeedback(null);
    setFixing(true);
    try {
      const res = await performanceApi.fixMediaAlt();
      if (res.success) {
        setOperationFeedback({
          type: "success",
          message: `已补全 ${res.data?.fixed || 0} 张图片的 Alt。`,
        });
      } else {
        setOperationFeedback({ type: "error", message: "修复失败，请重试。" });
      }
    } catch {
      setOperationFeedback({ type: "error", message: "修复失败，请重试。" });
    } finally {
      setFixing(false);
    }
  };

  const columns = [
    {
      title: "检测项",
      dataIndex: "type",
      key: "type",
      width: 120,
    },
    {
      title: "状态",
      dataIndex: "severity",
      key: "severity",
      width: 80,
      render: (severity: string) => {
        if (severity === "error") return <StatusTag status="异常" />;
        return <StatusTag status="待处理" />;
      },
    },
    {
      title: "数量",
      dataIndex: "count",
      key: "count",
      width: 80,
      render: (count: number) => `${count} 个`,
    },
  ];

  const dataSource = issues.map((item, i) => ({
    key: String(i),
    type: item.type,
    severity: item.severity || "warning",
    count: item.count || 0,
  }));

  return (
    <SettingsSection title="媒体库体检" description="媒体库健康体检">
      <Form
        name="media_health"
        labelCol={fromConfig.labelCol}
        wrapperCol={fromConfig.wrapperCol}
        style={{ maxWidth: fromConfig.maxWidth }}
        initialValues={publicData}
        autoComplete="off"
        onValuesChange={onValuesChange}
      >
        <ModuleRow
          title="启用媒体库体检"
          description="检查媒体库中的异常文件"
          featureId="performance-media_health-enabled"
          enabled={!!formData.enabled}
          onChange={(checked) => {
            setFormData((prev: any) => ({ ...prev, enabled: checked }));
          }}
        />

        <Form.Item wrapperCol={fromConfig.wrapperCol}>
          <Button type="primary" onClick={handleCheck} loading={checking} disabled={fixing}>
            开始体检
          </Button>
          <Button style={{ marginLeft: 8 }} onClick={handleFixAlt} loading={fixing} disabled={checking}>
            批量补全 Alt
          </Button>
        </Form.Item>

        {operationFeedback && (
          <Alert
            showIcon
            type={operationFeedback.type}
            role={operationFeedback.type === "error" ? "alert" : "status"}
            message={operationFeedback.message}
            style={{ marginBottom: 12 }}
          />
        )}

        {issues.length > 0 && (
          <CheckTable columns={columns} dataSource={dataSource} />
        )}
      </Form>
    </SettingsSection>
  );
};

export default App;
