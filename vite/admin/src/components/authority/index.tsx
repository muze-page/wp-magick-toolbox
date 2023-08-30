//权限管理
import Disable from "@/components/authority/disable";
import Auxiliary from "@/components/authority/auxiliary";
import B2 from "@/components/authority/b2";
import Wx_xcx_link from "@/components/authority/wx_xcx_link";
const App: React.FC = () => {
  return (
    <>
      <Disable />
      <Auxiliary />
      <Wx_xcx_link />
      <B2 />
    </>
  );
};

export default App;
