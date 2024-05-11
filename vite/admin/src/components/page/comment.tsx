/**
 * 页面优化 - 评论
 */
import { useState, useContext, useEffect } from "react";
import { Form,  Switch } from "antd";
import DataContext from "@/tool/dataContext";
import { StylePage } from "@/tool/interface";
import defaultVar from "@/tool/defaultVar";
import { AntConfig } from "@/tool/tool";

type FieldType = StylePage;

//Ant 组件配置
const fromConfig = AntConfig.from;

const App: React.FC = () => {
  //准备默认值
  const optionObj = useContext(DataContext) ?? { style: {} };
  const publicData = optionObj.style?.page || defaultVar.style.page;

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

  //修改公共值
  useEffect(() => {
    optionObj.style = {
      ...optionObj.style,
      page: formData,
    };
  }, [formData]);

  return (
    <>
      <Form
        name="page"
        labelCol={{ span: fromConfig.labelCol }}
        wrapperCol={{ span: fromConfig.wrapperCol }}
        style={{ maxWidth: fromConfig.maxWidth }}
        initialValues={publicData}
        autoComplete="off"
        onFinish={() => {}}
        onValuesChange={onValuesChange}
      >
        <Form.Item>
          <h2>其他特效</h2>
        </Form.Item>
        

        <Form.Item<FieldType>
          label="彩色背景标签云"
          name="color_tag"
          valuePropName="checked"
          extra={"可在小工具中添加圆角彩色背景标签云，前台即可看到效果"}
        >
          <Switch />
        </Form.Item>

       

        <Form.Item<FieldType>
          label="添加OWO表情包"
          name="comment_emote"
          valuePropName="checked"
          extra={"评论区添加OWO表情包"}
        >
          <Switch />
        </Form.Item>
      </Form>
    </>
  );
};

export default App;
