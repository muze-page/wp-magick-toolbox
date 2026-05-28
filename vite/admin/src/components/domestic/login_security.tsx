import React, { useContext, useState, useEffect } from "react";
import { Form, Input, InputNumber } from "antd";
import { DataContext } from "@/tool/dataContext";
import { AntConfig } from "@/tool/tool";
import { SettingsSection, ModuleRow } from "@/components/settings-ui";

const fromConfig = AntConfig.from;
const { TextArea } = Input;

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);
  const publicData = optionData.domestic?.login_security || {};
  const [formData, setFormData] = useState(publicData || {});

  const onValuesChange = (changedValues: any, _allValues?: any) => {
    setFormData((prev: any) => ({ ...prev, ...changedValues }));
  };

  useEffect(() => {
    updateOption("domestic", "login_security", formData);
  }, [formData]);

  return (
    <SettingsSection title="登录安全" description="登录安全中心，防止暴力破解">
      <Form
        name="login_security"
        labelCol={fromConfig.labelCol}
        wrapperCol={fromConfig.wrapperCol}
        style={{ maxWidth: fromConfig.maxWidth }}
        initialValues={publicData}
        autoComplete="off"
        onValuesChange={onValuesChange}
      >
        <ModuleRow
          title="限制登录失败次数"
          featureId="domestic-login_security-fail_limit_enabled"
          enabled={formData.fail_limit_enabled as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ fail_limit_enabled: checked });
          }}
        >
          <Form.Item label="最大失败次数" name="fail_limit_count">
            <InputNumber min={3} max={20} />
          </Form.Item>
          <Form.Item label="锁定时间(分钟)" name="fail_lock_duration">
            <InputNumber min={5} max={1440} />
          </Form.Item>
        </ModuleRow>

        <ModuleRow
          title="IP 登录失败锁定"
          featureId="domestic-login_security-ip_lock_enabled"
          enabled={formData.ip_lock_enabled as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ ip_lock_enabled: checked });
          }}
          tags={["高风险"]}
        >
          <Form.Item label="最大失败次数" name="ip_lock_count">
            <InputNumber min={5} max={50} />
          </Form.Item>
          <Form.Item label="锁定时间(分钟)" name="ip_lock_duration">
            <InputNumber min={5} max={1440} />
          </Form.Item>
        </ModuleRow>

        <ModuleRow
          title="自定义登录地址"
          featureId="domestic-login_security-custom_login_enabled"
          enabled={formData.custom_login_enabled as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ custom_login_enabled: checked });
          }}
          tags={["高风险"]}
        >
          <Form.Item label="登录 slug" name="custom_login_slug" extra="如：my-login">
            <Input />
          </Form.Item>
        </ModuleRow>

        <ModuleRow
          title="禁止用户名枚举"
          featureId="domestic-login_security-ban_enumeration_enabled"
          enabled={formData.ban_enumeration_enabled as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ ban_enumeration_enabled: checked });
          }}
        />

        <ModuleRow
          title="登录通知邮件"
          featureId="domestic-login_security-login_notify_enabled"
          enabled={formData.login_notify_enabled as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ login_notify_enabled: checked });
          }}
        />

        <ModuleRow
          title="登录日志"
          featureId="domestic-login_security-login_log_enabled"
          enabled={formData.login_log_enabled as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ login_log_enabled: checked });
          }}
        />

        <ModuleRow
          title="IP 白名单"
          featureId="domestic-login_security-ip_whitelist_enabled"
          enabled={formData.ip_whitelist_enabled as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ ip_whitelist_enabled: checked });
          }}
        >
          <Form.Item label="白名单 IP" name="ip_whitelist" extra="每行一个">
            <TextArea rows={4} />
          </Form.Item>
        </ModuleRow>
      </Form>
    </SettingsSection>
  );
};

export default App;
