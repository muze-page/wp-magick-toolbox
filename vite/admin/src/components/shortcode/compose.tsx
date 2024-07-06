/**
 * 页面优化 - 评论
 */
import { useState, useContext, useEffect } from "react";
import { Form, Switch } from "antd";
import { DataContext } from "@/tool/dataContext";
import { CodeCompose } from "@/tool/interface";
import { defaultVarOption } from "@/tool/defaultVar";
import { AntConfig } from "@/tool/tool";

type FieldType = CodeCompose;

//Ant 组件配置
const fromConfig = AntConfig.from;

const App: React.FC = () => {
  //拿到默认选项值和修改方法
  const { optionData, updateOption } = useContext(DataContext);
  const publicData = optionData.shortcode?.compose || defaultVarOption.shortcode.compose;

  //存储表单值
  const [formData, setFormData] = useState(publicData || {});

  //修改表单值
  const onValuesChange = (
    changedValues: Partial<FieldType>,
    _allValues: FieldType
  ) => {
    setFormData((prevState) => ({
      ...prevState,
      ...changedValues,
    }));
  };

  //表单值发生变化时更新选项值
  useEffect(() => {
    updateOption("shortcode", "compose", formData);
  }, [formData]);

  return (
    <>
      <Form
        name="compose"
        labelCol={{ span: fromConfig.labelCol }}
        wrapperCol={{ span: fromConfig.wrapperCol }}
        style={{ maxWidth: fromConfig.maxWidth }}
        initialValues={publicData}
        autoComplete="off"
        onFinish={() => {}}
        onValuesChange={onValuesChange}
      >
        <Form.Item>
          <h2>板式</h2>
        </Form.Item>

        <Form.Item<FieldType>
          label="文章列表"
          name="single_list"
          valuePropName="checked"
          extra={"填写若干文章 ID 就能生成漂亮的文章列表"}
        >
          <Switch />
        </Form.Item>
      </Form>
    </>
  );
};

export default App;
