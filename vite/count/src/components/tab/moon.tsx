import Count from "../block/count";
import { MoonShop } from "../tool/defaultVar";

const App: React.FC = () => (
  <>
    <div className="count-box">
      {MoonShop.map((item, index) => (
        <Count key={index} data={item} />
      ))}
    </div>
  </>
);

export default App;
