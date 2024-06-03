//基础组件 - 选中媒体库图片
import { Input, Space, Button, Modal, List, Image } from "antd";
import { useState } from "react";
const SelectImage: React.FC = (props: any) => {
  //弹窗
  const [isModalOpen, setIsModalOpen] = useState(false);

  //媒体图片
  const [mediaImage, setMediaImage] = useState<any>([]);

  const getMediaData = async () => {
    //准备网址
    const site = url_site + "/wp-json/wp/v2/media";

    try {
      const response = await axios.get(site);
        console.log(response.data);
      //取前10张图片
      const data = response.data.slice(0, 8);
      setMediaImage(data);
    } catch (error: any) {
      console.error("出错：" + error.message);
    }
  };

  //打开弹窗
  const showModal = () => {
    setIsModalOpen(true);
    //获取数据并存储
    getMediaData();
  };
  const handleOk = () => {
    setIsModalOpen(false);
    console.log("确定");
  };

  const handleCancel = () => {
    setIsModalOpen(false);
    console.log("取消");
  };
  //接收传来的值
  return (
    <>
      <Space.Compact style={{ width: "100%" }}>
        <Input {...props} placeholder="图片地址" />
        <Button onClick={showModal}>选择</Button>
      </Space.Compact>

      <Modal
        title="选择您需要的图片"
        open={isModalOpen}
        onOk={handleOk}
        onCancel={handleCancel}
      >
        <List
          grid={{
            gutter: 16,
            xs: 1,
            sm: 2,
            md: 4,
            lg: 4,
            xl: 6,
            xxl: 3,
          }}
          dataSource={mediaImage}
          renderItem={(item: any) => (
            <List.Item>
              <Image  alt={item.slug} src={item.source_url} />
            </List.Item>
          )}
        />
      </Modal>
    </>
  );
};

//获取媒体库数据
import axios from "axios";
import { url_site } from "@/tool/dataContext";

export default SelectImage;
