//发文统计
import ColumnMore from "./block/column_more";
import Count from "./block/count";
import { TodayUserSingle, SingleCount } from "./tool/defaultVar";

const App = () => {
  return (
    <>
      <div className="single-box">
        <div className="left">
          <ColumnMore data={TodayUserSingle} />
        </div>
        <div className="right">
          {SingleCount.map((item, index) => (
            <Count key={index} data={item} />
          ))}
        </div>
      </div>
    </>
  );
};
export default App;
