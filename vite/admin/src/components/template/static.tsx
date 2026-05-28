import { useState, useContext, useEffect } from "react";
import { Form } from "antd";
import { DataContext } from "@/tool/dataContext";
import { TemplateStatic } from "@/tool/interface";
import { defaultVarOption } from "@/tool/defaultVar";
import { AntConfig } from "@/tool/tool";
import TrianglePng from "@/assets/template/static/立体三角.png";
import { SettingsSection, ModuleRow } from "@/components/settings-ui";

type FieldType = TemplateStatic;

const fromConfig = AntConfig.from;

const templateItems = [
  {
    name: "立体三角",
    fieldName: "triangle" as const,
    featureId: "template-static-triangle",
    description: "展示高级质感的立体三角，底部是文章正文内容",
    preview: { title: "立体三角", img: TrianglePng },
  },
];

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);
  const publicData =
    optionData.template?.static || defaultVarOption.template.static;

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
    updateOption("template", "static", formData);
  }, [formData]);

  return (
    <SettingsSection title="静态模板" description="启用对应模板后，在页面中可选择对应模板">
      <Form
        name="static"
        labelCol={fromConfig.labelCol}
        wrapperCol={fromConfig.wrapperCol}
        style={{ maxWidth: fromConfig.maxWidth }}
        initialValues={publicData}
        autoComplete="off"
        onFinish={() => {}}
        onValuesChange={onValuesChange}
      >
        {templateItems.map((item) => (
          <ModuleRow
            key={item.fieldName}
            title={item.name}
            description={item.description}
            featureId={item.featureId}
            enabled={formData[item.fieldName] as boolean}
            onChange={(checked: boolean) => {
              onValuesChange({ [item.fieldName]: checked } as Partial<FieldType>, formData);
            }}
            preview={item.preview}
          />
        ))}
      </Form>
    </SettingsSection>
  );
};

export default App;
