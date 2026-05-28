import React from "react";
import { useState, useContext, useEffect } from "react";
import { Form, Input, Collapse } from "antd";
import type { CollapseProps } from "antd";
import { DataContext } from "@/tool/dataContext";
import { FunctionWxXcx } from "@/tool/interface";
import { defaultVarOption } from "@/tool/defaultVar";
import { AntConfig } from "@/tool/tool";
import { SettingsSection, ModuleRow } from "@/components/settings-ui";

type FieldType = FunctionWxXcx;

const fromConfig = AntConfig.from;

const link = (
  <ul className="list-disc ml-4">
    <li>access_token 的有效期目前为 2 个小时</li>
    <li>每天生成 URL Scheme 总数量上限为50万</li>
    <li>URL Scheme有效期最长 30 天</li>
    <li>
      每个独立的URL Scheme被用户访问后，仅此用户可以再次访问并打开对应小程序
    </li>
    <li>针对非个人主体小程序开放</li>
  </ul>
);
const text = (
  <ul className="list-decimal ml-4">
    <li>配置上述设置信息</li>
    <li>新建页面，选择对应提示的页面模版</li>
    <li>填写标题和页面内容即可，会自动添加跳转按钮，</li>
    <li>检测到移动端会自动申请打开微信客户端</li>
  </ul>
);
const achieve = (
  <ul className="list-disc ml-4">
    <li>本作者阅读官方文档后实现</li>
    <li>实现细节见下方链接</li>
    <li>
      <a href="https://www.npc.ink/276458.html" target="_blank">
        开发微信小程序的URL Scheme
      </a>
    </li>
  </ul>
);
const items: CollapseProps["items"] = [
  {
    key: "1",
    label: "小程序要求",
    children: <p>{link}</p>,
  },
  {
    key: "2",
    label: "跳转页使用",
    children: <p>{text}</p>,
  },
  {
    key: "3",
    label: "实现细节",
    children: <p>{achieve}</p>,
  },
];

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);

  const publicData =
    optionData.function?.wx_xcx || defaultVarOption.function.wx_xcx;

  const [formData, setFormData] = useState(publicData);

  const onValuesChange = (changedValues: Partial<FieldType>) => {
    setFormData((prevFormData) => ({
      ...prevFormData,
      ...changedValues,
    }));
  };

  useEffect(() => {
    updateOption("function", "wx_xcx", formData);
  }, [formData]);

  return (
    <SettingsSection title="微信小程序链接生成">
      <Form
        name="wx_xcx"
        labelCol={fromConfig.labelCol}
        wrapperCol={fromConfig.wrapperCol}
        style={{ maxWidth: fromConfig.maxWidth }}
        initialValues={publicData}
        autoComplete="off"
        onFinish={() => {}}
        onValuesChange={onValuesChange}
      >
        <ModuleRow
          title="是否启用"
          description="生成小程序跳转指定页面链接功能"
          featureId="function-wx_xcx-active"
          enabled={formData.active as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ active: checked });
          }}
        >
          <Form.Item<FieldType>
            label="AppId"
            name="appid"
            extra={
              <p>
                微信小程序 - 开发管理 - 开发设置，
                <a
                  href="https://mp.weixin.qq.com/wxamp/devprofile/get_profile?token=858704879&lang=zh_CN"
                  target="_blank"
                >
                  前往微信小程序
                </a>
              </p>
            }
          >
            <Input.Password />
          </Form.Item>
          <Form.Item<FieldType>
            label="AppSecret"
            name="secret"
            extra={
              <p>
                微信小程序 - 开发管理 - 开发设置，
                <a
                  href="https://mp.weixin.qq.com/wxamp/devprofile/get_profile?token=858704879&lang=zh_CN"
                  target="_blank"
                >
                  前往微信小程序
                </a>
              </p>
            }
          >
            <Input.Password />
          </Form.Item>
          <Form.Item<FieldType>
            label="网址"
            name="site"
            extra={
              <p>
                小程序中跳转的外部网址，例如
                <pre className="pre-meat">
                  https://www.npc.ink/300485.html
                </pre>
              </p>
            }
          >
            <Input />
          </Form.Item>

          <Form.Item<FieldType>
            label="路径参数"
            name="path"
            extra={
              <>
                需跳转的页面，
                <p>
                  例如
                  <pre className="pre-meat">pages/circle/index.html</pre>
                  则填写
                  <pre className="pre-meat">pages/circle</pre>
                </p>
                <p>
                  例如
                  <pre className="pre-meat">
                    pages/single/post.html?id=300485
                  </pre>
                  则填写
                  <pre className="pre-meat">pages/single/post</pre>
                </p>
              </>
            }
          >
            <Input />
          </Form.Item>
          <Form.Item<FieldType>
            label="查询参数"
            name="query"
            extra={
              <p>
                需跳转的指定页面，例如
                <pre className="pre-meat">
                  pages/single/post.html?id=300485
                </pre>
                则填写
                <pre className="pre-meat">id=300485</pre>
              </p>
            }
          >
            <Input />
          </Form.Item>
          <Form.Item<FieldType>
            label="介绍"
            extra={
              <p>
                此选项会添加接口，供自定义页面或其他页面调用，地址如下
                <pre className="pre-meat">您的网址/wp-json/wx_xcx/v1/qy</pre>
              </p>
            }
          >
            <Collapse accordion items={items} bordered={false} />
          </Form.Item>
        </ModuleRow>
      </Form>
    </SettingsSection>
  );
};

export default App;
