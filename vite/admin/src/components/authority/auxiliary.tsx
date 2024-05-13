//权限 - 辅助功能
import React from "react";
import { useState, useContext, useEffect } from "react";
import { Switch, Form, Input,  } from "antd";
import DataContext from "@/tool/dataContext";
import { AuthorityAuxiliary } from "@/tool/interface";
import defaultVar from "@/tool/defaultVar";
import { AntConfig } from "@/tool/tool";

//选项类型
type FieldType = AuthorityAuxiliary;

//Ant 组件配置
const fromConfig = AntConfig.from;

const App: React.FC = () => {
  //拿到值
  const optionObj = useContext(DataContext) ?? { authority: {} };

  //简化并提供默认值
  let publicData =
    optionObj.authority?.auxiliary || defaultVar.authority.auxiliary;

  //创建变量并设默认值
  const [formData, setFormData] = useState(publicData || {});

  //表单同步修改值
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
    //由于选项site可能不存在，这里需要使用复制来新建
    optionObj.authority = {
      ...optionObj.authority,
      auxiliary: formData,
    };
  }, [formData]);

  const { TextArea } = Input;
  return (
    <>
      <Form
        name="auxiliary"
        labelCol={{ span: fromConfig.labelCol }}
        wrapperCol={{ span: fromConfig.wrapperCol }}
        style={{ maxWidth: fromConfig.maxWidth }}
        //表单默认值，只有初始化以及重置时生效
        initialValues={publicData}
        //自动填充功能禁用
        autoComplete="off"
        //指定当表单提交时要执行的回调函数
        onFinish={() => {}}
        //指定当表单字段值发生变化时要执行的回调函数
        onValuesChange={onValuesChange}
      >
        <Form.Item>
          <h2>辅助功能</h2>
        </Form.Item>

        <Form.Item<FieldType>
          label="文章统计"
          name="single_count"
          valuePropName="checked"
          extra={"开启后显示在仪表盘下"}
        >
          <Switch />
        </Form.Item>

        <Form.Item<FieldType>
          label="屏蔽恶意关键词搜索"
          name="no_malice_key"
          valuePropName="checked"
          extra={"禁止搜索指定词汇"}
        >
          <Switch />
        </Form.Item>
        {formData.no_malice_key && (
          <Form.Item<FieldType>
            label="输入关键词"
            name="malice_keu_content"
            extra={"输入您的关键词，以“回车键”分隔，一行一个"}
          >
            <TextArea rows={4} placeholder="一行一个" />
          </Form.Item>
        )}

        

       
       
      </Form>
    </>
  );
};

export default App;
