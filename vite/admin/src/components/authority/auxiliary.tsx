//权限 - 辅助功能
import React from "react";
import { useState, useContext, useEffect, useRef } from "react";
import { Switch, Form, Input, Button, Space } from "antd";
import DataContext from "@/tool/dataContext";
import { AuthorityAuxiliary } from "@/tool/interface";
import defaultVar from "@/tool/defaultVar";
import { AntConfig } from "@/tool/tool";

//选项类型
type FieldType = AuthorityAuxiliary;

//Ant 组件配置
const fromConfig = AntConfig.from;

//多行输入
const { TextArea } = Input;

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

  //提取百度统计标识符
  const handleValueChange = (e: { target: { value: any } }) => {
    let value = e.target.value;
    let regex = /hm\.js\?([A-Za-z0-9]+)/;
    let match = value.match(regex);

    return match[1];
  };

  //提取谷歌或必应标识符
  const extract = (e: { target: { value: any } }) => {
    let value = e.target.value;
    let regex = /content="([A-Za-z0-9]+)"/;
    let match = value.match(regex);
    return match[1];
  };

  //重置必应统计
  const tongji_reset = (name: string) => {
    const updatedFormData = {
      ...formData,
      [name]: "",
      // 其他需要修改的属性和值
      uniqueKey: Math.random(),// 添加一个随机数作为唯一标识符
    };
    setFormData(updatedFormData);//更新传输的值
    form.setFieldsValue(updatedFormData); // 更新表单中的值
  };
  const [form] = Form.useForm();
  return (
    <>
      <Form
        form={form}
        name="auxiliary"
        labelCol={{ span: fromConfig.labelCol }}
        wrapperCol={{ span: fromConfig.wrapperCol }}
        style={{ maxWidth: fromConfig.maxWidth }}
        //表单默认值，只有初始化以及重置时生效
        initialValues={formData}
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
        {/**TODO:处理script标签 */}
        <Form.Item<FieldType>
          label="百度统计"
          name="baidu_tonji"
          getValueFromEvent={handleValueChange}
          extra={
            <p>
              <a
                href="https://tongji.baidu.com/main/setting/self/home/site/index"
                target="_blank"
              >
                百度统计
              </a>
              → 代码管理（左侧） → 代码获取 → 获取代码 →
              复制代码贴入输入框中并保存即可
            </p>
          }
        >
          <SiteInput
            tongji_reset={tongji_reset}
            name={"baidu_tonji"}
          />
        </Form.Item>
        <Form.Item<FieldType>
          label="谷歌统计"
          name="google_tonji"
          getValueFromEvent={extract}
          extra={
            <p>
              <a
                href="https://search.google.com/search-console/about"
                target="_blank"
              >
                谷歌统计
              </a>
              ：
              <pre className="pre-meat">
                &lt;meta name="google-site-verification" content="HB..." /&gt;
              </pre>
            </p>
          }
        >
          <SiteInput
            tongji_reset={tongji_reset}
            name={"google_tonji"}
          />
        </Form.Item>
        <Form.Item<FieldType>
          label="必应统计"
          name="biying_tonji"
          getValueFromEvent={extract}
          extra={
            <p>
              <a href="https://www.bing.com/webmasters" target="_blank">
                必应统计
              </a>
              ：
              <pre className="pre-meat">
                &lt;meta name="msvalidate.01" content="CF..." /&gt;
              </pre>
            </p>
          }
        >
          <SiteInput
            tongji_reset={tongji_reset}
            name={"biying_tonji"}
          />
        </Form.Item>
      </Form>
    </>
  );
};

//网址输入框
const SiteInput = (props: any) => {
  const inputRef = useRef(null);

  //不能直接执行，得用函数装起来
  const handleReset = () => {
    props.tongji_reset(props.name);//更新传出的值
  };

  return (
    <div>
      <Space.Compact style={{ width: "100%" }}>
        <Input ref={inputRef} {...props} placeholder="自动处理网址" />
        <Button onClick={handleReset}>清空</Button>
      </Space.Compact>
    </div>
  );
};

export default App;
