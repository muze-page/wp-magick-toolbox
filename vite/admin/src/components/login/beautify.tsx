import { useState, useContext, useEffect } from "react";
import { Form, ColorPicker, InputNumber } from "antd";

import { DataContext } from "@/tool/dataContext";
import { LoginBeautify } from "@/tool/interface";
import { defaultVarOption } from "@/tool/defaultVar";

import type { Color } from "antd/es/color-picker";
import { AntConfig } from "@/tool/tool";
import SelectImage from "@/basic/selectImage";
import { SettingsSection, ModuleRow } from "@/components/settings-ui";

type FieldType = LoginBeautify;

const fromConfig = AntConfig.from;

const getHexString = (color: Color | string): string => {
  return typeof color === "string" ? color : color.toHexString();
};

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);

  const publicData =
    optionData.login?.beautify || defaultVarOption.login.beautify;

  const [formData, setFormData] = useState(publicData || {});

  const onValuesChange = (
    changedValues: Partial<FieldType>,
    allValues: FieldType
  ) => {
    const updatedValues = {
      ...changedValues,
      background_left: getHexString(allValues.background_left || ""),
      background_right: getHexString(allValues.background_right || ""),
    };

    setFormData((prevState) => ({
      ...prevState,
      ...updatedValues,
    }));
  };

  useEffect(() => {
    updateOption("login", "beautify", formData);
  }, [formData]);

  return (
    <SettingsSection title="美化">
      <Form
        name="login_beautify"
        labelCol={fromConfig.labelCol}
        wrapperCol={fromConfig.wrapperCol}
        style={{ maxWidth: fromConfig.maxWidth }}
        initialValues={publicData}
        autoComplete="off"
        onFinish={() => {}}
        onValuesChange={onValuesChange}
      >
        <ModuleRow
          title="LOGO链接"
          description="改为首页链接"
          featureId="login-beautify-modify_login_link"
          enabled={formData.modify_login_link as boolean}
          onChange={(checked: boolean) => onValuesChange({ modify_login_link: checked } as Partial<FieldType>, { ...formData, modify_login_link: checked } as FieldType)}
        />
        <ModuleRow
          title="移除语言选择框"
          description="移除登录页面语言选择框"
          featureId="login-beautify-remove_langue"
          enabled={formData.remove_langue as boolean}
          onChange={(checked: boolean) => onValuesChange({ remove_langue: checked } as Partial<FieldType>, { ...formData, remove_langue: checked } as FieldType)}
        />
        <ModuleRow
          title="自定义登录页"
          description=""
          featureId="login-beautify-custom_login_page"
          enabled={formData.custom_login_page as boolean}
          onChange={(checked: boolean) => onValuesChange({ custom_login_page: checked } as Partial<FieldType>, { ...formData, custom_login_page: checked } as FieldType)}
        />

        {formData.custom_login_page && (
          <>
            <Form.Item<FieldType>
              label="左下角颜色"
              name="background_left"
              extra={""}
            >
              <ColorPicker showText />
            </Form.Item>
            <Form.Item<FieldType>
              label="右上角颜色"
              name="background_right"
              extra={""}
            >
              <ColorPicker showText />
            </Form.Item>

            <Form.Item<FieldType>
              label="LOGO尺寸(px)"
              name="logo_size"
              extra={"默认84，最大180（推荐宽高比为1:1的正方形LOGO）"}
            >
              <InputNumber
                min={0}
                max={180}
                formatter={(value) => `${value}px`}
              />
            </Form.Item>

            <Form.Item<FieldType> label="顶部LOGO" name="top_logo" extra={""}>
              <SelectImage />
            </Form.Item>

            <Form.Item<FieldType>
              label="文字背景图"
              name="background_img"
              extra={""}
            >
              <SelectImage />
            </Form.Item>
          </>
        )}
      </Form>
    </SettingsSection>
  );
};

export default App;
