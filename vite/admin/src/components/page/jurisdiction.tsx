/**
 * 页面优化 - 权限
 */
import { useState, useContext, useEffect } from "react";
import { Form, Select, Input, Radio } from "antd";
import { DataContext } from "@/tool/dataContext";
import { PageJurisdiction, ListData } from "@/tool/interface";
import { defaultVarOption } from "@/tool/defaultVar";
import { AntConfig } from "@/tool/tool";
import { getCategoryData } from "@/axios/axios";
import TextAreaHtml from "@/basic/htmlInput";
import FeatureSwitch from "@/basic/feature-switch";
import { SettingsSection, ModuleRow } from "@/components/settings-ui";

type FieldType = PageJurisdiction;

const fromConfig = AntConfig.from;

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);
  const publicData =
    optionData.page?.jurisdiction || defaultVarOption.page.jurisdiction;
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
    updateOption("page", "jurisdiction", formData);
  }, [formData]);

  interface TagData {
    categorys: ListData[];
    tags: ListData[];
    pages: ListData[];
  }
  const [tagArray, setTagArray] = useState<TagData>();
  const getData = async () => {
    try {
      const list = await getCategoryData();
      setTagArray(list);
    } catch (error) {
      console.error("Error fetching table data:", error);
    }
  };
  useEffect(() => {
    getData();
  }, []);

  return (
    <SettingsSection title="权限">
      <Form
        name="jurisdiction"
        labelCol={fromConfig.labelCol}
        wrapperCol={fromConfig.wrapperCol}
        style={{ maxWidth: fromConfig.maxWidth }}
        initialValues={publicData}
        autoComplete="off"
        onFinish={() => {}}
        onValuesChange={onValuesChange}
      >
        <h3 className="menu-header">隐私权限</h3>
        <ModuleRow
          title="禁止在微信中打开"
          description="可能有防红功能"
          featureId="page-jurisdiction-ban_open_weixing"
          enabled={formData.ban_open_weixing as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ ban_open_weixing: checked } as Partial<FieldType>, formData);
          }}
        >
          <Form.Item<FieldType>
            label="处理方式"
            name="ban_open_weixing_mode"
          >
            <Radio.Group>
              <Radio value="alert">弹窗提示</Radio>
              <Radio value="optimize">优化体验+引导</Radio>
            </Radio.Group>
          </Form.Item>
          {formData.ban_open_weixing_mode === 'optimize' && (
            <>
              <Form.Item<FieldType> label="引导文字" name="wechat_guide_text">
                <Input style={{ width: "70%" }} placeholder="点击右上角 ··· 在浏览器中打开" />
              </Form.Item>
              <Form.Item<FieldType>
                label="小程序引导"
                name="wechat_xcx_guide"
                valuePropName="checked"
              >
                <FeatureSwitch featureId="page-jurisdiction-wechat_xcx_guide" />
              </Form.Item>
              {formData.wechat_xcx_guide && (
                <>
                  <Form.Item<FieldType> label="小程序引导文字" name="wechat_xcx_guide_text">
                    <Input style={{ width: "50%" }} placeholder="在小程序中打开" />
                  </Form.Item>
                  <Form.Item<FieldType> label="小程序链接" name="wechat_xcx_link">
                    <Input style={{ width: "70%" }} placeholder="weixin://dl/business/..." />
                  </Form.Item>
                </>
              )}
            </>
          )}
        </ModuleRow>
        <ModuleRow
          title="禁止在 QQ 中打开"
          description="可能有防红功能"
          featureId="page-jurisdiction-ban_open_qq"
          enabled={formData.ban_open_qq as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ ban_open_qq: checked } as Partial<FieldType>, formData);
          }}
        />
        <ModuleRow
          title="禁止复制"
          featureId="page-jurisdiction-ban_copy"
          enabled={formData.ban_copy as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ ban_copy: checked } as Partial<FieldType>, formData);
          }}
        />
        <ModuleRow
          title="禁用F12前端调试"
          description="打开浏览器控制台显示空白内容"
          featureId="page-jurisdiction-front_debug"
          enabled={formData.front_debug as boolean}
          onChange={(checked: boolean) => {
            onValuesChange({ front_debug: checked } as Partial<FieldType>, formData);
          }}
        />
        <h3 className="menu-header">未登录权限</h3>

        <Form.Item<FieldType>
          label="隐藏指定分类下的内容"
          name="category_id"
          extra={"该分类下的内容未登录时，不可见，仅展示提示内容"}
        >
          <Select
            mode="multiple"
            allowClear
            style={{ width: "100%" }}
            placeholder="请选择要隐藏的分类"
            options={tagArray?.categorys}
          />
        </Form.Item>
        <Form.Item<FieldType>
          label="隐藏指定标签下的内容"
          name="tag_id"
          extra={"该标签下的内容未登录时，不可见，仅展示提示内容"}
        >
          <Select
            mode="multiple"
            allowClear
            style={{ width: "100%" }}
            placeholder="请选择要隐藏的标签"
            options={tagArray?.tags}
          />
        </Form.Item>
        <Form.Item<FieldType>
          label="隐藏指定页面"
          name="page_id"
          extra={"该页面下的内容未登录时，不可见，仅展示提示内容"}
        >
          <Select
            mode="multiple"
            allowClear
            style={{ width: "100%" }}
            placeholder="请选择要隐藏的页面"
            options={tagArray?.pages}
          />
        </Form.Item>
        <Form.Item<FieldType>
          label="隐藏时的提示内容"
          name="tip_content"
          extra={"内容被隐藏时的提示内容，支持HTML"}
        >
          <TextAreaHtml />
        </Form.Item>
      </Form>
    </SettingsSection>
  );
};

export default App;
