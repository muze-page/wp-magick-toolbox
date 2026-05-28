import React, { useContext, useState, useEffect } from "react";
import { Form, Button, Select, List, message, Modal, Typography, Alert } from "antd";
import { ExclamationCircleOutlined } from "@ant-design/icons";
import { DataContext } from "@/tool/dataContext";
import { AntConfig } from "@/tool/tool";
import { SettingsSection, ModuleRow, RiskNotice } from "@/components/settings-ui";
import FeatureSwitch from "@/basic/feature-switch";
import { performanceApi } from "@/api";

const { Text } = Typography;
const fromConfig = AntConfig.from;

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);
  const publicData = optionData.performance?.db_clean || {};
  const [formData, setFormData] = useState(publicData || {});
  const [stats, setStats] = useState<any>({});
  const [previewData, setPreviewData] = useState<Record<string, any>>({});
  const [previewLoading, setPreviewLoading] = useState(false);
  const [cleanLoadingType, setCleanLoadingType] = useState<string | null>(null);

  const onValuesChange = (changedValues: any, _allValues: any) => {
    setFormData((prev: any) => ({ ...prev, ...changedValues }));
  };

  useEffect(() => {
    updateOption("performance", "db_clean", formData);
  }, [formData]);

  const fetchStats = () => {
    performanceApi.getDbStats().then((res: any) => {
      if (res?.success) setStats(res.data);
    });
  };

  const handlePreview = (type: string) => {
    setPreviewLoading(true);
    fetch("/wp-json/mabox/v1/performance/db/preview", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": window.dataLocal?.nonce || "",
      },
      credentials: "same-origin",
      body: JSON.stringify({ type, dry_run: true }),
    })
      .then((r) => r.json())
      .then((res: any) => {
        setPreviewLoading(false);
        if (res?.success) {
          setPreviewData((prev: Record<string, any>) => ({ ...prev, [type]: res.data }));
        } else {
          message.error("预览失败");
        }
      })
      .catch(() => {
        setPreviewLoading(false);
        message.error("预览请求失败");
      });
  };

  const getAffectedCount = (type: string, data: any): number => {
    if (!data) return 0;
    if (type === "all") return data.total || 0;
    return data.affected || 0;
  };

  const handleClean = (type: string) => {
    const preview = previewData[type];
    const affectedCount = getAffectedCount(type, preview);
    Modal.confirm({
      title: "确认执行数据库清理？",
      icon: <ExclamationCircleOutlined />,
      content: (
        <div>
          <Alert
            message="此操作不可逆"
            description="删除的数据无法恢复，请确保已备份数据库。"
            type="error"
            showIcon
            style={{ marginBottom: 8 }}
          />
          <Text type="secondary">
            清理类型：{type}。将删除 {affectedCount} 条数据。
          </Text>
        </div>
      ),
      okText: "确认清理",
      okButtonProps: { danger: true },
      cancelText: "取消",
      onOk: () => {
        return new Promise<void>((resolve) => {
          setCleanLoadingType(type);
          fetch("/wp-json/mabox/v1/performance/db/clean", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-WP-Nonce": window.dataLocal?.nonce || "",
            },
            credentials: "same-origin",
            body: JSON.stringify({ type, dry_run: false }),
          })
            .then((r) => r.json())
            .then((res: any) => {
              setCleanLoadingType(null);
              if (res?.success) {
                message.success("清理完成" + (res?.data?.deleted ? "，删除 " + res.data.deleted + " 条" : ""));
                setPreviewData((prev: Record<string, any>) => {
                  const next = { ...prev };
                  delete next[type];
                  return next;
                });
                fetchStats();
              }
              resolve();
            })
            .catch(() => {
              setCleanLoadingType(null);
              message.error("清理失败");
              resolve();
            });
        });
      },
    });
  };

  return (
    <SettingsSection title="数据库清理" description="数据库清理与优化">
      <Form
        name="db_clean"
        labelCol={fromConfig.labelCol}
        wrapperCol={fromConfig.wrapperCol}
        style={{ maxWidth: fromConfig.maxWidth }}
        initialValues={publicData}
        autoComplete="off"
        onValuesChange={onValuesChange}
      >
        <RiskNotice warning="数据库清理操作不可逆，删除的数据无法恢复" suggestion="执行前务必先预览影响数量，并做好备份" />

        <ModuleRow
          title="启用数据库清理"
          featureId="performance-db_clean-enabled"
          enabled={!!formData.enabled}
          onChange={(checked) => {
            setFormData((prev: any) => ({ ...prev, enabled: checked }));
          }}
          tags={["高风险"]}
        />

        <Form.Item label="清理修订版本" name="clean_revisions" valuePropName="checked">
          <FeatureSwitch featureId="performance-db_clean-clean_revisions" />
        </Form.Item>
        <Form.Item label="清理自动草稿" name="clean_drafts" valuePropName="checked">
          <FeatureSwitch featureId="performance-db_clean-clean_drafts" />
        </Form.Item>
        <Form.Item label="清理垃圾评论" name="clean_spam_comments" valuePropName="checked">
          <FeatureSwitch featureId="performance-db_clean-clean_spam_comments" />
        </Form.Item>
        <Form.Item label="清理过期 Transient" name="clean_transients" valuePropName="checked">
          <FeatureSwitch featureId="performance-db_clean-clean_transients" />
        </Form.Item>

        <Form.Item label="定时自动清理" name="auto_clean" valuePropName="checked">
          <FeatureSwitch featureId="performance-db_clean-auto_clean" />
        </Form.Item>
        {formData.auto_clean && (
          <Form.Item label="清理周期" name="auto_clean_schedule">
            <Select options={[
              { label: "每天", value: "daily" },
              { label: "每周", value: "weekly" },
              { label: "每月", value: "monthly" },
            ]} />
          </Form.Item>
        )}

        <Form.Item wrapperCol={fromConfig.wrapperCol}>
          <Button onClick={fetchStats}>查看统计</Button>
          <Button
            type="primary"
            danger
            style={{ marginLeft: 8 }}
            onClick={() => handlePreview("all")}
            loading={previewLoading}
          >
            预览清理
          </Button>
          <Button
            danger
            style={{ marginLeft: 8 }}
            onClick={() => handleClean("all")}
            loading={cleanLoadingType === "all"}
            disabled={!previewData["all"]}
          >
            执行清理
          </Button>
        </Form.Item>

        {previewData["all"] && (
          <Form.Item wrapperCol={fromConfig.wrapperCol}>
            <Alert
              message="预览结果"
              description={`将清理 ${getAffectedCount("all", previewData["all"])} 条数据`}
              type="warning"
              showIcon
              closable
              onClose={() => setPreviewData((prev: Record<string, any>) => { const next = { ...prev }; delete next["all"]; return next; })}
            />
          </Form.Item>
        )}

        {stats.db_size && (
          <Form.Item wrapperCol={fromConfig.wrapperCol}>
            <List size="small" bordered>
              <List.Item>数据库大小：{stats.db_size}</List.Item>
              <List.Item>
                修订版本：{stats.revisions}{" "}
                <Button size="small" onClick={() => handlePreview("revisions")} loading={previewLoading}>预览</Button>
                <Button size="small" style={{ marginLeft: 4 }} onClick={() => handleClean("revisions")} loading={cleanLoadingType === "revisions"} disabled={!previewData["revisions"]}>清理</Button>
              </List.Item>
              <List.Item>
                自动草稿：{stats.drafts}{" "}
                <Button size="small" onClick={() => handlePreview("drafts")} loading={previewLoading}>预览</Button>
                <Button size="small" style={{ marginLeft: 4 }} onClick={() => handleClean("drafts")} loading={cleanLoadingType === "drafts"} disabled={!previewData["drafts"]}>清理</Button>
              </List.Item>
              <List.Item>
                垃圾评论：{stats.spam}{" "}
                <Button size="small" onClick={() => handlePreview("spam")} loading={previewLoading}>预览</Button>
                <Button size="small" style={{ marginLeft: 4 }} onClick={() => handleClean("spam")} loading={cleanLoadingType === "spam"} disabled={!previewData["spam"]}>清理</Button>
              </List.Item>
              <List.Item>
                Transient：{stats.transients}{" "}
                <Button size="small" onClick={() => handlePreview("transients")} loading={previewLoading}>预览</Button>
                <Button size="small" style={{ marginLeft: 4 }} onClick={() => handleClean("transients")} loading={cleanLoadingType === "transients"} disabled={!previewData["transients"]}>清理</Button>
              </List.Item>
            </List>
          </Form.Item>
        )}
      </Form>
    </SettingsSection>
  );
};

export default App;
