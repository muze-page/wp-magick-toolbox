//准备内容

import Pictorial from "@/assets/share/画报.svg";
import CopyLink from "@/assets/share/链接.svg";
import WeXin from "@/assets/share/微信.svg";
import Mail from "@/assets/share/邮件.svg";
import WeiBo from "@/assets/share/微博.svg";
import Qzone from "@/assets/share/QQ空间.svg";
import Facebook from "@/assets/share/Facebook.svg";
import X from "@/assets/share/X.svg";

import { message, QRCode } from "antd";
import { ScanOutlined } from "@ant-design/icons";
const App: React.FC = () => {
  //准备当前网页链接
  const url_site = window.location.href;
  //复制当前链接
  const copyLink = () => {
    navigator.clipboard.writeText(url_site).then(() => {
      message.info("链接已复制到剪贴板");
    });
  };

  //生成二维码
  const [messageApi, contextHolder] = message.useMessage();
  const qrCode = () => {
    messageApi.open({
      type: "success",
      content: (
        <>
          <QRCode errorLevel="H" value={url_site} icon={WeXin} />
          <span>微信扫一扫浏览本页</span>
        </>
      ),
      duration: 3, //10秒后自动关闭
      icon: (
        <ScanOutlined
          style={{ fontSize: "32px", color: "#000", display: "none" }}
        />
      ),
      style: {
        marginTop: "20vh",
      },
    });
  };

  return (
    <>
      {contextHolder}
      <section className="site-sharing-container site-overlay opened">
        <div className="site-sharing-content">
          <span className="title">分享</span>
          <ul>
            <li>
              <span className="icon">
                <img src={Pictorial} />
              </span>
              <span className="title">创建画报</span>
            </li>
            <li onClick={copyLink}>
              <span className="icon">
                <img src={CopyLink} />
              </span>
              <span className="title">复制链接</span>
            </li>
            <li onClick={qrCode}>
              <span className="icon">
                <img src={WeXin} />
              </span>
              <span className="title">微信</span>
            </li>
            <li>
              <span className="icon">
                <img src={Mail} />
              </span>
              <span className="title">邮件</span>
            </li>
            <li>
              <span className="icon">
                <img src={WeiBo} />
              </span>
              <span className="title">微博</span>
            </li>
            <li>
              <span className="icon">
                <img src={Qzone} />
              </span>
              <span className="title">QQ 空间</span>
            </li>
            <li>
              <span className="icon">
                <img src={Facebook} />
              </span>
              <span className="title">Facebook</span>
            </li>
            <li>
              <span className="icon">
                <img src={X} />
              </span>
              <span className="title">X</span>
            </li>
          </ul>
        </div>
      </section>
    </>
  );
};
export default App;
