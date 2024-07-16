import Compose from "@/components/shortcode/compose";
import Pendant from "@/components/shortcode/pendant";
import Demo from "@/components/shortcode/demo";

const App: React.FC = () => {
  return (
    <>
      <div className="describe">短代码描述</div>
      <Compose />
      <Pendant />
      <Demo />
    </>
  );
};

export default App;
