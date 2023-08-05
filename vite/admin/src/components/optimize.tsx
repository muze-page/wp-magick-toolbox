//优化菜单
import React from "react";
import { useState, useContext } from "react";
import { Button, Switch, Form, Input, InputNumber } from "antd";
import DataContext from "../dataContext";
//选项类型
type FieldType = {
  name: string;
  age: number;
  handle: boolean;
};

const App: React.FC = () => {
  //拿到值
  const optionObj = useContext(DataContext);
  //创建变量并设默认值
  const [, setFormData] = useState<FieldType>(optionObj.option || {});

  //表单修改值
  const onValuesChange = (allValues: FieldType) => {
    setFormData(allValues);
  };

  //打印修改后的值
  const printData = (value: FieldType) => {
    console.log(value);
  };

  // 打印DataContext文件中的值
  const printDataContextValue = () => {
    console.log(DataContext?._currentValue.option);
  };

  return (
    <>
     
      <Form
        name="opt"
        labelCol={{ span: 12 }}
        wrapperCol={{ span: 8 }}
        style={{ maxWidth: 600 }}
        //表单默认值，只有初始化以及重置时生效
        initialValues={optionObj.option}
        //自动填充功能禁用
        autoComplete="off"
        //指定当表单提交时要执行的回调函数
        onFinish={printData}
        //指定当表单字段值发生变化时要执行的回调函数
        onValuesChange={onValuesChange}
      >
        <Form.Item>
          <h2>验证</h2>
        </Form.Item>

        <Form.Item<FieldType> label="姓名" name="name">
          <Input />
        </Form.Item>
        <Form.Item<FieldType> label="年龄" name="age">
          <InputNumber min={1} max={100} />
        </Form.Item>
        <Form.Item<FieldType>
          label="是否开启顶部显示"
          name="handle"
          valuePropName="checked"
        >
          <Switch />
        </Form.Item>
        <Form.Item wrapperCol={{ offset: 8, span: 16 }}>
          <Button type="primary" htmlType="submit">
            提交
          </Button>
        </Form.Item>
      </Form>
      <Button onClick={printData}>打印</Button>
      <Button onClick={printDataContextValue}>上下文</Button>
    </>
  );
};

export default App;
