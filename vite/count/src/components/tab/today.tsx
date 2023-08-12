import Count from "../block/count";
import { ShopToday } from "../tool/defaultVar";

const App: React.FC = () => (
  <>
    <div className="count-box">
      {ShopToday.map((item, index) => (
        <Count key={index} data={item} />
      ))}
    </div>
  </>
);

export default App;
