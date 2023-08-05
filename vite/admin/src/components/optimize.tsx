//优化菜单
import React from "react";
import { useState, useContext, useEffect } from "react";
import {  Switch, Form, Input, InputNumber } from "antd";
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
  const [FormData, setFormData] = useState(optionObj.option || {});

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

  //打印修改后的值
  const printData = (value: FieldType) => {
    console.log(value);
  };

  useEffect(() => {
    // 表单值发生变化时更新dataContext的值
    optionObj.option = FormData;
  }, [FormData]);

  

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
        
      </Form>

     
    </>
  );
};

export default App;
