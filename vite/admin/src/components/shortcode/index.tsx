import Compose from "@/components/shortcode/compose";
import Pendant from "@/components/shortcode/pendant";

const App: React.FC = () => {
  return (
    <>
      <div className="describe">短代码描述</div>
      <Compose />
      <Pendant />
    </>
  );
};

export default App;
