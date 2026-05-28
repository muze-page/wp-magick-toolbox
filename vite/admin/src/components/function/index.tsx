import Auxiliary from "@/components/function/auxiliary";
import Wx_xcx_link from "@/components/function/wx_xcx_link";
import DownDatabase from "@/components/function/down_database";
import Seo from "@/components/function/seo";
import Tips from "@/components/function/tips";

const App: React.FC = () => {
  return (
    <div className="mabox-app-center">
      <Tips />
      <Seo />
      <DownDatabase />
      <Wx_xcx_link />
      <Auxiliary />
    </div>
  );
};

export default App;
