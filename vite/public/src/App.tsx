import reactLogo from "./assets/react.svg";
import viteLogo from "/vite.svg";
import "./App.css";
import Share from "@/components/share";

import { ConfigProvider } from "antd";

import zhCN from "antd/locale/zh_CN";

import { message } from "antd";

message.config({
  top: 50,

  duration: 2,

  maxCount: 3,

  rtl: true,

  prefixCls: "my-message",
});

function App() {
  return (
    <>
      <ConfigProvider locale={zhCN}>
        <div>
          <a href="https://vitejs.dev" target="_blank">
            <img src={viteLogo} className="logo" alt="Vite logo" />
          </a>
          <a href="https://react.dev" target="_blank">
            <img src={reactLogo} className="logo react" alt="React logo" />
          </a>
        </div>
        <Share />
      </ConfigProvider>
    </>
  );
}

export default App;
