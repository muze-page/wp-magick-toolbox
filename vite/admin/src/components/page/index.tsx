//页面
import { Anchor, Affix } from "antd";
import Comment from "@/components/page/comment";
import Feature from "@/components/page/feature";
import Function from "@/components/page/function";
import Jurisdiction from "@/components/page/jurisdiction";
const App: React.FC = () => {
  const menuList = [
    {
      key: "part-1",
      href: "#part-1",
      title: "外观",
    },
    {
      key: "part-2",
      href: "#part-2",
      title: "权限",
    },
    {
      key: "part-3",
      href: "#part-3",
      title: "功能",
    },
    {
      key: "part-4",
      href: "#part-4",
      title: "评论",
    },
  ];
  return (
    <>
      <Affix offsetTop={100}>
        <Anchor direction="horizontal" targetOffset={150} items={menuList} className="bg-[#f5f5f5]"/>
      </Affix>
      <div id="part-1">
        <Feature /> {/**外观 */}
      </div>
      <div id="part-2">
        <Jurisdiction />
        {/**权限 */}
      </div>

      <div id="part-3">
        <Function />
        {/**功能 */}
      </div>

      <div id="part-4">
        <Comment /> {/**评论 */}
      </div>
    </>
  );
};

export default App;
