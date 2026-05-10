import React, { useContext, useState, useEffect } from "react";
import { Form, Input, Card, Button, List } from "antd";
import { PlusOutlined, DeleteOutlined } from "@ant-design/icons";
import { DataContext } from "@/tool/dataContext";
import { AntConfig } from "@/tool/tool";
import FeatureSwitch from "@/basic/feature-switch";

const fromConfig = AntConfig.from;
const { TextArea } = Input;

const serviceItems = [
  { key: "service_custom_dev", label: "定制开发", desc: "根据您的需求定制功能或模块" },
  { key: "service_deployment", label: "代部署服务", desc: "代为安装、配置、调试插件" },
  { key: "service_theme_adapt", label: "主题适配", desc: "为您的主题定制专属兼容方案" },
  { key: "service_support", label: "技术支持", desc: "日常使用问题解答与故障排查" },
];

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);
  const data = optionData.services || {};
  const [formData, setFormData] = useState(data);

  const onValuesChange = (changedValues: any) => {
    setFormData((prev: any) => ({ ...prev, ...changedValues }));
  };

  useEffect(() => {
    updateOption("services", "", formData);
  }, [formData]);

  const addCase = () => {
    const cases = formData.cases || [];
    setFormData({ ...formData, cases: [...cases, { title: "", description: "", logo: "" }] });
  };

  const removeCase = (index: number) => {
    const cases = [...(formData.cases || [])];
    cases.splice(index, 1);
    setFormData({ ...formData, cases });
  };

  const updateCase = (index: number, field: string, value: string) => {
    const cases = [...(formData.cases || [])];
    cases[index] = { ...cases[index], [field]: value };
    setFormData({ ...formData, cases });
  };

  return (
    <Form
      name="services"
      labelCol={fromConfig.labelCol}
      wrapperCol={fromConfig.wrapperCol}
      style={{ maxWidth: fromConfig.maxWidth }}
      initialValues={data}
      autoComplete="off"
      onValuesChange={onValuesChange}
    >
      <Form.Item extra="为用户提供技术支持入口，不影响免费功能的完整体验">
        <h2>技术支持与服务</h2>
      </Form.Item>

      <Form.Item label="启用服务展示" name="enabled" valuePropName="checked">
        <FeatureSwitch featureId="services-enabled" />
      </Form.Item>

      {formData?.enabled && (
        <>
          <Card title="联系方式" size="small" style={{ marginBottom: 16 }}>
            <Form.Item label="微信号" name="wechat_id">
              <Input placeholder="您的微信号" />
            </Form.Item>
            <Form.Item label="微信二维码" name="wechat_qr" extra="上传图片 URL 地址">
              <Input placeholder="https://..." />
            </Form.Item>
            <Form.Item label="邮箱" name="email">
              <Input placeholder="support@example.com" />
            </Form.Item>
            <Form.Item label="网站" name="website">
              <Input placeholder="https://..." />
            </Form.Item>
          </Card>

          <Card title="服务项目" size="small" style={{ marginBottom: 16 }}>
            {serviceItems.map((item) => (
              <Form.Item key={item.key} label={item.label} name={item.key} valuePropName="checked" extra={item.desc}>
                <FeatureSwitch featureId={`services-${item.key}`} />
              </Form.Item>
            ))}
          </Card>

          <Card
            title="服务案例"
            size="small"
            extra={
              <Button type="link" size="small" icon={<PlusOutlined />} onClick={addCase}>
                添加案例
              </Button>
            }
          >
            <List
              dataSource={formData.cases || []}
              renderItem={(item: any, index: number) => (
                <List.Item
                  actions={[
                    <Button type="link" danger size="small" icon={<DeleteOutlined />} onClick={() => removeCase(index)}>
                      删除
                    </Button>,
                  ]}
                >
                  <div style={{ width: "100%" }}>
                    <Input
                      value={item.title}
                      onChange={(e) => updateCase(index, "title", e.target.value)}
                      placeholder="案例标题"
                      style={{ marginBottom: 8 }}
                    />
                    <TextArea
                      value={item.description}
                      onChange={(e) => updateCase(index, "description", e.target.value)}
                      placeholder="案例描述"
                      rows={2}
                      style={{ marginBottom: 8 }}
                    />
                    <Input
                      value={item.logo}
                      onChange={(e) => updateCase(index, "logo", e.target.value)}
                      placeholder="客户 Logo URL（可选）"
                    />
                  </div>
                </List.Item>
              )}
            />
            {(!formData.cases || formData.cases.length === 0) && (
              <div style={{ textAlign: "center", padding: "24px 0", color: "#999" }}>
                暂无案例，点击「添加案例」开始
              </div>
            )}
          </Card>
        </>
      )}
    </Form>
  );
};

export default App;
