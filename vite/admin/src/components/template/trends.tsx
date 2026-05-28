import { useState, useContext, useEffect } from "react";
import { Form } from "antd";
import { DataContext } from "@/tool/dataContext";
import { TemplateTrends } from "@/tool/interface";
import { defaultVarOption } from "@/tool/defaultVar";
import { AntConfig } from "@/tool/tool";
import { SettingsSection, ModuleRow } from "@/components/settings-ui";

type FieldType = TemplateTrends;

const fromConfig = AntConfig.from;

const templateItems = [
  {
    name: "专题列表",
    fieldName: "special" as const,
    featureId: "template-trends-special",
    description: "搜索包含指定关键词的标题组成列表",
  },
];

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);
  const publicData =
    optionData.template?.trends || defaultVarOption.template.trends;

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
    updateOption("template", "trends", formData);
  }, [formData]);

  return (
    <SettingsSection title="动态模板" description="启用对应模板后，在页面中可选择对应模板">
      <Form
        name="trends"
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
          />
        ))}
      </Form>
    </SettingsSection>
  );
};

export default App;
