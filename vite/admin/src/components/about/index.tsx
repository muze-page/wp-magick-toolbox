//关于我
import type { CollapseProps } from "antd";
import { Collapse } from "antd";

import { AboutPlugin, Proposal, Links } from "@/components/about/collapse";

import Source from "@/components/about/table";

const items: CollapseProps["items"] = [
  {
    key: "1",
    label: "关于插件",
    children: <AboutPlugin />,
  },
  {
    key: "2",
    label: "我有建议",
    children: <Proposal />,
  },
  {
    key: "3",
    label: "联系方式",
    children: <Links />,
  },
  {
    key: "4",
    label: "来源",
    children: <Source />,
  },
];
const App: React.FC = () => {
  return (
    <>
      <Collapse accordion items={items} />
    </>
  );
};

export default App;
