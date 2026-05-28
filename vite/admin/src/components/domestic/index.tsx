import Compliance from "@/components/domestic/compliance";
import BaiduPush from "@/components/domestic/baidu_push";
import Wechat from "@/components/domestic/wechat";
import CommentSecurity from "@/components/domestic/comment_security";
import LoginSecurity from "@/components/domestic/login_security";
import Environment from "@/components/domestic/environment";

const App: React.FC = () => {
  return (
    <div className="mabox-app-center">
      <Environment />
      <Compliance />
      <BaiduPush />
      <Wechat />
      <CommentSecurity />
      <LoginSecurity />
    </div>
  );
};

export default App;
