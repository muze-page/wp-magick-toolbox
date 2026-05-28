import { useState, useContext, useEffect } from "react";
import { Input } from "antd";
import { DataContext } from "@/tool/dataContext";
import { CodeCompose } from "@/tool/interface";
import { defaultVarOption } from "@/tool/defaultVar";
import { ModuleCard } from "@/components/settings-ui";
import Runcode from "@/assets/shortcode/compose/运行代码.png";
import SingleList from "@/assets/shortcode/compose/文章列表.png";
import CopyBtn from "@/assets/shortcode/compose/复制按钮.png";

type FieldType = CodeCompose;

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);
  const publicData =
    optionData.shortcode?.compose || defaultVarOption.shortcode.compose;

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
    updateOption("shortcode", "compose", formData);
  }, [formData]);

  return (
    <div className="mabox-module-grid">
      <ModuleCard
        title="文章列表"
        description="填写若干文章 ID 就能生成漂亮的文章列表"
        featureId="shortcode-compose-single_list"
        enabled={formData.single_list as boolean}
        onChange={(checked: boolean) => {
          onValuesChange({ single_list: checked } as Partial<FieldType>, formData);
        }}
        preview={{ title: "文章列表", img: SingleList }}
      />

      <ModuleCard
        title="复制按钮"
        description="第一个属性是按钮名称，第二个属性是弹窗内容，第三个属性是跳转网址"
        featureId="shortcode-compose-single_copy"
        enabled={formData.single_copy as boolean}
        onChange={(checked: boolean) => {
          onValuesChange({ single_copy: checked } as Partial<FieldType>, formData);
        }}
        preview={{ title: "复制按钮", img: CopyBtn }}
      />

      <ModuleCard
        title="前端运行代码"
        description="仅支持经典编辑器，[runcode]和[/runcode]不能换行，会有换行符"
        featureId="shortcode-compose-runcode"
        enabled={formData.runcode as boolean}
        onChange={(checked: boolean) => {
          onValuesChange({ runcode: checked } as Partial<FieldType>, formData);
        }}
        preview={{ title: "在线运行前端代码", img: Runcode }}
        tags={["经典编辑器"]}
      />

      <ModuleCard
        title="Bilibili 视频"
        description='使用 [mabox_bilibili bvid="BV号"] 嵌入 B 站视频，无广告播放'
        featureId="shortcode-compose-bilibili"
        enabled={formData.bilibili as boolean}
        onChange={(checked: boolean) => {
          onValuesChange({ bilibili: checked } as Partial<FieldType>, formData);
        }}
      />

      <ModuleCard
        title="公众号解锁"
        description="用户输入验证码后解锁隐藏内容，用于公众号引流"
        featureId="shortcode-compose-wx_unlock"
        enabled={formData.wx_unlock as boolean}
        onChange={(checked: boolean) => {
          onValuesChange({ wx_unlock: checked } as Partial<FieldType>, formData);
        }}
      >
        <div style={{ display: "flex", flexDirection: "column", gap: 12 }}>
          <div>
            <div style={{ fontSize: 13, color: "#666", marginBottom: 4 }}>公众号名称</div>
            <Input
              style={{ width: "50%" }}
              placeholder="例如：NPCink"
              value={formData.wx_unlock_name as string}
              onChange={(e) =>
                onValuesChange({ wx_unlock_name: e.target.value } as Partial<FieldType>, formData)
              }
            />
          </div>
          <div>
            <div style={{ fontSize: 13, color: "#666", marginBottom: 4 }}>公众号二维码</div>
            <Input
              style={{ width: "70%" }}
              placeholder="上传二维码后的图片地址"
              value={formData.wx_unlock_qrcode as string}
              onChange={(e) =>
                onValuesChange({ wx_unlock_qrcode: e.target.value } as Partial<FieldType>, formData)
              }
            />
          </div>
          <div>
            <div style={{ fontSize: 13, color: "#666", marginBottom: 4 }}>验证码列表</div>
            <Input.TextArea
              rows={4}
              placeholder={"ABC123\nDEF456"}
              value={formData.wx_unlock_codes as string}
              onChange={(e) =>
                onValuesChange({ wx_unlock_codes: e.target.value } as Partial<FieldType>, formData)
              }
            />
            <div style={{ fontSize: 12, color: "#999", marginTop: 4 }}>每行一个验证码，用户关注后发送关键词获取</div>
          </div>
          <div>
            <div style={{ fontSize: 13, color: "#666", marginBottom: 4 }}>解锁提示</div>
            <Input
              style={{ width: "70%" }}
              placeholder="关注公众号获取验证码"
              value={formData.wx_unlock_tip as string}
              onChange={(e) =>
                onValuesChange({ wx_unlock_tip: e.target.value } as Partial<FieldType>, formData)
              }
            />
          </div>
          <div>
            <div style={{ fontSize: 13, color: "#666", marginBottom: 4 }}>关键词提示</div>
            <Input
              style={{ width: "70%" }}
              placeholder="关注公众号，发送关键词获取验证码"
              value={formData.wx_unlock_keyword_tip as string}
              onChange={(e) =>
                onValuesChange({ wx_unlock_keyword_tip: e.target.value } as Partial<FieldType>, formData)
              }
            />
          </div>
        </div>
      </ModuleCard>

      <ModuleCard
        title="打赏模块"
        description="文章末尾添加打赏按钮，支持微信/支付宝收款码弹窗展示"
        featureId="shortcode-compose-reward"
        enabled={formData.reward as boolean}
        onChange={(checked: boolean) => {
          onValuesChange({ reward: checked } as Partial<FieldType>, formData);
        }}
      >
        <div style={{ display: "flex", flexDirection: "column", gap: 12 }}>
          <div>
            <div style={{ fontSize: 13, color: "#666", marginBottom: 4 }}>微信收款码</div>
            <Input
              style={{ width: "70%" }}
              placeholder="上传微信收款码图片地址"
              value={formData.reward_wx_qr as string}
              onChange={(e) =>
                onValuesChange({ reward_wx_qr: e.target.value } as Partial<FieldType>, formData)
              }
            />
          </div>
          <div>
            <div style={{ fontSize: 13, color: "#666", marginBottom: 4 }}>支付宝收款码</div>
            <Input
              style={{ width: "70%" }}
              placeholder="上传支付宝收款码图片地址"
              value={formData.reward_ali_qr as string}
              onChange={(e) =>
                onValuesChange({ reward_ali_qr: e.target.value } as Partial<FieldType>, formData)
              }
            />
          </div>
          <div>
            <div style={{ fontSize: 13, color: "#666", marginBottom: 4 }}>弹窗标题</div>
            <Input
              style={{ width: "50%" }}
              placeholder="感谢您的支持"
              value={formData.reward_title as string}
              onChange={(e) =>
                onValuesChange({ reward_title: e.target.value } as Partial<FieldType>, formData)
              }
            />
          </div>
          <div>
            <div style={{ fontSize: 13, color: "#666", marginBottom: 4 }}>按钮文字</div>
            <Input
              style={{ width: "30%" }}
              placeholder="打赏"
              value={formData.reward_btn_text as string}
              onChange={(e) =>
                onValuesChange({ reward_btn_text: e.target.value } as Partial<FieldType>, formData)
              }
            />
          </div>
        </div>
      </ModuleCard>
    </div>
  );
};

export default App;
