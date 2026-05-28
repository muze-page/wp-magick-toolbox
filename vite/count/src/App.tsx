import "./App.css";
import SingleCount from "@/components/page/singleCount/index";
import { ConfigProvider } from "antd";
import zhCN from "antd/locale/zh_CN";
function App() {
  return (
    <ConfigProvider locale={zhCN}>
      <SingleCount />
    </ConfigProvider>
  );
}

export default App;