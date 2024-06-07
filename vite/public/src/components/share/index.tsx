//分享
import { useState } from "react";
import { Drawer, Button } from "antd";
import { ShareAltOutlined } from "@ant-design/icons";
import ShareContent from "@/components/share/content";
import "@/components/share/index.css";

const App: React.FC = () => {
  const [open, setOpen] = useState(false);

  //开弹窗
  const showDrawer = () => {
    setOpen(true);
  };

  //关弹窗
  const onClose = () => {
    setOpen(false);
  };

  //准备样式

  const classNameNames = {
    content: "drawer_content",
  };

  //准备内容

  return (
    <>
      
      <Button
        shape="circle"
        icon={<ShareAltOutlined />}
        onClick={showDrawer}
        className="open_share"
      />

      <Drawer
        placement="bottom"
        closable={false}
        onClose={onClose}
        open={open}
        rootClassName="share"
        classNames={classNameNames}
      >
        <ShareContent toggleDrawer={onClose} />
      </Drawer>
    </>
  );
};

export default App;
