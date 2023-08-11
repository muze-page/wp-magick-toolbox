//页面特效
import React from "react";
import { useState, useContext, useEffect } from "react";
import { Switch, Form, ColorPicker, Input, InputNumber } from "antd";
import { FileImageOutlined } from "@ant-design/icons";
import DataContext from "@/tool/dataContext";
import { StylePage } from "@/tool/interface";
import defaultVar from "@/tool/defaultVar";

//选项类型
type FieldType = StylePage;

const App: React.FC = () => {
  //拿到值
  const optionObj = useContext(DataContext) ?? { style: {} };

  //简化并提供默认值
  let publicData = optionObj.style?.page || defaultVar.style.page;

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

  // 表单值发生变化时更新dataContext的值
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
          <h2>特效</h2>
        </Form.Item>

        <Form.Item<FieldType>
          label="添加粒子特效"
          name="particle"
          valuePropName="checked"
          extra={"考虑到性能以及操作问题，移动端不加载此特效"}
        >
          <Switch />
        </Form.Item>
        <Form.Item<FieldType>
          label="添加圆角彩色背景标签云"
          name="color_tag"
          valuePropName="checked"
          extra={"可在小工具中添加标签云，前台即可看到效果"}
        >
          <Switch />
        </Form.Item>
        <Form.Item<FieldType>
          label="评论区添加OWO表情包"
          name="comment_emote"
          valuePropName="checked"
          extra={""}
        >
          <Switch />
        </Form.Item>

        <Form.Item<FieldType>
          label="自定义登录页"
          name="custom_login_page"
          valuePropName="checked"
          extra={""}
        >
          <Switch />
        </Form.Item>

        {/**
         * TODO:解决颜色hec格式问题
         */}
        {formData.custom_login_page && (
          <>
            <Form.Item<FieldType>
              label="左下角颜色"
              name="background_left"
              extra={""}
            >
              <ColorPicker />
            </Form.Item>
            <Form.Item<FieldType>
              label="右上角颜色"
              name="background_right"
              extra={""}
            >
              <ColorPicker />
            </Form.Item>

            <Form.Item<FieldType>
              label="LOGO尺寸(px)"
              name="logo_size"
              extra={""}
            >
              <InputNumber />
            </Form.Item>

            <Form.Item<FieldType> label="顶部LOGO" name="top_logo" extra={""}>
              <Input
                addonBefore={<FileImageOutlined />}
                placeholder="图片网址"
              />
            </Form.Item>

            <Form.Item<FieldType>
              label="文字背景图"
              name="background_img"
              extra={""}
            >
              <Input
                addonBefore={<FileImageOutlined />}
                placeholder="图片网址"
              />
            </Form.Item>
          </>
        )}
      </Form>
    </>
  );
};

export default App;
