import { useContext, useState, useEffect } from "react";
import { Button, Space, message } from "antd";
import { DataContext } from "@/tool/dataContext";
import { saceOption } from "@/axios/save";
import { createSnapshot } from "@/tool/snapshot";
import { UpOutlined } from "@ant-design/icons";

const App: React.FC = () => {
  const { optionData } = useContext(DataContext);
  const [saving, setSaving] = useState(false);

  const postData = async () => {
    setSaving(true);
    try {
      createSnapshot(optionData);
      await saceOption(optionData);
    } catch (error) {
      message.error("保存失败，请重试");
    } finally {
      setSaving(false);
    }
  };

  //返回顶部
  const [showButton, setShowButton] = useState(false);
  useEffect(() => {
    const handleScroll = () => {
      // 获取当前滚动的垂直距离
      const scrollY = window.scrollY || window.pageYOffset;
      // 设置一个阈值，例如 50vh，即页面高度的一半
      const threshold = window.innerHeight * 0.5;

      if (scrollY > threshold) {
        setShowButton(true);
      } else {
        setShowButton(false);
      }
    };

    window.addEventListener("scroll", handleScroll);
    return () => {
      window.removeEventListener("scroll", handleScroll);
    };
  }, []);

  const handleButtonClick = () => {
    // 滚动到页面顶部
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  return (
    <div>
      <Space size={"large"}>
        {showButton && (
          <Button
            type="text"
            shape="circle"
            onClick={handleButtonClick}
            icon={<UpOutlined />}
          ></Button>
        )}
        <Button type="primary" onClick={postData} loading={saving}>
          保存
        </Button>
      </Space>
    </div>
  );
};

export default App;