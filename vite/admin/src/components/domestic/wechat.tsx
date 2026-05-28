import React, { useContext, useState, useEffect } from "react";
import { Form, Input, Select } from "antd";
import { DataContext } from "@/tool/dataContext";
import { AntConfig } from "@/tool/tool";
import { SettingsSection, ModuleRow } from "@/components/settings-ui";

const fromConfig = AntConfig.from;

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);
  const publicData = optionData.domestic?.wechat || {};
  const [formData, setFormData] = useState(publicData || {});

  const onValuesChange = (changedValues: any, _allValues?: any) => {
    setFormData((prev: any) => ({ ...prev, ...changedValues }));
  };

  useEffect(() => {
    updateOption("domestic", "wechat", formData);
  }, [formData]);

  return (
    <SettingsSection title="微信生态" description="微信生态增强功能">
      <Form
        name="wechat"
        labelCol={fromConfig.labelCol}
        wrapperCol={fromConfig.wrapperCol}
        style={{ maxWidth: fromConfig.maxWidth }}
        initialValues={publicData}
        autoComplete="off"
        onValuesChange={onValuesChange}
      >
        <ModuleRow
          title="JSSDK 分享"
          featureId="domestic-wechat-jssdk_enabled"
          enabled={formData.jssdk_enabled as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ jssdk_enabled: checked });
          }}
        >
          <Form.Item label="AppID" name="appid">
            <Input />
          </Form.Item>
          <Form.Item label="AppSecret" name="appsecret">
            <Input />
          </Form.Item>
        </ModuleRow>

        <ModuleRow
          title="微信/QQ 打开引导"
          featureId="domestic-wechat-guide_overlay_enabled"
          enabled={formData.guide_overlay_enabled as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ guide_overlay_enabled: checked });
          }}
        >
          <Form.Item label="处理方式" name="guide_mode">
            <Select options={[{ label: "仅提示", value: "guide" }, { label: "强制跳转", value: "redirect" }]} />
          </Form.Item>
          <Form.Item label="引导文案" name="guide_text">
            <Input />
          </Form.Item>
          <Form.Item label="二维码图片" name="guide_qrcode">
            <Input placeholder="图片 URL，可选" />
          </Form.Item>
        </ModuleRow>
      </Form>
    </SettingsSection>
  );
};

export default App;
