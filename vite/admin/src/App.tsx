import "./App.css";
import { Layout, Affix } from "antd";
import Tab from "./components/tab";
import Save from "./tool/save";
import React from "react";

const { Header, Footer, Content } = Layout;

const headerStyle: React.CSSProperties = {
  display: "flex",
  justifyContent: "space-between",
  alignItems: "center",
  height: 64,
  paddingInline: 48,
  lineHeight: "64px",
  borderBottom: "1px solid #ccd0d4",
  background: "linear-gradient(#fefefe, #f5f5f5)",
};

const footerStyle: React.CSSProperties = {
  float: "right",
  borderBottom: "1px solid #ccd0d4",
  background: "linear-gradient(#fefefe, #f5f5f5)",
};
const App: React.FC = () => {
  return (
    <div className="mami_option">
      <Layout>
        <Affix offsetTop={20}>
          <Header style={headerStyle}>
            <HeaderBlock />
          </Header>
        </Affix>
        <Content>
          <Tab />
        </Content>
        <Footer style={footerStyle}>
          <div className="float-right">
            <Save />
          </div>
        </Footer>
      </Layout>
    </div>
  );
};

const HeaderBlock: React.FC = () => {
  return (
    <>
      <h1 className="text-2xl leading-7 font-medium">
       魔法优化
        <small className="text-xs font-light text-gray-400 ml-2 ">
          For Npcink
        </small>
      </h1>
      <Save />
    </>
  );
};

export default App;
