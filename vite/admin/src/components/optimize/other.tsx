//其他
import React from "react";
import { useState, useContext, useEffect } from "react";
import { Switch, Form } from "antd";
import DataContext from "@/tool/dataContext";
import { OptimizeOther } from "@/tool/interface";
import defaultVar from "@/tool/defaultVar";

//选项类型
type FieldType = OptimizeOther;

const App: React.FC = () => {
  //拿到值
  const optionObj = useContext(DataContext) ?? { optimize: {} };

  //简化
  let publicData = optionObj.optimize.other;

  //提供默认值
  if (!publicData) {
    publicData = defaultVar.optimize.other;
  }

  //创建变量并设默认值
  const [FormData, setFormData] = useState(publicData || {});

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

  // 表单值发生变化时更新dataContext的值
  useEffect(() => {
    optionObj.optimize.other = FormData;
  }, [FormData]);

  return (
    <>
      <Form
        name="other"
        labelCol={{ span: 8 }}
        wrapperCol={{ span: 16 }}
        style={{ maxWidth: 800 }}
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
          <h2>其他</h2>
        </Form.Item>

        <Form.Item<FieldType>
          label="文章菜单添加作者筛选项"
          name="add_user"
          valuePropName="checked"
        >
          <Switch />
        </Form.Item>
        <Form.Item<FieldType>
          label="文章和媒体菜单添加时间筛选项"
          name="add_time"
          valuePropName="checked"
          extra={"媒体菜单需为列表布局"}
        >
          <Switch />
        </Form.Item>
        <Form.Item<FieldType>
          label="各个列表显示链接ID"
          name="show_id"
          valuePropName="checked"
          extra={"支持 文章、页面、链接、多媒体、评论、分类、标签、用户 等"}
        >
          <Switch />
        </Form.Item>
      </Form>
    </>
  );
};

export default App;
