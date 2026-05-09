import Compliance from "@/components/domestic/compliance";
import BaiduPush from "@/components/domestic/baidu_push";
import Wechat from "@/components/domestic/wechat";
import CommentSecurity from "@/components/domestic/comment_security";
import LoginSecurity from "@/components/domestic/login_security";

const App: React.FC = () => {
  return (
    <>
      <Compliance />
      <BaiduPush />
      <Wechat />
      <CommentSecurity />
      <LoginSecurity />
    </>
  );
};

export default App;