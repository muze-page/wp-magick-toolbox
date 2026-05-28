import React, { useState, useCallback, useContext } from "react";
import { Card, Button, Row, Col, Tag, Space, Spin, Typography, message } from "antd";
import { ReloadOutlined, ThunderboltOutlined, CheckCircleOutlined, CloseCircleOutlined } from "@ant-design/icons";
import { domesticApi } from "@/api";
import DiffModal from "@/components/diff-modal";
import { DataContext } from "@/tool/dataContext";
import { createSnapshot } from "@/tool/snapshot";
import { saveOption } from "@/axios/save";
import { SettingsSection } from "@/components/settings-ui";

const { Text } = Typography;

interface CheckResult {
  service: string;
  reachable: boolean;
  latency: number;
  suggestion: string;
}

const Environment: React.FC = () => {
  const { optionData, refreshOption } = useContext(DataContext);
  const [results, setResults] = useState<Record<string, CheckResult> | null>(null);
  const [loading, setLoading] = useState(false);
  const [diffVisible, setDiffVisible] = useState(false);
  const [pendingDiffs, setPendingDiffs] = useState<any[]>([]);
  const [pendingProposed, setPendingProposed] = useState<Record<string, any> | null>(null);

  const handleCheck = useCallback(async () => {
    setLoading(true);
    try {
      const res = await domesticApi.checkEnvironment();
      if (res?.success && res?.data) {
        setResults(res.data);
      } else {
        message.error("检测失败，请重试");
      }
    } catch (err) {
      message.error("检测请求失败");
    } finally {
      setLoading(false);
    }
  }, []);

  const handleOneClickFix = useCallback(async () => {
    if (!results) return;
    const unreachable = Object.entries(results)
      .filter(([_, r]) => !r.reachable)
      .map(([key]) => key)
      .filter((key) => ["gravatar", "google_fonts", "google_ajax"].includes(key));
    if (unreachable.length === 0) {
      message.info("所有服务可达，无需修复");
      return;
    }
    try {
      const res = await domesticApi.applyEnvironmentFix(unreachable);
      if (res?.success && res?.data?.diffs) {
        setPendingDiffs(
          res.data.diffs.map((d: any) => ({
            path: `optimize.site.${d.key}`,
            label: d.label,
            module: "optimize",
            before: d.before,
            after: d.after,
            riskLevel: d.risk_level === "high" ? "high" : ("none" as const),
          }))
        );
        if (res.data.proposed) {
          setPendingProposed(res.data.proposed);
        }
        setDiffVisible(true);
      } else {
        message.error("获取修复建议失败");
      }
    } catch (err) {
      message.error("修复请求失败");
    }
  }, [results]);

  const handleApplyFixes = useCallback(async () => {
    if (!pendingProposed) return;
    try {
      const merged: any = JSON.parse(JSON.stringify(optionData));
      if (!merged.optimize) merged.optimize = {};
      if (!merged.optimize.site) merged.optimize.site = {};
      Object.entries(pendingProposed).forEach(([key, value]) => {
        merged.optimize.site[key] = value;
      });
      createSnapshot(optionData);
      await saveOption(merged);
      await refreshOption();
      message.success("已应用修复");
      setDiffVisible(false);
      setPendingDiffs([]);
      setPendingProposed(null);
      handleCheck();
    } catch (err) {
      message.error("修复请求失败");
    }
  }, [pendingProposed, optionData, refreshOption, handleCheck]);

  return (
    <SettingsSection title="中国访问适配">
      <Card
        extra={
          <Space>
            <Button size="small" icon={<ReloadOutlined />} onClick={handleCheck} loading={loading}>
              检测
            </Button>
            {results && (
              <Button
                type="primary"
                size="small"
                icon={<ThunderboltOutlined />}
                onClick={handleOneClickFix}
              >
                一键修复
              </Button>
            )}
          </Space>
        }
      >
        {!results && !loading && (
          <div style={{ textAlign: "center", padding: 24 }}>
            <Text type="secondary">
              点击"检测"按钮，检测 Google Fonts、Gravatar、Google Ajax、WordPress.org 等服务在国内的可达性。
            </Text>
          </div>
        )}

        {loading && (
          <div style={{ textAlign: "center", padding: 24 }}>
            <Spin tip="正在检测服务可达性..." />
          </div>
        )}

        {results && !loading && (
          <Row gutter={[16, 16]}>
            {Object.entries(results).map(([key, result]) => (
              <Col xs={24} sm={12} md={6} key={key}>
                <Card size="small" className="h-full">
                  <Space direction="vertical" className="w-full" size="small">
                    <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                      <Text strong>{result.service}</Text>
                      {result.reachable ? (
                        <Tag icon={<CheckCircleOutlined />} color="success">可达</Tag>
                      ) : (
                        <Tag icon={<CloseCircleOutlined />} color="error">不可达</Tag>
                      )}
                    </div>
                    {result.reachable && (
                      <Text type="secondary" style={{ fontSize: 12 }}>
                        延迟：{result.latency}ms
                      </Text>
                    )}
                    {!result.reachable && result.suggestion && (
                      <Text type="warning" style={{ fontSize: 12 }}>
                        {result.suggestion}
                      </Text>
                    )}
                  </Space>
                </Card>
              </Col>
            ))}
          </Row>
        )}

        <DiffModal
          visible={diffVisible}
          onCancel={() => { setDiffVisible(false); setPendingDiffs([]); setPendingProposed(null); }}
          onConfirm={handleApplyFixes}
          diffs={pendingDiffs}
        />
      </Card>
    </SettingsSection>
  );
};

export default Environment;
