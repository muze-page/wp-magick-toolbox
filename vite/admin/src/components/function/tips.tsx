import React from "react";
import { useState, useContext, useEffect } from "react";
import { Form, Input } from "antd";
import TimePeriod from "@/basic/timeInput";
import { DataContext } from "@/tool/dataContext";
import { FunctionTips } from "@/tool/interface";
import { defaultVarOption } from "@/tool/defaultVar";
import { AntConfig } from "@/tool/tool";
import TextAreaHtml from "@/basic/htmlInput";
import { SettingsSection, ModuleRow } from "@/components/settings-ui";

type FieldType = FunctionTips;

const fromConfig = AntConfig.from;

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);

  const publicData =
    optionData.function?.config || defaultVarOption.function.config;

  const [formData, setFormData] = useState(publicData);

  const onValuesChange = (changedValues: Partial<FieldType>) => {
    setFormData((prevFormData) => ({
      ...prevFormData,
      ...changedValues,
    }));
  };

  useEffect(() => {
    updateOption("function", "config", formData);
  }, [formData]);

  return (
    <SettingsSection title="插件设置">
      <Form
        name="config"
        labelCol={fromConfig.labelCol}
        wrapperCol={fromConfig.wrapperCol}
        style={{ maxWidth: fromConfig.maxWidth }}
        initialValues={publicData}
        autoComplete="off"
        onFinish={() => {}}
        onValuesChange={onValuesChange}
      >
        <ModuleRow
          title="弹窗提示"
          description="添加页面提示"
          featureId="function-tips-pop_tips"
          enabled={formData.pop_tips as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ pop_tips: checked });
          }}
        >
          <Form.Item<FieldType>
            label="提示内容"
            name="tips_content"
            extra={"支持HTML"}
          >
            <TextAreaHtml />
          </Form.Item>
          <Form.Item<FieldType> label="按钮文字" name="tips_button">
            <Input />
          </Form.Item>
          <Form.Item<FieldType> label="按钮链接" name="tips_link">
            <Input />
          </Form.Item>
          <Form.Item<FieldType> label="显示时间" name="tips_time">
            <TimePeriod />
          </Form.Item>
        </ModuleRow>
      </Form>
    </SettingsSection>
  );
};

export default App;
