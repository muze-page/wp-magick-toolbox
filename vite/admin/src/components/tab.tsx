import React from "react";

import { Tabs } from "antd";
import type { TabsProps } from "antd";
import Test from "./test";
import Optimize from "@/components/optimize/index";
import Style from "@/components/style/index";
import Authority from "@/components/authority/index";

const onChange = (key: string) => {
  console.log(key);
};

const items: TabsProps["items"] = [
  {
    key: "1",
    label: `优化`,
    children: <Optimize />,
  },
  {
    key: "2",
    label: `风格`,
    children: <Style />,
  },
  {
    key: "3",
    label: `其他`,
    children: <Authority />,
  },
  {
    key: "4",
    label: `其他`,
    children: <Test />,
  },
];

const App: React.FC = () => (
  <Tabs defaultActiveKey="1" items={items} onChange={onChange} />
);

export default App;
