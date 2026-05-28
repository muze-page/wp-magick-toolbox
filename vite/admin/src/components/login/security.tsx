import React from "react";
import { useState, useContext, useEffect } from "react";
import { Form, Input, Select } from "antd";
import { DataContext } from "@/tool/dataContext";
import { LoginSecurity } from "@/tool/interface";
import { defaultVarOption } from "@/tool/defaultVar";
import { AntConfig } from "@/tool/tool";
import { SettingsSection } from "@/components/settings-ui";

type FieldType = LoginSecurity;

const fromConfig = AntConfig.from;

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);

  const publicData =
    optionData.login?.security || defaultVarOption.login.security;

  const [formData, setFormData] = useState(publicData || {});

  const onValuesChange = (
    changedValues: Partial<FieldType>,
    _allValues: FieldType
  ) => {
    setFormData((prevState) => ({
      ...prevState,
      ...changedValues,
    }));
  };

  useEffect(() => {
    updateOption("login", "security", formData);
  }, [formData]);

  return (
    <SettingsSection title="登录安全">
      <Form
        name="login_security"
        labelCol={fromConfig.labelCol}
        wrapperCol={fromConfig.wrapperCol}
        style={{ maxWidth: fromConfig.maxWidth }}
        initialValues={publicData}
        autoComplete="off"
        onFinish={() => {}}
        onValuesChange={onValuesChange}
      >
        <Form.Item<FieldType>
          label="登录验证码"
          name="login_code"
          extra={"登录时需填写验证码才可登录"}
        >
          <Select
            style={{ width: 200 }}
            options={[
              { value: "false", label: "禁用" },
              { value: "math", label: "数学验证码" },
              { value: "random", label: "随机混合验证码" },
              { value: "tecent", label: " 腾讯验证码-功能未验证" },
            ]}
          />
        </Form.Item>

        {formData.login_code === "tecent" && (
          <>
            <Form.Item<FieldType>
              label="App ID"
              name="tecent_id"
              extra={"貌似随便填也能用"}
            >
              <Input />
            </Form.Item>
            <Form.Item<FieldType>
              label="App Secret Key"
              name="tecent_key"
              extra={"貌似随便填也能用"}
            >
              <Input.Password />
            </Form.Item>
          </>
        )}
      </Form>
    </SettingsSection>
  );
};

export default App;
